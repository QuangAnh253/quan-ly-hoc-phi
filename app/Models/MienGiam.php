<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MienGiam extends Model
{
    protected $table = 'mien_giams';

    protected $fillable = [
        'sinh_vien_id', 'loai', 'phan_tram_giam', 'so_tien_giam_co_dinh',
        'so_quyet_dinh', 'chung_tu', 'nam_ap_dung', 'active',
    ];

    protected $casts = [
        'phan_tram_giam'       => 'decimal:2',
        'so_tien_giam_co_dinh' => 'decimal:2',
    ];

    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /** Tính số tiền được giảm dựa trên tổng học phí trước giảm */
    public function tinhSoTienGiam(float $tongTruocGiam): float
    {
        if ($this->so_tien_giam_co_dinh > 0) {
            return min($this->so_tien_giam_co_dinh, $tongTruocGiam);
        }
        return $tongTruocGiam * ($this->phan_tram_giam / 100);
    }

    public function getLabelAttribute(): string
    {
        return match($this->loai) {
            'ho_ngheo'           => 'Hộ nghèo (miễn 100%)',
            'ho_can_ngheo'       => 'Hộ cận nghèo (giảm 50%)',
            'chinh_sach'         => 'Chính sách (giảm 50%)',
            'thuong_binh'        => 'Thương binh (giảm 70%)',
            'mo_coi'             => 'Mồ côi (miễn 100%)',
            'khuyet_tat'         => 'Khuyết tật (miễn 100%)',
            'hoc_bong_kk'        => 'Học bổng khuyến khích',
            'hoc_bong_toan_phan' => 'Học bổng toàn phần (miễn 100%)',
            default              => $this->loai,
        };
    }
}
