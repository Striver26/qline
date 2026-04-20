<?php

namespace App\Listeners;

use App\Events\TicketCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessLoyaltyRewards
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketCompleted $event): void
    {
        $entry = $event->entry;

        if (!$entry->wa_id) {
            return;
        }

        $visitNumber = \App\Models\Marketing\LoyaltyVisit::where('business_id', $entry->business_id)
            ->where('wa_id', $entry->wa_id)
            ->count() + 1;

        \App\Models\Marketing\LoyaltyVisit::create([
            'business_id' => $entry->business_id,
            'wa_id' => $entry->wa_id,
            'queue_entry_id' => $entry->id,
            'visit_number' => $visitNumber
        ]);

        // Check if any loyalty rewards were earned
        $rewards = \App\Models\Marketing\LoyaltyReward::where('business_id', $entry->business_id)
            ->where('is_active', true)
            ->get();

        foreach ($rewards as $reward) {
            if ($visitNumber % $reward->required_visits === 0) {
                \App\Models\Marketing\EarnedReward::create([
                    'business_id' => $entry->business_id,
                    'wa_id' => $entry->wa_id,
                    'loyalty_reward_id' => $reward->id,
                    'status' => \App\Enums\RewardStatus::AVAILABLE->value
                ]);
            }
        }
    }
}
