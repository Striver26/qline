<?php

namespace App\Models\Tenant;

use App\Models\Marketing\CustomerFeedback;
use App\Models\Marketing\LoyaltyReward;
use App\Models\Marketing\LoyaltyVisit;
use App\Models\Queue\QueueEntry;
use App\Models\User;
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
        'timezone',
    ];

    protected function casts(): array
    {
        return [
            'last_reset_at' => 'datetime',
            'business_hours' => 'array',
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
        return $this->hasMany(User::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    public function servicePoints(): HasMany
    {
        return $this->hasMany(ServicePoint::class);
    }

    public function customerFeedbacks(): HasMany
    {
        return $this->hasMany(CustomerFeedback::class);
    }

    public function loyaltyRewards(): HasMany
    {
        return $this->hasMany(LoyaltyReward::class);
    }

    public function loyaltyVisits(): HasMany
    {
        return $this->hasMany(LoyaltyVisit::class);
    }
}
