<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Compte admin. store_id null + is_admin = accès groupe (tout voir).
 * store_id renseigné = l'équipe ne voit que les demandes de son magasin.
 */
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'store_id', 'is_admin'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin || $this->store_id !== null;
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function isGroupAdmin(): bool
    {
        return $this->is_admin && $this->store_id === null;
    }
}
