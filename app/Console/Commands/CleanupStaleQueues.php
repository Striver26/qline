<?php

namespace App\Console\Commands;

use App\Enums\BusinessQueueStatus;
use App\Enums\QueueStatus;
use App\Events\QueueUpdated;
use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;
use App\Services\Queue\QueueService;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('queue:cleanup-stale')]
#[Description('Clean up stale queues and close queues outside business hours.')]
class CleanupStaleQueues extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(QueueService $queueService)
    {
        $this->markStaleTicketsAsNoShow();
        $this->closeQueuesOutsideBusinessHours($queueService);
    }

    /**
     * Mark WAITING tickets older than 12 hours as NO_SHOW.
     */
    private function markStaleTicketsAsNoShow(): void
    {
        $staleEntries = QueueEntry::query()
            ->select(['id', 'business_id'])
            ->where('status', QueueStatus::WAITING->value)
            ->where('created_at', '<', now()->subHours(12))
            ->get();

        $staleCount = $staleEntries->count();

        QueueEntry::query()
            ->whereIn('id', $staleEntries->pluck('id'))
            ->update([
                'status' => QueueStatus::NO_SHOW->value,
                'position' => 0,
            ]);

        $staleEntries
            ->pluck('business_id')
            ->unique()
            ->each(fn (int $businessId) => event(new QueueUpdated($businessId, 'staleCleanup')));

        $this->info("Marked {$staleCount} stale ticket(s) as NO_SHOW.");
    }

    /**
     * Automatically close queues currently open outside defined business hours.
     */
    private function closeQueuesOutsideBusinessHours(QueueService $queueService): void
    {
        $closedCount = 0;

        // Use chunk to prevent memory spikes if scaling up drastically
        Business::where('queue_status', BusinessQueueStatus::OPEN->value)
            ->chunk(100, function ($businesses) use ($queueService, &$closedCount) {
                foreach ($businesses as $business) {
                    if ($this->shouldCloseQueue($business)) {
                        try {
                            $queueService->closeQueue($business);
                            $closedCount++;
                        } catch (Exception $e) {
                            $this->error("Failed to automatically close queue for business ID [{$business->id}]: {$e->getMessage()}");
                        }
                    }
                }
            });

        $this->info("Automatically closed {$closedCount} queue(s) outside business hours.");
    }

    private function shouldCloseQueue(Business $business): bool
    {
        $timezone = $business->timezone ?? 'Asia/Kuala_Lumpur';
        $now = now()->timezone($timezone);
        $day = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        $hours = $business->business_hours;

        if (! $hours || ! isset($hours[$day])) {
            return false; // Leave as is if no explicit hours are provided
        }

        $config = $hours[$day];

        // If explicitly marked as closed for the day
        if (isset($config['is_open']) && ! $config['is_open']) {
            return true;
        }

        $openTime = $config['open'] ?? '00:00';
        $closeTime = $config['close'] ?? '23:59';

        // Same time means open 24h
        if ($openTime === $closeTime) {
            return false;
        }

        // Case 1: Normal shift (e.g. 08:00 - 18:00)
        if ($openTime < $closeTime) {
            return $currentTime < $openTime || $currentTime > $closeTime;
        }

        // Case 2: Overnight shift (e.g. 22:00 - 04:00)
        // Correct logic: Closed only if currentTime is between closeTime (04:00) and openTime (22:00)
        return $currentTime > $closeTime && $currentTime < $openTime;
    }
}
