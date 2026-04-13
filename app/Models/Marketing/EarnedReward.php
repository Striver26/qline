<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class EarnedReward extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'redeemed_at' => 'datetime',
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
