<?php

namespace App\Models\Tenant;

use App\Enums\SubTier;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'business_id',
        'type',
        'status',
        'starts_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => SubTier::class,
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // The business this subscription belongs to
    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    // The payment history for this subscription
    public function payments()
    {
        return $this->hasMany(\App\Models\Tenant\Payment::class);
    }
}
