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

    public function mount($slug, $token)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
        $this->entry = QueueEntry::where('cancel_token', $token)
            ->where('business_id', $this->business->id)
            ->firstOrFail();
            
        $this->currentStatus = $this->entry->status;

        $this->dispatchStoragePersistance($slug, $token);
    }

    #[\Livewire\Attributes\Computed]
    public function positionInfo()
    {
        return app(QueueService::class)->getPositionInfo($this->entry);
    }

    #[\Livewire\Attributes\Computed]
    public function statusLabel()
    {
        return QueueStatus::tryFrom($this->entry->status)?->getLabel() ?? $this->entry->status;
    }

    #[\Livewire\Attributes\Computed]
    public function statusColor()
    {
        return QueueStatus::tryFrom($this->entry->status)?->getColor() ?? 'text-slate-400';
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
        if (!$this->entry->wa_id) {
            return null;
        }

        $visits = \App\Models\Marketing\LoyaltyVisit::where('business_id', $this->business->id)
            ->where('wa_id', $this->entry->wa_id)
            ->count();

        $availableReward = \App\Models\Marketing\EarnedReward::where('business_id', $this->business->id)
            ->where('wa_id', $this->entry->wa_id)
            ->where('status', 'available')
            ->first();

        $nextMilestone = $this->calculateNextMilestone($visits);

        return [
            'visits' => $visits,
            'has_reward' => (bool) $availableReward,
            'reward_name' => $availableReward?->reward?->reward_value ?? 'Gift',
            'next_reward_in' => $nextMilestone['in'],
            'next_reward_name' => $nextMilestone['name']
        ];
    }

    public function render()
    {
        $this->entry->refresh();
        $this->currentStatus = $this->entry->status;

        return view('livewire.public-queue.ticket-status')
            ->layout('layouts.public');
    }

    /**
     * Emit event to explicitly ensure the browser registers session data globally.
     */
    private function dispatchStoragePersistance(string $slug, string $token): void
    {
        $this->dispatch('ticket-joined', 
            slug: $slug, 
            token: $token, 
            date: now()->toDateString()
        );
    }

    /**
     * Compute the exact distance to the next cyclic reward milestone mathematically.
     */
    private function calculateNextMilestone(int $visits): array
    {
        $rewards = \App\Models\Marketing\LoyaltyReward::where('business_id', $this->business->id)->get();

        $nextRewardIn = null;
        $nextRewardName = null;

        foreach ($rewards as $reward) {
            if ($reward->required_visits <= 0) continue;

            $progress = $visits % $reward->required_visits;
            $remaining = $reward->required_visits - $progress;

            if ($nextRewardIn === null || $remaining < $nextRewardIn) {
                $nextRewardIn = $remaining;
                $nextRewardName = $reward->reward_value;
            }
        }

        return [
            'in' => $nextRewardIn,
            'name' => $nextRewardName
        ];
    }
}
