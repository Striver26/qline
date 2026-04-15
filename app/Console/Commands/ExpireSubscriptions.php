<?php

namespace App\Console\Commands;

use App\Models\Tenant\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire subscriptions that have passed their expiration date';

    public function handle(): int
    {
        $expired = Subscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info("No expired subscriptions found.");
        } else {
            foreach ($expired as $sub) {
                $sub->update(['status' => 'expired']);
                $this->info("Expired subscription for Business ID: {$sub->business_id}");
            }
            $this->info("Total expired: {$expired->count()} subscription(s).");
        }

        return self::SUCCESS;
    }
}
