<?php

namespace App\Models;

use App\UserAccountStatus;
use App\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'account_status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role instanceof UserRole && $this->role->canAccessPanel();
    }

    public function canAccess(string $ability): bool
    {
        return $this->role instanceof UserRole && $this->role->can($ability);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isDirektur(): bool
    {
        return $this->role === UserRole::Direktur;
    }

    public function isPimpinanRedaktur(): bool
    {
        return $this->role === UserRole::PimpinanRedaktur;
    }

    public function isRedaktur(): bool
    {
        return $this->role === UserRole::Redaktur;
    }

    public function isKontributor(): bool
    {
        return $this->role === UserRole::Kontributor;
    }

    public function isAccountActive(): bool
    {
        return $this->account_status === UserAccountStatus::Active;
    }

    public function isAccountPending(): bool
    {
        return $this->account_status === UserAccountStatus::Pending;
    }

    public function canSubmitArticles(): bool
    {
        return $this->isKontributor() && $this->isAccountActive();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function scopeKontributor(Builder $query): Builder
    {
        return $query->where('role', UserRole::Kontributor);
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->kontributor()->where('account_status', UserAccountStatus::Pending);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'account_status' => UserAccountStatus::class,
        ];
    }
}
