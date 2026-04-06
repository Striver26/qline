<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Tenant\Subscription;
use App\Models\Tenant\Payment;
use App\Services\Billing\BillPlzService;

class SubscriptionBilling extends Component
{
    public function subscribe(BillPlzService $billPlz, $tier = 'daily')
    {
        $business = auth()->user()->business;
        
        $amount = $tier === 'daily' ? 15.00 : 400.00;
        $description = "QLine " . ucfirst($tier) . " Subscription for {$business->name}";
        
        $sub = Subscription::updateOrCreate(
            ['business_id' => $business->id],
            [
                'tier' => $tier,
                'status' => 'pending',
                'expires_at' => null
            ]
        );

        $payment = Payment::create([
            'business_id' => $business->id,
            'subscription_id' => $sub->id,
            'amount' => $amount,
            'payment_status' => 'pending'
        ]);

        $callbackUrl = route('invite.show', 'dummy'); 
        $redirectUrl = route('business.billing');

        try {
            $bill = $billPlz->createBill($business, $amount, $description, $callbackUrl, $redirectUrl);
            $payment->update(['bill_id' => $bill['id'] ?? 'mock']);
            return redirect()->away($bill['url']);
        } catch (\Exception $e) {
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
}

