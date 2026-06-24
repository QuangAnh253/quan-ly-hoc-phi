<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Thông báo thu học phí</title></head>
<body style="font-family:sans-serif;color:#333;max-width:600px;margin:0 auto;padding:20px">
    <h2 style="color:#0d6efd">📢 Thông báo mở đợt thu học phí</h2>

    <p>Xin chào <strong>{{ $sinhVien->ho_ten }}</strong> ({{ $sinhVien->ma_sinh_vien }}),</p>

    <p>Nhà trường thông báo <strong>{{ $dotThu->ten_dot }}</strong> đã được mở.</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0">
        <tr style="background:#f8f9fa">
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Đợt thu</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6">{{ $dotThu->ten_dot }}</td>
        </tr>
        <tr>
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Hạn đóng</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6">{{ $dotThu->han_dong->format('d/m/Y') }}</td>
        </tr>
        <tr style="background:#f8f9fa">
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Số tiền phải đóng</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6;color:#dc3545;font-weight:bold">
                {{ number_format($soTienPhaiDong, 0, ',', '.') }}đ
            </td>
        </tr>
    </table>

    <p>Vui lòng đóng học phí đúng hạn để tránh bị tính phí phạt.</p>

    <hr style="border:none;border-top:1px solid #dee2e6;margin:24px 0">
    <p style="font-size:12px;color:#6c757d">Email tự động từ hệ thống Quản lý học phí – UTT</p>
</body>
</html>
