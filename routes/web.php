<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InviteController;

Route::view('/', 'welcome')->name('home');

// Webhooks
Route::get('/webhook/whatsapp', [\App\Http\Controllers\Webhooks\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook/whatsapp', [\App\Http\Controllers\Webhooks\WhatsAppWebhookController::class, 'process']);

// Auth routes for standard Fortify happen automatically

// Invite Logic
Route::get('/register/invite/{token}', [InviteController::class, 'show'])->name('invite.show');
Route::post('/register/invite/{token}', [InviteController::class, 'process'])->name('invite.process');

// Public Queue App
Route::get('/q/{slug}/status/{id}', \App\Livewire\PublicQueue\TicketStatus::class)->name('public.status');
Route::get('/q/{slug}/tv', \App\Livewire\PublicQueue\TvDisplay::class)->name('public.tv');
Route::get('/q/{slug}/join', \App\Livewire\PublicQueue\JoinQueue::class)->name('public.join');
Route::get('/q/{slug}/feedback/{token}', \App\Livewire\PublicQueue\FeedbackForm::class)->name('public.feedback');

// Business Panel
Route::middleware(['auth', 'verified'])->prefix('business')->name('business.')->group(function () {
    // Map settings.business directly to BusinessSettings to serve as onboarding portal
    // Moved to routes/settings.php

    // The core business panel routes
    Route::middleware([\App\Http\Middleware\RequireProfileCompleted::class])->group(function () {
        Route::get('dashboard', \App\Livewire\Business\QueueDashboard::class)->name('dashboard');
        Route::get('entries', \App\Livewire\Business\QueueEntries::class)->name('entries');
        Route::get('feedback', \App\Livewire\Business\CustomerFeedback::class)->name('feedback');
        Route::get('staff', \App\Livewire\Business\StaffManagement::class)->name('staff');

        Route::get('qr', \App\Livewire\Business\QrCode::class)->name('qr');
        Route::get('billing', \App\Livewire\Business\SubscriptionBilling::class)->name('billing');
        Route::get('rewards', \App\Livewire\Business\LoyaltyRewards::class)->name('rewards');
    });
});

// Admin Platform Panel
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('users', \App\Livewire\Admin\Users\UsersIndex::class)->name('users');
    Route::get('businesses', \App\Livewire\Admin\Businesses\BusinessesIndex::class)->name('businesses');
    Route::get('subscriptions', \App\Livewire\Admin\Subscriptions\SubscriptionsIndex::class)->name('subscriptions');
    Route::get('payments', \App\Livewire\Admin\Payments\PaymentsIndex::class)->name('payments');
    Route::get('wa-messages', \App\Livewire\Admin\WaMessages\WaMessagesIndex::class)->name('wa-messages');
    Route::get('queue-entries', \App\Livewire\Admin\QueueEntries\QueueEntriesIndex::class)->name('queue-entries');
    Route::get('feedback', \App\Livewire\Admin\Feedback\FeedbackIndex::class)->name('feedback');
});

require __DIR__ . '/settings.php';
