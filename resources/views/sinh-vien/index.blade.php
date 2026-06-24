@extends('layouts.app')
@section('title','Danh sách sinh viên')
@section('page-title','Sinh viên')
@section('topbar-actions')
  <a href="{{ route('ketoan.sinh-vien.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg me-1"></i>Thêm sinh viên
  </a>
@endsection

@section('content')
{{-- Bộ lọc --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label mb-1">Tìm kiếm</label>
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Mã SV hoặc họ tên..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label mb-1">Khoa</label>
        <select name="khoa_id" class="form-select form-select-sm">
          <option value="">-- Tất cả khoa --</option>
          @foreach($khoas as $k)
            <option value="{{ $k->id }}" {{ request('khoa_id')==$k->id?'selected':'' }}>{{ $k->ten_khoa }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1">Lọc</label>
        <select name="con_no" class="form-select form-select-sm">
          <option value="">Tất cả</option>
          <option value="1" {{ request('con_no')?'selected':'' }}>Còn nợ HP</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search me-1"></i>Tìm</button>
        <a href="{{ route('ketoan.sinh-vien.index') }}" class="btn btn-outline-secondary btn-sm">Xóa lọc</a>
      </div>
    </form>
  </div>
</div>

{{-- Bảng --}}
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-people me-2 text-primary"></i>Danh sách sinh viên ({{ $sinhViens->total() }})</span>
  </div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead><tr>
        <th>Mã SV</th><th>Họ tên</th><th>Khoa</th><th>Lớp</th>
        <th>Diện miễn giảm</th><th>Trạng thái HP</th><th></th>
      </tr></thead>
      <tbody>
        @forelse($sinhViens as $sv)
        <tr>
          <td class="fw-600 text-primary">{{ $sv->ma_sv }}</td>
          <td>{{ $sv->ho_ten }}</td>
          <td>{{ $sv->khoa->ten_khoa }}</td>
          <td>{{ $sv->lop }}</td>
          <td>
            @if($sv->dien_mien_giam !== 'binh_thuong')
              <span class="badge bg-info text-dark">{{ Str::title(str_replace('_',' ',$sv->dien_mien_giam)) }}</span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            @php $no = $sv->hocPhis->where('trang_thai','!=','da_dong_du')->where('trang_thai','!=','mien_hoan_toan')->count() @endphp
            @if($no > 0)
              <span class="badge bg-danger">Còn nợ</span>
            @else
              <span class="badge bg-success">Đã đóng đủ</span>
            @endif
          </td>
          <td>
            <a href="{{ route('ketoan.sinh-vien.show',$sv) }}" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('ketoan.sinh-vien.edit',$sv) }}" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-pencil"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-5">Không tìm thấy sinh viên nào</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($sinhViens->hasPages())
  <div class="card-footer bg-white d-flex justify-content-center py-3">
    {{ $sinhViens->links() }}
  </div>
  @endif
</div>
@endsection
