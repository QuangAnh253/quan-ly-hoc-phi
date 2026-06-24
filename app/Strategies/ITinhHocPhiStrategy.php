<?php

namespace App\Strategies;

/**
 * Strategy Pattern — Interface tính học phí
 *
 * Mỗi diện sinh viên sẽ có một class implement interface này.
 * Controller / Service chỉ biết đến interface, không biết class cụ thể.
 * → Thêm diện mới: chỉ tạo thêm 1 class, không sửa code cũ (Open/Closed Principle).
 */
interface ITinhHocPhiStrategy
{
    /**
     * Tính số tiền học phí phải đóng.
     *
     * @param  int   $soTinChi      Số tín chỉ đăng ký trong kỳ
     * @param  float $donGiaTinChi  Đơn giá 1 tín chỉ (VNĐ)
     * @param  float $soTienMienGiam Số tiền được miễn giảm thêm ngoài tỷ lệ (nếu có)
     * @return array{
     *     tong_truoc_giam: float,
     *     phan_tram_giam:  float,
     *     so_tien_giam:    float,
     *     tong_phai_dong:  float,
     *     ghi_chu:         string
     * }
     */
    public function tinh(int $soTinChi, float $donGiaTinChi, float $soTienMienGiam = 0): array;

    /**
     * Tên diện để hiển thị trên giao diện / báo cáo.
     */
    public function getTenDien(): string;
}
