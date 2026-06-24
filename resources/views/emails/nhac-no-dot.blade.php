<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Nhắc nợ học phí</title></head>
<body style="font-family:sans-serif;color:#333;max-width:600px;margin:0 auto;padding:20px">
    <h2 style="color:#dc3545">⚠️ Nhắc nhở đóng học phí</h2>

    <p>Xin chào <strong>{{ $sinhVien->ho_ten }}</strong> ({{ $sinhVien->ma_sinh_vien }}),</p>

    <p>Theo hệ thống, bạn <strong>chưa hoàn tất đóng học phí</strong> cho đợt <strong>{{ $dotThu->ten_dot }}</strong>.</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0">
        <tr style="background:#fff3cd">
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Số tiền còn nợ</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6;color:#dc3545;font-weight:bold">
                {{ number_format($soTienConNo, 0, ',', '.') }}đ
            </td>
        </tr>
        <tr>
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Hạn đóng</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6">{{ $dotThu->han_dong->format('d/m/Y') }}</td>
        </tr>
    </table>

    <p>⚡ Sau hạn đóng, hệ thống sẽ tự động tính <strong>phí phạt quá hạn</strong>.</p>
    <p>Vui lòng đến phòng kế toán hoặc đóng qua cổng trực tuyến.</p>

    <hr style="border:none;border-top:1px solid #dee2e6;margin:24px 0">
    <p style="font-size:12px;color:#6c757d">Email tự động từ hệ thống Quản lý học phí – UTT</p>
</body>
</html>
