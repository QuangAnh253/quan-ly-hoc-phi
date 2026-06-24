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

    <p style="margin: 20px 0;">
        <a href="https://hocphi.lequanganh.id.vn/login" style="background-color:#0d6efd;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:4px;font-weight:bold;display:inline-block;">Truy cập cổng thanh toán</a>
    </p>

    <p>Vui lòng đóng học phí đúng hạn để tránh bị tính phí phạt.</p>

    <hr style="border:none;border-top:1px solid #dee2e6;margin:24px 0">
    
    <div style="background-color:#fff3cd;color:#856404;padding:12px;border-radius:4px;font-size:13px;margin-bottom:15px;border:1px solid #ffeeba;line-height:1.5;">
        <strong>⚠️ LƯU Ý QUAN TRỌNG:</strong> Đây chỉ là email tự động được gửi từ hệ thống thử nghiệm nhằm phục vụ mục đích bảo vệ đồ án môn học tại UTT. Toàn bộ số liệu và yêu cầu thanh toán đều là giả định, không có giá trị pháp lý. Xin vui lòng bỏ qua email này.
    </div>

    <p style="font-size:12px;color:#6c757d">Email tự động từ hệ thống thử nghiệm Quản lý học phí – UTT</p>
</body>
</html>