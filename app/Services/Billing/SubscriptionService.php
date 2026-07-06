<?php

namespace App\Services\Billing;

use App\Models\Tenant\Business;
use App\Models\Tenant\Subscription;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function ensureActiveOrFreeSubscription(Business $business): Subscription
    {
        $subscription = $business->loadMissing('subscription')->subscription;

        if (
            $subscription
            && $subscription->status === 'active'
            && (! $subscription->expires_at || $subscription->expires_at->isFuture())
        ) {
            return $subscription;
        }

        return $this->activateFreeSubscription($business);
    }

    public function activateFreeSubscription(Business $business): Subscription
    {
        $subscription = $business->subscription()->updateOrCreate(
            ['business_id' => $business->id],
            [
                'type' => 'free',
                'billing_cycle' => 'free',
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => null,
            ]
        );

        $this->activateSubscription($subscription);

        return $subscription->refresh();
    }

    /**
     * Activate the attached subscription based on configuration tiers.
     */
    public function activateSubscription(?Subscription $subscription): void
    {
        if (! $subscription) {
            return;
        }

        $tier = $subscription->type->value ?? $subscription->type;
        $tierConfig = config("qline.tiers.{$tier}", []);
        $billingCycle = $this->billingCycleFor($tier, $subscription->billing_cycle);

        $expiresAt = match ($billingCycle) {
            'free' => null,
            'daily' => now()->addDay(),
            'yearly' => now()->addYear(),
            default => now()->addMonth(),
        };

        $subscription->update([
            'billing_cycle' => $billingCycle,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        $dailyLimit = $tierConfig['daily_limit'] ?? 100;
        $business = $subscription->business;
        $business?->update([
            'daily_limit' => $dailyLimit === 0 ? 999999 : $dailyLimit,
        ]);

        if (($tierConfig['service_points'] ?? false) && $business) {
            $this->enforceServicePointLimit($business, (int) ($tierConfig['service_point_limit'] ?? 0));
        }

        Log::info("Subscription activated for business #{$subscription->business_id}", [
            'tier' => $tier,
            'billing_cycle' => $billingCycle,
            'expires_at' => $expiresAt,
        ]);
    }

    public function billingCycleFor(string $tier, ?string $requestedCycle = null): string
    {
        return match ($tier) {
            'free' => 'free',
            'daily' => 'daily',
            'monthly', 'advanced' => in_array($requestedCycle, ['monthly', 'yearly'], true)
                ? $requestedCycle
                : (string) config("qline.tiers.{$tier}.billing_cycle", 'monthly'),
            default => $requestedCycle ?: 'monthly',
        };
    }

    private function enforceServicePointLimit(Business $business, int $limit): void
    {
        if ($business->servicePoints()->count() === 0) {
            $business->servicePoints()->create([
                'name' => 'Service Point 1',
                'type' => 'service_point',
                'is_active' => true,
            ]);
        }

        if ($limit <= 0) {
            return;
        }

        $servicePointIdsToKeep = $business->servicePoints()
            ->orderByDesc('is_active')
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id');

        $business->servicePoints()
            ->whereNotIn('id', $servicePointIdsToKeep)
            ->update(['is_active' => false]);
    }
}