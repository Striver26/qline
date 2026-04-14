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
        $this->business = auth()->user()->business;
    }

    #[\Livewire\Attributes\Computed]
    public function queueStats()
    {
        $businessId = $this->business->id;

        $totalEntries = QueueEntry::where('business_id', $businessId)->count();
        $completedEntries = QueueEntry::where('business_id', $businessId)
            ->where('status', QueueStatus::COMPLETED->value)
            ->count();
        
        $cancelledEntries = QueueEntry::where('business_id', $businessId)
            ->whereIn('status', [QueueStatus::CANCELLED->value, QueueStatus::SKIPPED->value])
            ->count();

        $completionRate = $totalEntries > 0 ? round(($completedEntries / $totalEntries) * 100) : 0;

        // Calculate average wait time (minutes)
        $avgWaitTime = QueueEntry::where('business_id', $businessId)
            ->whereNotNull('called_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, called_at)) as avg_wait'))
            ->value('avg_wait');

        // Busiest hour (0-23)
        $busiestHour = QueueEntry::where('business_id', $businessId)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        return [
            'total' => $totalEntries,
            'completed' => $completedEntries,
            'cancelled' => $cancelledEntries,
            'completion_rate' => $completionRate,
            'avg_wait_time' => $avgWaitTime ? round($avgWaitTime) : 0,
            'busiest_hour' => $busiestHour ? str_pad($busiestHour->hour, 2, '0', STR_PAD_LEFT) . ':00' : 'N/A',
        ];
    }

    public function render()
    {
        return view('livewire.business.analytics-dashboard')
            ->layout('layouts.app');
    }
}
