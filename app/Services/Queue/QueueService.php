<?php

namespace App\Services\Queue;

use App\Enums\BusinessQueueStatus;
use App\Enums\QueueStatus;
use App\Enums\TableStatus;
use App\Events\QueueUpdated;
use App\Events\TicketCompleted;
use App\Events\TicketJoined;
use App\Events\TicketStatusUpdated;
use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Business;
use App\Models\Tenant\ServicePoint;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class QueueService
{
    private const WAITING_PREVIEW_LIMIT = 50;

    public function getCommandCenterSnapshot(Business $business): array
    {
        $business = Business::query()
            ->select([
                'id',
                'name',
                'slug',
                'tv_token',
                'queue_status',
                'pause_reason',
                'timezone',
            ])
            ->with('subscription:id,business_id,type,status,expires_at')
            ->findOrFail($business->getKey());

        $waitingCount = QueueEntry::query()
            ->forBusiness($business->id)
            ->waiting()
            ->count();

        $completedToday = QueueEntry::query()
            ->forBusiness($business->id)
            ->where('status', QueueStatus::COMPLETED->value)
            ->whereDate('completed_at', now()->toDateString())
            ->count();

        $waitingEntries = QueueEntry::query()
            ->select([
                'id',
                'business_id',
                'wa_id',
                'ticket_code',
                'status',
                'source',
                'created_at',
            ])
            ->forBusiness($business->id)
            ->waiting()
            ->orderBy('id')
            ->limit(self::WAITING_PREVIEW_LIMIT)
            ->get();

        $activeEntries = QueueEntry::query()
            ->select([
                'id',
                'business_id',
                'wa_id',
                'ticket_code',
                'status',
                'source',
                'service_point_id',
                'called_at',
                'served_at',
            ])
            ->forBusiness($business->id)
            ->active()
            ->with([
                'servicePoint:id,name,status,is_active',
            ])
            ->orderByDesc('called_at')
            ->orderByDesc('id')
            ->get();

        $servicePoints = ServicePoint::query()
            ->select(['id', 'business_id', 'name', 'status', 'is_active'])
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $averageServiceMinutes = $this->getAverageServiceMinutes($business->id);
        $activeByServicePoint = $activeEntries
            ->filter(fn(QueueEntry $entry) => $entry->service_point_id !== null)
            ->keyBy('service_point_id');

        $formattedWaitingEntries = $waitingEntries
            ->values()
            ->map(function (QueueEntry $entry, int $index) use ($averageServiceMinutes): array {
                return [
                    'id' => $entry->id,
                    'ticket_code' => $entry->ticket_code,
                    'customer_label' => $entry->wa_id ?: 'Anonymous',
                    'source_label' => $entry->source === 'whatsapp' ? 'WhatsApp' : 'Anonymous',
                    'queue_position' => $index + 1,
                    'estimated_wait_mins' => $index * $averageServiceMinutes,
                    'created_human' => $entry->created_at?->diffForHumans(),
                    'created_at' => $entry->created_at?->toIso8601String(),
                    'is_next' => $index === 0,
                ];
            })
            ->all();

        $formattedActiveEntries = $activeEntries
            ->values()
            ->map(fn(QueueEntry $entry): array => $this->formatActiveEntry($entry))
            ->all();

        $formattedServicePoints = $servicePoints
            ->values()
            ->map(function (ServicePoint $servicePoint) use ($activeByServicePoint): array {
                $currentEntry = $activeByServicePoint->get($servicePoint->id);
                $isFree = $servicePoint->status === TableStatus::FREE->value && !$currentEntry;

                return [
                    'id' => $servicePoint->id,
                    'name' => $servicePoint->name,
                    'status' => $servicePoint->status,
                    'is_active' => $servicePoint->is_active,
                    'is_busy' => (bool) $currentEntry,
                    'is_free' => $isFree,
                    'active_ticket' => $currentEntry ? [
                        'id' => $currentEntry->id,
                        'ticket_code' => $currentEntry->ticket_code,
                        'status_label' => QueueStatus::tryFrom($currentEntry->status)?->getLabel() ?? Str::headline($currentEntry->status),
                    ] : null,
                    'active_ticket_code' => $currentEntry?->ticket_code,
                ];
            })
            ->all();

        return [
            'business' => [
                'id' => $business->id,
                'name' => $business->name ?: 'Your Business',
                'slug' => $business->slug,
                'tv_token' => $business->tv_token,
                'queue_status' => $business->queue_status,
                'pause_reason' => $business->pause_reason,
                'subscription_type' => $business->subscription?->type?->value ?? null,
                'can_use_servicePoints' => $this->canUseServicePoints($business),
            ],
            'metrics' => [
                'waiting_count' => $waitingCount,
                'active_count' => count($formattedActiveEntries),
                'served_today' => $completedToday,
                'free_table_count' => collect($formattedServicePoints)->where('is_free', true)->count(),
                'occupied_table_count' => collect($formattedServicePoints)->where('is_free', false)->count(),
                'waiting_hidden_count' => max(0, $waitingCount - count($formattedWaitingEntries)),
            ],
            'next_entry' => $formattedWaitingEntries[0] ?? null,
            'waiting_entries' => $formattedWaitingEntries,
            'active_entries' => $formattedActiveEntries,
            'servicePoints' => $formattedServicePoints,
        ];
    }

    public function openQueue(Business $business): void
    {
        $this->ensureActiveSubscription($business);

        $today = now()->startOfDay();
        $lastReset = $business->last_reset_at ? $business->last_reset_at->startOfDay() : null;

        if (!$lastReset || $lastReset->lt($today)) {
            $business->update([
                'current_number' => 0,
                'entries_today' => 0,
                'last_reset_at' => now(),
            ]);
        }

        $business->update([
            'queue_status' => BusinessQueueStatus::OPEN->value,
            'pause_reason' => null,
        ]);

        $this->broadcastQueueMutation($business->id, 'openQueue');
    }

    public function pauseQueue(Business $business, ?string $reason = null): void
    {
        $business->update([
            'queue_status' => BusinessQueueStatus::PAUSED->value,
            'pause_reason' => $reason,
        ]);

        $this->broadcastQueueMutation($business->id, 'pauseQueue');
    }

    public function closeQueue(Business $business): void
    {
        DB::transaction(function () use ($business): void {
            $lockedBusiness = $this->lockBusiness($business);

            $activeEntries = QueueEntry::query()
                ->forBusiness($lockedBusiness->id)
                ->whereIn('status', [
                    QueueStatus::WAITING->value,
                    QueueStatus::CALLED->value,
                    QueueStatus::SERVING->value,
                ])
                ->lockForUpdate()
                ->get();

            foreach ($activeEntries as $entry) {
                if ($entry->service_point_id) {
                    $this->releaseServicePointById($entry->service_point_id);
                }

                $entry->update([
                    'status' => QueueStatus::CANCELLED->value,
                    'position' => 0,
                ]);

                event(new TicketStatusUpdated($entry->fresh(), $lockedBusiness));
            }

            ServicePoint::query()
                ->where('business_id', $lockedBusiness->id)
                ->update(['status' => TableStatus::FREE->value]);

            $lockedBusiness->update([
                'queue_status' => BusinessQueueStatus::CLOSED->value,
                'pause_reason' => null,
            ]);

            $this->broadcastQueueMutation($lockedBusiness->id, 'closeQueue');
        });
    }

    public function join(Business $business, string $waId): QueueEntry
    {
        return app(\App\Actions\Queue\JoinQueueAction::class, ['queueService' => $this])->join($business, $waId);
    }

    public function addManual(Business $business): QueueEntry
    {
        return app(\App\Actions\Queue\JoinQueueAction::class, ['queueService' => $this])->addManual($business);
    }

    public function getNextEntry(Business|int $business): ?QueueEntry
    {
        $businessId = $business instanceof Business ? $business->id : $business;

        return QueueEntry::query()
            ->forBusiness($businessId)
            ->waiting()
            ->orderBy('id')
            ->first();
    }

    public function callNext(Business $business, ?int $servicePointId = null): ?QueueEntry
    {
        $this->ensureActiveSubscription($business);

        return DB::transaction(function () use ($business, $servicePointId): ?QueueEntry {
            $lockedBusiness = $this->lockBusiness($business);

            $this->ensureQueueOpen($lockedBusiness);

            $servicePoint = $this->lockServicePointIfNeeded($lockedBusiness, $servicePointId, requireAvailable: true);

            $nextEntry = QueueEntry::query()
                ->forBusiness($lockedBusiness->id)
                ->waiting()
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (!$nextEntry) {
                return null;
            }

            $entry = $this->activateEntry(
                business: $lockedBusiness,
                entry: $nextEntry,
                servicePoint: $servicePoint,
                action: 'callNext',
            );

            $this->notifyUpcomingEntry($lockedBusiness);

            return $entry;
        });
    }

    public function callEntry(Business $business, int $entryId, ?int $servicePointId = null): QueueEntry
    {
        $this->ensureActiveSubscription($business);

        return DB::transaction(function () use ($business, $entryId, $servicePointId): QueueEntry {
            $lockedBusiness = $this->lockBusiness($business);

            $this->ensureQueueOpen($lockedBusiness);

            $servicePoint = $this->lockServicePointIfNeeded($lockedBusiness, $servicePointId, requireAvailable: true);

            $entry = QueueEntry::query()
                ->forBusiness($lockedBusiness->id)
                ->waiting()
                ->whereKey($entryId)
                ->lockForUpdate()
                ->first();

            if (!$entry) {
                throw new RuntimeException('Only waiting tickets can be called to a service point.');
            }

            $activatedEntry = $this->activateEntry(
                business: $lockedBusiness,
                entry: $entry,
                servicePoint: $servicePoint,
                action: 'callEntry',
            );

            $this->notifyUpcomingEntry($lockedBusiness);

            return $activatedEntry;
        });
    }

    public function assignToServicePoint(int $entryId, int $servicePointId): QueueEntry
    {
        return DB::transaction(function () use ($entryId, $servicePointId): QueueEntry {
            $entry = QueueEntry::query()
                ->whereKey($entryId)
                ->lockForUpdate()
                ->first();

            if (!$entry) {
                throw new ModelNotFoundException('Queue entry not found.');
            }

            $business = $this->lockBusiness($entry->business_id);

            $this->ensureActiveSubscription($business);
            $this->ensureQueueOpen($business);

            if ($entry->status !== QueueStatus::WAITING->value) {
                throw new RuntimeException('Only waiting tickets can be assigned to a service point.');
            }

            $servicePoint = ServicePoint::query()
                ->where('business_id', $business->id)
                ->whereKey($servicePointId)
                ->lockForUpdate()
                ->first();

            if (!$servicePoint) {
                throw new RuntimeException('Selected service point could not be found.');
            }

            $servicePointIsBusy = QueueEntry::query()
                ->forBusiness($business->id)
                ->active()
                ->where('service_point_id', $servicePoint->id)
                ->lockForUpdate()
                ->exists();

            if ($servicePointIsBusy) {
                throw new RuntimeException("{$servicePoint->name} is already handling another ticket.");
            }

            $activatedEntry = $this->activateEntry(
                business: $business,
                entry: $entry,
                servicePoint: $servicePoint,
                action: 'assignToServicePoint',
            );

            $this->notifyUpcomingEntry($business);

            return $activatedEntry;
        });
    }

    /**
     * Reassign an active (called/serving) ticket to a different service point.
     */
    public function reassignEntry(int $entryId, int $servicePointId): QueueEntry
    {
        return DB::transaction(function () use ($entryId, $servicePointId): QueueEntry {
            $entry = QueueEntry::query()
                ->whereKey($entryId)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($entry->status, [QueueStatus::CALLED->value, QueueStatus::SERVING->value], true)) {
                throw new RuntimeException('Only active tickets can be reassigned to another service point.');
            }

            $business = $this->lockBusiness($entry->business_id);

            $this->ensureActiveSubscription($business);
            $this->ensureQueueOpen($business);

            // Ensure the target is different from the current
            if ($entry->service_point_id === $servicePointId) {
                throw new RuntimeException('Ticket is already at this service point.');
            }

            // Lock and validate the target service point
            $targetServicePoint = ServicePoint::query()
                ->where('business_id', $business->id)
                ->whereKey($servicePointId)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$targetServicePoint) {
                throw new RuntimeException('Target service point is unavailable.');
            }

            $targetIsBusy = QueueEntry::query()
                ->forBusiness($business->id)
                ->active()
                ->where('service_point_id', $targetServicePoint->id)
                ->lockForUpdate()
                ->exists();

            if ($targetIsBusy) {
                throw new RuntimeException("{$targetServicePoint->name} is already handling another ticket.");
            }

            // Release the old service point
            if ($entry->service_point_id) {
                $this->releaseServicePointById($entry->service_point_id);
            }

            // Occupy the new service point
            $targetServicePoint->update(['status' => TableStatus::OCCUPIED->value]);

            $entry->update([
                'service_point_id' => $targetServicePoint->id,
                'called_at' => now(),
            ]);

            $freshEntry = $entry->fresh(['servicePoint:id,name,status,is_active']);

            event(new TicketStatusUpdated($freshEntry, $business));
            $this->broadcastQueueMutation(
                $business->id,
                'reassignEntry',
                $freshEntry->id,
                $freshEntry->service_point_id,
            );

            return $freshEntry;
        });
    }

    public function markServing(QueueEntry $entry): QueueEntry
    {
        return DB::transaction(function () use ($entry): QueueEntry {
            $lockedEntry = $this->lockEntry($entry);

            if ($lockedEntry->status !== QueueStatus::CALLED->value) {
                throw new RuntimeException('Only called tickets can be marked as serving.');
            }

            $lockedEntry->update([
                'status' => QueueStatus::SERVING->value,
                'served_at' => now(),
            ]);

            $business = Business::query()->findOrFail($lockedEntry->business_id);

            event(new TicketStatusUpdated($lockedEntry->fresh(), $business));
            $this->broadcastQueueMutation(
                $business->id,
                'markServing',
                $lockedEntry->id,
                $lockedEntry->service_point_id,
            );

            return $lockedEntry->fresh(['servicePoint:id,name,status,is_active']);
        });
    }

    public function markDone(int|QueueEntry $entry): QueueEntry
    {
        return DB::transaction(function () use ($entry): QueueEntry {
            $lockedEntry = $this->lockEntry($entry);

            if (!in_array($lockedEntry->status, [QueueStatus::CALLED->value, QueueStatus::SERVING->value], true)) {
                throw new RuntimeException('Only active tickets can be completed.');
            }

            if ($lockedEntry->service_point_id) {
                $this->releaseServicePointById($lockedEntry->service_point_id);
            }

            $lockedEntry->update([
                'status' => QueueStatus::COMPLETED->value,
                'completed_at' => now(),
                'processed_by_user_id' => auth()->id(),
                'position' => 0,
            ]);

            $business = Business::query()->findOrFail($lockedEntry->business_id);

            if ($lockedEntry->wa_id) {
                event(new TicketCompleted($lockedEntry->fresh()));
            }

            event(new TicketStatusUpdated($lockedEntry->fresh(), $business));
            $this->broadcastQueueMutation(
                $business->id,
                'markDone',
                $lockedEntry->id,
                $lockedEntry->service_point_id,
            );

            Cache::forget($this->averageWaitCacheKey($business->id));

            return $lockedEntry->fresh(['servicePoint:id,name,status,is_active']);
        });
    }

    public function recallEntry(int|QueueEntry $entry): QueueEntry
    {
        $lockedEntry = $this->lockEntry($entry);

        if (!in_array($lockedEntry->status, [QueueStatus::CALLED->value, QueueStatus::SERVING->value], true)) {
            throw new RuntimeException('Only active tickets can be recalled.');
        }

        $business = Business::query()->findOrFail($lockedEntry->business_id);

        // Re-fire event to trigger sound/WhatsApp/TV
        event(new TicketStatusUpdated($lockedEntry, $business));
        
        $this->broadcastQueueMutation(
            $business->id,
            'recall',
            $lockedEntry->id,
            $lockedEntry->service_point_id,
        );

        return $lockedEntry;
    }

    public function skip(int|QueueEntry $entry): QueueEntry
    {
        return DB::transaction(function () use ($entry): QueueEntry {
            $lockedEntry = $this->lockEntry($entry);

            if (!in_array($lockedEntry->status, [QueueStatus::CALLED->value, QueueStatus::SERVING->value], true)) {
                throw new RuntimeException('Only active tickets can be skipped.');
            }

            if ($lockedEntry->service_point_id) {
                $this->releaseServicePointById($lockedEntry->service_point_id);
            }

            $lockedEntry->update([
                'status' => QueueStatus::SKIPPED->value,
                'processed_by_user_id' => auth()->id(),
                'position' => 0,
            ]);

            $business = Business::query()->findOrFail($lockedEntry->business_id);

            event(new TicketStatusUpdated($lockedEntry->fresh(), $business));
            $this->broadcastQueueMutation(
                $business->id,
                'skip',
                $lockedEntry->id,
                $lockedEntry->service_point_id,
            );

            return $lockedEntry->fresh(['servicePoint:id,name,status,is_active']);
        });
    }

    public function cancel(int|QueueEntry $entry): QueueEntry
    {
        return DB::transaction(function () use ($entry): QueueEntry {
            $lockedEntry = $this->lockEntry($entry);

            if (!in_array($lockedEntry->status, [QueueStatus::WAITING->value, QueueStatus::CALLED->value], true)) {
                throw new RuntimeException('Only waiting or called tickets can be cancelled.');
            }

            if ($lockedEntry->service_point_id) {
                $this->releaseServicePointById($lockedEntry->service_point_id);
            }

            $lockedEntry->update([
                'status' => QueueStatus::CANCELLED->value,
                'processed_by_user_id' => auth()->id(),
                'position' => 0,
            ]);

            $business = Business::query()->findOrFail($lockedEntry->business_id);

            event(new TicketStatusUpdated($lockedEntry->fresh(), $business));
            $this->broadcastQueueMutation(
                $business->id,
                'cancel',
                $lockedEntry->id,
                $lockedEntry->service_point_id,
            );

            return $lockedEntry->fresh(['servicePoint:id,name,status,is_active']);
        });
    }

    public function rejoin(int|QueueEntry $entry): QueueEntry
    {
        return DB::transaction(function () use ($entry): QueueEntry {
            $lockedEntry = $this->lockEntry($entry);

            if (!in_array($lockedEntry->status, [QueueStatus::SKIPPED->value, QueueStatus::CANCELLED->value], true)) {
                throw new RuntimeException('Only skipped or cancelled tickets can be rejoined.');
            }

            $business = $this->lockBusiness($lockedEntry->business_id);

            $this->ensureQueueOpen($business);

            if ($lockedEntry->service_point_id) {
                $this->releaseServicePointById($lockedEntry->service_point_id);
            }

            $lockedEntry->update([
                'status' => QueueStatus::WAITING->value,
                'position' => 0,
                'service_point_id' => null,
                'processed_by_user_id' => null,
                'called_at' => null,
                'served_at' => null,
                'completed_at' => null,
            ]);

            event(new TicketStatusUpdated($lockedEntry->fresh(), $business));
            $this->broadcastQueueMutation($business->id, 'rejoin', $lockedEntry->id);

            return $lockedEntry->fresh(['servicePoint:id,name,status,is_active']);
        });
    }

    public function getPositionInfo(QueueEntry $entry): array
    {
        if ($entry->status !== QueueStatus::WAITING->value) {
            return [
                'position' => 0,
                'ahead' => 0,
                'estimated_wait_mins' => 0,
            ];
        }

        $ahead = QueueEntry::query()
            ->forBusiness($entry->business_id)
            ->waiting()
            ->where('id', '<', $entry->id)
            ->count();

        return [
            'position' => $ahead + 1,
            'ahead' => $ahead,
            'estimated_wait_mins' => $ahead * $this->getAverageServiceMinutes($entry->business_id),
        ];
    }

    public function activateEntry(
        Business $business,
        QueueEntry $entry,
        ?ServicePoint $servicePoint,
        string $action,
    ): QueueEntry {
        if ($servicePoint) {
            $servicePoint->update(['status' => TableStatus::OCCUPIED->value]);
        }

        $entry->update([
            'status' => QueueStatus::CALLED->value,
            'service_point_id' => $servicePoint?->id,
            'processed_by_user_id' => auth()->id(),
            'called_at' => now(),
            'position' => 0,
        ]);

        $freshEntry = $entry->fresh(['servicePoint:id,name,status,is_active']);

        event(new TicketStatusUpdated($freshEntry, $business));
        $this->broadcastQueueMutation(
            $business->id,
            $action,
            $freshEntry->id,
            $freshEntry->service_point_id,
        );

        return $freshEntry;
    }

    public function createEntry(Business $business, array $attributes): QueueEntry
    {
        $ticketNumber = $business->current_number + 1;
        $ticketCode = $business->queue_prefix . str_pad((string) $ticketNumber, 3, '0', STR_PAD_LEFT);

        $business->update([
            'current_number' => $ticketNumber,
            'entries_today' => $business->entries_today + 1,
        ]);

        return QueueEntry::query()->create([
            'business_id' => $business->id,
            'wa_id' => $attributes['wa_id'],
            'ticket_number' => $ticketNumber,
            'ticket_code' => $ticketCode,
            'status' => QueueStatus::WAITING->value,
            'source' => $attributes['source'],
            'cancel_token' => Str::random(32),
            'position' => 0,
        ]);
    }

    public function ensureActiveSubscription(Business $business): void
    {
        $subscription = $business->loadMissing('subscription')->subscription;

        if (
            !$subscription
            || $subscription->status !== 'active'
            || ($subscription->expires_at && $subscription->expires_at->isPast())
        ) {
            $this->closeQueue($business);

            throw new RuntimeException('The business has no active subscription.');
        }
    }

    public function ensureQueueOpen(Business $business): void
    {
        if ($business->queue_status !== BusinessQueueStatus::OPEN->value) {
            throw new RuntimeException('The queue is currently closed.');
        }
    }

    public function ensureDailyLimitNotReached(Business $business): void
    {
        if ($business->daily_limit > 0 && $business->entries_today >= $business->daily_limit) {
            throw new RuntimeException("Queue limit reached for today ({$business->daily_limit} tickets).");
        }
    }

    public function notifyUpcomingEntry(Business $business): void
    {
        $notifyTurnsBefore = $business->notify_turns_before ?? 3;

        if ($notifyTurnsBefore <= 0) {
            return;
        }

        $upcomingEntry = QueueEntry::query()
            ->forBusiness($business->id)
            ->waiting()
            ->orderBy('id')
            ->offset($notifyTurnsBefore - 1)
            ->first();

        if (!$upcomingEntry || !$upcomingEntry->wa_id) {
            return;
        }

        app(WhatsAppService::class)->sendText(
            $upcomingEntry->wa_id,
            "Your turn at {$business->name} is coming soon! You are now number {$notifyTurnsBefore} in line. Please get ready.",
            $business->id,
            $upcomingEntry->id,
        );
    }

    private function getAverageServiceMinutes(int $businessId): int
    {
        return Cache::remember(
            $this->averageWaitCacheKey($businessId),
            now()->addMinute(),
            function () use ($businessId): int {
                $seconds = QueueEntry::query()
                    ->select(['called_at', 'completed_at'])
                    ->forBusiness($businessId)
                    ->where('status', QueueStatus::COMPLETED->value)
                    ->whereNotNull('called_at')
                    ->whereNotNull('completed_at')
                    ->orderByDesc('id')
                    ->limit(25)
                    ->get()
                    ->avg(fn(QueueEntry $entry): int => $entry->completed_at->diffInSeconds($entry->called_at));

                return $seconds ? max(1, (int) ceil($seconds / 60)) : 5;
            },
        );
    }

    private function averageWaitCacheKey(int $businessId): string
    {
        return "queue:{$businessId}:average-service-minutes";
    }

    private function canUseServicePoints(Business $business): bool
    {
        $tier = $business->loadMissing('subscription')->subscription?->type?->value;

        if (!$tier) {
            return false;
        }

        return (bool) config("qline.tiers.{$tier}.counters", false);
    }

    private function formatActiveEntry(QueueEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'ticket_code' => $entry->ticket_code,
            'customer_label' => $entry->wa_id ?: 'Anonymous',
            'source_label' => $entry->source === 'whatsapp' ? 'WhatsApp' : 'Anonymous',
            'status' => $entry->status,
            'status_label' => QueueStatus::tryFrom($entry->status)?->getLabel() ?? Str::headline($entry->status),
            'called_at' => $entry->called_at?->format('H:i'),
            'called_human' => $entry->called_at?->diffForHumans(),
            'service_point_label' => $entry->servicePoint?->name,
            'service_point_type' => 'service_point',
            'service_point_id' => $entry->service_point_id,
        ];
    }

    public function broadcastQueueMutation(
        int $businessId,
        string $action,
        ?int $entryId = null,
        ?int $servicePointId = null,
    ): void {
        event(new QueueUpdated($businessId, $action, $entryId, $servicePointId));
    }

    public function lockBusiness(Business|int $business): Business
    {
        $businessId = $business instanceof Business ? $business->id : $business;

        return Business::query()
            ->with('subscription')
            ->whereKey($businessId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function lockEntry(int|QueueEntry $entry): QueueEntry
    {
        $entryId = $entry instanceof QueueEntry ? $entry->id : $entry;

        return QueueEntry::query()
            ->with(['servicePoint:id,name,status,is_active'])
            ->whereKey($entryId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function lockServicePointIfNeeded(Business $business, ?int $servicePointId, bool $requireAvailable): ?ServicePoint
    {
        if (!$servicePointId || !$this->canUseServicePoints($business)) {
            return null;
        }

        $servicePoint = ServicePoint::query()
            ->where('business_id', $business->id)
            ->whereKey($servicePointId)
            ->where('is_active', true)
            ->lockForUpdate()
            ->first();

        if (!$servicePoint) {
            throw new RuntimeException('Selected service point is unavailable.');
        }

        if (!$requireAvailable) {
            return $servicePoint;
        }

        $servicePointIsBusy = QueueEntry::query()
            ->forBusiness($business->id)
            ->active()
            ->where('service_point_id', $servicePoint->id)
            ->lockForUpdate()
            ->exists();

        if ($servicePointIsBusy) {
            throw new RuntimeException("{$servicePoint->name} is already handling another ticket.");
        }

        return $servicePoint;
    }

    public function releaseServicePointById(int $servicePointId): void
    {
        ServicePoint::query()
            ->whereKey($servicePointId)
            ->lockForUpdate()
            ->update(['status' => TableStatus::FREE->value]);
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '60' . substr($digits, 1);
        }

        return $digits;
    }
}
