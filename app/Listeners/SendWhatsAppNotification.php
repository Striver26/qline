<?php

namespace App\Listeners;

use App\Enums\QueueStatus;
use App\Events\TicketStatusUpdated;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWhatsAppNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected WhatsAppService $waService) {}

    public function handle(TicketStatusUpdated $event): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        if (! $event->entry->wa_id) {
            return;
        }

        $msg = null;
        $servicePointName = $event->entry->servicePoint?->name;

        if ($event->entry->status === QueueStatus::CALLED->value) {
            $destination = $servicePointName ? "Please proceed to {$servicePointName}" : 'Please proceed to the service point';
            $msg = "It's your turn! {$destination} at {$event->business->name}.";
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
                $event->entry->id,
            );
        }
    }
}
