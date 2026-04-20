<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;
use App\Enums\QueueStatus;
use App\Enums\BusinessQueueStatus;
use App\Services\Queue\QueueService;
use Exception;

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
        $staleCount = QueueEntry::where('status', QueueStatus::WAITING->value)
            ->where('created_at', '<', now()->subHours(12))
            ->update([
                'status' => QueueStatus::NO_SHOW->value,
                'position' => 0,
            ]);

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
        $day = strtolower(now()->timezone($timezone)->format('l'));
        $currentTime = now()->timezone($timezone)->format('H:i');

        $hours = is_string($business->business_hours)
            ? json_decode($business->business_hours, true)
            : $business->business_hours;

        if (!$hours || !isset($hours[$day])) {
            return false; // Leave as is if no explicit hours are provided
        }

        $openTime = $hours[$day][0] ?? '00:00';
        $closeTime = $hours[$day][1] ?? '23:59';

        return $currentTime < $openTime || $currentTime > $closeTime;
    }
}
