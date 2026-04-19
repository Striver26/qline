<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class LoyaltyReward extends Model
{
    protected $fillable = [
        'business_id',
        'required_visits',
        'reward_type',
        'reward_value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'required_visits' => 'integer',
        ];
    }

    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    public function earnedRewards()
    {
        return $this->hasMany(\App\Models\Marketing\EarnedReward::class);
    }
}
