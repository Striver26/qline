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

        $nextReward = \App\Models\Marketing\LoyaltyReward::where('business_id', $this->business->id)
            ->where('is_active', true)
            ->where('required_visits', '>', $visits)
            ->orderBy('required_visits', 'asc')
            ->first();

        return [
            'visits' => $visits,
            'has_reward' => !!$availableReward,
            'reward_name' => $availableReward?->reward?->reward_value ?? 'Gift',
            'next_reward_in' => $nextReward ? ($nextReward->required_visits - $visits) : null,
            'next_reward_name' => $nextReward?->reward_value
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
