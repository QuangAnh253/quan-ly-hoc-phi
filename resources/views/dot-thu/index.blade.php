@extends('layouts.app')
@section('title','Đợt thu học phí')
@section('page-title','Quản lý đợt thu')
@section('topbar-actions')
  <a href="{{ route('admin.dot-thu.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-lg me-1"></i>Tạo đợt mới
  </a>
@endsection

@section('content')
<div class="card">
  <div class="card-header"><i class="bi bi-calendar-event me-2 text-primary"></i>Danh sách đợt thu ({{ $dotThus->total() }})</div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead><tr>
        <th>Tên đợt</th><th>Học kỳ</th><th>Đơn giá TC</th>
        <th>Hạn đóng</th><th>Phí phạt/ngày</th><th>Trạng thái</th><th></th>
      </tr></thead>
      <tbody>
        @forelse($dotThus as $dot)
        <tr>
          <td>
            <div class="fw-600">{{ $dot->ten_dot }}</div>
            <small class="text-muted">Tạo bởi {{ $dot->createdBy->name }}</small>
          </td>
          <td>HK{{ $dot->hoc_ky }} — {{ $dot->nam_hoc }}</td>
          <td>{{ number_format($dot->don_gia_tin_chi,0,',','.') }}đ</td>
          <td class="{{ $dot->isQuaHan() && $dot->trang_thai==='dang_thu' ? 'text-danger fw-500' : '' }}">
            {{ $dot->han_dong->format('d/m/Y') }}
            @if($dot->isQuaHan() && $dot->trang_thai==='dang_thu')
              <br><small>Quá {{ $dot->so_ngay_qua_han }} ngày</small>
            @endif
          </td>
          <td>{{ number_format($dot->phi_phat_ngay,0,',','.') }}đ</td>
          <td>
            @php $sc=['sap_mo'=>['secondary','Sắp mở'],'dang_thu'=>['success','Đang thu'],'da_dong'=>['dark','Đã đóng']] @endphp
            @php [$c,$l]=$sc[$dot->trang_thai]??['secondary','—'] @endphp
            <span class="badge bg-{{ $c }}">{{ $l }}</span>
          </td>
          <td class="d-flex gap-1">
            <a href="{{ route('admin.dot-thu.show',$dot) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
            @if($dot->trang_thai==='sap_mo')
              <form method="POST" action="{{ route('admin.dot-thu.mo',$dot) }}" onsubmit="return confirm('Mở đợt thu và gửi thông báo cho tất cả SV?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-play-fill"></i> Mở</button>
              </form>
            @elseif($dot->trang_thai==='dang_thu')
              <form method="POST" action="{{ route('admin.dot-thu.dong',$dot) }}" onsubmit="return confirm('Đóng đợt thu này?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-warning btn-sm text-dark"><i class="bi bi-stop-fill"></i> Đóng</button>
              </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-5">Chưa có đợt thu nào. <a href="{{ route('admin.dot-thu.create') }}">Tạo ngay</a></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($dotThus->hasPages())
  <div class="card-footer bg-white d-flex justify-content-center py-3">{{ $dotThus->links() }}</div>
  @endif
</div>
@endsection
