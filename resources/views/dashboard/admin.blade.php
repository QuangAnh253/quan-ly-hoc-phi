@extends('layouts.app')
@section('title','Dashboard — Admin')
@section('page-title','Dashboard')

@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e7f1ff"><i class="bi bi-people-fill text-primary"></i></div>
      <div>
        <div class="stat-label">Tổng sinh viên</div>
        <div class="stat-value">{{ number_format($thongKe['tong_sv']) }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff3cd"><i class="bi bi-exclamation-triangle-fill text-warning"></i></div>
      <div>
        <div class="stat-label">SV còn nợ học phí</div>
        <div class="stat-value text-warning">{{ number_format($thongKe['tong_sv_con_no']) }}</div>
      </div>
    </div>
  </div>
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
</div>

<div class="row g-3">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-calendar-event me-2 text-primary"></i>Đợt thu học phí gần đây</span>
        <a href="{{ route('admin.dot-thu.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Tạo đợt mới
        </a>
      </div>
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead><tr>
            <th>Tên đợt</th><th>Học kỳ</th><th>Hạn đóng</th><th>Trạng thái</th><th></th>
          </tr></thead>
          <tbody>
            @forelse(\App\Models\DotThu::latest()->take(5)->get() as $dot)
            <tr>
              <td class="fw-500">{{ $dot->ten_dot }}</td>
              <td>HK{{ $dot->hoc_ky }} — {{ $dot->nam_hoc }}</td>
              <td>{{ $dot->han_dong->format('d/m/Y') }}</td>
              <td>
                @php $colors=['sap_mo'=>'secondary','dang_thu'=>'success','da_dong'=>'dark'] @endphp
                @php $labels=['sap_mo'=>'Sắp mở','dang_thu'=>'Đang thu','da_dong'=>'Đã đóng'] @endphp
                <span class="badge bg-{{ $colors[$dot->trang_thai]??'secondary' }}">{{ $labels[$dot->trang_thai]??$dot->trang_thai }}</span>
              </td>
              <td><a href="{{ route('admin.dot-thu.show',$dot) }}" class="btn btn-outline-primary btn-sm">Chi tiết</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Chưa có đợt thu nào</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Thống kê nhanh</div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <span class="text-muted">Đợt thu đang mở</span>
          <span class="badge bg-success fs-6">{{ $thongKe['dot_thu_dang_mo'] }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <span class="text-muted">SV đã đóng đủ</span>
          <span class="fw-600 text-success">{{ number_format($thongKe['tong_sv'] - $thongKe['tong_sv_con_no']) }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-2">
          <span class="text-muted">Tỷ lệ thu</span>
          @php
            $tyle = $thongKe['tong_sv'] > 0
              ? round(($thongKe['tong_sv'] - $thongKe['tong_sv_con_no']) / $thongKe['tong_sv'] * 100, 1)
              : 0;
          @endphp
          <span class="fw-600 {{ $tyle >= 80 ? 'text-success' : 'text-warning' }}">{{ $tyle }}%</span>
        </div>
        <div class="mt-3">
          <div class="progress" style="height:8px;border-radius:4px">
            <div class="progress-bar bg-success" style="width:{{ $tyle }}%"></div>
          </div>
        </div>
        <div class="mt-3">
          <a href="{{ route('admin.bao-cao.nam-hoc') }}" class="btn btn-outline-primary w-100 btn-sm">
            <i class="bi bi-bar-chart-line me-1"></i>Xem báo cáo chi tiết
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
