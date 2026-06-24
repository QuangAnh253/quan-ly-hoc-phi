<?php

namespace App\Strategies;

/**
 * Diện học bổng — giảm một khoản tiền cố định (do bộ phận học bổng xác định).
 *
 * Khác với các diện trên (tính theo %), học bổng được quy đổi thành số tiền
 * cụ thể (VD: 2.000.000đ/kỳ). Nếu học bổng lớn hơn học phí → miễn toàn bộ,
 * không hoàn lại phần dư.
 */
class HocBongStrategy implements ITinhHocPhiStrategy
{
    private float $soTienHocBong;
    private string $loaiHocBong;

    /**
     * @param float  $soTienHocBong  Số tiền học bổng (VNĐ) cấp cho kỳ này
     * @param string $loaiHocBong    Tên loại học bổng để ghi vào ghi chú
     */
    public function __construct(float $soTienHocBong, string $loaiHocBong = 'Học bổng khuyến khích')
    {
        $this->soTienHocBong = $soTienHocBong;
        $this->loaiHocBong   = $loaiHocBong;
    }

    public function tinh(int $soTinChi, float $donGiaTinChi, float $soTienMienGiam = 0): array
    {
        $tongTruocGiam = $soTinChi * $donGiaTinChi;

        // Học bổng không được vượt quá tổng học phí (không hoàn tiền dư)
        $soTienGiam   = min($this->soTienHocBong, $tongTruocGiam);
        $tongPhaiDong = max(0, $tongTruocGiam - $soTienGiam);
        $phanTramGiam = $tongTruocGiam > 0
            ? round($soTienGiam / $tongTruocGiam * 100, 2)
            : 0;

        return [
            'tong_truoc_giam' => $tongTruocGiam,
            'phan_tram_giam'  => $phanTramGiam,
            'so_tien_giam'    => $soTienGiam,
            'tong_phai_dong'  => $tongPhaiDong,
            'ghi_chu'         => sprintf(
                "%s: %s đ — còn phải đóng: %s đ",
                $this->loaiHocBong,
                number_format($soTienGiam,   0, ',', '.'),
                number_format($tongPhaiDong, 0, ',', '.')
            ),
        ];
    }

    public function getTenDien(): string
    {
        return $this->loaiHocBong;
    }
}
