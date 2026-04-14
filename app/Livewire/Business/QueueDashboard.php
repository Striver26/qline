<?php

namespace App\Livewire\Business;



use Livewire\Component;
use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Services\Queue\QueueService;
use App\Enums\QueueStatus;

class QueueDashboard extends Component
{
    public Business $business;
    public string $pauseReason = '';

    public function mount()
    {
        $this->business = auth()->user()->business;
    }

    #[\Livewire\Attributes\Computed]
    public function waitingEntries()
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->where('status', QueueStatus::WAITING->value)
            ->orderBy('position', 'asc')
            ->get();
    }

    #[\Livewire\Attributes\Computed]
    public function activeEntries()
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->whereIn('status', [QueueStatus::CALLED->value, QueueStatus::SERVING->value])
            ->orderBy('called_at', 'desc')
            ->get();
    }

    public function callNext(QueueService $queueService)
    {
        $queueService->callNext($this->business);
        $this->business->refresh();
    }

    public function markServing(QueueService $queueService, $entryId)
    {
        $entry = QueueEntry::find($entryId);
        if ($entry && $entry->business_id === $this->business->id) {
            $queueService->markServing($entry);
        }
    }

    public function markDone(QueueService $queueService, $entryId)
    {
        $entry = QueueEntry::find($entryId);
        if ($entry && $entry->business_id === $this->business->id) {
            $queueService->markDone($entry);
        }
    }

    public function skip(QueueService $queueService, $entryId)
    {
        $entry = QueueEntry::find($entryId);
        if ($entry && $entry->business_id === $this->business->id) {
            $queueService->skip($entry);
        }
    }

    public function toggleQueue(QueueService $queueService)
    {
        if ($this->business->queue_status === 'open' || $this->business->queue_status === 'paused') {
            $queueService->closeQueue($this->business);
        } else {
            $queueService->openQueue($this->business);
        }
        $this->business->refresh();
    }

    public function pauseQueue(QueueService $queueService)
    {
        $this->validate(['pauseReason' => 'required|string|max:255']);
        $queueService->pauseQueue($this->business, $this->pauseReason);
        $this->business->refresh();
        $this->pauseReason = '';
        $this->dispatch('close-modal', name: 'pause-queue-modal');
    }

    public function resumeQueue(QueueService $queueService)
    {
        $queueService->openQueue($this->business);
        $this->business->refresh();
    }

    public function redeemReward($rewardId)
    {
        $reward = \App\Models\Marketing\EarnedReward::where('business_id', $this->business->id)
            ->where('id', $rewardId)
            ->where('status', 'available')
            ->first();

        if ($reward) {
            $reward->update([
                'status' => 'redeemed',
                'redeemed_at' => now(),
            ]);
            $this->dispatch('reward-redeemed');
        }
    }

    public function render()
    {
        return view('livewire.business.queue-dashboard')
            ->layout('layouts.app');
    }
}

