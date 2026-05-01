<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Enums\UserRole;

#[Fillable(['name', 'email', 'password', 'phone', 'address', 'is_active', 'profile_completed'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's current business tenant.
     * Fallback to the owned business if business_id is not set.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant\Business::class);
    }

    /**
     * Get the business owned by this user (for owners).
     */
    public function ownedBusiness(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Tenant\Business::class, 'user_id');
    }

    /**
     * Helper to get the active business regardless of how it's linked.
     */
    public function getActiveBusiness(): ?\App\Models\Tenant\Business
    {
        return $this->business ?: $this->ownedBusiness;
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::BUSINESS_OWNER;
    }
}
