@extends('layouts.app')

@section('title','Học phí của tôi')
@section('page-title','Tổng quan học phí')

@section('content')

{{-- Stat cards --}}
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f8d7da">
        <i class="bi bi-wallet2 text-danger"></i>
      </div>
      <div>
        <div class="stat-label">Tổng còn nợ</div>
        <div class="stat-value text-danger" style="font-size:18px">
          {{ number_format($conNo['tong_con_no'], 0, ',', '.') }}đ
        </div>
        @if($conNo['tong_con_no'] == 0)
          <small class="text-success"><i class="bi bi-check-circle me-1"></i>Đã đóng đủ</small>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon" style="background:#d1e7dd">
        <i class="bi bi-person-badge text-success"></i>
      </div>
      <div>
        <div class="stat-label">Mã sinh viên</div>
        <div class="stat-value" style="font-size:18px">{{ $sv->ma_sv }}</div>
        <small class="text-muted">{{ $sv->lop }}</small>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e7f1ff">
        <i class="bi bi-building text-primary"></i>
      </div>
      <div>
        <div class="stat-label">Khoa</div>
        <div class="stat-value" style="font-size:15px;line-height:1.3">
          {{ $sv->khoa->ten_khoa }}
        </div>
        <small class="text-muted">{{ $sv->he_dao_tao === 'chinh_quy' ? 'Chính quy' : Str::title(str_replace('_',' ',$sv->he_dao_tao)) }}</small>
      </div>
    </div>
  </div>
</div>


<div class="row g-3">
  {{-- Công nợ theo đợt --}}
  <div class="col-md-7">
    <div class="card">
      <div class="card-header">
        <i class="bi bi-receipt me-2 text-primary"></i>Học phí theo đợt
      </div>
      <div class="card-body p-0">
        @if($conNo['chi_tiet']->isEmpty())
          <div class="text-center py-5 text-muted">
            <i class="bi bi-check-circle-fill text-success" style="font-size:40px"></i>
            <p class="mt-2 mb-0">Bạn đã đóng đầy đủ học phí. Chúc mừng!</p>
          </div>
        @else
        <table class="table mb-0">
          <thead>
            <tr>
              <th>Đợt thu</th>
              <th class="text-end">Phải đóng</th>
              <th class="text-end">Đã đóng</th>
              <th class="text-end">Còn nợ</th>
              <th>TT</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($conNo['chi_tiet'] as $hp)
            <tr>
              <td>
                <div class="fw-500" style="font-size:13px">{{ $hp->dotThu->ten_dot }}</div>
                <small class="text-muted">
                  HK{{ $hp->dotThu->hoc_ky }} |
                  Hạn: <span class="{{ $hp->dotThu->isQuaHan() ? 'text-danger' : '' }}">
                    {{ $hp->dotThu->han_dong->format('d/m/Y') }}
                  </span>
                </small>
              </td>
              <td class="text-end">{{ number_format($hp->tong_phai_dong, 0, ',', '.') }}đ</td>
              <td class="text-end text-success">{{ number_format($hp->da_dong, 0, ',', '.') }}đ</td>
              <td class="text-end fw-600 text-danger">{{ number_format($hp->con_no, 0, ',', '.') }}đ</td>
              <td>
                @if($hp->trang_thai === 'chua_dong')
                  <span class="badge bg-danger">Chưa</span>
                @elseif($hp->trang_thai === 'dong_mot_phan')
                  <span class="badge bg-warning text-dark">1 phần</span>
                @elseif($hp->trang_thai === 'da_dong_du')
                  <span class="badge bg-success">Đủ</span>
                @elseif($hp->trang_thai === 'mien_hoan_toan')
                  <span class="badge bg-info">Miễn</span>
                @endif
              </td>
              {{-- ✅ NÚT THANH TOÁN QR: chỉ hiện khi còn nợ --}}
              <td>
                @if(in_array($hp->trang_thai, ['chua_dong', 'dong_mot_phan']) && $hp->con_no > 0)
                  <button
                    type="button"
                    class="btn btn-primary btn-sm btn-qr-pay"
                    data-mssv="{{ $sv->ma_sv }}"
                    data-hoc-ky="{{ $hp->dotThu->hoc_ky }}"
                    data-nam-hoc="{{ $hp->dotThu->nam_hoc }}"
                    data-con-no="{{ (int) $hp->con_no }}"
                    data-ten-dot="{{ $hp->dotThu->ten_dot }}"
                    title="Thanh toán qua QR"
                  >
                    <i class="bi bi-qr-code me-1"></i>Thanh toán
                  </button>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
      </div>
    </div>
  </div>

  {{-- Lịch sử giao dịch --}}
  <div class="col-md-5">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Lịch sử thanh toán</span>
        <a href="{{ route('sinhvien.hoc-phi') }}" class="btn btn-outline-secondary btn-sm"
           style="font-size:11px">Xem tất cả</a>
      </div>
      <div class="card-body p-0">
        @forelse($lichSu->take(6) as $tt)
        <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
          <div style="width:32px;height:32px;border-radius:8px;background:#d1e7dd;
                      display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-check-lg text-success" style="font-size:14px"></i>
          </div>
          <div class="flex-fill" style="min-width:0">
            <div class="fw-500" style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $tt->hocPhi->dotThu->ten_dot }}
            </div>
            <div class="text-muted" style="font-size:11px">
              {{ $tt->thoi_gian_thu->format('H:i d/m/Y') }}
            </div>
          </div>
          <div class="text-success fw-600" style="font-size:13px;white-space:nowrap">
            +{{ number_format($tt->so_tien, 0, ',', '.') }}đ
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4" style="font-size:13px">
          <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:8px"></i>
          Chưa có giao dịch nào
        </div>
        @endforelse
      </div>
    </div>

    {{-- Thông tin cá nhân nhanh --}}
    <div class="card mt-3">
      <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Thông tin của tôi</div>
      <div class="card-body py-2 px-3">
        <table class="table table-sm table-borderless mb-0" style="font-size:13px">
          <tr><td class="text-muted ps-0">Họ tên</td><td class="fw-500">{{ $sv->ho_ten }}</td></tr>
          <tr><td class="text-muted ps-0">Ngày sinh</td><td>{{ $sv->ngay_sinh?->format('d/m/Y') ?? '—' }}</td></tr>
          <tr><td class="text-muted ps-0">Email</td><td>{{ $sv->email ?? '—' }}</td></tr>
          <tr><td class="text-muted ps-0">SĐT</td><td>{{ $sv->so_dien_thoai ?? '—' }}</td></tr>
          @if($sv->dien_mien_giam !== 'binh_thuong')
          <tr>
            <td class="text-muted ps-0">Miễn giảm</td>
            <td><span class="badge bg-info text-dark">{{ Str::title(str_replace('_',' ',$sv->dien_mien_giam)) }}</span></td>
          </tr>
          @endif
        </table>
      </div>
    </div>
  </div>
