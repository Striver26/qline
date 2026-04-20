<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'join_code',
        'tv_token',
        'phone',
        'address',
        'city',
        'state',
        'postcode',
        'business_hours',
        'pause_reason',
        'is_active',
        'queue_status',
        'queue_prefix',
        'current_number',
        'daily_limit',
        'entries_today',
        'notify_turns_before',
        'last_reset_at',
    ];

    protected function casts(): array
    {
        return [
            'last_reset_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($business) {
            if (empty($business->tv_token)) {
                $business->tv_token = Str::random(32);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(\App\Models\Tenant\Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Tenant\Payment::class);
    }

    public function queueEntries(): HasMany
    {
        return $this->hasMany(\App\Models\Queue\QueueEntry::class);
    }

    public function counters(): HasMany
    {
        return $this->hasMany(\App\Models\Tenant\Counter::class);
    }

    public function customerFeedbacks(): HasMany
    {
        return $this->hasMany(\App\Models\Marketing\CustomerFeedback::class);
    }

    public function loyaltyRewards(): HasMany
    {
        return $this->hasMany(\App\Models\Marketing\LoyaltyReward::class);
    }

    public function loyaltyVisits(): HasMany
    {
        return $this->hasMany(\App\Models\Marketing\LoyaltyVisit::class);
    }
}
