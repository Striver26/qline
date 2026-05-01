<?php

namespace App\Actions\Queue;

use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;
use App\Services\Queue\QueueService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class JoinQueueAction
{
    public function __construct(private readonly QueueService $queueService)
    {
    }

    public function join(Business $business, string $waId): QueueEntry
    {
        $waId = $this->queueService->normalizePhone($waId);

        $this->queueService->ensureActiveSubscription($business);

        return DB::transaction(function () use ($business, $waId): QueueEntry {
            $lockedBusiness = $this->queueService->lockBusiness($business);

            $this->queueService->ensureQueueOpen($lockedBusiness);
            $this->queueService->ensureDailyLimitNotReached($lockedBusiness);

            $todayCount = QueueEntry::query()
                ->forBusiness($lockedBusiness->id)
                ->where('wa_id', $waId)
                ->whereDate('created_at', now()->toDateString())
                ->count();

            if ($todayCount >= 3) {
                throw new RuntimeException('You have reached the maximum queue limit of 3 tickets per day.');
            }

            $entry = $this->queueService->createEntry($lockedBusiness, [
                'wa_id' => $waId,
                'source' => 'whatsapp',
            ]);

            event(new \App\Events\TicketJoined($entry, $lockedBusiness));
            $this->queueService->broadcastQueueMutation($lockedBusiness->id, 'join', $entry->id);

            return $entry;
        });
    }

    public function addManual(Business $business): QueueEntry
    {
        $this->queueService->ensureActiveSubscription($business);

        return DB::transaction(function () use ($business): QueueEntry {
            $lockedBusiness = $this->queueService->lockBusiness($business);

            $this->queueService->ensureQueueOpen($lockedBusiness);
            $this->queueService->ensureDailyLimitNotReached($lockedBusiness);

            $entry = $this->queueService->createEntry($lockedBusiness, [
                'wa_id' => null,
                'source' => 'anonymous',
            ]);

            $this->queueService->broadcastQueueMutation($lockedBusiness->id, 'addManual', $entry->id);

            return $entry;
        });
    }
}
