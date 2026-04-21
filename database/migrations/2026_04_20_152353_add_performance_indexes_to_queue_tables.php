<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix duplicate cancel_tokens first
        $duplicates = \Illuminate\Support\Facades\DB::table('queue_entries')
            ->select('cancel_token')
            ->whereNotNull('cancel_token')
            ->groupBy('cancel_token')
            ->havingRaw('count(*) > 1')
            ->pluck('cancel_token');

        foreach ($duplicates as $token) {
            $ids = \Illuminate\Support\Facades\DB::table('queue_entries')
                ->where('cancel_token', $token)
                ->pluck('id');
            
            // Keep the first ID, update others
            $ids->shift();
            foreach ($ids as $id) {
                \Illuminate\Support\Facades\DB::table('queue_entries')
                    ->where('id', $id)
                    ->update(['cancel_token' => \Illuminate\Support\Str::random(32)]);
            }
        }

        Schema::table('queue_entries', function (Blueprint $table) {
            $table->index(['business_id', 'status']);
            $table->unique('cancel_token');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Ensure we don't have multiple 'mock' references if not in local
            $table->unique('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_entries', function (Blueprint $table) {
            $table->dropUnique(['cancel_token']);
            $table->dropIndex(['business_id', 'status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['reference']);
        });
    }
};
