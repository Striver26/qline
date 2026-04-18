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
    public ?\App\Models\Marketing\LoyaltyReward $activeReward = null;
    public ?QueueEntry $ticket = null;
    public string $phone = '';
    public bool $joined = false;
    public string $errorMessage = '';

    public function mount($slug)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();

        $this->activeReward = \App\Models\Marketing\LoyaltyReward::where('business_id', $this->business->id)
            ->where('is_active', true)
            ->orderBy('required_visits', 'asc')
            ->first();
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
            if ($this->business->queue_status === 'paused') {
                $this->errorMessage = 'Sorry, the queue is currently paused: ' . ($this->business->pause_reason ?: 'On a short break.');
            } else {
                $this->errorMessage = 'Sorry, the queue is currently closed.';
            }
            return;
        }

        // Clean up phone — allow empty for anonymous walk-ins
        $waId = trim($this->phone) ?: null;

        try {
            if ($waId) {
                $ticket = $queueService->join($this->business, $waId);
            } else {
                $ticket = $queueService->addManual($this->business);
            }

            return redirect()->route('public.status', ['slug' => $this->business->slug, 'token' => $ticket->cancel_token]);
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
