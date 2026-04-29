<?php

namespace App\Events;

use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdated implements ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $entry;

    public $business;

    public function __construct(QueueEntry $entry, Business $business)
    {
        $this->entry = $entry;
        $this->business = $business;
    }
}
