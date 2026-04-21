<?php

namespace App\Console\Commands;

use App\Models\Tenant\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire subscriptions that have passed their expiration date';

    public function handle(\App\Services\Queue\QueueService $queueService): int
    {
        $expired = Subscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with('business')
            ->get();

        if ($expired->isEmpty()) {
            $this->info("No expired subscriptions found.");
        } else {
            foreach ($expired as $sub) {
                $sub->update(['status' => 'expired']);
                
                // Force close the queue to cancel pending tickets
                $queueService->closeQueue($sub->business);
                
                $this->info("Expired subscription and closed queue for: {$sub->business->name}");
            }
            $this->info("Total expired: {$expired->count()} subscription(s).");
        }

        return self::SUCCESS;
    }
}
