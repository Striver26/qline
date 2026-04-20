<?php

namespace App\Models\Queue;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'processed_by_user_id',
        'called_at',
        'served_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'called_at' => 'datetime',
            'served_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant\Counter::class);
    }

    public function customerFeedback(): HasOne
    {
        return $this->hasOne(\App\Models\Marketing\CustomerFeedback::class);
    }

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(\App\Models\Marketing\WhatsappMessage::class);
    }
}
