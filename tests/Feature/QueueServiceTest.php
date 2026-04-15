<?php

use App\Models\Tenant\Business;
use App\Models\Tenant\Subscription;
use App\Models\Queue\QueueEntry;
use App\Services\Queue\QueueService;
use App\Enums\QueueStatus;

beforeEach(function () {
    $this->business = Business::create([
        'name' => 'Test Business',
        'slug' => 'test-business',
        'join_code' => 'JOIN01',
        'queue_prefix' => 'T',
        'queue_status' => 'open',
        'current_number' => 0,
        'entries_today' => 0,
        'daily_limit' => 100,
    ]);

    $this->service = new QueueService();
});

// --- Join Queue ---

it('can join an open queue', function () {
    $entry = $this->service->join($this->business, '60123456789');

    expect($entry)->toBeInstanceOf(QueueEntry::class);
    expect($entry->status)->toBe(QueueStatus::WAITING->value);
    expect($entry->ticket_code)->toStartWith('T');
    expect($entry->position)->toBe(1);
    expect($entry->wa_id)->toBe('60123456789');
    expect($entry->source)->toBe('whatsapp');
});

it('rejects joining a closed queue', function () {
    $this->business->update(['queue_status' => 'closed']);

    $this->service->join($this->business, '60123456789');
})->throws(Exception::class, 'The queue is currently closed.');

it('rejects joining a paused queue', function () {
    $this->business->update(['queue_status' => 'paused']);

    $this->service->join($this->business, '60123456789');
})->throws(Exception::class, 'The queue is currently closed.');

it('limits customers to 3 tickets per day', function () {
    $this->service->join($this->business, '60123456789');
    $this->service->join($this->business, '60123456789');
    $this->service->join($this->business, '60123456789');

    $this->service->join($this->business, '60123456789');
})->throws(Exception::class, 'maximum queue limit');

it('assigns sequential ticket numbers', function () {
    $e1 = $this->service->join($this->business, '60111111111');
    $e2 = $this->service->join($this->business, '60222222222');
    $e3 = $this->service->join($this->business, '60333333333');

    expect($e1->ticket_number)->toBe(1);
    expect($e2->ticket_number)->toBe(2);
    expect($e3->ticket_number)->toBe(3);
});

it('assigns correct positions on join', function () {
    $e1 = $this->service->join($this->business, '60111111111');
    $e2 = $this->service->join($this->business, '60222222222');

    expect($e1->position)->toBe(1);
    expect($e2->position)->toBe(2);
});

// --- Manual Add ---

it('can manually add a ticket', function () {
    $entry = $this->service->addManual($this->business);

    expect($entry)->toBeInstanceOf(QueueEntry::class);
    expect($entry->source)->toBe('manual');
    expect($entry->wa_id)->toBeNull();
    expect($entry->position)->toBe(1);
});

// --- Call Next ---

it('calls the next waiting ticket', function () {
    $e1 = $this->service->join($this->business, '60111111111');
    $e2 = $this->service->join($this->business, '60222222222');

    $called = $this->service->callNext($this->business);

    expect($called->id)->toBe($e1->id);
    expect($called->status)->toBe(QueueStatus::CALLED->value);
    expect($called->called_at)->not->toBeNull();
    expect($called->position)->toBe(0);

    // Second entry should now be position 1
    $e2->refresh();
    expect($e2->position)->toBe(1);
});

it('returns null when no tickets are waiting', function () {
    $result = $this->service->callNext($this->business);

    expect($result)->toBeNull();
});

// --- Mark Serving ---

it('marks a called ticket as serving', function () {
    $entry = $this->service->join($this->business, '60111111111');
    $this->service->callNext($this->business);

    $entry->refresh();
    $this->service->markServing($entry);

    $entry->refresh();
    expect($entry->status)->toBe(QueueStatus::SERVING->value);
    expect($entry->served_at)->not->toBeNull();
});

