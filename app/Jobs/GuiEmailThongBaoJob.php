<?php

namespace App\Jobs;

use App\Mail\ThongBaoMoDotMail;
use App\Mail\NhacNoDotMail;
use App\Mail\XacNhanThanhToanMail;
use App\Models\DotThu;
use App\Models\SinhVien;
use App\Models\ThanhToan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GuiEmailThongBaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $loai,
        private array $data
    ) {}

    public function handle(): void
    {
        switch ($this->loai) {

            case 'mo_dot':

                Mail::to($this->data['sinhVien']->email)
                    ->send(
                        new ThongBaoMoDotMail(
                            $this->data['dotThu'],
                            $this->data['sinhVien'],
                            $this->data['soTienPhaiDong']
                        )
                    );

                break;

            case 'nhac_no':

                Mail::to($this->data['sinhVien']->email)
                    ->send(
                        new NhacNoDotMail(
                            $this->data['dotThu'],
                            $this->data['sinhVien'],
                            $this->data['soTienConNo']
                        )
                    );

                break;

            case 'xac_nhan_thanh_toan':

                Mail::to(
                    $this->data['thanhToan']
                        ->hocPhi
                        ->sinhVien
                        ->email
                )->send(
                    new XacNhanThanhToanMail(
                        $this->data['thanhToan']
                    )
                );

                break;
        }
    }
}
