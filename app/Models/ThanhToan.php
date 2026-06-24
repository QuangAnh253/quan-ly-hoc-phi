<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThanhToan extends Model
{
    protected $table = 'thanh_toans';

    protected $fillable = [
        'hoc_phi_id', 'nguoi_thu_id', 'ma_giao_dich', 'so_tien',
        'hinh_thuc', 'ngan_hang', 'so_tham_chieu', 'ghi_chu', 'thoi_gian_thu',
    ];

    protected $casts = [
        'so_tien'      => 'decimal:2',
        'thoi_gian_thu' => 'datetime',
    ];

    // ── Relationships ───────────────────────────────────
    public function hocPhi(): BelongsTo
    {
        return $this->belongsTo(HocPhi::class, 'hoc_phi_id');
    }

    public function nguoiThu(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_thu_id');
    }

    // ── Business helpers ────────────────────────────────
    /** Tạo mã giao dịch tự động: TT-YYYYMMDD-XXXX */
    public static function generateMaGiaoDich(): string
    {
        $today = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return sprintf('TT-%s-%04d', $today, $count);
    }

    public function getHinhThucLabelAttribute(): string
    {
        return match($this->hinh_thuc) {
            'tien_mat'     => 'Tiền mặt',
            'chuyen_khoan' => 'Chuyển khoản',
            'the_ngan_hang' => 'Thẻ ngân hàng',
            'vi_dien_tu'   => 'Ví điện tử',
            default        => $this->hinh_thuc,
        };
    }
}
