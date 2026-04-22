<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The qr_codes table was created in the initial migration but is never
     * used by the application — QR code generation is done on-the-fly using
     * the chillerlan/php-qrcode library. Dropping to remove dead schema.
     */
    public function up(): void
    {
        Schema::dropIfExists('qr_codes');
    }

    public function down(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }
};
