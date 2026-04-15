<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'advanced' to the subscriptions type enum.
     * Only runs on MySQL/MariaDB — SQLite treats enum as string already.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE subscriptions MODIFY COLUMN type ENUM('daily', 'monthly', 'advanced') DEFAULT 'daily'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE subscriptions MODIFY COLUMN type ENUM('daily', 'monthly') DEFAULT 'daily'");
        }
    }
};
