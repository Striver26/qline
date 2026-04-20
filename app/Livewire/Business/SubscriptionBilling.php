<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Tenant\Subscription;
use App\Models\Tenant\Payment;
use App\Services\Billing\BillPlzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class SubscriptionBilling extends Component
{
    public function subscribe(BillPlzService $billPlz, string $tier = 'daily')
    {
        $business = auth()->user()->business;

        $tierConfig = config("qline.tiers.{$tier}");
        $amount = $tierConfig['price'] ?? ($tier === 'daily' ? 10.00 : 300.00);
        $description = "QLine " . ucfirst($tier) . " Subscription for {$business->name}";

        $sub = $this->createPendingSubscription($business->id, $tier);
        $payment = $this->createPendingPayment($business->id, $sub->id, (float) $amount);

        try {
            return $this->dispatchToPaymentGateway($billPlz, $business, $payment, (float) $amount, $description);
        } catch (Exception $e) {
            Log::error("Payment gateway generation failed for Business {$business->id}: {$e->getMessage()}");
            session()->flash('error', 'Payment gateway error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $business = auth()->user()->business;
        $subscription = $business->subscription;
        $payments = Payment::where('business_id', $business->id)->orderBy('created_at', 'desc')->get();

        return view('livewire.business.subscription-billing', compact('business', 'subscription', 'payments'))
            ->layout('layouts.app');
    }

    private function createPendingSubscription(int $businessId, string $tier): Subscription
    {
        return Subscription::updateOrCreate(
            ['business_id' => $businessId],
            [
                'type' => $tier,
                'status' => 'pending',
                'expires_at' => null
            ]
        );
    }

    private function createPendingPayment(int $businessId, int $subscriptionId, float $amount): Payment
    {
        return Payment::create([
            'business_id' => $businessId,
            'subscription_id' => $subscriptionId,
            'amount' => $amount,
            'status' => 'pending'
        ]);
    }

    private function dispatchToPaymentGateway(
        BillPlzService $billPlz, 
        \App\Models\Tenant\Business $business, 
        Payment $payment, 
        float $amount, 
        string $description
    ): \Illuminate\Http\RedirectResponse {
        $callbackUrl = route('webhook.billplz.callback');
        $redirectUrl = route('webhook.billplz.redirect');

        $bill = $billPlz->createBill($business, $amount, $description, $callbackUrl, $redirectUrl);
        $payment->update(['reference' => $bill['id'] ?? 'mock']);

        return redirect()->away($bill['url']);
    }
}
