<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    // The business that made this payment
    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    // The subscription instance this payment was applied to
    public function subscription()
    {
        return $this->belongsTo(\App\Models\Tenant\Subscription::class);
    }
}