</div>


{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  MODAL THANH TOÁN QR — MB Bank VietQR                      ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="modal fade" id="modalQRPay" tabindex="-1" aria-labelledby="modalQRPayLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
    <div class="modal-content border-0 shadow-lg">

      {{-- Header --}}
      <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#1a56db,#0ea5e9);border-radius:12px 12px 0 0">
        <div class="text-white">
          <h5 class="modal-title fw-700 mb-0" id="modalQRPayLabel">
            <i class="bi bi-qr-code me-2"></i>Thanh toán học phí
          </h5>
          <div style="font-size:12px;opacity:.85" id="qr-ten-dot">—</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body px-4 py-3">

        {{-- Thông tin chuyển khoản --}}
        <div class="rounded-3 p-3 mb-3" style="background:#f0f7ff;border:1px solid #c8e1ff">
          <div class="d-flex justify-content-between mb-1" style="font-size:13px">
            <span class="text-muted">Ngân hàng</span>
            <strong>MB Bank</strong>
          </div>
          <div class="d-flex justify-content-between mb-1" style="font-size:13px">
            <span class="text-muted">Số tài khoản</span>
            <strong class="font-monospace">0833250305</strong>
          </div>
          <div class="d-flex justify-content-between mb-1" style="font-size:13px">
            <span class="text-muted">Số tiền</span>
            <strong class="text-danger" id="qr-so-tien">—</strong>
          </div>
          <div class="mt-2 pt-2 border-top" style="font-size:12px">
            <span class="text-muted d-block mb-1">Nội dung chuyển khoản</span>
            <div class="d-flex align-items-center gap-2">
              <code class="fw-600 flex-fill" id="qr-noi-dung" style="font-size:12px;word-break:break-all;background:#e7f1ff;padding:4px 8px;border-radius:6px">—</code>
              <button type="button" class="btn btn-outline-secondary btn-sm px-2" id="btn-copy-noi-dung" title="Sao chép">
                <i class="bi bi-clipboard" style="font-size:13px"></i>
              </button>
            </div>
          </div>
        </div>

        {{-- QR Code --}}
        <div class="text-center mb-3">
          <div class="d-inline-block p-2 rounded-3 border" style="background:#fff">
            <img id="qr-img" src="" alt="QR Code thanh toán"
                 style="width:220px;height:220px;display:block;border-radius:8px"
                 onerror="this.src='https://placehold.co/220x220/f8d7da/842029?text=Lỗi+tải+QR'">
          </div>
          <div class="mt-2 text-muted" style="font-size:11px">
            <i class="bi bi-phone me-1"></i>Mở app MB Bank → Quét mã để thanh toán
          </div>
        </div>

        {{-- Hướng dẫn --}}
        <div class="rounded-3 p-3" style="background:#fff8e1;border:1px solid #ffe082;font-size:12px">
          <div class="fw-600 mb-1 text-warning-emphasis"><i class="bi bi-info-circle me-1"></i>Lưu ý</div>
          <ul class="mb-0 ps-3" style="line-height:1.7">
            <li>Vui lòng <strong>không thay đổi</strong> nội dung chuyển khoản.</li>
            <li>Hệ thống tự đối soát trong vòng <strong>24 giờ</strong> sau khi nhận tiền.</li>
            <li>Liên hệ phòng Tài vụ nếu sau 24h chưa được cập nhật.</li>
          </ul>
        </div>
      </div>

      {{-- Footer --}}
      <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
          <i class="bi bi-x me-1"></i>Đóng
        </button>
      </div>

    </div>
  </div>
</div>

@endsection


@push('scripts')
<script>
/**
 * ╔══════════════════════════════════════════════════════════╗
 * ║  QR THANH TOÁN — VietQR API (miễn phí, không cần key)  ║
 * ║                                                          ║
 * ║  URL mẫu:                                                ║
 * ║  https://img.vietqr.io/image/{BANK_ID}-{STK}-           ║
 * ║  {TEMPLATE}.png?amount={SO_TIEN}&addInfo={NOI_DUNG}     ║
 * ╚══════════════════════════════════════════════════════════╝
 */
(function () {
  const BANK_ID  = 'MB';          // Mã ngân hàng MB Bank trên VietQR
  const STK      = '0833250305';  // Số tài khoản nhận
  const TEMPLATE = 'compact2';    // compact | compact2 | qr_only | print

  /**
   * Tạo nội dung chuyển khoản theo format:
   * MSSV-HocKy_NamHoc-DD_MM_YYYY
   * VD: 74DCHT21175-1_2025_2026-16_06_2026
   */
  function buildNoiDung(mssv, hocKy, namHoc) {
    const now  = new Date();
    const dd   = String(now.getDate()).padStart(2, '0');
    const mm   = String(now.getMonth() + 1).padStart(2, '0');
    const yyyy = now.getFullYear();
    // namHoc dạng "2025-2026" → chuyển thành "2025_2026"
    const namHocFmt = namHoc.replace('-', '_');
    return `${mssv}-${hocKy}_${namHocFmt}-${dd}_${mm}_${yyyy}`;
  }

  /**
   * Tạo URL QR theo chuẩn VietQR
   */
  function buildQrUrl(soTien, noiDung) {
    const encoded = encodeURIComponent(noiDung);
    return `https://img.vietqr.io/image/${BANK_ID}-${STK}-${TEMPLATE}.png`
         + `?amount=${soTien}&addInfo=${encoded}&accountName=PHONG%20TAI%20VU`;
  }

  // Lắng nghe sự kiện click các nút "Thanh toán QR"
  document.querySelectorAll('.btn-qr-pay').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const mssv   = this.dataset.mssv;
      const hocKy  = this.dataset.hocKy;
      const namHoc = this.dataset.namHoc;
      const conNo  = parseInt(this.dataset.conNo, 10);
      const tenDot = this.dataset.tenDot;

      // Tạo nội dung và URL QR
      const noiDung = buildNoiDung(mssv, hocKy, namHoc);
      const qrUrl   = buildQrUrl(conNo, noiDung);

      // Điền thông tin vào modal
      document.getElementById('qr-ten-dot').textContent  = tenDot;
      document.getElementById('qr-so-tien').textContent  =
        new Intl.NumberFormat('vi-VN').format(conNo) + 'đ';
      document.getElementById('qr-noi-dung').textContent = noiDung;
      document.getElementById('qr-img').src = qrUrl;

      // Mở modal
      var modal = new bootstrap.Modal(document.getElementById('modalQRPay'));
      modal.show();
    });
  });

  // Nút sao chép nội dung chuyển khoản
  document.getElementById('btn-copy-noi-dung').addEventListener('click', function () {
    const text = document.getElementById('qr-noi-dung').textContent;
    navigator.clipboard.writeText(text).then(function () {
      const btn = document.getElementById('btn-copy-noi-dung');
      btn.innerHTML = '<i class="bi bi-check-lg text-success" style="font-size:13px"></i>';
      setTimeout(function () {
        btn.innerHTML = '<i class="bi bi-clipboard" style="font-size:13px"></i>';
      }, 2000);
    }).catch(function () {
      // Fallback cho trình duyệt cũ
      const el = document.createElement('textarea');
      el.value = text;
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
    });
  });
})();
</script>
@endpush
