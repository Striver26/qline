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
        match (DB::getDriverName()) {
            'mysql', 'mariadb' => DB::statement(
                "ALTER TABLE queue_entries MODIFY COLUMN status ENUM('waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled', 'no_show') DEFAULT 'waiting'"
            ),
            'pgsql' => $this->changePostgresEnum(),
            default => Schema::table('queue_entries', function (Blueprint $table) {
                $table->string('status')->default('waiting')->change();
            }),
        };
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        match (DB::getDriverName()) {
            'mysql', 'mariadb' => DB::statement(
                "ALTER TABLE queue_entries MODIFY COLUMN status ENUM('waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled') DEFAULT 'waiting'"
            ),
            'pgsql' => $this->revertPostgresEnum(),
            default => Schema::table('queue_entries', function (Blueprint $table) {
                $table->string('status')->default('waiting')->change();
            }),
        };
    }

    private function changePostgresEnum(): void
    {
        DB::statement('ALTER TABLE queue_entries DROP CONSTRAINT IF EXISTS queue_entries_status_check');
        DB::statement('ALTER TABLE queue_entries ALTER COLUMN status TYPE VARCHAR(255)');
        DB::statement("ALTER TABLE queue_entries ADD CONSTRAINT queue_entries_status_check CHECK (status IN ('waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled', 'no_show'))");
    }

    private function revertPostgresEnum(): void
    {
        DB::statement('ALTER TABLE queue_entries DROP CONSTRAINT IF EXISTS queue_entries_status_check');
        DB::statement("ALTER TABLE queue_entries ADD CONSTRAINT queue_entries_status_check CHECK (status IN ('waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled'))");
    }
};