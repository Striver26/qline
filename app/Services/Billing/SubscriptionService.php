<?php

namespace App\Services\Billing;

use App\Models\Tenant\Business;
use App\Models\Tenant\Subscription;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
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
        $billingCycle = $tier === 'free'
            ? 'free'
            : ($subscription->billing_cycle ?: ($tierConfig['billing_cycle'] ?? 'monthly'));

        $expiresAt = match ($billingCycle) {
            'free' => null,
            'daily' => now()->addDay(),
            'yearly' => now()->addYear(),
            default => now()->addMonth(),
        };

        $subscription->update([
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
