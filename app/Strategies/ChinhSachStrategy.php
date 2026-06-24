<?php

namespace App\Strategies;

/**
 * Diện chính sách (con thương binh, liệt sĩ, gia đình có công) — giảm 50%.
 * Căn cứ: Nghị định 81/2021/NĐ-CP Điều 16.
 */
class ChinhSachStrategy implements ITinhHocPhiStrategy
{
    private const PHAN_TRAM_GIAM = 50.0;

    public function tinh(int $soTinChi, float $donGiaTinChi, float $soTienMienGiam = 0): array
    {
        $tongTruocGiam = $soTinChi * $donGiaTinChi;
        $soTienGiam    = $tongTruocGiam * (self::PHAN_TRAM_GIAM / 100);
        $tongPhaiDong  = $tongTruocGiam - $soTienGiam;

        return [
            'tong_truoc_giam' => $tongTruocGiam,
            'phan_tram_giam'  => self::PHAN_TRAM_GIAM,
            'so_tien_giam'    => $soTienGiam,
            'tong_phai_dong'  => $tongPhaiDong,
            'ghi_chu'         => sprintf(
                "Chính sách — giảm %g%%: %s đ → còn %s đ",
                self::PHAN_TRAM_GIAM,
                number_format($tongTruocGiam, 0, ',', '.'),
                number_format($tongPhaiDong,  0, ',', '.')
            ),
        ];
    }

    public function getTenDien(): string
    {
        return 'Chính sách (giảm 50%)';
    }
}
