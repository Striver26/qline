<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = [];

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
