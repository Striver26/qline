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
        Schema::create('queue_entries', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('wa_id')->nullable();
            $table->string('cancel_token')->nullable();
            $table->integer('ticket_number');
            $table->string('ticket_code');
            $table->enum('status', ['waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled'])->default('waiting');
            $table->string('source')->default('whatsapp');
            $table->integer('position')->default(0);
            $table->foreignId('counter_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_entries');
    }
};
