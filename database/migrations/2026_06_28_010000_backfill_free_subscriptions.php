<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscriptions') || ! Schema::hasTable('businesses')) {
            return;
        }

        $now = now();
        $freeLimit = (int) config('qline.tiers.free.daily_limit', 50);

        DB::table('subscriptions')
            ->where('type', 'free')
            ->update([
                'billing_cycle' => 'free',
                'status' => 'active',
                'expires_at' => null,
                'updated_at' => $now,
            ]);

        DB::table('subscriptions')
            ->where('type', 'daily')
            ->update([
                'billing_cycle' => 'daily',
                'updated_at' => $now,
            ]);

        DB::table('subscriptions')
            ->whereIn('type', ['monthly', 'advanced'])
            ->whereNotIn('billing_cycle', ['monthly', 'yearly'])
            ->update([
                'billing_cycle' => 'monthly',
                'updated_at' => $now,
            ]);

        DB::table('subscriptions')
            ->where(function ($query): void {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'active')
                    ->orWhere(function ($expired): void {
                        $expired->whereNotNull('expires_at')
                            ->where('expires_at', '<', now());
                    });
            })
            ->update([
                'type' => 'free',
                'billing_cycle' => 'free',
                'status' => 'active',
                'starts_at' => $now,
                'expires_at' => null,
                'updated_at' => $now,
            ]);

        $businessIdsWithSubscription = DB::table('subscriptions')->pluck('business_id')->all();

        DB::table('businesses')
            ->when($businessIdsWithSubscription !== [], function ($query) use ($businessIdsWithSubscription): void {
                $query->whereNotIn('id', $businessIdsWithSubscription);
            })
            ->orderBy('id')
            ->chunkById(100, function ($businesses) use ($now): void {
                $rows = $businesses->map(fn ($business): array => [
                    'business_id' => $business->id,
                    'type' => 'free',
                    'billing_cycle' => 'free',
                    'status' => 'active',
                    'starts_at' => $now,
                    'expires_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if ($rows !== []) {
                    DB::table('subscriptions')->insert($rows);
                }
            });

        $freeBusinessIds = DB::table('subscriptions')
            ->where('type', 'free')
            ->where('status', 'active')
            ->pluck('business_id')
            ->all();

        if ($freeBusinessIds !== []) {
            DB::table('businesses')
                ->whereIn('id', $freeBusinessIds)
                ->update([
                    'daily_limit' => $freeLimit,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        // Data backfill only; do not remove subscriptions or downgrade active plans.
    }
};