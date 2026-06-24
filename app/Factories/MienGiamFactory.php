<?php

namespace App\Factories;

use App\Strategies\ITinhHocPhiStrategy;
use App\Strategies\BinhThuongStrategy;
use App\Strategies\HoNgheoStrategy;
use App\Strategies\ChinhSachStrategy;
use App\Strategies\HocBongStrategy;
use App\Models\MienGiam;
use InvalidArgumentException;

/**
 * Factory Pattern — Tạo đúng Strategy dựa vào diện sinh viên.
 *
 * HocPhiService gọi MienGiamFactory::create($dien, $mienGiam)
 * và nhận về đúng Strategy cần dùng — không cần biết class cụ thể nào.
 */
class MienGiamFactory
{
    /**
     * @param  string        $dien      Giá trị cột dien_mien_giam trong bảng sinh_viens
     * @param  MienGiam|null $mienGiam  Bản ghi miễn giảm hiện tại (nếu có)
     * @return ITinhHocPhiStrategy
     */
    public static function create(string $dien, ?MienGiam $mienGiam = null): ITinhHocPhiStrategy
    {
        return match($dien) {
            'binh_thuong'  => new BinhThuongStrategy(),

            'ho_ngheo',
            'mo_coi',
            'khuyet_tat'   => new HoNgheoStrategy(),

            'ho_can_ngheo',
            'chinh_sach',
            'thuong_binh'  => new ChinhSachStrategy(),

            'hoc_bong'     => new HocBongStrategy(
                soTienHocBong: $mienGiam?->so_tien_giam_co_dinh ?? 0,
                loaiHocBong:   $mienGiam?->label ?? 'Học bổng'
            ),

            default => throw new InvalidArgumentException(
                "Diện miễn giảm không hợp lệ: [{$dien}]"
            ),
        };
    }
}
