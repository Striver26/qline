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
        'delivered_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    public function queueEntry()
    {
        return $this->belongsTo(\App\Models\Queue\QueueEntry::class);
    }
}
