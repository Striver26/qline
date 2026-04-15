<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;

class LoyaltyVisit extends Model
{
    protected $fillable = [
        'business_id',
        'queue_entry_id',
        'wa_id',
        'visit_number',
    ];

    protected function casts(): array
    {
        return [
            'visit_number' => 'integer',
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
