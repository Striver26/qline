<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class CustomerFeedback extends Model
{
    protected $fillable = [
        'business_id',
        'queue_entry_id',
        'rating',
        'comment',
        'wa_id',
    ];

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
