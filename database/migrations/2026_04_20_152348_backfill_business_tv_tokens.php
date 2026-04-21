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
        // First, ensure existing nulls are filled
        \Illuminate\Support\Facades\DB::table('businesses')->whereNull('tv_token')->get()->each(function ($business) {
            \Illuminate\Support\Facades\DB::table('businesses')
                ->where('id', $business->id)
                ->update(['tv_token' => \Illuminate\Support\Str::random(32)]);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->string('tv_token', 32)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('tv_token')->nullable()->unique(false)->change();
            $table->dropUnique(['tv_token']);
        });
    }
};
