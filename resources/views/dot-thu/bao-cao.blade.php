@extends('layouts.app')
@section('title','Báo cáo đợt thu')
@section('page-title','Báo cáo đợt thu')
@section('topbar-actions')
  <a href="{{ route('ketoan.bao-cao.xuat-excel', $dotThu) }}"
     class="btn btn-success btn-sm me-2">
    <i class="bi bi-file-earmark-excel me-1"></i>Xuất Excel
  </a>
  <a href="{{ route('ketoan.thanh-toan.cong-no', $dotThu) }}"
     class="btn btn-outline-warning btn-sm">
    <i class="bi bi-exclamation-triangle me-1"></i>Xem công nợ
  </a>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- Header đợt thu --}}
<div class="card mb-4">
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h5 class="fw-700 mb-1">{{ $dotThu->ten_dot }}</h5>
        <div class="text-muted" style="font-size:13px">
          Học kỳ {{ $dotThu->hoc_ky }} — Năm học {{ $dotThu->nam_hoc }} |
          Hạn đóng: {{ $dotThu->han_dong->format('d/m/Y') }}
          @if($dotThu->isQuaHan())
            <span class="badge bg-danger ms-1">Quá {{ $dotThu->so_ngay_qua_han }} ngày</span>
          @endif
        </div>
      </div>
      <div class="col-md-6 text-md-end mt-2 mt-md-0">
        @php
          $sc = ['sap_mo'=>['secondary','Sắp mở'],'dang_thu'=>['success','Đang thu'],'da_dong'=>['dark','Đã đóng']];
          [$c,$l] = $sc[$dotThu->trang_thai] ?? ['secondary','—'];
        @endphp
        <span class="badge bg-{{ $c }} fs-6 px-3 py-2">{{ $l }}</span>
      </div>
    </div>
  </div>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e7f1ff">
        <i class="bi bi-people-fill text-primary"></i>
      </div>
      <div>
        <div class="stat-label">Tổng sinh viên</div>
        <div class="stat-value">{{ number_format($baoCao['tong_quan']->tong_sv ?? 0) }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#d1e7dd">
        <i class="bi bi-check-circle-fill text-success"></i>
      </div>
      <div>
        <div class="stat-label">Đã đóng đủ</div>
        <div class="stat-value text-success">
          {{ number_format($baoCao['tong_quan']->sv_da_dong ?? 0) }}
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff3cd">
        <i class="bi bi-hourglass-split text-warning"></i>
      </div>
      <div>
        <div class="stat-label">Đóng 1 phần</div>
        <div class="stat-value text-warning">
          {{ number_format($baoCao['tong_quan']->sv_dong_mot_phan ?? 0) }}
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f8d7da">
        <i class="bi bi-x-circle-fill text-danger"></i>
      </div>
      <div>
        <div class="stat-label">Chưa đóng</div>
        <div class="stat-value text-danger">
          {{ number_format($baoCao['tong_quan']->sv_chua_dong ?? 0) }}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Biểu đồ + tổng tiền --}}
