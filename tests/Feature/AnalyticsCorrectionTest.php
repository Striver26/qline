<?php

use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Enums\QueueStatus;
use App\Services\Analytics\QueueAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AnalyticsCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_refined_metrics_correctly()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $this->markTestSkipped('Analytics service uses MySQL-specific functions (TIMESTAMPDIFF, HOUR). Run against MySQL.');
        }

        $business = Business::create([
            'name' => 'Metric Biz',
            'slug' => 'metric-biz',
            'join_code' => 'MT01',
            'queue_prefix' => 'M',
        ]);

        $now = now();

        // 1. COMPLETED Entry (Wait: 10m, Service: 5m)
        $entry = new QueueEntry([
            'business_id' => $business->id,
            'ticket_code' => 'M001',
            'ticket_number' => 1,
            'status' => QueueStatus::COMPLETED->value,
            'called_at' => $now->copy()->subMinutes(5),
            'completed_at' => $now,
        ]);
        $entry->created_at = $now->copy()->subMinutes(15);
        $entry->save();

        // 2. CANCELLED Entry (Wait N/A for KPI, should not affect avg_wait)
        QueueEntry::create([
            'business_id' => $business->id,
            'ticket_code' => 'M002',
            'ticket_number' => 2,
            'status' => QueueStatus::CANCELLED->value,
            'created_at' => $now->copy()->subMinutes(10),
            'called_at' => $now->copy()->subMinutes(2), // Called then cancelled
        ]);

        // 3. WAITING Entry (Should not affect completion_rate)
        QueueEntry::create([
            'business_id' => $business->id,
            'ticket_code' => 'M003',
            'ticket_number' => 3,
            'status' => QueueStatus::WAITING->value,
            'created_at' => $now,
        ]);

        $service = new QueueAnalyticsService();
        $stats = $service->getSummaryStats($business);

        // Completion Rate: 1 done, 1 cancelled. 1 / (1+1) = 50%
        $this->assertEquals(50, $stats['completion_rate']);

        // Avg Wait Time: Only COMPLETED M001 (10 mins).
        $this->assertEquals(10, $stats['avg_wait_time']);

        // Avg Service Time: Only COMPLETED M001 (5 mins).
        $this->assertEquals(5, $stats['avg_service_time']);
    }
}
