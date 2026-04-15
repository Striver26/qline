<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Subscription;
use App\Services\Billing\BillPlzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillPlzWebhookController extends Controller
{
    /**
     * Handle BillPlz payment callback (server-to-server).
     * This is the callback_url that BillPlz POSTs to after payment.
     */
    public function callback(Request $request, BillPlzService $billPlz)
    {
        $data = $request->all();

        Log::info('BillPlz Callback Received', ['data' => $data]);

        // Verify the x_signature to ensure authenticity
        if (!$billPlz->verifySignature($data)) {
            Log::warning('BillPlz callback signature verification failed', ['data' => $data]);
            return response('Invalid signature', 403);
        }

        return $this->processPayment($data);
    }

    /**
     * Handle BillPlz redirect (user returns to app after payment).
     * This is the redirect_url the user sees after paying.
     */
    public function redirect(Request $request, BillPlzService $billPlz)
    {
        $data = $request->all();

        Log::info('BillPlz Redirect Received', ['data' => $data]);

        // Verify X-Signature
        if (!$billPlz->verifySignature($data)) {
            return redirect()->route('business.billing')
                ->with('error', 'Payment verification failed. Please contact support if you were charged.');
        }

        $this->processPayment($data);

        $isPaid = ($data['paid'] ?? '') === 'true';

        if ($isPaid) {
            return redirect()->route('business.billing')
                ->with('success', 'Payment confirmed! Your subscription is now active.');
        }

        return redirect()->route('business.billing')
            ->with('error', 'Payment was not completed. Please try again.');
    }

    /**
     * Process the payment data from BillPlz.
     */
    protected function processPayment(array $data): \Illuminate\Http\Response
    {
        $billId = $data['id'] ?? null;
        $isPaid = ($data['paid'] ?? '') === 'true';
        $paidAmount = $data['paid_amount'] ?? null;

        if (!$billId) {
            Log::error('BillPlz callback missing bill ID');
            return response('Missing bill ID', 400);
        }

        // Find the payment record by BillPlz bill reference
        $payment = Payment::where('reference', $billId)->first();

        if (!$payment) {
            Log::warning("BillPlz callback: no payment found for bill ID {$billId}");
            return response('Payment not found', 404);
        }

        // Prevent double-processing
        if ($payment->status === 'paid') {
            return response('Already processed', 200);
        }

        if ($isPaid) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Activate the subscription
            $subscription = $payment->subscription;
            if ($subscription) {
                $tier = $subscription->type->value;
                $tierConfig = config("qline.tiers.{$tier}", []);
                $billingCycle = $tierConfig['billing_cycle'] ?? 'daily';

                $expiresAt = $billingCycle === 'daily'
                    ? now()->addDay()
                    : now()->addMonth();

                $subscription->update([
                    'status' => 'active',
                    'starts_at' => now(),
                    'expires_at' => $expiresAt,
                ]);

                // Update business daily_limit based on tier
                $dailyLimit = $tierConfig['daily_limit'] ?? 100;
                $subscription->business?->update([
                    'daily_limit' => $dailyLimit === 0 ? 999999 : $dailyLimit,
                ]);

                Log::info("Subscription activated for business #{$subscription->business_id}", [
                    'tier' => $tier,
                    'expires_at' => $expiresAt,
                ]);
            }
        } else {
            $payment->update([
                'status' => 'failed',
            ]);

            Log::info("Payment failed for bill {$billId}");
        }

        return response('OK', 200);
    }
}
