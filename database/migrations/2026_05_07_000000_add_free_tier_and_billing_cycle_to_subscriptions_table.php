<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE subscriptions MODIFY COLUMN type ENUM('free', 'daily', 'monthly', 'advanced') DEFAULT 'free'");
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'billing_cycle')) {
                $table->string('billing_cycle')->default('monthly')->after('type');
            }
        });

        DB::table('subscriptions')
            ->where('type', 'daily')
            ->update(['billing_cycle' => 'daily']);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'billing_cycle')) {
                $table->dropColumn('billing_cycle');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE subscriptions MODIFY COLUMN type ENUM('daily', 'monthly', 'advanced') DEFAULT 'daily'");
        }
    }
};
