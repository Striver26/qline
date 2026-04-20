<?php

namespace App\Livewire\PublicQueue;

use Livewire\Component;
use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Enums\QueueStatus;

class TvDisplay extends Component
{
    public Business $business;

    public function mount($slug)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
        $this->authorizeDisplayAccess();
    }

    /**
     * Prevent unauthorized viewing of the private TV feed.
     */
    private function authorizeDisplayAccess(): void
    {
        if (request()->query('token') !== $this->business->tv_token) {
            abort(403, 'Unauthorized access to TV Display.');
        }
    }

    #[\Livewire\Attributes\Computed]
    public function nowServing(): \Illuminate\Database\Eloquent\Collection
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->whereIn('status', [QueueStatus::CALLED->value, QueueStatus::SERVING->value])
            ->orderBy('called_at', 'desc')
            ->take(4)
            ->get();
    }

    #[\Livewire\Attributes\Computed]
    public function waitingList(): \Illuminate\Database\Eloquent\Collection
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->where('status', QueueStatus::WAITING->value)
            ->orderBy('id', 'asc')
            ->take(12)
            ->get();
    }

    #[\Livewire\Attributes\Computed]
    public function waitingCount(): int
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->where('status', QueueStatus::WAITING->value)
            ->count();
    }

    public function render()
    {
        return view('livewire.public-queue.tv-display')
            ->layout('layouts.public');
    }
}
