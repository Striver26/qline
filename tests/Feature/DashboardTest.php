<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $business = \App\Models\Tenant\Business::create([
        'name' => 'Test Business',
        'slug' => 'test-dash',
        'join_code' => 'DASH01',
    ]);

    $user = User::factory()->create([
        'business_id' => $business->id,
        'profile_completed' => true
    ]);
    
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('business.dashboard'));
    
    $this->get(route('business.dashboard'))->assertOk();
});