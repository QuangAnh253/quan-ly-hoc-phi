<?php

namespace App\Mail;

use App\Models\ThanhToan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class XacNhanThanhToanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ThanhToan $thanhToan,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ Xác nhận đóng học phí thành công – {$this->thanhToan->ma_giao_dich}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.xac-nhan-thanh-toan',
        );
    }
}
