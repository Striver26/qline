<?php

use App\Enums\TableStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default(TableStatus::FREE->value);
            $table->timestamps();

            $table->index(['business_id', 'status']);
        });

        Schema::table('queue_entries', function (Blueprint $table) {
            $table->foreignId('table_id')
                ->nullable()
                ->after('counter_id')
                ->constrained('tables')
                ->nullOnDelete();

            $table->index(['business_id', 'table_id']);
            $table->index(['business_id', 'status', 'id']);
            $table->index(['business_id', 'status', 'called_at']);
        });
    }

    public function down(): void
    {
        Schema::table('queue_entries', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            $table->dropIndex(['business_id', 'table_id']);
            $table->dropIndex(['business_id', 'status', 'id']);
            $table->dropIndex(['business_id', 'status', 'called_at']);
            $table->dropColumn('table_id');
        });

        Schema::dropIfExists('tables');
    }
};
