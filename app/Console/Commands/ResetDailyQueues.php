<?php

namespace App\Console\Commands;

use App\Models\Tenant\Business;
use App\Enums\QueueStatus;
use Illuminate\Console\Command;

class ResetDailyQueues extends Command
{
    protected $signature = 'queue:reset-daily';

    protected $description = 'Reset daily queue counters for all businesses and close any open queues';

    public function handle(\App\Services\Queue\QueueService $queueService): int
    {
        $businesses = Business::all();
        $closedCount = 0;
        $resetCount = 0;
        $skippedCount = 0;

        foreach ($businesses as $business) {
            $isStale = false;
            
            // Check if business has recent activity (tickets in last 4 hours)
            $lastEntry = $business->queueEntries()->latest()->first();
            $hasRecentActivity = $lastEntry && $lastEntry->created_at->gt(now()->subHours(4));

            if ($business->queue_status === 'open' || $business->queue_status === 'paused') {
                if ($hasRecentActivity) {
                    $this->info("Skipping active business: {$business->name} (Last ticket at {$lastEntry->created_at})");
                    $skippedCount++;
                    continue;
                }
                
                // If not active, force close it
                $queueService->closeQueue($business);
                $this->info("Force closed stale queue for: {$business->name}");
                $closedCount++;
                $resetCount++;
            } else {
                // Business is already closed, just ensure counters are reset for the new day
                $today = now()->startOfDay();
                $lastReset = $business->last_reset_at ? $business->last_reset_at->startOfDay() : null;

                if (!$lastReset || $lastReset->lt($today)) {
                    $business->update([
                        'current_number' => 0,
                        'entries_today' => 0,
                        'last_reset_at' => now(),
                    ]);
                    $resetCount++;
                }
            }
        }

        $this->info("Summary: {$closedCount} force-closed, {$resetCount} reset, {$skippedCount} skipped (active).");

        return self::SUCCESS;
    }
}
