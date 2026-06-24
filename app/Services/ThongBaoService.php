<?php

namespace App\Services;

use App\Models\{DotThu, SinhVien, HocPhi, ThanhToan};
use App\Jobs\GuiEmailThongBaoJob;
use Illuminate\Support\Facades\{Log, Mail};

/**
 * ThongBaoService — Gửi thông báo email cho sinh viên.
 * Được DotThuObserver gọi tự động khi có sự kiện.
 */
class ThongBaoService
{
    /**
     * Gửi email thông báo mở đợt thu mới đến tất cả SV active.
     * Dispatch Job vào Queue để xử lý bất đồng bộ.
     */
    public function guiThongBaoMoDot(DotThu $dotThu): void
    {
        // Lấy danh sách email SV active (chunk để tránh out-of-memory)
        // Lấy học phí đã tính cho đợt này để có số tiền phải đóng
        $dotThu->loadMissing('hocPhis.sinhVien');

        SinhVien::where('active', true)
            ->whereNotNull('email')
            ->chunk(100, function ($sinhViens) use ($dotThu) {
                foreach ($sinhViens as $sv) {
                    // Tìm bản ghi học phí tương ứng để lấy số tiền
                    $hocPhi = $dotThu->hocPhis->firstWhere('sinh_vien_id', $sv->id);
                    $soTienPhaiDong = $hocPhi?->tong_phai_dong ?? 0;

                    GuiEmailThongBaoJob::dispatch('mo_dot', [
                        'sinhVien'       => $sv,
                        'dotThu'         => $dotThu,
                        'soTienPhaiDong' => $soTienPhaiDong,
                    ])->onQueue('emails');
                }
            });

        Log::info("Đã queue thông báo mở đợt#{$dotThu->id} cho toàn bộ SV");
    }

    /**
     * Gửi email nhắc nhở SV sắp hết hạn đóng (chạy qua Scheduler hàng ngày).
     */
    public function guiNhacNhoSapHetHan(DotThu $dotThu, int $soNgayCanhBao = 3): void
    {
        $hanDong = $dotThu->han_dong;
        if (now()->diffInDays($hanDong, false) > $soNgayCanhBao) return;

        // Chỉ gửi cho SV chưa đóng đủ
        HocPhi::with('sinhVien')
            ->where('dot_thu_id', $dotThu->id)
            ->whereIn('trang_thai', ['chua_dong', 'dong_mot_phan'])
            ->chunk(100, function ($hocPhis) use ($dotThu) {
                foreach ($hocPhis as $hp) {
                    if (!$hp->sinhVien->email) continue;
                    GuiEmailThongBaoJob::dispatch('nhac_no', [
                        'sinhVien'    => $hp->sinhVien,
                        'dotThu'      => $dotThu,
                        'soTienConNo' => $hp->so_tien_con_no ?? ($hp->tong_phai_dong - $hp->da_dong),
                    ])->onQueue('emails');
                }
            });

        Log::info("Đã queue nhắc nhở sắp hết hạn đợt#{$dotThu->id}");
    }

    /**
     * Gửi email xác nhận thanh toán thành công cho SV.
     */
    public function guiXacNhanThanhToan(SinhVien $sv, DotThu $dotThu, float $soTien, ThanhToan $thanhToan): void
    {
        if (!$sv->email) return;

        GuiEmailThongBaoJob::dispatch('xac_nhan_thanh_toan', [
            'thanhToan' => $thanhToan,
        ])->onQueue('emails');
    }

    /**
     * Gửi thông báo đóng đợt thu.
     */
    public function guiThongBaoDongDot(DotThu $dotThu): void
    {
        Log::info("Thông báo đóng đợt#{$dotThu->id}: {$dotThu->ten_dot}");
        // Implement gửi email tổng kết cho admin/kế toán nếu cần
    }
}
