<?php

namespace App\Services\Marketing;

use App\Models\Tenant\Business;
use App\Models\Marketing\EarnedReward;

class RewardService
{
    /**
     * Mark an available earned reward as redeemed.
     */
    public function redeem(int $rewardId, Business $business): bool
    {
        $reward = EarnedReward::where('business_id', $business->id)
            ->where('id', $rewardId)
            ->where('status', 'available')
            ->first();

        if ($reward) {
            return $reward->update([
                'status' => 'redeemed',
                'redeemed_at' => now(),
            ]);
        }

        return false;
    }
}
