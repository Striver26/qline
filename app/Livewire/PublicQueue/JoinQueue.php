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
        $this->loadBusinessBySlug($slug);
        $this->loadActiveReward();
    }

    #[\Livewire\Attributes\Computed]
    public function waitingCount(): int
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->where('status', QueueStatus::WAITING->value)
            ->count();
    }

    public function joinQueue(QueueService $queueService)
    {
        $this->errorMessage = '';

        if (!$this->ensureQueueIsJoinable()) {
            return;
        }

        $waId = $this->sanitizePhone($this->phone);

        try {
            $ticket = $waId 
                ? $queueService->join($this->business, $waId) 
                : $queueService->addManual($this->business);

            return redirect()->route('public.status', [
                'slug' => $this->business->slug, 
                'token' => $ticket->cancel_token
            ]);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    private function loadBusinessBySlug(string $slug): void
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
    }

    private function loadActiveReward(): void
    {
        $this->activeReward = \App\Models\Marketing\LoyaltyReward::where('business_id', $this->business->id)
            ->where('is_active', true)
            ->orderBy('required_visits', 'asc')
            ->first();
    }

    private function ensureQueueIsJoinable(): bool
    {
        if ($this->business->queue_status === \App\Enums\BusinessQueueStatus::OPEN->value) {
            return true;
        }

        if ($this->business->queue_status === \App\Enums\BusinessQueueStatus::PAUSED->value) {
            $this->errorMessage = 'Sorry, the queue is currently paused: ' . ($this->business->pause_reason ?: 'On a short break.');
        } else {
            $this->errorMessage = 'Sorry, the queue is currently closed.';
        }

        return false;
    }

    private function sanitizePhone(string $phone): ?string
    {
        return trim($phone) ?: null;
    }

    public function render()
    {
        return view('livewire.public-queue.join-queue')
            ->layout('layouts.public');
    }
}
