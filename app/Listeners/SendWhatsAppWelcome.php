<?php

namespace App\Listeners;

use App\Events\TicketJoined;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWhatsAppWelcome implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected WhatsAppService $waService)
    {
    }

    public function handle(TicketJoined $event): void
    {
        if (!$event->entry->wa_id) {
            return;
        }

        $trackerUrl = url("/q/{$event->business->slug}/status/{$event->entry->id}");
        $responseMsg = "Hi! Welcome to {$event->business->name}.\n\nYour ticket is *{$event->entry->ticket_code}*.\nTrack your turn live here: {$trackerUrl}";
        
        $this->waService->sendText(
            $event->entry->wa_id, 
            $responseMsg, 
            $event->business->id, 
            $event->entry->id
        );
    }
}
