<?php

namespace App\Mail;

use App\Models\DotThu;
use App\Models\SinhVien;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NhacNoDotMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DotThu   $dotThu,
        public SinhVien $sinhVien,
        public float    $soTienConNo,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠️ [{$this->dotThu->ten_dot}] Nhắc nhở đóng học phí",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nhac-no-dot',
        );
    }
}
