<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'active'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

    // ── Helpers phân quyền ──────────────────────────────
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isKeToan(): bool  { return $this->role === 'ketoan'; }
    public function isSinhVien(): bool { return $this->role === 'sinhvien'; }

    public function canManageHocPhi(): bool
    {
        return in_array($this->role, ['admin', 'ketoan']);
    }

    // ── Relationships ───────────────────────────────────
    public function sinhVien()
    {
        return $this->hasOne(SinhVien::class, 'user_id');
    }

    public function thanhToans()
    {
        return $this->hasMany(ThanhToan::class, 'nguoi_thu_id');
    }
}
