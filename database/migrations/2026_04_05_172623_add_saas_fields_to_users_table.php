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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('business_id')->nullable();
            $table->enum('role', ['superadmin', 'platform_staff', 'business_owner', 'business_staff'])->default('business_owner');
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('profile_completed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['business_id', 'role', 'phone', 'address', 'city', 'state', 'postcode', 'is_active', 'profile_completed']);
        });
    }
};
