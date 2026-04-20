<?php

namespace App\Services\Analytics;

use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Enums\QueueStatus;
use Illuminate\Support\Facades\DB;

class QueueAnalyticsService
{
    public function getSummaryStats(Business $business): array
    {
        $totalEntries = $this->getTotalEntries($business->id);
        $completedEntries = $this->getCompletedEntries($business->id);
        $cancelledEntries = $this->getCancelledEntries($business->id);
        
        $completionRate = $totalEntries > 0 ? (int) round(($completedEntries / $totalEntries) * 100) : 0;

        return [
            'total' => $totalEntries,
            'completed' => $completedEntries,
            'cancelled' => $cancelledEntries,
            'completion_rate' => $completionRate,
            'avg_wait_time' => $this->getAverageWaitTime($business->id),
            'busiest_hour' => $this->getBusiestHour($business->id),
        ];
    }

    private function getTotalEntries(int $businessId): int
    {
        return QueueEntry::where('business_id', $businessId)->count();
    }

    private function getCompletedEntries(int $businessId): int
    {
        return QueueEntry::where('business_id', $businessId)
            ->where('status', QueueStatus::COMPLETED->value)
            ->count();
    }

    private function getCancelledEntries(int $businessId): int
    {
        return QueueEntry::where('business_id', $businessId)
            ->whereIn('status', [QueueStatus::CANCELLED->value, QueueStatus::SKIPPED->value])
            ->count();
    }

    private function getAverageWaitTime(int $businessId): int
    {
        $avgWaitTime = QueueEntry::where('business_id', $businessId)
            ->whereNotNull('called_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, called_at)) as avg_wait'))
            ->value('avg_wait');

        return $avgWaitTime ? (int) round($avgWaitTime) : 0;
    }

    private function getBusiestHour(int $businessId): string
    {
        $busiestHour = QueueEntry::where('business_id', $businessId)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        return $busiestHour ? str_pad($busiestHour->hour, 2, '0', STR_PAD_LEFT) . ':00' : 'N/A';
    }
}
