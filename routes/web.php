<?php

use App\Enums\UserRole;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\Webhooks\BillPlzWebhookController;
use App\Http\Controllers\Webhooks\WhatsAppWebhookController;
use App\Http\Middleware\RequireAdminRole;
use App\Http\Middleware\RequireOwnerRole;
use App\Http\Middleware\RequireProfileCompleted;
use App\Livewire\Admin\Businesses\BusinessesIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Feedback\FeedbackIndex;
use App\Livewire\Admin\Payments\PaymentsIndex;
use App\Livewire\Admin\QueueEntries\QueueEntriesIndex;
use App\Livewire\Admin\Subscriptions\SubscriptionsIndex;
use App\Livewire\Admin\Users\UsersIndex;
use App\Livewire\Admin\WaMessages\WaMessagesIndex;
use App\Livewire\Business\AnalyticsDashboard;
use App\Livewire\Business\CommandCenter\Index;
use App\Livewire\Business\ServicePointManagement;
use App\Livewire\Business\CustomerFeedback;
use App\Livewire\Business\LoyaltyRewards;
use App\Livewire\Business\QrCode;
use App\Livewire\Business\QueueEntries;
use App\Livewire\Business\StaffManagement;
use App\Livewire\Business\SubscriptionBilling;
use App\Livewire\PublicQueue\FeedbackForm;
use App\Livewire\PublicQueue\JoinQueue;
use App\Livewire\PublicQueue\TicketStatus;
use App\Livewire\PublicQueue\TvDisplay;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('dashboard', function () {
    if (
        auth()->user()->role === UserRole::SUPERADMIN ||
        auth()->user()->role === UserRole::PLATFORM_STAFF
    ) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('business.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('contact', function () {
    return view('contact');
})->name('contact');

Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'ms'])) {
        abort(400);
    }

    session(['locale' => $locale]);

    return back();
})->name('lang.switch');

// Webhooks
Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'process']);

// BillPlz Payment Webhooks (no auth — server-to-server)
Route::post('/webhook/billplz/callback', [BillPlzWebhookController::class, 'callback'])->name('webhook.billplz.callback');
Route::get('/webhook/billplz/redirect', [BillPlzWebhookController::class, 'redirect'])->name('webhook.billplz.redirect');

// Auth routes for standard Fortify happen automatically

// Invite Logic
Route::get('/register/invite/{token}', [InviteController::class, 'show'])->name('invite.show');
Route::post('/register/invite/{token}', [InviteController::class, 'process'])->name('invite.process');

// Public Queue App
Route::get('/q/{slug}/status/{token}', TicketStatus::class)->name('public.status');
Route::get('/q/{slug}/tv', TvDisplay::class)->name('public.tv');
Route::get('/q/{slug}/join', JoinQueue::class)->middleware('throttle:5,1')->name('public.join');
Route::get('/q/{slug}/feedback/{token}', FeedbackForm::class)->name('public.feedback');

// Business Panel
Route::middleware(['auth', 'verified'])->prefix('business')->name('business.')->group(function () {
    // Map settings.business directly to BusinessSettings to serve as onboarding portal
    // Moved to routes/settings.php

    // The core business panel routes
    Route::middleware([RequireProfileCompleted::class])->group(function () {
        Route::get('dashboard', Index::class)->name('dashboard');
        Route::get('entries', QueueEntries::class)->name('entries');
        Route::get('feedback', CustomerFeedback::class)->name('feedback');
        Route::get('staff', StaffManagement::class)->name('staff');
        Route::get('analytics', AnalyticsDashboard::class)->name('analytics');

        Route::get('qr', QrCode::class)->name('qr');
        Route::get('billing', SubscriptionBilling::class)
            ->middleware([
                RequireOwnerRole::class,
                'password.confirm',
            ])
            ->name('billing');
        Route::get('rewards', LoyaltyRewards::class)->name('rewards');
        Route::get('service-points', ServicePointManagement::class)->name('service-points');
    });
});

// Admin Platform Panel
Route::middleware(['auth', 'verified', RequireAdminRole::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('analytics', App\Livewire\Admin\AnalyticsDashboard::class)->name('analytics');
    // Sensitive management pages — require fresh password confirmation
    Route::middleware('password.confirm')->group(function () {
        Route::get('users', UsersIndex::class)->name('users');
        Route::get('businesses', BusinessesIndex::class)->name('businesses');
    });
    Route::get('subscriptions', SubscriptionsIndex::class)->name('subscriptions');
    Route::get('payments', PaymentsIndex::class)->name('payments');
    Route::get('wa-messages', WaMessagesIndex::class)->name('wa-messages');
    Route::get('queue-entries', QueueEntriesIndex::class)->name('queue-entries');
    Route::get('feedback', FeedbackIndex::class)->name('feedback');
    Route::get('users/{user}/impersonate', [\App\Http\Controllers\ImpersonateController::class, 'take'])->name('impersonate');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/impersonate/leave', [\App\Http\Controllers\ImpersonateController::class, 'leave'])->name('impersonate.leave');
});

require __DIR__.'/settings.php';
