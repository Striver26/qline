<?php

use App\Models\User;
use App\Enums\UserRole;

it('blocks unauthenticated users from admin routes', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
});

it('blocks business owners from admin routes', function () {
    $user = User::factory()->create([
        'role' => UserRole::BUSINESS_OWNER->value,
    ]);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertStatus(403);
});

it('blocks business staff from admin routes', function () {
    $user = User::factory()->create([
        'role' => UserRole::BUSINESS_STAFF->value,
    ]);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertStatus(403);
});

it('allows superadmin to access admin routes', function () {
    $user = User::factory()->create([
        'role' => UserRole::SUPERADMIN->value,
    ]);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertSuccessful();
});

it('allows platform staff to access admin routes', function () {
    $user = User::factory()->create([
        'role' => UserRole::PLATFORM_STAFF->value,
    ]);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertSuccessful();
});

it('blocks business owners from all admin sub-routes', function () {
    $user = User::factory()->create([
        'role' => UserRole::BUSINESS_OWNER->value,
    ]);

    $routes = [
        '/admin/analytics',
        '/admin/users',
        '/admin/businesses',
        '/admin/subscriptions',
        '/admin/payments',
        '/admin/wa-messages',
        '/admin/queue-entries',
        '/admin/feedback',
    ];

    foreach ($routes as $route) {
        $this->actingAs($user)
            ->get($route)
            ->assertStatus(403, "Expected 403 for {$route}");
    }
});
