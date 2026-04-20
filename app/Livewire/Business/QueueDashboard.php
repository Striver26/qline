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
            ->orderBy('id', 'asc')
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

    public function markServing(QueueService $queueService, int $entryId)
    {
        $entry = $this->getBusinessEntry($entryId);
        if ($entry) {
            $queueService->markServing($entry);
        }
    }

    public function markDone(QueueService $queueService, int $entryId)
    {
        $entry = $this->getBusinessEntry($entryId);
        if ($entry) {
            $queueService->markDone($entry);
        }
    }

    public function skip(QueueService $queueService, int $entryId)
    {
        $entry = $this->getBusinessEntry($entryId);
        if ($entry) {
            $queueService->skip($entry);
        }
    }

    public function toggleQueue(QueueService $queueService)
    {
        try {
            if ($this->isQueueActive()) {
                $queueService->closeQueue($this->business);
            } else {
                $queueService->openQueue($this->business);
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
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
        try {
            $queueService->openQueue($this->business);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
        $this->business->refresh();
    }

    public function addCustomer(QueueService $queueService)
    {
        try {
            $queueService->addManual($this->business);
            $this->business->refresh();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function redeemReward(\App\Services\Marketing\RewardService $rewardService, int $rewardId)
    {
        if ($rewardService->redeem($rewardId, $this->business)) {
            $this->dispatch('reward-redeemed');
        }
    }

    public function render()
    {
        return view('livewire.business.queue-dashboard')
            ->layout('layouts.app');
    }

    /**
     * Helper to safely fetch an entry explicitly constrained to the logged-in business.
     */
    private function getBusinessEntry(int $entryId): ?QueueEntry
    {
        return QueueEntry::where('business_id', $this->business->id)->find($entryId);
    }

    /**
     * Check if the queue is open or paused.
     */
    private function isQueueActive(): bool
    {
        return in_array($this->business->queue_status, [
            \App\Enums\BusinessQueueStatus::OPEN->value, 
            \App\Enums\BusinessQueueStatus::PAUSED->value
        ], true);
    }
}