// --- Mark Done ---

it('marks a ticket as completed', function () {
    $entry = $this->service->join($this->business, '60111111111');
    $this->service->callNext($this->business);

    $entry->refresh();
    $this->service->markDone($entry);

    $entry->refresh();
    expect($entry->status)->toBe(QueueStatus::COMPLETED->value);
    expect($entry->completed_at)->not->toBeNull();
    expect($entry->position)->toBe(0);
});

// --- Skip ---

it('marks a ticket as skipped', function () {
    $e1 = $this->service->join($this->business, '60111111111');
    $e2 = $this->service->join($this->business, '60222222222');

    $this->service->skip($e1);

    $e1->refresh();
    expect($e1->status)->toBe(QueueStatus::SKIPPED->value);
    expect($e1->position)->toBe(0);

    // Second entry should now be position 1
    $e2->refresh();
    expect($e2->position)->toBe(1);
});

// --- Cancel ---

it('cancels a waiting ticket', function () {
    $e1 = $this->service->join($this->business, '60111111111');
    $e2 = $this->service->join($this->business, '60222222222');

    $this->service->cancel($e1);

    $e1->refresh();
    expect($e1->status)->toBe(QueueStatus::CANCELLED->value);
    expect($e1->position)->toBe(0);

    $e2->refresh();
    expect($e2->position)->toBe(1);
});

// --- Close Queue ---

it('cancels all active tickets and resets counters when queue is closed', function () {
    $e1 = $this->service->join($this->business, '60111111111'); // waiting
    $e2 = $this->service->addManual($this->business);           // waiting

    $this->service->callNext($this->business);                  // e1 becomes called

    $this->business->update([
        'current_number' => 10,
        'entries_today' => 10
    ]);

    $this->service->closeQueue($this->business->refresh());

    $e1->refresh();
    $e2->refresh();
    $this->business->refresh();

    expect($e1->status)->toBe(QueueStatus::CANCELLED->value);
    expect($e2->status)->toBe(QueueStatus::CANCELLED->value);
    expect($this->business->queue_status)->toBe('closed');
    expect($this->business->current_number)->toBe(0);
    expect($this->business->entries_today)->toBe(0);
});

// --- Open Queue ---

it('opens queue and resets daily counters', function () {
    $this->business->update([
        'queue_status' => 'closed',
        'current_number' => 50,
        'entries_today' => 50,
        'last_reset_at' => now()->subDay(),
    ]);

    // Requires active subscription
    Subscription::create([
        'business_id' => $this->business->id,
        'type' => 'daily',
        'status' => 'active',
        'starts_at' => now(),
        'expires_at' => now()->addDay(),
    ]);

    $this->service->openQueue($this->business->refresh());
    $this->business->refresh();

    expect($this->business->queue_status)->toBe('open');
    expect($this->business->current_number)->toBe(0);
    expect($this->business->entries_today)->toBe(0);
});

it('rejects opening queue without active subscription', function () {
    $this->business->update(['queue_status' => 'closed']);

    $this->service->openQueue($this->business);
})->throws(Exception::class, 'active subscription');

// --- Pause Queue ---

it('pauses queue with a reason', function () {
    $this->service->pauseQueue($this->business, 'Lunch break');
    $this->business->refresh();

    expect($this->business->queue_status)->toBe('paused');
    expect($this->business->pause_reason)->toBe('Lunch break');
});

// --- Recalculate Positions ---

it('recalculates positions correctly after removal', function () {
    $e1 = $this->service->join($this->business, '60111111111');
    $e2 = $this->service->join($this->business, '60222222222');
    $e3 = $this->service->join($this->business, '60333333333');

    // Call the first ticket (removes from waiting)
    $this->service->callNext($this->business);

    $e2->refresh();
    $e3->refresh();

    expect($e2->position)->toBe(1);
    expect($e3->position)->toBe(2);
});
