<?php

use App\Models\Tenant\Business;
use App\Models\Tenant\Subscription;
use App\Models\Queue\QueueEntry;
use App\Services\Queue\QueueService;
use App\Enums\QueueStatus;
use App\Enums\BusinessQueueStatus;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->business = Business::create([
        'name' => 'Expired Biz',
        'slug' => 'expired-biz',
        'join_code' => 'EXP01',
        'queue_prefix' => 'E',
        'queue_status' => BusinessQueueStatus::OPEN->value,
        'current_number' => 0,
        'entries_today' => 0,
        'is_active' => true,
    ]);

    $this->service = new QueueService();
});

it('force closes the queue when a join is attempted with an expired subscription', function () {
    // Create an expired subscription
    Subscription::create([
        'business_id' => $this->business->id,
        'type' => 'daily',
        'status' => 'active',
        'starts_at' => now()->subDays(2),
        'expires_at' => now()->subDay(),
    ]);

    // Create some existing entries
    $entry = QueueEntry::create([
        'business_id' => $this->business->id,
        'ticket_code' => 'T001',
        'ticket_number' => 1,
        'status' => QueueStatus::WAITING->value,
    ]);

    // Attempt to join
    try {
        $this->service->join($this->business, '60123456789');
    } catch (Exception $e) {
        expect($e->getMessage())->toContain('no active subscription');
    }

    $this->business->refresh();
    $entry->refresh();

    // The business should now be CLOSED
    expect($this->business->queue_status)->toBe(BusinessQueueStatus::CLOSED->value);
    
    // The existing entry should be CANCELLED
    expect($entry->status)->toBe(QueueStatus::CANCELLED->value);
});

it('force closes companies when running the expiry command', function () {
    // Create an expired subscription
    Subscription::create([
        'business_id' => $this->business->id,
        'type' => 'daily',
        'status' => 'active',
        'starts_at' => now()->subDays(2),
        'expires_at' => now()->subDay(),
    ]);

    // Create some existing entries
    $entry = QueueEntry::create([
        'business_id' => $this->business->id,
        'ticket_code' => 'T001',
        'ticket_number' => 1,
        'status' => QueueStatus::WAITING->value,
    ]);

    // Run the command
    Artisan::call('subscriptions:expire');

    $this->business->refresh();
    $entry->refresh();

    // The business should now be CLOSED
    expect($this->business->queue_status)->toBe(BusinessQueueStatus::CLOSED->value);
    
    // The existing entry should be CANCELLED
    expect($entry->status)->toBe(QueueStatus::CANCELLED->value);
});
