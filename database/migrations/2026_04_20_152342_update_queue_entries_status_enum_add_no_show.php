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
        Schema::table('queue_entries', function (Blueprint $table) {
            $table->enum('status', ['waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled', 'no_show'])
                ->default('waiting')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_entries', function (Blueprint $table) {
            $table->enum('status', ['waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled'])
                ->default('waiting')
                ->change();
        });
    }
};
