<?php

namespace App\Models\Queue;

use App\Enums\QueueStatus;
use App\Models\Marketing\CustomerFeedback;
use App\Models\Marketing\WhatsappMessage;
use App\Models\Tenant\Business;
use App\Models\Tenant\ServicePoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'service_point_id',
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
        return $this->belongsTo(Business::class);
    }

    public function servicePoint(): BelongsTo
    {
        return $this->belongsTo(ServicePoint::class, 'service_point_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public function customerFeedback(): HasOne
    {
        return $this->hasOne(CustomerFeedback::class);
    }

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class);
    }

    public function scopeForBusiness(Builder $query, int $businessId): Builder
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', QueueStatus::WAITING->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            QueueStatus::CALLED->value,
            QueueStatus::SERVING->value,
        ]);
    }
}
