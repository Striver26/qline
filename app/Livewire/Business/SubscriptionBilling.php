<?php

namespace App\Livewire\Business;

use App\Models\Tenant\Business;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Subscription;
use App\Services\Billing\BillPlzService;
use App\Services\Billing\SubscriptionService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SubscriptionBilling extends Component
{
    public string $billingCycle = 'monthly';

    public function mount(): void
    {
        $business = auth()->user()?->getActiveBusiness();

        if ($business) {
            app(SubscriptionService::class)->ensureActiveOrFreeSubscription($business);
        }
    }

    public function subscribe(string $tier = 'free')
    {
        if (! auth()->user()->isOwner()) {
            session()->flash('error', 'Only business owners can modify subscription plans.');

            return;
        }

        $business = auth()->user()->getActiveBusiness();

        if (! $business) {
            session()->flash('error', 'Business account not found.');

            return;
        }

        $tierConfig = config("qline.tiers.{$tier}");
        if (! $tierConfig) {
            session()->flash('error', 'Selected subscription plan is not available.');

            return;
        }

        $cycle = match ($tier) {
            'free' => 'free',
            'daily' => 'daily',
            default => $this->normalizedBillingCycle(),
        };
        $amount = $this->resolveAmount($tier, $cycle);
        $label = $tierConfig['label'] ?? ucfirst($tier);
        $description = "QLine {$label} ".ucfirst($cycle)." Subscription for {$business->name}";

        if ($amount <= 0) {
            app(SubscriptionService::class)->activateFreeSubscription($business);
            session()->flash('success', "{$label} plan activated. Your free queue is ready.");

            return;
        }

        $sub = $this->createPendingSubscription($business->id, $tier, $cycle);
        $payment = $this->createPendingPayment($business->id, $sub->id, $amount);

        try {
            return $this->dispatchToPaymentGateway(app(BillPlzService::class), $business, $payment, $amount, $description);
        } catch (Exception $e) {
            Log::error("Payment gateway generation failed for Business {$business->id}: {$e->getMessage()}");
            session()->flash('error', 'Payment gateway error: '.$e->getMessage());
        }
    }

    public function render()
    {
        $business = auth()->user()->getActiveBusiness();
        if (! $business) {
            return view('livewire.business.subscription-billing', [
                'business' => null,
                'subscription' => null,
                'payments' => collect(),
                'plans' => $this->planCards(),
            ])
                ->layout('layouts.app');
        }
        $subscription = app(SubscriptionService::class)->ensureActiveOrFreeSubscription($business);
        $payments = Payment::where('business_id', $business->id)->orderBy('created_at', 'desc')->limit(20)->get();

        return view('livewire.business.subscription-billing', [
            'business' => $business,
            'subscription' => $subscription,
            'payments' => $payments,
            'plans' => $this->planCards(),
        ])
            ->layout('layouts.app');
    }

    private function createPendingSubscription(int $businessId, string $tier, string $cycle): Subscription
    {
        return Subscription::updateOrCreate(
            ['business_id' => $businessId],
            [
                'type' => $tier,
                'billing_cycle' => $cycle,
                'status' => 'pending',
                'expires_at' => null,
            ]
        );
    }

    private function createPendingPayment(int $businessId, int $subscriptionId, float $amount): Payment
    {
        return Payment::create([
            'business_id' => $businessId,
            'subscription_id' => $subscriptionId,
            'amount' => $amount,
            'status' => 'pending',
        ]);
    }

    private function dispatchToPaymentGateway(
        BillPlzService $billPlz,
        Business $business,
        Payment $payment,
        float $amount,
        string $description
    ): RedirectResponse {
        $callbackUrl = route('webhook.billplz.callback');
        $redirectUrl = route('webhook.billplz.redirect');

        $bill = $billPlz->createBill($business, $amount, $description, $callbackUrl, $redirectUrl);
        $payment->update(['reference' => $bill['id'] ?? 'mock']);

        return redirect()->away($bill['url']);
    }

    private function normalizedBillingCycle(): string
    {
        return $this->billingCycle === 'yearly' ? 'yearly' : 'monthly';
    }

    private function resolveAmount(string $tier, string $cycle): float
    {
        $tierConfig = config("qline.tiers.{$tier}", []);
        $monthlyPrice = (float) ($tierConfig['price'] ?? 0);

        if ($monthlyPrice <= 0) {
            return 0.0;
        }

        if ($cycle === 'yearly') {
            return (float) ($tierConfig['yearly_price'] ?? round($monthlyPrice * 12 * 0.8, 2));
        }

        return $monthlyPrice;
    }

    private function planCards(): array
    {
        $cycle = $this->normalizedBillingCycle();

        return [
            [
                'tier' => 'free',
                'badge' => 'Free',
                'name' => 'Free',
                'description' => 'Run a simple queue at no cost while you get started.',
                'price' => 0,
                'period' => 'forever',
                'button' => 'Use Free Plan',
                'featured' => false,
                'features' => [
                    config('qline.tiers.free.daily_limit', 50).' queue tickets per day',
                    '1 active service point',
                    'QR and web queue access',
                    'Standard support',
                ],
            ],
            [
                'tier' => 'daily',
                'badge' => 'Daily',
                'name' => 'Daily Pass',
                'description' => 'Open the queue for a busy day without committing to a monthly plan.',
                'price' => $this->resolveAmount('daily', 'daily'),
                'period' => 'day',
                'button' => 'Buy Daily Pass',
                'featured' => false,
                'features' => [
                    config('qline.tiers.daily.daily_limit', 100).' queue tickets for the day',
                    '1 active service point',
                    'QR and web queue access',
                    'WhatsApp notifications',
                ],
            ],
            [
                'tier' => 'monthly',
                'badge' => 'Popular',
                'name' => 'Growth',
                'description' => 'For steady businesses that need higher volume and smoother operations.',
                'price' => $this->resolveAmount('monthly', $cycle),
                'period' => $cycle === 'yearly' ? 'yr' : 'mo',
                'button' => $cycle === 'yearly' ? 'Go Yearly' : 'Go Monthly',
                'featured' => false,
                'features' => [
                    config('qline.tiers.monthly.daily_limit', 500).' queue tickets per day',
                    'Up to '.config('qline.tiers.monthly.service_point_limit', 5).' service points',
                    'WhatsApp notifications',
                    'Customer feedback and loyalty rewards',
                ],
            ],
            [
                'tier' => 'advanced',
                'badge' => 'Scale',
                'name' => 'Scale',
                'description' => 'For larger teams that need unlimited volume and priority support.',
                'price' => $this->resolveAmount('advanced', $cycle),
                'period' => $cycle === 'yearly' ? 'yr' : 'mo',
                'button' => 'Get Scale',
                'featured' => true,
                'features' => [
                    'Unlimited queue tickets',
                    'Unlimited service points',
                    'Advanced analytics',
                    'Priority support',
                ],
            ],
        ];
    }
}
