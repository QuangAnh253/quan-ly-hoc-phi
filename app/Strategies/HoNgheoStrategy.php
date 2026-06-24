<?php

namespace App\Strategies;

/**
 * Diện hộ nghèo — miễn 100% học phí.
 * Căn cứ: Nghị định 81/2021/NĐ-CP.
 */
class HoNgheoStrategy implements ITinhHocPhiStrategy
{
    private const PHAN_TRAM_MIEN = 100.0;

    public function tinh(int $soTinChi, float $donGiaTinChi, float $soTienMienGiam = 0): array
    {
        $tongTruocGiam = $soTinChi * $donGiaTinChi;
        $soTienGiam    = $tongTruocGiam; // miễn toàn bộ

        return [
            'tong_truoc_giam' => $tongTruocGiam,
            'phan_tram_giam'  => self::PHAN_TRAM_MIEN,
            'so_tien_giam'    => $soTienGiam,
            'tong_phai_dong'  => 0,
            'ghi_chu'         => "Hộ nghèo — miễn 100% (NĐ 81/2021): " . number_format($soTienGiam, 0, ',', '.') . " đ",
        ];
    }

    public function getTenDien(): string
    {
        return 'Hộ nghèo (miễn 100%)';
    }
}