<div class="row g-3 mb-4">
  <div class="col-md-5">
    <div class="card h-100">
      <div class="card-header">
        <i class="bi bi-pie-chart me-2 text-primary"></i>Tỷ lệ thu học phí
      </div>
      <div class="card-body d-flex flex-column align-items-center justify-content-center">
        <canvas id="pieChart" style="max-height:200px"></canvas>
        <div class="mt-3 text-center">
          <div style="font-size:32px;font-weight:700;
            color:{{ $baoCao['ty_le_tong'] >= 80 ? '#198754' : ($baoCao['ty_le_tong'] >= 50 ? '#fd7e14' : '#dc3545') }}">
            {{ $baoCao['ty_le_tong'] }}%
          </div>
          <div class="text-muted" style="font-size:12px">Tỷ lệ thu đợt này</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card h-100">
      <div class="card-header">
        <i class="bi bi-cash-stack me-2 text-primary"></i>Tổng hợp tiền
      </div>
      <div class="card-body">
        @php
          $tongQuan = $baoCao['tong_quan'];
        @endphp
        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
          <span class="text-muted">Tổng phải thu</span>
          <span class="fw-600 fs-6">
            {{ number_format($tongQuan->tong_phai_thu ?? 0, 0, ',', '.') }}đ
          </span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
          <span class="text-muted">Đã thu được</span>
          <span class="fw-600 fs-6 text-success">
            {{ number_format($tongQuan->tong_da_thu ?? 0, 0, ',', '.') }}đ
          </span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
          <span class="text-muted">Còn tồn đọng</span>
          <span class="fw-600 fs-6 text-danger">
            {{ number_format($tongQuan->tong_con_no ?? 0, 0, ',', '.') }}đ
          </span>
        </div>
        <div class="d-flex justify-content-between align-items-center py-3">
          <span class="text-muted">SV miễn hoàn toàn</span>
          <span class="fw-600 fs-6 text-info">
            {{ number_format($tongQuan->sv_mien ?? 0) }} SV
          </span>
        </div>
        <div class="progress mt-2" style="height:10px;border-radius:5px">
          <div class="progress-bar
            {{ $baoCao['ty_le_tong'] >= 80 ? 'bg-success' : ($baoCao['ty_le_tong'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
            style="width:{{ $baoCao['ty_le_tong'] }}%">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Breakdown theo khoa --}}
<div class="card mb-4">
  <div class="card-header">
    <i class="bi bi-building me-2 text-primary"></i>Thu theo khoa
  </div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>Khoa</th>
          <th class="text-center">SV</th>
          <th class="text-end">Phải thu</th>
          <th class="text-end">Đã thu</th>
          <th class="text-end">Còn nợ</th>
          <th style="min-width:160px">Tỷ lệ</th>
        </tr>
      </thead>
      <tbody>
        @forelse($baoCao['theo_khoa'] as $row)
        <tr>
          <td>
            <div class="fw-500">{{ $row->ten_khoa }}</div>
            <small class="text-muted">{{ $row->ma_khoa }}</small>
          </td>
          <td class="text-center">{{ $row->tong_sv }}</td>
          <td class="text-end">{{ number_format($row->phai_thu, 0, ',', '.') }}đ</td>
          <td class="text-end text-success fw-500">{{ number_format($row->da_thu, 0, ',', '.') }}đ</td>
          <td class="text-end text-danger">{{ number_format($row->con_no, 0, ',', '.') }}đ</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-fill" style="height:7px;border-radius:4px">
                <div class="progress-bar
                  {{ $row->ty_le_thu >= 80 ? 'bg-success' : ($row->ty_le_thu >= 50 ? 'bg-warning' : 'bg-danger') }}"
                  style="width:{{ $row->ty_le_thu }}%">
                </div>
              </div>
              <span style="font-size:12px;min-width:38px;text-align:right">
                {{ $row->ty_le_thu }}%
              </span>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Danh sách SV quá hạn --}}
@if($quaHan->count())
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>
      <i class="bi bi-alarm me-2 text-danger"></i>
      Sinh viên quá hạn chưa đóng
      <span class="badge bg-danger ms-1">{{ $quaHan->count() }}</span>
    </span>
  </div>
  <div class="card-body p-0">
    <table class="table mb-0" style="font-size:13px">
      <thead>
        <tr>
          <th>Mã SV</th><th>Họ tên</th><th>Khoa</th>
          <th class="text-end">Còn nợ</th>
          <th class="text-end">Phí phạt</th>
          <th>Ngày quá hạn</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($quaHan->take(10) as $row)
        <tr>
          <td class="fw-600 text-primary">{{ $row['ma_sv'] }}</td>
          <td>{{ $row['ho_ten'] }}</td>
          <td>{{ $row['khoa'] }}</td>
          <td class="text-end text-danger fw-500">
            {{ number_format($row['con_no'], 0, ',', '.') }}đ
          </td>
          <td class="text-end text-warning">
            {{ number_format($row['phi_phat'], 0, ',', '.') }}đ
          </td>
          <td>
            <span class="badge bg-danger">{{ $row['so_ngay_qua_han'] }} ngày</span>
          </td>
          <td>
            <a href="{{ route('ketoan.thanh-toan.create') }}?ma_sv={{ $row['ma_sv'] }}"
               class="btn btn-primary btn-sm">Thu</a>
          </td>
        </tr>
        @endforeach
        @if($quaHan->count() > 10)
        <tr>
          <td colspan="7" class="text-center text-muted py-2" style="font-size:12px">
            ... và {{ $quaHan->count() - 10 }} sinh viên khác.
            <a href="{{ route('ketoan.bao-cao.xuat-excel', $dotThu) }}">Xuất Excel để xem đầy đủ</a>
          </td>
        </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
@php
  $tongQuan = $baoCao['tong_quan'];
  $daDong   = $tongQuan->sv_da_dong ?? 0;
  $motPhan  = $tongQuan->sv_dong_mot_phan ?? 0;
  $chuaDong = $tongQuan->sv_chua_dong ?? 0;
  $mien     = $tongQuan->sv_mien ?? 0;
@endphp
new Chart(document.getElementById('pieChart'), {
  type: 'doughnut',
  data: {
    labels: ['Đã đóng đủ','Đóng 1 phần','Chưa đóng','Miễn hoàn toàn'],
    datasets: [{
      data: [{{ $daDong }}, {{ $motPhan }}, {{ $chuaDong }}, {{ $mien }}],
      backgroundColor: ['#198754','#fd7e14','#dc3545','#0dcaf0'],
      borderWidth: 0,
    }]
  },
  options: {
    cutout: '65%',
    plugins: {
      legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } }
    }
  }
});
</script>
@endpush
