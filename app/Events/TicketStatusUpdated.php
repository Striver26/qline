<?php

namespace App\Events;

use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $entry;
    public $business;

    public function __construct(QueueEntry $entry, Business $business)
    {
        $this->entry = $entry;
        $this->business = $business;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('queue.' . $this->business->slug),
        ];
    }

    /**
     * Data to broadcast with the event.
     */
    public function broadcastWith(): array
    {
        return [
            'entry_id' => $this->entry->id,
            'ticket_code' => $this->entry->ticket_code,
            'position' => $this->entry->position,
            'status' => $this->entry->status,
            'counter_id' => $this->entry->counter_id,
            'business_id' => $this->business->id,
        ];
    }
}
