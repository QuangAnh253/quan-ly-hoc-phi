<?php

namespace App\Console\Commands;

use App\Models\DotThu;
use App\Services\HocPhiService;
use Illuminate\Console\Command;

/**
 * Chạy hàng ngày qua Scheduler:
 *   php artisan hocphi:tinh-phi-phat
 *
 * Đăng ký trong app/Console/Kernel.php:
 *   $schedule->command('hocphi:tinh-phi-phat')->dailyAt('00:05');
 */
class TinhPhiPhatCommand extends Command
{
    protected $signature   = 'hocphi:tinh-phi-phat';
    protected $description = 'Tính phí phạt quá hạn cho tất cả đợt thu đang mở';

    public function handle(HocPhiService $service): int
    {
        $dotThus = DotThu::where('trang_thai', 'dang_thu')->get();

        foreach ($dotThus as $dotThu) {
            if (!$dotThu->isQuaHan()) continue;

            $ketQua = $service->tinhPhiPhatQuaHan($dotThu);
            $this->info("Đợt #{$dotThu->id}: cập nhật {$ketQua['cap_nhat']} SV | Tổng phí phạt: " . number_format($ketQua['tong_phi_phat']) . "đ");
        }

        $this->info('✅ Hoàn tất tính phí phạt.');
        return Command::SUCCESS;
    }
}
