<?php

namespace App\Services\Analytics;

use App\Enums\QueueStatus;
use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;

class QueueAnalyticsService
{
    public function getSummaryStats(Business $business): array
    {
        $totalEntries = $this->getTotalEntries($business->id);
        $completedEntries = $this->getCompletedEntries($business->id);
        $cancelledEntries = $this->getCancelledEntries($business->id);

        $denominator = $completedEntries + $cancelledEntries;
        $completionRate = $denominator > 0 ? (int) round(($completedEntries / $denominator) * 100) : 0;

        return [
            'total' => $totalEntries,
            'completed' => $completedEntries,
            'cancelled' => $cancelledEntries,
            'completion_rate' => $completionRate,
            'avg_wait_time' => $this->getAverageWaitTime($business->id),
            'avg_service_time' => $this->getAverageServiceTime($business->id),
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
        $avgSeconds = QueueEntry::where('business_id', $businessId)
            ->where('status', QueueStatus::COMPLETED->value)
            ->whereNotNull('called_at')
            ->select(['created_at', 'called_at'])
            ->get()
            ->avg(fn (QueueEntry $entry): int => $entry->called_at->diffInSeconds($entry->created_at));

        return $avgSeconds ? (int) round($avgSeconds / 60) : 0;
    }

    private function getAverageServiceTime(int $businessId): int
    {
        $avgSeconds = QueueEntry::where('business_id', $businessId)
            ->where('status', QueueStatus::COMPLETED->value)
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->select(['called_at', 'completed_at'])
            ->get()
            ->avg(fn (QueueEntry $entry): int => $entry->completed_at->diffInSeconds($entry->called_at));

        return $avgSeconds ? (int) round($avgSeconds / 60) : 0;
    }

    private function getBusiestHour(int $businessId): string
    {
        $busiestHour = QueueEntry::where('business_id', $businessId)
            ->pluck('created_at')
            ->groupBy(fn ($createdAt) => $createdAt->format('H'))
            ->sortByDesc(fn ($group) => $group->count())
            ->keys()
            ->first();

        return $busiestHour ? "{$busiestHour}:00" : 'N/A';
    }
}
