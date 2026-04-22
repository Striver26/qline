<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');           // e.g. 'user.delete', 'business.delete', 'role.change'
            $table->string('target_type');      // e.g. 'App\Models\User'
            $table->unsignedBigInteger('target_id');
            $table->json('meta')->nullable();   // Extra context: old value, new value, etc.
            $table->string('ip')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
    }
};
