<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Enums\QueueStatus;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboard extends Component
{
    public Business $business;

    public function mount()
    {
        $this->business = auth()->user()->getActiveBusiness();
    }

    #[\Livewire\Attributes\Computed]
    public function queueStats(): array
    {
        $analyticsService = app(\App\Services\Analytics\QueueAnalyticsService::class);
        return $analyticsService->getSummaryStats($this->business);
    }

    public function render()
    {
        return view('livewire.business.analytics-dashboard')
            ->layout('layouts.app');
    }
}
