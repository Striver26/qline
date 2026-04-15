<?php

use App\Models\Tenant\Business;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Subscription;
use App\Services\Billing\BillPlzService;

beforeEach(function () {
    $this->business = Business::create([
        'name' => 'Test Biz',
        'slug' => 'test-biz',
        'join_code' => 'TEST01',
        'queue_prefix' => 'T',
    ]);

    $this->subscription = Subscription::create([
        'business_id' => $this->business->id,
        'type' => 'daily',
        'status' => 'pending',
    ]);

    $this->payment = Payment::create([
        'business_id' => $this->business->id,
        'subscription_id' => $this->subscription->id,
        'amount' => 10.00,
        'status' => 'pending',
        'reference' => 'bill_test_123',
    ]);
});

it('processes a successful payment callback', function () {
    // Mock the BillPlzService to accept any signature in tests
    $this->mock(BillPlzService::class, function ($mock) {
        $mock->shouldReceive('verifySignature')->andReturn(true);
    });

    $response = $this->post('/webhook/billplz/callback', [
        'id' => 'bill_test_123',
        'paid' => 'true',
        'paid_amount' => '1000',
        'x_signature' => 'test_signature',
    ]);

    $response->assertStatus(200);

    $this->payment->refresh();
    expect($this->payment->status)->toBe('paid');
    expect($this->payment->paid_at)->not->toBeNull();

    $this->subscription->refresh();
    expect($this->subscription->status)->toBe('active');
    expect($this->subscription->starts_at)->not->toBeNull();
    expect($this->subscription->expires_at)->not->toBeNull();
});

it('handles a failed payment callback', function () {
    $this->mock(BillPlzService::class, function ($mock) {
        $mock->shouldReceive('verifySignature')->andReturn(true);
    });

    $response = $this->post('/webhook/billplz/callback', [
        'id' => 'bill_test_123',
        'paid' => 'false',
        'x_signature' => 'test_signature',
    ]);

    $response->assertStatus(200);

    $this->payment->refresh();
    expect($this->payment->status)->toBe('failed');

    $this->subscription->refresh();
    expect($this->subscription->status)->toBe('pending');
});

it('rejects callbacks with invalid signatures', function () {
    $this->mock(BillPlzService::class, function ($mock) {
        $mock->shouldReceive('verifySignature')->andReturn(false);
    });

    $response = $this->post('/webhook/billplz/callback', [
        'id' => 'bill_test_123',
        'paid' => 'true',
        'x_signature' => 'forged_signature',
    ]);

    $response->assertStatus(403);

    // Payment should remain unchanged
    $this->payment->refresh();
    expect($this->payment->status)->toBe('pending');
});

it('returns 404 for unknown bill IDs', function () {
    $this->mock(BillPlzService::class, function ($mock) {
        $mock->shouldReceive('verifySignature')->andReturn(true);
    });

    $response = $this->post('/webhook/billplz/callback', [
        'id' => 'bill_nonexistent',
        'paid' => 'true',
        'x_signature' => 'test_signature',
    ]);

    $response->assertStatus(404);
});

it('prevents double-processing of paid payments', function () {
    $this->payment->update([
        'status' => 'paid',
        'paid_at' => now(),
    ]);

    $this->mock(BillPlzService::class, function ($mock) {
        $mock->shouldReceive('verifySignature')->andReturn(true);
    });

    $response = $this->post('/webhook/billplz/callback', [
        'id' => 'bill_test_123',
        'paid' => 'true',
        'x_signature' => 'test_signature',
    ]);

    $response->assertStatus(200);
    $response->assertContent('Already processed');
});

it('redirects user with success message after paid payment', function () {
    $this->mock(BillPlzService::class, function ($mock) {
        $mock->shouldReceive('verifySignature')->andReturn(true);
    });

    $response = $this->get('/webhook/billplz/redirect?' . http_build_query([
        'id' => 'bill_test_123',
        'paid' => 'true',
        'x_signature' => 'test_signature',
    ]));

    $response->assertRedirect(route('business.billing'));
    $response->assertSessionHas('success');
});
