<?php

namespace App\Listeners;

use App\Events\TicketCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessLoyaltyRewards implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TicketCompleted $event): void
    {
        $entry = $event->entry;

        if (!$entry->wa_id) {
            return;
        }

        // Use firstOrCreate so that queue retries don't double-count visits
        $visit = \App\Models\Marketing\LoyaltyVisit::firstOrCreate([
            'business_id'    => $entry->business_id,
            'queue_entry_id' => $entry->id,
        ], [
            'wa_id'        => $entry->wa_id,
            'visit_number' => \App\Models\Marketing\LoyaltyVisit::where('business_id', $entry->business_id)
                ->where('wa_id', $entry->wa_id)
                ->count() + 1,
        ]);

        $visitNumber = $visit->visit_number;

        // Check if any loyalty rewards were earned
        $rewards = \App\Models\Marketing\LoyaltyReward::where('business_id', $entry->business_id)
            ->where('is_active', true)
            ->get();

        foreach ($rewards as $reward) {
            if ($reward->required_visits <= 0) {
                continue;
            }

            if ($visitNumber % $reward->required_visits === 0) {
                // firstOrCreate protects against duplicate rewards on queue retry
                \App\Models\Marketing\EarnedReward::firstOrCreate([
                    'queue_entry_id'   => $entry->id,
                    'loyalty_reward_id'=> $reward->id,
                ], [
                    'business_id' => $entry->business_id,
                    'wa_id'       => $entry->wa_id,
                    'status'      => \App\Enums\RewardStatus::AVAILABLE->value,
                ]);
            }
        }
    }
}
