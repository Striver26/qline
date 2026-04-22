<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\TicketCompleted;
use App\Events\TicketJoined;
use App\Events\TicketStatusUpdated;
use App\Listeners\ProcessLoyaltyRewards;
use App\Listeners\SendWhatsAppWelcome;
use App\Listeners\SendWhatsAppNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketJoined::class => [
            SendWhatsAppWelcome::class,
        ],

        TicketStatusUpdated::class => [
            SendWhatsAppNotification::class,
        ],

        TicketCompleted::class => [
            ProcessLoyaltyRewards::class,
        ],
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
