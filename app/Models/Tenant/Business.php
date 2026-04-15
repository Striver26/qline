<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'join_code',
        'phone',
        'address',
        'city',
        'state',
        'postcode',
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

    // Cast properties
    protected function casts(): array
    {
        return [
            'last_reset_at' => 'datetime',
        ];
    }

    // The staff/owners connected to this business
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    // The active subscription for this business
    public function subscription()
    {
        return $this->hasOne(\App\Models\Tenant\Subscription::class);
    }

    // Billing history
    public function payments()
    {
        return $this->hasMany(\App\Models\Tenant\Payment::class);
    }

    // Historical queue tickets
    public function queueEntries()
    {
        return $this->hasMany(\App\Models\Queue\QueueEntry::class);
    }

    // Physical counters
    public function counters()
    {
        return $this->hasMany(\App\Models\Tenant\Counter::class);
    }

    // Customer feedbacks
    public function customerFeedbacks()
    {
        return $this->hasMany(\App\Models\Marketing\CustomerFeedback::class);
    }

    // Loyalty reward programs
    public function loyaltyRewards()
    {
        return $this->hasMany(\App\Models\Marketing\LoyaltyReward::class);
    }

    // Customer loyalty visits
    public function loyaltyVisits()
    {
        return $this->hasMany(\App\Models\Marketing\LoyaltyVisit::class);
    }
}
