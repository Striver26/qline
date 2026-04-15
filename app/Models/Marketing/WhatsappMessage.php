<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'business_id',
        'queue_entry_id',
        'wa_id',
        'direction',
        'template',
        'body',
        'message_id',
        'status',
    ];

    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    public function queueEntry()
    {
        return $this->belongsTo(\App\Models\Queue\QueueEntry::class);
    }
}
