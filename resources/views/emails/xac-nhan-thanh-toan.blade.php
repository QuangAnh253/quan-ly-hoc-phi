<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Xác nhận thanh toán</title></head>
<body style="font-family:sans-serif;color:#333;max-width:600px;margin:0 auto;padding:20px">
    <h2 style="color:#198754">✅ Xác nhận đóng học phí thành công</h2>

    <p>Xin chào <strong>{{ $thanhToan->hocPhi->sinhVien->ho_ten }}</strong>,</p>

    <p>Nhà trường xác nhận đã nhận thanh toán học phí của bạn.</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0">
        <tr style="background:#f8f9fa">
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Mã giao dịch</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6">{{ $thanhToan->ma_giao_dich }}</td>
        </tr>
        <tr>
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Đợt thu</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6">{{ $thanhToan->hocPhi->dotThu->ten_dot }}</td>
        </tr>
        <tr style="background:#f8f9fa">
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Số tiền</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6;color:#198754;font-weight:bold">
                {{ number_format($thanhToan->so_tien, 0, ',', '.') }}đ
            </td>
        </tr>
        <tr>
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Hình thức</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6">{{ $thanhToan->hinh_thuc }}</td>
        </tr>
        <tr style="background:#f8f9fa">
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Thời gian</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6">{{ $thanhToan->thoi_gian_thu->format('H:i d/m/Y') }}</td>
        </tr>
    </table>

    <p>Vui lòng lưu email này làm bằng chứng thanh toán.</p>

    <hr style="border:none;border-top:1px solid #dee2e6;margin:24px 0">
    <p style="font-size:12px;color:#6c757d">Email tự động từ hệ thống Quản lý học phí – UTT</p>
</body>
</html>
