<?php

namespace App\Models\Queue;

use Illuminate\Database\Eloquent\Model;

class QueueEntry extends Model
{
    protected $fillable = [
        'business_id',
        'wa_id',
        'ticket_number',
        'ticket_code',
        'status',
        'source',
        'cancel_token',
        'position',
        'counter_id',
        'called_at',
        'served_at',
        'completed_at',
    ];

    // The business this ticket belongs to
    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    // The physical counter this ticket was called to (if any)
    public function counter()
    {
        return $this->belongsTo(\App\Models\Tenant\Counter::class);
    }

    // Customer feedback submitted for this ticket
    public function customerFeedback()
    {
        return $this->hasOne(\App\Models\Marketing\CustomerFeedback::class);
    }

    // WhatsApp messages related to this queue entry
    public function whatsappMessages()
    {
        return $this->hasMany(\App\Models\Marketing\WhatsappMessage::class);
    }
}
