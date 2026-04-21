<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:cleanup-stale')->hourly()->onOneServer()->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
*/

// Reset daily queue counters and close open queues at midnight
Schedule::command('queue:reset-daily')->dailyAt('00:00')->onOneServer()->withoutOverlapping();

// Check for expired subscriptions every hour
Schedule::command('subscriptions:expire')->hourly()->onOneServer()->withoutOverlapping();
