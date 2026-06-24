@extends('layouts.app')
@section('title','Công nợ đợt thu')
@section('page-title','Danh sách công nợ')
@section('topbar-actions')
  <a href="{{ route('ketoan.bao-cao.xuat-excel', $dotThu) }}"
     class="btn btn-success btn-sm me-2">
    <i class="bi bi-file-earmark-excel me-1"></i>Xuất Excel
  </a>
  <a href="{{ route('ketoan.thanh-toan.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-credit-card me-1"></i>Thu học phí
  </a>
@endsection

@section('content')

{{-- Thông tin đợt thu --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <div class="row g-2 align-items-center">
      <div class="col-md-5">
        <div class="fw-600 fs-6">{{ $dotThu->ten_dot }}</div>
        <small class="text-muted">
          HK{{ $dotThu->hoc_ky }} — {{ $dotThu->nam_hoc }} |
          Hạn đóng: <span class="{{ $dotThu->isQuaHan() ? 'text-danger fw-500' : '' }}">
            {{ $dotThu->han_dong->format('d/m/Y') }}
          </span>
          @if($dotThu->isQuaHan())
            <span class="badge bg-danger ms-1">Quá {{ $dotThu->so_ngay_qua_han }} ngày</span>
          @endif
        </small>
      </div>
      <div class="col-md-7">
        <div class="row g-2 text-center">
          <div class="col-4">
            <div class="text-muted" style="font-size:11px">Tổng phải thu</div>
            <div class="fw-600" style="font-size:15px">
              {{ number_format($congNo['tong_phai_thu'], 0, ',', '.') }}đ
            </div>
          </div>
          <div class="col-4">
            <div class="text-muted" style="font-size:11px">Đã thu</div>
            <div class="fw-600 text-success" style="font-size:15px">
              {{ number_format($congNo['tong_da_thu'], 0, ',', '.') }}đ
            </div>
          </div>
          <div class="col-4">
            <div class="text-muted" style="font-size:11px">Còn nợ</div>
            <div class="fw-600 text-danger" style="font-size:15px">
              {{ number_format($congNo['tong_con_no'], 0, ',', '.') }}đ
            </div>
          </div>
        </div>
        <div class="progress mt-2" style="height:6px;border-radius:3px">
          <div class="progress-bar bg-success"
               style="width:{{ $congNo['ty_le_thu'] }}%"></div>
        </div>
        <div class="text-end mt-1" style="font-size:11px;color:#6c757d">
          Tỷ lệ thu: <strong>{{ $congNo['ty_le_thu'] }}%</strong>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Danh sách SV còn nợ --}}
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span>
      <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
      Sinh viên chưa đóng đủ
      <span class="badge bg-warning text-dark ms-1">
        {{ $congNo['danh_sach_con_no']->count() }}
      </span>
    </span>
    <div class="d-flex gap-2">
      <input type="text" id="search-sv" class="form-control form-control-sm"
             placeholder="Tìm mã SV, họ tên..." style="width:220px"
             oninput="filterTable(this.value)">
    </div>
  </div>
  <div class="card-body p-0">
    <table class="table mb-0" id="cong-no-table">
      <thead>
        <tr>
          <th>Mã SV</th>
          <th>Họ tên</th>
          <th>Khoa / Lớp</th>
          <th class="text-end">Phải đóng</th>
          <th class="text-end">Đã đóng</th>
          <th class="text-end">Còn nợ</th>
          <th class="text-end">Phí phạt</th>
          <th>Trạng thái</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($congNo['danh_sach_con_no'] as $hp)
        <tr>
          <td class="fw-600 text-primary" style="font-size:12px">
            {{ $hp->sinhVien->ma_sv }}
          </td>
          <td>{{ $hp->sinhVien->ho_ten }}</td>
          <td>
            <div style="font-size:12px">{{ $hp->sinhVien->khoa->ten_khoa }}</div>
            <small class="text-muted">{{ $hp->sinhVien->lop }}</small>
          </td>
          <td class="text-end">
            {{ number_format($hp->tong_phai_dong, 0, ',', '.') }}đ
          </td>
          <td class="text-end text-success">
            {{ number_format($hp->da_dong, 0, ',', '.') }}đ
          </td>
          <td class="text-end fw-600 text-danger">
            {{ number_format($hp->tong_phai_dong - $hp->da_dong, 0, ',', '.') }}đ
          </td>
          <td class="text-end text-warning">
            @if($hp->phi_phat > 0)
              {{ number_format($hp->phi_phat, 0, ',', '.') }}đ
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            @if($hp->trang_thai === 'chua_dong')
              <span class="badge bg-danger">Chưa đóng</span>
            @elseif($hp->trang_thai === 'dong_mot_phan')
              <span class="badge bg-warning text-dark">Đóng 1 phần</span>
            @endif
          </td>
          <td>
            <a href="{{ route('ketoan.thanh-toan.create') }}?ma_sv={{ $hp->sinhVien->ma_sv }}"
               class="btn btn-primary btn-sm">
              <i class="bi bi-credit-card"></i> Thu
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="text-center py-5">
            <i class="bi bi-check-circle-fill text-success" style="font-size:36px"></i>
            <p class="mt-2 mb-0 text-muted">Tất cả sinh viên đã đóng đủ học phí!</p>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
function filterTable(keyword) {
  const kw = keyword.toLowerCase();
  document.querySelectorAll('#cong-no-table tbody tr').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(kw) ? '' : 'none';
  });
}
</script>
@endpush
