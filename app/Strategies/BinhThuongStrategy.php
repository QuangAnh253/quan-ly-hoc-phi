<?php

namespace App\Strategies;

/**
 * Diện bình thường — không được miễn giảm.
 * Học phí = số tín chỉ × đơn giá.
 */
class BinhThuongStrategy implements ITinhHocPhiStrategy
{
    public function tinh(int $soTinChi, float $donGiaTinChi, float $soTienMienGiam = 0): array
    {
        $tongTruocGiam = $soTinChi * $donGiaTinChi;

        return [
            'tong_truoc_giam' => $tongTruocGiam,
            'phan_tram_giam'  => 0,
            'so_tien_giam'    => 0,
            'tong_phai_dong'  => $tongTruocGiam,
            'ghi_chu'         => "Học phí bình thường: {$soTinChi} TC × " . number_format($donGiaTinChi, 0, ',', '.') . " đ/TC",
        ];
    }

    public function getTenDien(): string
    {
        return 'Bình thường';
    }
}
