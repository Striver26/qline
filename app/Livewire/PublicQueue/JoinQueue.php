<?php

namespace App\Livewire\PublicQueue;

use Livewire\Component;
use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Services\Queue\QueueService;
use App\Enums\QueueStatus;

class JoinQueue extends Component
{
    public Business $business;
    public ?QueueEntry $ticket = null;
    public string $phone = '';
    public bool $joined = false;
    public string $errorMessage = '';

    public function mount($slug)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
    }

    #[\Livewire\Attributes\Computed]
    public function waitingCount()
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->where('status', QueueStatus::WAITING->value)
            ->count();
    }

    public function joinQueue(QueueService $queueService)
    {
        $this->errorMessage = '';

        if ($this->business->queue_status !== 'open') {
            $this->errorMessage = 'Sorry, the queue is currently closed.';
            return;
        }

        // Clean up phone — allow empty for anonymous walk-ins
        $waId = trim($this->phone) ?: null;

        try {
            if ($waId) {
                $this->ticket = $queueService->join($this->business, $waId);
            } else {
                $this->ticket = $queueService->addManual($this->business);
            }
            $this->joined = true;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.public-queue.join-queue')
            ->layout('layouts.public');
    }
}
