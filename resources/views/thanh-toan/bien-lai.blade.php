@extends('layouts.app')
@section('title','Biên lai — '.$thanh_toan->ma_giao_dich)
@section('page-title','Biên lai thanh toán')
@section('topbar-actions')
  <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-printer me-1"></i>In biên lai
  </button>
  <a href="{{ route('ketoan.thanh-toan.create') }}" class="btn btn-primary btn-sm ms-2">
    <i class="bi bi-plus-lg me-1"></i>Thu tiếp
  </a>
@endsection

@push('styles')
<style>
@media print {
  .sidebar,.topbar,.btn { display:none!important }
  .main { margin-left:0!important }
  .content { padding:0!important }
  .no-print { display:none!important }
}
.bien-lai { max-width:580px; margin:0 auto; background:#fff; border:1px solid #e9ecef; border-radius:12px; overflow:hidden }
.bien-lai-header { background:linear-gradient(135deg,#0d6efd,#0a58ca); color:#fff; padding:24px; text-align:center }
.bien-lai-body { padding:24px }
.info-row { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px dashed #e9ecef; font-size:14px }
.info-row:last-child { border-bottom:none }
.info-label { color:#6c757d }
.info-value { font-weight:500; text-align:right }
.total-row { background:#f8f9fa; border-radius:8px; padding:16px; text-align:center; margin:16px 0 }
.qr-placeholder { width:80px; height:80px; background:#e9ecef; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:10px; color:#6c757d; margin:0 auto }
</style>
@endpush

@section('content')
<div class="bien-lai">
  <div class="bien-lai-header">
    <div style="font-size:13px;opacity:.8">BIÊN LAI THU HỌC PHÍ</div>
    <div style="font-size:22px;font-weight:700;letter-spacing:.04em;margin:4px 0">{{ $thanh_toan->ma_giao_dich }}</div>
    <div style="font-size:13px;opacity:.8">{{ $thanh_toan->thoi_gian_thu->format('H:i — d/m/Y') }}</div>
  </div>
  <div class="bien-lai-body">
    <div class="total-row">
      <div style="font-size:12px;color:#6c757d;margin-bottom:4px">SỐ TIỀN ĐÃ THU</div>
      <div style="font-size:32px;font-weight:700;color:#198754">
        {{ number_format($thanh_toan->so_tien,0,',','.') }}đ
      </div>
      <div style="font-size:13px;color:#6c757d;margin-top:4px">
        {{ ['tien_mat'=>'Tiền mặt','chuyen_khoan'=>'Chuyển khoản','the_ngan_hang'=>'Thẻ ngân hàng','vi_dien_tu'=>'Ví điện tử'][$thanh_toan->hinh_thuc]??$thanh_toan->hinh_thuc }}
        @if($thanh_toan->ngan_hang) — {{ $thanh_toan->ngan_hang }} @endif
      </div>
    </div>

    <div class="mb-3">
      <div style="font-size:11px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:#6c757d;margin-bottom:8px">Thông tin sinh viên</div>
      <div class="info-row"><span class="info-label">Họ tên</span><span class="info-value">{{ $sinh_vien->ho_ten }}</span></div>
      <div class="info-row"><span class="info-label">Mã SV</span><span class="info-value">{{ $sinh_vien->ma_sv }}</span></div>
      <div class="info-row"><span class="info-label">Lớp</span><span class="info-value">{{ $sinh_vien->lop }}</span></div>
      <div class="info-row"><span class="info-label">Khoa</span><span class="info-value">{{ $sinh_vien->khoa->ten_khoa }}</span></div>
    </div>

    <div class="mb-3">
      <div style="font-size:11px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:#6c757d;margin-bottom:8px">Chi tiết học phí</div>
      <div class="info-row"><span class="info-label">Đợt thu</span><span class="info-value">{{ $dot_thu->ten_dot }}</span></div>
      <div class="info-row"><span class="info-label">Học kỳ</span><span class="info-value">HK{{ $dot_thu->hoc_ky }} — {{ $dot_thu->nam_hoc }}</span></div>
      <div class="info-row"><span class="info-label">Tổng phải đóng</span><span class="info-value">{{ number_format($hoc_phi->tong_phai_dong,0,',','.') }}đ</span></div>
      <div class="info-row"><span class="info-label">Đã đóng (cộng dồn)</span><span class="info-value text-success fw-600">{{ number_format($hoc_phi->da_dong,0,',','.') }}đ</span></div>
      <div class="info-row">
        <span class="info-label">Còn nợ sau giao dịch</span>
        <span class="info-value {{ $hoc_phi->con_no>0?'text-danger':'text-success' }} fw-600">
          {{ number_format($hoc_phi->con_no,0,',','.') }}đ
          @if($hoc_phi->con_no==0) ✓ @endif
        </span>
      </div>
    </div>

    @if($thanh_toan->so_tham_chieu)
    <div class="info-row"><span class="info-label">Số tham chiếu</span><span class="info-value text-muted">{{ $thanh_toan->so_tham_chieu }}</span></div>
    @endif
    @if($thanh_toan->ghi_chu)
    <div class="info-row"><span class="info-label">Ghi chú</span><span class="info-value text-muted">{{ $thanh_toan->ghi_chu }}</span></div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
      <div>
        <div style="font-size:11px;color:#6c757d">Thu bởi</div>
        <div style="font-size:13px;font-weight:500">{{ $thanh_toan->nguoiThu->name }}</div>
      </div>
      <div class="qr-placeholder"><i class="bi bi-qr-code" style="font-size:32px;color:#adb5bd"></i></div>
    </div>

    <div class="text-center mt-3 pt-3 border-top no-print">
      <a href="{{ route('ketoan.sinh-vien.show',$sinh_vien) }}" class="btn btn-outline-secondary btn-sm me-2">
        <i class="bi bi-person me-1"></i>Hồ sơ SV
      </a>
      <a href="{{ route('ketoan.thanh-toan.create') }}?ma_sv={{ $sinh_vien->ma_sv }}" class="btn btn-primary btn-sm">
        <i class="bi bi-credit-card me-1"></i>Thu tiếp
      </a>
    </div>
  </div>
</div>
@endsection
