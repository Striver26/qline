<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    protected $guarded = [];

    // The business receiving the feedback
    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    // The specific queue ticket this feedback applies to
    public function queueEntry()
    {
        return $this->belongsTo(\App\Models\Queue\QueueEntry::class);
    }
}
