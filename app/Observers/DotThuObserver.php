<?php

namespace App\Observers;

use App\Models\{DotThu, SinhVien};
use App\Services\ThongBaoService;
use Illuminate\Support\Facades\Log;

/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║  OBSERVER PATTERN — DotThuObserver                          ║
 * ║                                                              ║
 * ║  Lắng nghe các sự kiện trên Model DotThu.                   ║
 * ║  Khi đợt thu được tạo / chuyển trạng thái → tự động        ║
 * ║  gửi thông báo cho sinh viên mà không cần Controller biết.  ║
 * ╚══════════════════════════════════════════════════════════════╝
 */
class DotThuObserver
{
    public function __construct(private ThongBaoService $thongBao) {}

    // ── Sự kiện: Đợt thu vừa được tạo ───────────────────────────
    public function created(DotThu $dotThu): void
    {
        Log::info("DotThuObserver@created — Đợt#{$dotThu->id}: {$dotThu->ten_dot}");

        // Gửi thông báo bất đồng bộ qua Queue để không block request
        $this->thongBao->guiThongBaoMoDot($dotThu);
    }

    // ── Sự kiện: Trạng thái đợt thu thay đổi ────────────────────
    public function updated(DotThu $dotThu): void
    {
        // Chỉ xử lý khi cột trang_thai thay đổi
        if (! $dotThu->wasChanged('trang_thai')) return;

        $cu  = $dotThu->getOriginal('trang_thai');
        $moi = $dotThu->trang_thai;

        Log::info("DotThuObserver@updated — Đợt#{$dotThu->id}: {$cu} → {$moi}");

        match($moi) {
            // Admin vừa mở đợt: gửi email toàn bộ SV
            'dang_thu' => $this->thongBao->guiThongBaoMoDot($dotThu),

            // Admin đóng đợt: gửi thông báo kết thúc
            'da_dong'  => $this->thongBao->guiThongBaoDongDot($dotThu),

            default    => null,
        };
    }
}
