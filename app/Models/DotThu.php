<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DotThu extends Model
{
    protected $table = 'dot_thus';

    protected $fillable = [
        'ten_dot', 'hoc_ky', 'nam_hoc', 'don_gia_tin_chi',
        'ngay_bat_dau', 'han_dong', 'phi_phat_ngay', 'trang_thai',
        'ghi_chu', 'created_by',
    ];

    protected $casts = [
        'ngay_bat_dau'    => 'date',
        'han_dong'        => 'date',
        'don_gia_tin_chi' => 'decimal:2',
        'phi_phat_ngay'   => 'decimal:2',
    ];

    // ── Relationships ───────────────────────────────────
    public function hocPhis(): HasMany
    {
        return $this->hasMany(HocPhi::class, 'dot_thu_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Business helpers ────────────────────────────────
    public function isQuaHan(): bool
    {
        return now()->gt($this->han_dong);
    }

    public function getSoNgayQuaHanAttribute(): int
    {
        if (!$this->isQuaHan()) return 0;
        return (int) now()->diffInDays($this->han_dong);
    }

    public function getTongThuAttribute(): float
    {
        return $this->hocPhis()->sum('da_dong');
    }

    public function getTongPhaiThuAttribute(): float
    {
        return $this->hocPhis()->sum('tong_phai_dong');
    }

    public function getTyLeThuAttribute(): float
    {
        if ($this->tong_phai_thu == 0) return 0;
        return round($this->tong_thu / $this->tong_phai_thu * 100, 2);
    }

    public function scopeDangThu($query)
    {
        return $query->where('trang_thai', 'dang_thu');
    }
}
