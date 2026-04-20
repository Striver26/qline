<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class EarnedReward extends Model
{
    protected $fillable = [
        'business_id',
        'wa_id',
        'loyalty_reward_id',
        'status',
        'redeemed_at',
    ];

    protected function casts(): array
    {
        return [
            'redeemed_at' => 'datetime',
            'status' => \App\Enums\RewardStatus::class,
        ];
    }

    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    public function reward()
    {
        return $this->belongsTo(\App\Models\Marketing\LoyaltyReward::class, 'loyalty_reward_id');
    }
}
