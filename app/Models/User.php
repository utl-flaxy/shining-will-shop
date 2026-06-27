<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Filament 管理画面へのアクセス権を許可する判定
     */
    public function canAccessFilament(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Filament のヘッダーなどに表示される名前（任意）
     */
    public function getFilamentName(): string
    {
        return $this->name ?? $this->email;
    }
}
