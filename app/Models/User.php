<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_RH = 'rh';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_EMPLOYE = 'employe';

    public function employe()
    {
        return $this->hasOne(Employe::class);
    }

    public function estAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function estRH(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_RH], true);
    }

    public function estGestion(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_RH, self::ROLE_MANAGER], true);
    }

    public function libelleRole(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrateur',
            self::ROLE_RH => 'Gestionnaire RH',
            self::ROLE_MANAGER => 'Manager',
            default => 'Employé',
        };
    }
}
