<?php

namespace App\Livewire\PublicQueue;

use Livewire\Component;
use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;
use App\Enums\QueueStatus;
use App\Services\Queue\QueueService;

class TicketStatus extends Component
{
    public Business $business;
    public QueueEntry $entry;
    public string $currentStatus;

    public function mount($slug, $id)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
        $this->entry = QueueEntry::where('id', $id)
            ->where('business_id', $this->business->id)
            ->firstOrFail();
            
        $this->currentStatus = $this->entry->status;

        // Ensure browser remembers this ticket
        $this->dispatch('ticket-joined', slug: $slug, id: $id);
    }

    #[\Livewire\Attributes\Computed]
    public function positionInfo()
    {
        return app(QueueService::class)->getPositionInfo($this->entry);
    }

    #[\Livewire\Attributes\Computed]
    public function statusLabel()
    {
        return match($this->entry->status) {
            QueueStatus::WAITING->value => 'Waiting',
            QueueStatus::CALLED->value => 'Called',
            QueueStatus::SERVING->value => 'Being Served',
            QueueStatus::COMPLETED->value => 'Completed',
            QueueStatus::SKIPPED->value => 'Skipped',
            QueueStatus::CANCELLED->value => 'Cancelled',
            default => $this->entry->status,
        };
    }

    #[\Livewire\Attributes\Computed]
    public function statusColor()
    {
        return match($this->entry->status) {
            QueueStatus::WAITING->value => 'text-amber-400',
            QueueStatus::CALLED->value => 'text-teal-400',
            QueueStatus::SERVING->value => 'text-blue-400',
            QueueStatus::COMPLETED->value => 'text-emerald-400',
            QueueStatus::SKIPPED->value => 'text-orange-400',
            QueueStatus::CANCELLED->value => 'text-red-400',
            default => 'text-slate-400',
        };
    }

    public function cancelTicket()
    {
        if ($this->entry->status === QueueStatus::WAITING->value) {
            app(QueueService::class)->cancel($this->entry);
            $this->entry->refresh();

            // Persistence: clear from storage
            $this->dispatch('ticket-cleared', slug: $this->business->slug);
        }
    }

    #[\Livewire\Attributes\Computed]
    public function loyaltyPoints()
    {
        if (!$this->entry->wa_id) return null;

        $visits = \App\Models\Marketing\LoyaltyVisit::where('business_id', $this->business->id)
            ->where('wa_id', $this->entry->wa_id)
            ->count();

        $availableReward = \App\Models\Marketing\EarnedReward::where('business_id', $this->business->id)
            ->where('wa_id', $this->entry->wa_id)
            ->where('status', 'available')
            ->first();

        // Cyclic Loyalty Logic: Calculate next milestone for all active rewards
        $rewards = \App\Models\Marketing\LoyaltyReward::where('business_id', $this->business->id)
            ->get();

        $nextRewardIn = null;
        $nextRewardName = null;

        foreach ($rewards as $reward) {
            if ($reward->required_visits <= 0) continue;

            // How many visits remaining in the current cycle for this reward
            $progress = $visits % $reward->required_visits;
            $remaining = $reward->required_visits - $progress;

            // If remaining matches required_visits and we have no reward pending, 
            // it means they are at the start of a cycle. 
            // If they HAVE a reward pending, we don't need to show 'Next' yet as 
            // the 'has_reward' check takes precedence in the Blade.

            if ($nextRewardIn === null || $remaining < $nextRewardIn) {
                $nextRewardIn = $remaining;
                $nextRewardName = $reward->reward_value;
            }
        }

        return [
            'visits' => $visits,
            'has_reward' => !!$availableReward,
            'reward_name' => $availableReward?->reward?->reward_value ?? 'Gift',
            'next_reward_in' => $nextRewardIn,
            'next_reward_name' => $nextRewardName
        ];
    }

    public function render()
    {
        $this->entry->refresh();
        $this->currentStatus = $this->entry->status;

        return view('livewire.public-queue.ticket-status')
            ->layout('layouts.public');
    }
}
