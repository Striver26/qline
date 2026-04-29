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
        Schema::rename('counters', 'service_points');

        Schema::table('service_points', function (Blueprint $table) {
            $table->string('type')->default('counter')->after('name');
            $table->string('status')->default('free')->after('type');
        });

        $tables = \Illuminate\Support\Facades\DB::table('tables')->get();
        $tableMapping = [];
        foreach ($tables as $t) {
            $newId = \Illuminate\Support\Facades\DB::table('service_points')->insertGetId([
                'business_id' => $t->business_id,
                'name' => $t->name,
                'type' => 'table',
                'status' => $t->status,
                'is_active' => true,
                'created_at' => $t->created_at,
                'updated_at' => $t->updated_at,
            ]);
            $tableMapping[$t->id] = $newId;
        }

        Schema::table('queue_entries', function (Blueprint $table) {
            $table->dropForeign(['counter_id']);
            $table->renameColumn('counter_id', 'service_point_id');
        });

        Schema::table('queue_entries', function (Blueprint $table) {
            $table->foreign('service_point_id')->references('id')->on('service_points')->nullOnDelete();
        });

        foreach ($tableMapping as $oldId => $newId) {
            \Illuminate\Support\Facades\DB::table('queue_entries')->where('table_id', $oldId)->update(['service_point_id' => $newId]);
        }

        Schema::table('queue_entries', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            // Drop indexes created previously
            $table->dropIndex(['business_id', 'table_id']);
            $table->dropColumn('table_id');
        });

        Schema::dropIfExists('tables');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way migration.
    }
};
