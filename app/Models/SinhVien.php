<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class SinhVien extends Model
{
    protected $table = 'sinh_viens';

    protected $fillable = [
        'user_id', 'khoa_id', 'ma_sv', 'ho_ten', 'ngay_sinh',
        'gioi_tinh', 'cccd', 'lop', 'nien_khoa', 'he_dao_tao',
        'dien_mien_giam', 'so_dien_thoai', 'email', 'dia_chi', 'active',
    ];

    protected $casts = ['ngay_sinh' => 'date'];

    // ── Relationships ───────────────────────────────────
    public function khoa(): BelongsTo
    {
        return $this->belongsTo(Khoa::class, 'khoa_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hocPhis(): HasMany
    {
        return $this->hasMany(HocPhi::class, 'sinh_vien_id');
    }

    public function mienGiams(): HasMany
    {
        return $this->hasMany(MienGiam::class, 'sinh_vien_id');
    }

    // ── Business helpers ────────────────────────────────
    /** Tổng công nợ chưa đóng của sinh viên này */
    public function getTongConNoAttribute(): float
    {
        return $this->hocPhis()->sum(DB::raw('tong_phai_dong - da_dong + phi_phat'));
    }

    /** Lấy diện miễn giảm đang áp dụng năm hiện tại */
    public function mienGiamHienTai()
    {
        return $this->mienGiams()
            ->where('nam_ap_dung', now()->year)
            ->where('active', true)
            ->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeConNo(Builder $query): Builder
    {
        return $query->whereHas('hocPhis', function ($q) {
            $q->whereIn('trang_thai', ['chua_dong', 'dong_mot_phan']);
        });
    }
}
