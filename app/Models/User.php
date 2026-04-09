<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'pin'];
    protected $hidden   = ['password', 'remember_token', 'pin'];
    protected $casts    = ['is_active' => 'boolean'];

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isManager(): bool  { return in_array($this->role, ['admin', 'manager']); }
    public function isCashier(): bool  { return $this->role === 'cashier'; }

    public function sales() { return $this->hasMany(Sale::class); }

    public function getSalesTodayCountAttribute(): int
    {
        return $this->sales()->whereDate('created_at', today())->count();
    }
}
