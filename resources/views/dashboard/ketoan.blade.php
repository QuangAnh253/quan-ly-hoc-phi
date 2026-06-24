@extends('layouts.app')
@section('title','Dashboard — Kế toán')
@section('page-title','Dashboard kế toán')

@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#d1e7dd"><i class="bi bi-cash-coin text-success"></i></div>
      <div>
        <div class="stat-label">Thu trong tháng</div>
        <div class="stat-value text-success" style="font-size:16px">{{ number_format($thongKe['tong_thu_thang_nay'],0,',','.') }}đ</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f8d7da"><i class="bi bi-wallet2 text-danger"></i></div>
      <div>
        <div class="stat-label">Tổng công nợ</div>
        <div class="stat-value text-danger" style="font-size:16px">{{ number_format($thongKe['tong_con_no'],0,',','.') }}đ</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff3cd"><i class="bi bi-exclamation-triangle text-warning"></i></div>
      <div>
        <div class="stat-label">SV còn nợ</div>
        <div class="stat-value text-warning">{{ number_format($thongKe['tong_sv_con_no']) }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e7f1ff"><i class="bi bi-calendar-check text-primary"></i></div>
      <div>
        <div class="stat-label">Đợt đang mở</div>
        <div class="stat-value text-primary">{{ $thongKe['dot_thu_dang_mo'] }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><i class="bi bi-lightning me-2 text-warning"></i>Thao tác nhanh</div>
      <div class="card-body d-flex flex-column gap-2">
        <a href="{{ route('ketoan.thanh-toan.create') }}" class="btn btn-success">
          <i class="bi bi-credit-card me-2"></i>Thu học phí sinh viên
        </a>
        <a href="{{ route('ketoan.sinh-vien.index') }}?con_no=1" class="btn btn-outline-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>Xem danh sách SV còn nợ
        </a>
        <a href="{{ route('ketoan.sinh-vien.index') }}" class="btn btn-outline-primary">
          <i class="bi bi-people me-2"></i>Tìm kiếm sinh viên
        </a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><i class="bi bi-calendar-event me-2 text-primary"></i>Đợt thu đang mở</div>
      <div class="card-body p-0">
        @forelse(\App\Models\DotThu::where('trang_thai','dang_thu')->latest()->take(3)->get() as $dot)
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
          <div>
            <div class="fw-500">{{ $dot->ten_dot }}</div>
            <small class="text-muted">Hạn: {{ $dot->han_dong->format('d/m/Y') }}</small>
            @if($dot->isQuaHan())<small class="text-danger ms-2">Quá hạn {{ $dot->so_ngay_qua_han }} ngày</small>@endif
          </div>
          <a href="{{ route('ketoan.thanh-toan.cong-no',$dot) }}" class="btn btn-outline-primary btn-sm">
            Xem nợ
          </a>
        </div>
        @empty
        <div class="text-center text-muted py-4">Không có đợt thu nào đang mở</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
