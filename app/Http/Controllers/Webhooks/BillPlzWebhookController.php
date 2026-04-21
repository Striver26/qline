<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Subscription;
use App\Services\Billing\BillPlzService;
use App\Services\Billing\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillPlzWebhookController extends Controller
{
    /**
     * Handle BillPlz payment callback (server-to-server).
     */
    public function callback(Request $request, BillPlzService $billPlz, SubscriptionService $subscriptionService): \Illuminate\Http\Response
    {
        $data = $request->all();

        if (!$this->verifySignatureAndLog('Callback', $data, $billPlz)) {
            return response('Invalid signature', 403);
        }

        return $this->processPayment($data, $subscriptionService);
    }

    /**
     * Handle BillPlz redirect (user returns to app after payment).
     */
    public function redirect(Request $request, BillPlzService $billPlz, SubscriptionService $subscriptionService): \Illuminate\Http\RedirectResponse
    {
        $data = $request->all();

        if (!$this->verifySignatureAndLog('Redirect', $data, $billPlz)) {
            return redirect()->route('business.billing')
                ->with('error', 'Payment verification failed. Please contact support if you were charged.');
        }

        $response = $this->processPayment($data, $subscriptionService);

        if ($response->status() !== 200) {
            return redirect()->route('business.billing')
                ->with('error', 'Payment processing failed: ' . $response->content());
        }

        if ($this->isPaid($data)) {
            return redirect()->route('business.billing')
                ->with('success', 'Payment confirmed! Your subscription is now active.');
        }

        return redirect()->route('business.billing')
            ->with('error', 'Payment was not completed. Please try again.');
    }

    /**
     * Helper to verify signature and log the inbound request.
     */
    private function verifySignatureAndLog(string $context, array $data, BillPlzService $billPlz): bool
    {
        Log::info("BillPlz {$context} Received", ['data' => $data]);

        if (!$billPlz->verifySignature($data)) {
            Log::warning("BillPlz {$context} signature verification failed", ['data' => $data]);
            return false;
        }

        return true;
    }

    /**
     * Check if payload indicates payment was successful.
     */
    private function isPaid(array $data): bool
    {
        return ($data['paid'] ?? '') === 'true';
    }

    /**
     * Process the payment data from BillPlz.
     */
    protected function processPayment(array $data, SubscriptionService $subscriptionService): \Illuminate\Http\Response
    {
        $billId = $data['id'] ?? null;
        $isPaid = $this->isPaid($data);

        if (!$billId) {
            Log::error('BillPlz callback missing bill ID');
            return response('Missing bill ID', 400);
        }

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($billId, $isPaid, $subscriptionService, $data) {
                $payment = Payment::where('reference', $billId)->lockForUpdate()->first();

                if (!$payment) {
                    Log::warning("BillPlz callback: no payment found for bill ID {$billId}");
                    return response('Payment not found', 404);
                }

                if ($payment->status === 'paid') {
                    return response('Already processed', 200);
                }

                if (isset($data['amount']) && (float) $data['amount'] !== (float) ($payment->amount * 100)) {
                    Log::error("BillPlz callback: amount mismatch for bill ID {$billId}");
                    return response('Amount mismatch', 422);
                }

                if ($isPaid) {
                    $this->handleSuccessfulPayment($payment, $subscriptionService);
                } else {
                    $this->handleFailedPayment($payment, $billId);
                }

                return response('OK', 200);
            });
        } catch (\Exception $e) {
            Log::error("Failed to process payment for bill {$billId}: {$e->getMessage()}");
            return response('System Error', 500);
        }
    }

    /**
     * Update payment and activate the subscription.
     */
    private function handleSuccessfulPayment(Payment $payment, SubscriptionService $subscriptionService): void
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $subscriptionService->activateSubscription($payment->subscription);
    }

    /**
     * Mark payment as failed.
     */
    private function handleFailedPayment(Payment $payment, string $billId): void
    {
        $payment->update([
            'status' => 'failed',
        ]);

        Log::info("Payment failed for bill {$billId}");
    }
}
