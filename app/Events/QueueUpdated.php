<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcastNow, ShouldDispatchAfterCommit
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public int $businessId,
        public string $action,
        public ?int $entryId = null,
        public ?int $servicePointId = null,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("business.{$this->businessId}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'business_id' => $this->businessId,
            'action' => $this->action,
            'entry_id' => $this->entryId,
            'service_point_id' => $this->servicePointId,
            'emitted_at' => now()->toIso8601String(),
        ];
    }
}
