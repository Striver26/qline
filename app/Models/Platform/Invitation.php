<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    // The business sending the invitation
    public function business()
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }
}
