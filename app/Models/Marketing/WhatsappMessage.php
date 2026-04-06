<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $guarded = [];

    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    public function queueEntry()
    {
        return $this->belongsTo(\App\Models\Queue\QueueEntry::class);
    }
}
