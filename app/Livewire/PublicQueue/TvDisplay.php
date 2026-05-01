<?php

namespace App\Livewire\PublicQueue;

use App\Enums\QueueStatus;
use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class TvDisplay extends Component
{
    public int $businessId;

    public Business $business;

    public function mount($slug)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
        $this->businessId = $this->business->id;
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

    #[Computed]
    public function nowServing(): Collection
    {
        return QueueEntry::query()
            ->with(['servicePoint:id,name'])
            ->where('business_id', $this->businessId)
            ->whereIn('status', [QueueStatus::CALLED->value, QueueStatus::SERVING->value])
            ->orderBy('called_at', 'desc')
            ->take(4)
            ->get();
    }

    #[Computed]
    public function waitingList(): Collection
    {
        return QueueEntry::query()
            ->where('business_id', $this->businessId)
            ->where('status', QueueStatus::WAITING->value)
            ->orderBy('id', 'asc')
            ->take(12)
            ->get();
    }

    #[Computed]
    public function waitingCount(): int
    {
        return QueueEntry::where('business_id', $this->businessId)
            ->where('status', QueueStatus::WAITING->value)
            ->count();
    }

    #[On('echo:business.{businessId},QueueUpdated')]
    public function syncRealtime(): void
    {
        $this->business->refresh();
    }

    public function render()
    {
        return view('livewire.public-queue.tv-display')
            ->layout('layouts.tv');
    }
}
