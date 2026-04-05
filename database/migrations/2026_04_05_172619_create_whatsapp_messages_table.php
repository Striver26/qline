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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('queue_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('wa_id');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('template')->nullable();
            $table->text('body');
            $table->string('message_id')->nullable();
            $table->string('status')->default('sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
