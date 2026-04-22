<?php

namespace App\Services\Queue;

use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Models\Marketing\LoyaltyVisit;
use App\Enums\QueueStatus;
use App\Events\TicketStatusUpdated;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class QueueService
{
    public function openQueue(Business $business)
    {
        // Issue #19: Require active subscription before opening queue
        $sub = $business->subscription;
        if (!$sub || $sub->status !== 'active') {
            throw new Exception("An active subscription is required to open the queue.");
        }

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
            'queue_status' => \App\Enums\BusinessQueueStatus::OPEN->value,
            'pause_reason' => null
        ]);
    }

    public function pauseQueue(Business $business, $reason = null)
    {
        $business->update([
            'queue_status' => \App\Enums\BusinessQueueStatus::PAUSED->value,
            'pause_reason' => $reason
        ]);
    }

    public function closeQueue(Business $business)
    {
        $activeEntries = QueueEntry::where('business_id', $business->id)
            ->whereIn('status', [
                QueueStatus::WAITING->value,
                QueueStatus::CALLED->value,
                QueueStatus::SERVING->value
            ])
            ->get();

        foreach ($activeEntries as $entry) {
            $entry->update([
                'status' => QueueStatus::CANCELLED->value,
                'position' => 0
            ]);
            
            event(new TicketStatusUpdated($entry, $business));
        }

        $business->update([
            'queue_status' => \App\Enums\BusinessQueueStatus::CLOSED->value,
            'pause_reason' => null,
        ]);
    }

    public function join(Business $business, $waId)
    {
        $waId = $this->normalizePhone($waId);

        $this->ensureActiveSubscription($business);

        return DB::transaction(function () use ($business, $waId) {
            $lockedBusiness = Business::where('id', $business->id)->lockForUpdate()->first();
            
            if ($lockedBusiness->queue_status !== \App\Enums\BusinessQueueStatus::OPEN->value) {
                throw new Exception("The queue is currently closed.");
            }

            if ($lockedBusiness->daily_limit > 0 && $lockedBusiness->entries_today >= $lockedBusiness->daily_limit) {
                throw new Exception("Queue limit reached for today ({$lockedBusiness->daily_limit} tickets).");
            }

            $todayCount = QueueEntry::where('business_id', $lockedBusiness->id)
                ->where('wa_id', $waId)
                ->whereDate('created_at', now()->toDateString())
                ->count();

            if ($todayCount >= 3) {
                throw new Exception("You have reached the maximum queue limit of 3 tickets per day.");
            }

            $lockedBusiness->increment('current_number');
            $lockedBusiness->increment('entries_today');

            $number = $lockedBusiness->current_number;
            $code = $lockedBusiness->queue_prefix . str_pad($number, 3, '0', STR_PAD_LEFT);

            $entry = QueueEntry::create([
                'business_id' => $lockedBusiness->id,
                'wa_id' => $waId,
                'ticket_number' => $number,
                'ticket_code' => $code,
                'status' => QueueStatus::WAITING->value,
                'source' => 'whatsapp',
                'cancel_token' => Str::random(32),
                'position' => 0
            ]);

            event(new \App\Events\TicketJoined($entry, $lockedBusiness));

            return $entry;
        });
    }

    public function addManual(Business $business)
    {
        $this->ensureActiveSubscription($business);

        return DB::transaction(function () use ($business) {
            $lockedBusiness = Business::where('id', $business->id)->lockForUpdate()->first();
            
            if ($lockedBusiness->queue_status !== \App\Enums\BusinessQueueStatus::OPEN->value) {
                throw new Exception("The queue is currently closed.");
            }

            if ($lockedBusiness->daily_limit > 0 && $lockedBusiness->entries_today >= $lockedBusiness->daily_limit) {
                throw new Exception("Queue limit reached for today ({$lockedBusiness->daily_limit} tickets).");
            }

            $lockedBusiness->increment('current_number');
            $lockedBusiness->increment('entries_today');

            $number = $lockedBusiness->current_number;
            $code = $lockedBusiness->queue_prefix . str_pad($number, 3, '0', STR_PAD_LEFT);

            return QueueEntry::create([
                'business_id' => $lockedBusiness->id,
                'wa_id' => null,
                'ticket_number' => $number,
                'ticket_code' => $code,
                'status' => QueueStatus::WAITING->value,
                'source' => 'anonymous',
                'cancel_token' => Str::random(32),
                'position' => 0
            ]);
        });
    }

    public function callNext(Business $business, $counterId = null)
    {
        $this->ensureActiveSubscription($business);

        return DB::transaction(function () use ($business, $counterId) {
            $lockedBusiness = Business::where('id', $business->id)->lockForUpdate()->first();

            $nextEntry = QueueEntry::where('business_id', $lockedBusiness->id)
                ->where('status', QueueStatus::WAITING->value)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$nextEntry) {
                return null;
            }

            // Enforce tier entitlement for counters
            $tier = $business->subscription?->type->value ?? $business->subscription?->type;
            $canUseCounters = config("qline.tiers.{$tier}.counters", false);
            
            if (!$canUseCounters) {
                $counterId = null;
            }

            $nextEntry->update([
                'status' => QueueStatus::CALLED->value,
                'counter_id' => $counterId,
                'processed_by_user_id' => auth()->id(),
                'called_at' => now(),
                'position' => 0
            ]);

            event(new TicketStatusUpdated($nextEntry, $business));

            // Issue #29: Pre-turn notification
            $notifyTurnsBefore = $lockedBusiness->notify_turns_before ?? 3;
            if ($notifyTurnsBefore > 0) {
                $upcomingEntry = QueueEntry::where('business_id', $lockedBusiness->id)
                    ->where('status', QueueStatus::WAITING->value)
                    ->orderBy('id', 'asc')
                    ->offset($notifyTurnsBefore - 1)
                    ->first();

                if ($upcomingEntry && $upcomingEntry->wa_id) {
                    app(\App\Services\WhatsApp\WhatsAppService::class)->sendText(
                        $upcomingEntry->wa_id,
                        "Your turn at {$lockedBusiness->name} is coming soon! You are now number {$notifyTurnsBefore} in line. Please get ready.",
                        $lockedBusiness->id,
                        $upcomingEntry->id
                    );
                }
            }

            return $nextEntry;
        });
    }

    public function markServing(QueueEntry $entry)
    {
        if ($entry->status !== QueueStatus::CALLED->value) {
            throw new Exception("Only called tickets can be marked as serving.");
        }

        $entry->update([
            'status' => QueueStatus::SERVING->value,
            'served_at' => now()
        ]);

        // Issue #13: Broadcast serving status to clients
        event(new TicketStatusUpdated($entry, $entry->business));
    }

    public function markDone(QueueEntry $entry)
    {
        if (!in_array($entry->status, [QueueStatus::CALLED->value, QueueStatus::SERVING->value])) {
            throw new Exception("Only active tickets can be completed.");
        }

        $entry->update([
            'status' => QueueStatus::COMPLETED->value,
            'completed_at' => now(),
            'processed_by_user_id' => auth()->id(),
            'position' => 0
        ]);

        if ($entry->wa_id) {
            event(new \App\Events\TicketCompleted($entry));
        }

        // Issue #13: Broadcast completed status to clients
        event(new TicketStatusUpdated($entry, $entry->business));
    }

    public function skip(QueueEntry $entry)
    {
        if (!in_array($entry->status, [QueueStatus::CALLED->value, QueueStatus::SERVING->value])) {
            throw new Exception("Only active tickets can be skipped.");
        }

        $entry->update([
            'status' => QueueStatus::SKIPPED->value,
            'processed_by_user_id' => auth()->id(),
            'position' => 0
        ]);
        
        event(new TicketStatusUpdated($entry, $entry->business));
    }

    public function cancel(QueueEntry $entry)
    {
        if (!in_array($entry->status, [QueueStatus::WAITING->value, QueueStatus::CALLED->value])) {
            throw new Exception("Only waiting or called tickets can be cancelled.");
        }

        $entry->update([
            'status' => QueueStatus::CANCELLED->value,
            'processed_by_user_id' => auth()->id(),
            'position' => 0
        ]);
        
        event(new TicketStatusUpdated($entry, $entry->business));
    }

    public function rejoin(QueueEntry $entry)
    {
        if (!in_array($entry->status, [QueueStatus::SKIPPED->value, QueueStatus::CANCELLED->value])) {
            throw new Exception("Only skipped or cancelled tickets can be rejoined.");
        }

        return DB::transaction(function () use ($entry) {
            $lockedBusiness = Business::where('id', $entry->business_id)->lockForUpdate()->first();
            
            if ($lockedBusiness->queue_status !== \App\Enums\BusinessQueueStatus::OPEN->value) {
                throw new Exception("The queue is currently closed.");
            }

            $entry->update([
                'status' => QueueStatus::WAITING->value,
                'position' => 0,
                'counter_id' => null,
                'processed_by_user_id' => null,
                'called_at' => null,
                'served_at' => null,
                'completed_at' => null,
            ]);

            event(new TicketStatusUpdated($entry, $lockedBusiness));

            return $entry;
        });
    }

    public function getPositionInfo(QueueEntry $entry)
    {
        if ($entry->status !== QueueStatus::WAITING->value) {
            return [
                'position' => 0,
                'ahead' => 0,
                'estimated_wait_mins' => 0
            ];
        }

        $ahead = QueueEntry::where('business_id', $entry->business_id)
            ->where('status', QueueStatus::WAITING->value)
            ->where('id', '<', $entry->id)
            ->count();

        // Calculate dynamic wait time rolling average (last 10 completed)
        $avgWaitSeconds = QueueEntry::where('business_id', $entry->business_id)
            ->where('status', QueueStatus::COMPLETED->value)
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get()
            ->avg(function ($completedEntry) {
                return $completedEntry->completed_at->diffInSeconds($completedEntry->called_at);
            });

        // Fallback to 5 mins if no data
        $avgWaitMins = $avgWaitSeconds ? ceil($avgWaitSeconds / 60) : 5;

        return [
            'position' => $ahead + 1,
            'ahead' => $ahead,
            'estimated_wait_mins' => $ahead * $avgWaitMins
        ];
    }

    private function ensureActiveSubscription(Business $business): void
    {
        $sub = $business->subscription;
        if (!$sub || $sub->status !== 'active' || ($sub->expires_at && $sub->expires_at->isPast())) {
            // Force closure on detection of invalid subscription
            $this->closeQueue($business);
            
            throw new Exception("The business has no active subscription.");
        }
    }

    private function normalizePhone($phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        
        if (str_starts_with($digits, '0')) {
            $digits = '60' . substr($digits, 1);
        }
        
        return $digits;
    }
}
