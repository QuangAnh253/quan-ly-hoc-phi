<?php

namespace App\Services;

use App\Models\{HocPhi, ThanhToan, SinhVien, DotThu};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

/**
 * ThanhToanService — Xử lý nghiệp vụ thanh toán & lịch sử giao dịch
 * Được HocPhiService gọi nội bộ, hoặc Controller gọi trực tiếp
 * khi cần tra cứu lịch sử / in biên lai.
 */
class ThanhToanService
{
    // ── 1. Lịch sử giao dịch ────────────────────────────────────

    /**
     * Lịch sử thanh toán của một sinh viên — toàn bộ các kỳ.
     */
    public function lichSuTheoSinhVien(SinhVien $sv): Collection
    {
        return ThanhToan::with(['hocPhi.dotThu'])
            ->whereHas('hocPhi', fn($q) => $q->where('sinh_vien_id', $sv->id))
            ->orderByDesc('thoi_gian_thu')
            ->get();
    }

    /**
     * Lịch sử thanh toán của một đợt thu — dùng cho báo cáo kế toán.
     */
    public function lichSuTheoDotThu(DotThu $dotThu): Collection
    {
        return ThanhToan::with(['hocPhi.sinhVien', 'nguoiThu'])
            ->whereHas('hocPhi', fn($q) => $q->where('dot_thu_id', $dotThu->id))
            ->orderByDesc('thoi_gian_thu')
            ->get();
    }

    // ── 2. Biên lai ─────────────────────────────────────────────

    /**
     * Lấy đủ dữ liệu để render biên lai PDF / in ra màn hình.
     *
     * @return array{
     *     thanh_toan: ThanhToan,
     *     hoc_phi:    HocPhi,
     *     sinh_vien:  SinhVien,
     *     dot_thu:    DotThu,
     * }
     */
    public function layDuLieuBienLai(string $maGiaoDich): array
    {
        $thanhToan = ThanhToan::with([
            'hocPhi.sinhVien.khoa',
            'hocPhi.dotThu',
            'nguoiThu',
        ])->where('ma_giao_dich', $maGiaoDich)->firstOrFail();

        return [
            'thanh_toan' => $thanhToan,
            'hoc_phi'    => $thanhToan->hocPhi,
            'sinh_vien'  => $thanhToan->hocPhi->sinhVien,
            'dot_thu'    => $thanhToan->hocPhi->dotThu,
        ];
    }

    // ── 3. Hủy giao dịch (admin only) ───────────────────────────

    /**
     * Hủy một giao dịch thanh toán — trừ lại da_dong, cập nhật trạng thái.
     *
     * @throws \RuntimeException Nếu giao dịch đã bị hủy trước đó
     */
    public function huyGiaoDich(ThanhToan $thanhToan, string $lyDo): void
    {
        DB::transaction(function () use ($thanhToan, $lyDo) {
            $hocPhi = $thanhToan->hocPhi;

            // Trừ lại số tiền đã thu
            $hocPhi->decrement('da_dong', $thanhToan->so_tien);
            $hocPhi->refresh();
            $hocPhi->capNhatTrangThai();

            // Ghi chú hủy vào bản ghi
            $thanhToan->update([
                'ghi_chu' => "[HỦY - {$lyDo}] " . ($thanhToan->ghi_chu ?? ''),
                'so_tien' => 0,
            ]);

            Log::warning("Hủy GD {$thanhToan->ma_giao_dich} | Lý do: {$lyDo}");
        });
    }
}
