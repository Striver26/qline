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
    }

    #[\Livewire\Attributes\Computed]
    public function nowServing()
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->whereIn('status', [QueueStatus::CALLED->value, QueueStatus::SERVING->value])
            ->orderBy('called_at', 'desc')
            ->take(4)
            ->get();
    }

    #[\Livewire\Attributes\Computed]
    public function waitingList()
    {
        return QueueEntry::where('business_id', $this->business->id)
            ->where('status', QueueStatus::WAITING->value)
            ->orderBy('position', 'asc')
            ->take(12)
            ->get();
    }

    #[\Livewire\Attributes\Computed]
    public function waitingCount()
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
