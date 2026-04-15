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
            'queue_status' => 'open',
            'pause_reason' => null
        ]);
    }

    public function pauseQueue(Business $business, $reason = null)
    {
        $business->update([
            'queue_status' => 'paused',
            'pause_reason' => $reason
        ]);
    }

    public function closeQueue(Business $business)
    {
        QueueEntry::where('business_id', $business->id)
            ->whereIn('status', [
                QueueStatus::WAITING->value,
                QueueStatus::CALLED->value,
                QueueStatus::SERVING->value
            ])
            ->update([
                'status' => QueueStatus::CANCELLED->value,
                'position' => 0
            ]);

        $business->update([
            'queue_status' => 'closed',
            'pause_reason' => null,
            'current_number' => 0,
            'entries_today' => 0,
            'last_reset_at' => now(),
        ]);
    }

    public function join(Business $business, $waId)
    {
        if ($business->queue_status !== 'open') {
            throw new Exception("The queue is currently closed.");
        }

        // Issue #6: Enforce daily queue limit (0 = unlimited)
        if ($business->daily_limit > 0 && $business->entries_today >= $business->daily_limit) {
            throw new Exception("Queue limit reached for today ({$business->daily_limit} tickets).");
        }

        $todayCount = QueueEntry::where('business_id', $business->id)
            ->where('wa_id', $waId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($todayCount >= 3) {
            throw new Exception("You have reached the maximum queue limit of 3 tickets per day.");
        }

        return DB::transaction(function () use ($business, $waId) {
            $business->increment('current_number');
            $business->increment('entries_today');

            $number = $business->current_number;
            $code = $business->queue_prefix . str_pad($number, 3, '0', STR_PAD_LEFT);

            // Issue #7: Lock WAITING entries to prevent race condition on position
            $position = QueueEntry::where('business_id', $business->id)
                ->where('status', QueueStatus::WAITING->value)
                ->lockForUpdate()
                ->count() + 1;

            $entry = QueueEntry::create([
                'business_id' => $business->id,
                'wa_id' => $waId,
                'ticket_number' => $number,
                'ticket_code' => $code,
                'status' => QueueStatus::WAITING->value,
                'source' => 'whatsapp',
                'cancel_token' => Str::random(32),
                'position' => $position
            ]);

            event(new \App\Events\TicketJoined($entry, $business));

            return $entry;
        });
    }

    public function addManual(Business $business)
    {
        // Issue #6: Enforce daily queue limit (0 = unlimited)
        if ($business->daily_limit > 0 && $business->entries_today >= $business->daily_limit) {
            throw new Exception("Queue limit reached for today ({$business->daily_limit} tickets).");
        }

        return DB::transaction(function () use ($business) {
            $business->increment('current_number');
            $business->increment('entries_today');

            $number = $business->current_number;
            $code = $business->queue_prefix . str_pad($number, 3, '0', STR_PAD_LEFT);

            // Issue #7: Lock WAITING entries to prevent race condition on position
            $position = QueueEntry::where('business_id', $business->id)
                ->where('status', QueueStatus::WAITING->value)
                ->lockForUpdate()
                ->count() + 1;

            return QueueEntry::create([
                'business_id' => $business->id,
                'wa_id' => null,
                'ticket_number' => $number,
                'ticket_code' => $code,
                'status' => QueueStatus::WAITING->value,
                'source' => 'manual',
                'cancel_token' => Str::random(32),
                'position' => $position
            ]);
        });
    }

    public function callNext(Business $business, $counterId = null)
    {
        return DB::transaction(function () use ($business, $counterId) {
            $nextEntry = QueueEntry::where('business_id', $business->id)
                ->where('status', QueueStatus::WAITING->value)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$nextEntry) {
                return null;
            }

            $nextEntry->update([
                'status' => QueueStatus::CALLED->value,
                'counter_id' => $counterId,
                'called_at' => now(),
                'position' => 0
            ]);

            $this->recalculatePositions($business->id);

            event(new TicketStatusUpdated($nextEntry, $business));

            return $nextEntry;
        });
    }

    public function markServing(QueueEntry $entry)
    {
        $entry->update([
            'status' => QueueStatus::SERVING->value,
            'served_at' => now()
        ]);

        // Issue #13: Broadcast serving status to clients
        event(new TicketStatusUpdated($entry, $entry->business));
    }

    public function markDone(QueueEntry $entry)
    {
        $entry->update([
            'status' => QueueStatus::COMPLETED->value,
            'completed_at' => now(),
            'position' => 0
        ]);

        if ($entry->wa_id) {
            $visitNumber = LoyaltyVisit::where('business_id', $entry->business_id)
                ->where('wa_id', $entry->wa_id)
                ->count() + 1;

            LoyaltyVisit::create([
                'business_id' => $entry->business_id,
                'wa_id' => $entry->wa_id,
                'queue_entry_id' => $entry->id,
                'visit_number' => $visitNumber
            ]);

            // Check if any loyalty rewards were earned
            $rewards = \App\Models\Marketing\LoyaltyReward::where('business_id', $entry->business_id)
                ->where('is_active', true)
                ->get();

            foreach ($rewards as $reward) {
                if ($visitNumber % $reward->required_visits === 0) {
                    \App\Models\Marketing\EarnedReward::create([
                        'business_id' => $entry->business_id,
                        'wa_id' => $entry->wa_id,
                        'loyalty_reward_id' => $reward->id,
                        'status' => 'available'
                    ]);
                }
            }
        }

        $this->recalculatePositions($entry->business_id);

        // Issue #13: Broadcast completed status to clients
        event(new TicketStatusUpdated($entry, $entry->business));
    }

    public function skip(QueueEntry $entry)
    {
        $entry->update([
            'status' => QueueStatus::SKIPPED->value,
            'position' => 0
        ]);
        $this->recalculatePositions($entry->business_id);
        event(new TicketStatusUpdated($entry, $entry->business));
    }

    public function cancel(QueueEntry $entry)
    {
        $entry->update([
            'status' => QueueStatus::CANCELLED->value,
            'position' => 0
        ]);
        $this->recalculatePositions($entry->business_id);
        event(new TicketStatusUpdated($entry, $entry->business));
    }

    /**
     * Issue #8: Optimized position recalculation.
     * Uses raw SQL on MySQL for single-query performance,
     * falls back to batch update on SQLite (test environment).
     */
    public function recalculatePositions($businessId)
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                UPDATE queue_entries qe
                JOIN (
                    SELECT id, ROW_NUMBER() OVER (ORDER BY id ASC) as new_pos
                    FROM queue_entries
                    WHERE business_id = ? AND status = 'waiting'
                ) ranked ON qe.id = ranked.id
                SET qe.position = ranked.new_pos
            ", [$businessId]);
        } else {
            // Portable fallback (SQLite) — single query to get IDs, then batch update
            $ids = QueueEntry::where('business_id', $businessId)
                ->where('status', QueueStatus::WAITING->value)
                ->orderBy('id', 'asc')
                ->pluck('id');

            foreach ($ids as $index => $id) {
                QueueEntry::where('id', $id)->update(['position' => $index + 1]);
            }
        }
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

        $ahead = $entry->position - 1;
        
        return [
            'position' => $entry->position,
            'ahead' => $ahead,
            'estimated_wait_mins' => $ahead * 5
        ];
    }
}
