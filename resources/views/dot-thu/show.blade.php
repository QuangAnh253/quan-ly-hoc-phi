@extends('layouts.app')
@section('title','Chi tiết đợt thu')
@section('page-title','Chi tiết đợt thu')
@section('topbar-actions')
  @if($dotThu->trang_thai==='sap_mo')
    <form method="POST" action="{{ route('admin.dot-thu.mo',$dotThu) }}" onsubmit="return confirm('Mở đợt và gửi email thông báo cho toàn bộ SV?')" class="d-inline">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-play-fill me-1"></i>Mở đợt thu</button>
    </form>
  @elseif($dotThu->trang_thai==='dang_thu')
    <a href="{{ route('ketoan.bao-cao.xuat-excel',$dotThu) }}" class="btn btn-outline-success btn-sm me-2">
      <i class="bi bi-file-earmark-excel me-1"></i>Xuất Excel
    </a>
    <form method="POST" action="{{ route('admin.dot-thu.nhac-no',$dotThu) }}" onsubmit="return confirm('Gửi email nhắc nợ đến tất cả SV chưa đóng đủ học phí?')" class="d-inline me-2">
      @csrf
      <button type="submit" class="btn btn-outline-warning btn-sm text-dark"><i class="bi bi-bell me-1"></i>Nhắc nợ</button>
    </form>
    <form method="POST" action="{{ route('admin.dot-thu.dong',$dotThu) }}" onsubmit="return confirm('Đóng đợt thu này?')" class="d-inline">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-warning btn-sm text-dark"><i class="bi bi-stop-fill me-1"></i>Đóng đợt</button>
    </form>
  @endif
@endsection

@section('content')
{{-- Thông tin đợt --}}
<div class="row g-3 mb-4">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin đợt thu</div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-6"><span class="text-muted">Tên đợt:</span> <strong>{{ $dotThu->ten_dot }}</strong></div>
          <div class="col-md-3"><span class="text-muted">Học kỳ:</span> HK{{ $dotThu->hoc_ky }} — {{ $dotThu->nam_hoc }}</div>
          <div class="col-md-3"><span class="text-muted">Đơn giá TC:</span> <strong>{{ number_format($dotThu->don_gia_tin_chi,0,',','.') }}đ</strong></div>
          <div class="col-md-3"><span class="text-muted">Ngày bắt đầu:</span> {{ $dotThu->ngay_bat_dau->format('d/m/Y') }}</div>
          <div class="col-md-3"><span class="text-muted">Hạn đóng:</span>
            <span class="{{ $dotThu->isQuaHan()?'text-danger fw-500':'' }}">{{ $dotThu->han_dong->format('d/m/Y') }}</span>
          </div>
          <div class="col-md-3"><span class="text-muted">Phí phạt:</span> {{ number_format($dotThu->phi_phat_ngay,0,',','.') }}đ/ngày</div>
          <div class="col-md-3"><span class="text-muted">Trạng thái:</span>
            @php $sc=['sap_mo'=>['secondary','Sắp mở'],'dang_thu'=>['success','Đang thu'],'da_dong'=>['dark','Đã đóng']] @endphp
            @php [$c,$l]=$sc[$dotThu->trang_thai]??['secondary','—'] @endphp
            <span class="badge bg-{{ $c }}">{{ $l }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-bar-chart me-2 text-primary"></i>Tỷ lệ thu</div>
      <div class="card-body d-flex flex-column justify-content-center">
        @php $ty=$baoCao['ty_le_tong'] @endphp
        <div class="text-center mb-2">
          <span style="font-size:36px;font-weight:700;color:{{ $ty>=80?'#198754':($ty>=50?'#fd7e14':'#dc3545') }}">{{ $ty }}%</span>
        </div>
        <div class="progress mb-3" style="height:10px;border-radius:5px">
          <div class="progress-bar {{ $ty>=80?'bg-success':($ty>=50?'bg-warning':'bg-danger') }}" style="width:{{ $ty }}%"></div>
        </div>
        <div class="d-flex justify-content-between text-muted" style="font-size:12px">
          <span>Đã thu: <strong class="text-success">{{ number_format($baoCao['tong_quan']->tong_da_thu??0,0,',','.') }}đ</strong></span>
          <span>Còn nợ: <strong class="text-danger">{{ number_format($baoCao['tong_quan']->tong_con_no??0,0,',','.') }}đ</strong></span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Breakdown theo khoa --}}
<div class="card mb-4">
  <div class="card-header"><i class="bi bi-building me-2 text-primary"></i>Tình hình thu theo khoa</div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead><tr>
        <th>Khoa</th><th>Số SV</th><th class="text-end">Phải thu</th>
        <th class="text-end">Đã thu</th><th class="text-end">Còn nợ</th><th>Tỷ lệ</th>
      </tr></thead>
      <tbody>
        @forelse($baoCao['theo_khoa'] as $row)
        <tr>
          <td class="fw-500">{{ $row->ten_khoa }}</td>
          <td>{{ $row->tong_sv }}</td>
          <td class="text-end">{{ number_format($row->phai_thu,0,',','.') }}đ</td>
          <td class="text-end text-success">{{ number_format($row->da_thu,0,',','.') }}đ</td>
          <td class="text-end text-danger">{{ number_format($row->con_no,0,',','.') }}đ</td>
          <td style="min-width:120px">
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-fill" style="height:6px;border-radius:3px">
                <div class="progress-bar {{ $row->ty_le_thu>=80?'bg-success':($row->ty_le_thu>=50?'bg-warning':'bg-danger') }}"
                     style="width:{{ $row->ty_le_thu }}%"></div>
              </div>
              <span style="font-size:12px;min-width:36px">{{ $row->ty_le_thu }}%</span>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
