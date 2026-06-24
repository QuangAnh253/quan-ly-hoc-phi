<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HocPhi extends Model
{
    protected $table = 'hoc_phis';

    protected $fillable = [
        'sinh_vien_id', 'dot_thu_id', 'so_tin_chi', 'don_gia_tin_chi',
        'phan_tram_giam', 'so_tien_giam', 'tong_phai_dong',
        'da_dong', 'phi_phat', 'trang_thai',
    ];

    protected $casts = [
        'don_gia_tin_chi' => 'decimal:2',
        'tong_phai_dong'  => 'decimal:2',
        'da_dong'         => 'decimal:2',
        'phi_phat'        => 'decimal:2',
    ];

    // ── Relationships ───────────────────────────────────
    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function dotThu(): BelongsTo
    {
        return $this->belongsTo(DotThu::class, 'dot_thu_id');
    }

    public function thanhToans(): HasMany
    {
        return $this->hasMany(ThanhToan::class, 'hoc_phi_id');
    }

    // ── Business helpers ────────────────────────────────
    public function getConNoAttribute(): float
    {
        return max(0, $this->tong_phai_dong - $this->da_dong + $this->phi_phat);
    }

    public function getDaDongDuAttribute(): bool
    {
        return $this->trang_thai === 'da_dong_du'
            || $this->trang_thai === 'mien_hoan_toan';
    }

    /** Cập nhật trạng thái tự động sau mỗi thanh toán */
    public function capNhatTrangThai(): void
    {
        $this->trang_thai = match(true) {
            $this->phan_tram_giam >= 100 => 'mien_hoan_toan',
            $this->da_dong >= $this->tong_phai_dong => 'da_dong_du',
            $this->da_dong > 0 => 'dong_mot_phan',
            default => 'chua_dong',
        };
        $this->save();
    }

    public function scopeChuaDong($query)
    {
        return $query->whereIn('trang_thai', ['chua_dong', 'dong_mot_phan']);
    }
}
