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
        Schema::create('loyalty_visits', function (Blueprint $table) {
                        $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('wa_id');
            $table->foreignId('queue_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('visit_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_visits');
    }
};
