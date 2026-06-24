@extends('layouts.app')
@section('title','Báo cáo năm học')
@section('page-title','Báo cáo năm học')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
{{-- Bộ lọc --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="d-flex align-items-end gap-3">
      <div>
        <label class="form-label mb-1">Năm học</label>
        <select name="nam_hoc" class="form-select form-select-sm" onchange="this.form.submit()">
          @foreach($dotThus as $nh)
            <option value="{{ $nh }}" {{ $nh===$namHoc?'selected':'' }}>{{ $nh }}</option>
          @endforeach
        </select>
      </div>
      <span class="text-muted" style="font-size:13px">Năm học: <strong>{{ $namHoc }}</strong></span>
    </form>
  </div>
</div>

@if($baoCao->count())
{{-- Tổng quan --}}
@php
  $tongPhaiThu = $baoCao->sum('tong_phai_thu');
  $tongDaThu   = $baoCao->sum('tong_da_thu');
  $tongConNo   = $tongPhaiThu - $tongDaThu;
  $tyLe        = $tongPhaiThu > 0 ? round($tongDaThu/$tongPhaiThu*100,1) : 0;
@endphp
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e7f1ff"><i class="bi bi-cash-stack text-primary"></i></div>
      <div>
        <div class="stat-label">Tổng phải thu</div>
        <div class="stat-value" style="font-size:16px">{{ number_format($tongPhaiThu,0,',','.') }}đ</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#d1e7dd"><i class="bi bi-check-circle text-success"></i></div>
      <div>
        <div class="stat-label">Đã thu được</div>
        <div class="stat-value text-success" style="font-size:16px">{{ number_format($tongDaThu,0,',','.') }}đ</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f8d7da"><i class="bi bi-exclamation-circle text-danger"></i></div>
      <div>
        <div class="stat-label">Còn tồn đọng</div>
        <div class="stat-value text-danger" style="font-size:16px">{{ number_format($tongConNo,0,',','.') }}đ</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff3cd"><i class="bi bi-percent text-warning"></i></div>
      <div>
        <div class="stat-label">Tỷ lệ thu</div>
        <div class="stat-value {{ $tyLe>=80?'text-success':($tyLe>=50?'text-warning':'text-danger') }}">{{ $tyLe }}%</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  {{-- Biểu đồ --}}
  <div class="col-md-7">
    <div class="card">
      <div class="card-header"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Tình hình thu theo đợt</div>
      <div class="card-body">
        <canvas id="chart" height="200"></canvas>
      </div>
    </div>
  </div>
  {{-- Bảng chi tiết --}}
  <div class="col-md-5">
    <div class="card">
      <div class="card-header"><i class="bi bi-table me-2 text-primary"></i>Chi tiết theo đợt</div>
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead><tr><th>Đợt</th><th class="text-end">Đã thu</th><th>Tỷ lệ</th></tr></thead>
          <tbody>
            @foreach($baoCao as $dot)
            <tr>
              <td>
                <div style="font-size:13px;font-weight:500">HK{{ $dot->hoc_ky }}</div>
                <div style="font-size:11px;color:#6c757d">{{ $dot->ten_dot }}</div>
              </td>
              <td class="text-end text-success fw-500">{{ number_format($dot->tong_da_thu??0,0,',','.') }}đ</td>
              <td>
                <div class="d-flex align-items-center gap-1">
                  <div class="progress flex-fill" style="height:5px;border-radius:3px">
                    <div class="progress-bar {{ ($dot->ty_le??0)>=80?'bg-success':($dot->ty_le>=50?'bg-warning':'bg-danger') }}"
                         style="width:{{ $dot->ty_le??0 }}%"></div>
                  </div>
                  <span style="font-size:11px;min-width:32px">{{ $dot->ty_le??0 }}%</span>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@else
<div class="card"><div class="card-body text-center py-5 text-muted">
  <i class="bi bi-bar-chart" style="font-size:48px"></i>
  <p class="mt-3">Chưa có dữ liệu cho năm học {{ $namHoc }}</p>
</div></div>
@endif
@endsection

@push('scripts')
@if($baoCao->count())
<script>
new Chart(document.getElementById('chart'), {
  type: 'bar',
  data: {
    labels: {!! $baoCao->map(fn($d)=>'"HK'.$d->hoc_ky.'"')->join(',') !!},
    datasets: [
      { label: 'Phải thu', data: [{{ $baoCao->map(fn($d)=>$d->tong_phai_thu??0)->join(',') }}],
        backgroundColor: 'rgba(13,110,253,.15)', borderColor: '#0d6efd', borderWidth: 2, borderRadius: 4 },
      { label: 'Đã thu',   data: [{{ $baoCao->map(fn($d)=>$d->tong_da_thu??0)->join(',') }}],
        backgroundColor: 'rgba(25,135,84,.8)', borderColor: '#198754', borderWidth: 0, borderRadius: 4 },
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top' } },
    scales: { y: { beginAtZero: true, ticks: { callback: v => (v/1000000).toFixed(0)+'tr' } } }
  }
});
</script>
@endif
@endpush
