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
        Schema::create('businesses', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->default('New Business');
            $table->string('slug')->unique();
            $table->string('join_code')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('pause_reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('queue_status', ['open', 'paused', 'closed'])->default('closed');
            $table->string('queue_prefix', 5)->default('Q');
            $table->integer('current_number')->default(0);
            $table->integer('daily_limit')->default(100);
            $table->integer('entries_today')->default(0);
            $table->integer('notify_turns_before')->default(3);
            $table->timestamp('last_reset_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
