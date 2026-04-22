<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a proper foreign key constraint on users.business_id so that
     * deleting a business cascades to nullify users rather than leaving
     * orphaned references. Previously this column existed with no FK.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ensure no orphaned references exist before adding constraint
            // (nullify any users pointing to non-existent businesses)
            \DB::statement('UPDATE users SET business_id = NULL WHERE business_id IS NOT NULL AND business_id NOT IN (SELECT id FROM businesses)');

            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });
    }
};
