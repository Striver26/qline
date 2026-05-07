<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('service_points')) {
            return;
        }

        DB::table('service_points')
            ->where('type', 'counter')
            ->update(['type' => 'service_point']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('service_points')) {
            return;
        }

        DB::table('service_points')
            ->where('type', 'service_point')
            ->update(['type' => 'counter']);
    }
};
