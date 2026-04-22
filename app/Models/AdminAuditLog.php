<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'meta',
        'ip',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Record a destructive admin action.
     */
    public static function record(string $action, Model $target, array $meta = []): void
    {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'target_type' => get_class($target),
            'target_id'   => $target->getKey(),
            'meta'        => $meta ?: null,
            'ip'          => request()->ip(),
        ]);
    }
}
