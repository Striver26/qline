<?php

namespace App\Listeners;

use App\Events\TicketStatusUpdated;
use App\Services\WhatsApp\WhatsAppService;
use App\Enums\QueueStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWhatsAppNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected WhatsAppService $waService)
    {
    }

    public function handle(TicketStatusUpdated $event): void
    {
        if (!$event->entry->wa_id) {
            return;
        }

        $msg = null;

        if ($event->entry->status === QueueStatus::CALLED->value) {
            $msg = "It's your turn! Please proceed to the counter at {$event->business->name}.";
        } elseif ($event->entry->status === QueueStatus::CANCELLED->value) {
            $msg = "Your ticket {$event->entry->ticket_code} has been cancelled.";
        } elseif ($event->entry->status === QueueStatus::SKIPPED->value) {
            $msg = "Your ticket {$event->entry->ticket_code} was skipped. Please speak to staff.";
        }

        if ($msg) {
            $this->waService->sendText(
                $event->entry->wa_id, 
                $msg, 
                $event->business->id, 
                $event->entry->id
            );
        }
    }
}
