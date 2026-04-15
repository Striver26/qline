<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'is_active',
    ];

    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    public function queueEntries()
    {
        return $this->hasMany(\App\Models\Queue\QueueEntry::class);
    }
}
