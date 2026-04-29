<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prevent duplicate feedback per ticket and duplicate reward accrual
     * per (ticket, reward program) pair. These constraints enforce data integrity
     * at the DB level, complementing the application-level checks.
     */
    public function up(): void
    {
        // One feedback submission per queue ticket
        try {
            Schema::table('customer_feedback', function (Blueprint $table) {
                $table->unique('queue_entry_id', 'unique_feedback_per_ticket');
            });
        } catch (\Exception $e) {
            // Index might already exist if migration failed halfway previously
        }

        // One earned reward record per (ticket, reward program) — prevents
        // duplicate rewards if the event fires more than once (e.g., retry)
        Schema::table('earned_rewards', function (Blueprint $table) {
            $table->foreignId('queue_entry_id')->nullable()->after('business_id')->constrained()->nullOnDelete();
            $table->unique(['queue_entry_id', 'loyalty_reward_id'], 'unique_reward_per_ticket');
        });
    }

    public function down(): void
    {
        Schema::table('customer_feedback', function (Blueprint $table) {
            $table->dropUnique('unique_feedback_per_ticket');
        });

        Schema::table('earned_rewards', function (Blueprint $table) {
            $table->dropUnique('unique_reward_per_ticket');
            $table->dropForeign(['queue_entry_id']);
            $table->dropColumn('queue_entry_id');
        });
    }
};
