<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Thông báo tài khoản thử nghiệm</title></head>
<body style="font-family:sans-serif;color:#333;max-width:600px;margin:0 auto;padding:20px">
    <h2 style="color:#0d6efd">🔑 Thông báo cấp tài khoản trải nghiệm hệ thống</h2>

    <p>Xin chào <strong>{{ $sinhVien->ho_ten }}</strong>,</p>

    <p>Hệ thống Quản lý học phí gửi đến bạn thông tin tài khoản để truy cập và thử nghiệm các tính năng:</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0">
        <tr style="background:#f8f9fa">
            <td style="padding:8px 12px;border:1px solid #dee2e6;width:40%"><strong>Trang truy cập</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6">
                <a href="https://hocphi.lequanganh.id.vn/login" style="color:#0d6efd;text-decoration:none;font-weight:bold;">hocphi.lequanganh.id.vn</a>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Tài khoản đăng nhập</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6;font-weight:bold">{{ $sinhVien->email }}</td>
        </tr>
        <tr style="background:#f8f9fa">
            <td style="padding:8px 12px;border:1px solid #dee2e6"><strong>Mật khẩu thử nghiệm</strong></td>
            <td style="padding:8px 12px;border:1px solid #dee2e6;color:#dc3545;font-weight:bold">{{ $matKhau }}</td>
        </tr>
    </table>

    <p style="margin: 20px 0;">
        <a href="https://hocphi.lequanganh.id.vn/login" style="background-color:#0d6efd;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:4px;font-weight:bold;display:inline-block;">Đăng nhập hệ thống</a>
    </p>

    <p>Vui lòng sử dụng thông tin trên để kiểm tra luồng đóng học phí online và phản hồi lại cho nhóm phát triển nếu gặp lỗi.</p>

    <hr style="border:none;border-top:1px solid #dee2e6;margin:24px 0">
    
    <div style="background-color:#fff3cd;color:#856404;padding:12px;border-radius:4px;font-size:13px;margin-bottom:15px;border:1px solid #ffeeba;line-height:1.5;">
        <strong>⚠️ LƯU Ý QUAN TRỌNG:</strong> Đây chỉ là email tự động được gửi từ hệ thống thử nghiệm nhằm phục vụ mục đích bảo vệ đồ án môn học tại UTT. Toàn bộ số liệu tài khoản và yêu cầu thanh toán đều là giả định, không có giá trị pháp lý. Xin vui lòng bỏ qua email này.
    </div>

    <p style="font-size:12px;color:#6c757d">Email tự động từ hệ thống thử nghiệm Quản lý học phí – UTT</p>
</body>
</html>