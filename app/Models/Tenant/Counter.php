<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $guarded = [];

    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    public function queueEntries()
    {
        return $this->hasMany(\App\Models\Queue\QueueEntry::class);
    }
}
