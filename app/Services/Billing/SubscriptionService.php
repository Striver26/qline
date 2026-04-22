<?php

namespace App\Services\Billing;

use App\Models\Tenant\Subscription;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Activate the attached subscription based on configuration tiers.
     */
    public function activateSubscription(?Subscription $subscription): void
    {
        if (!$subscription) {
            return;
        }

        $tier = $subscription->type->value ?? $subscription->type;
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

        $dailyLimit = $tierConfig['daily_limit'] ?? 100;
        $business = $subscription->business;
        $business?->update([
            'daily_limit' => $dailyLimit === 0 ? 999999 : $dailyLimit,
        ]);

        // Seed a default counter if tier supports counters and none exist
        if (($tierConfig['counters'] ?? false) && $business && $business->counters()->count() === 0) {
            $business->counters()->create([
                'name' => 'Counter 1',
                'is_active' => true,
            ]);
        }

        Log::info("Subscription activated for business #{$subscription->business_id}", [
            'tier' => $tier,
            'expires_at' => $expiresAt,
        ]);
    }
}
