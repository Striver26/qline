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

    public function mount($slug, $id)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
        $this->entry = QueueEntry::where('id', $id)
            ->where('business_id', $this->business->id)
            ->firstOrFail();
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
        }
    }

    public function render()
    {
        $this->entry->refresh();

        return view('livewire.public-queue.ticket-status')
            ->layout('layouts.public');
    }
}
