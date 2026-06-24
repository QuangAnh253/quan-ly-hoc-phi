@extends('layouts.app')
@section('title','Chi tiết — '.$sinhVien->ho_ten)
@section('page-title','Chi tiết sinh viên')
@section('topbar-actions')
  <a href="{{ route('ketoan.sinh-vien.edit',$sinhVien) }}" class="btn btn-outline-secondary btn-sm me-2">
    <i class="bi bi-pencil me-1"></i>Chỉnh sửa
  </a>
  <a href="{{ route('ketoan.thanh-toan.create') }}?ma_sv={{ $sinhVien->ma_sv }}" class="btn btn-primary btn-sm">
    <i class="bi bi-credit-card me-1"></i>Thu học phí
  </a>
@endsection

@section('content')
<div class="row g-3">
  {{-- Thông tin cá nhân --}}
  <div class="col-md-4">
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Thông tin sinh viên</div>
      <div class="card-body">
        <div class="text-center mb-3">
          <div style="width:64px;height:64px;border-radius:50%;background:#e7f1ff;
                      display:flex;align-items:center;justify-content:center;
                      font-size:24px;font-weight:700;color:#0d6efd;margin:0 auto">
            {{ strtoupper(substr($sinhVien->ho_ten,0,1)) }}
          </div>
          <h6 class="mt-2 mb-0 fw-600">{{ $sinhVien->ho_ten }}</h6>
          <small class="text-muted">{{ $sinhVien->ma_sv }}</small>
        </div>
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted">Khoa</td><td class="fw-500">{{ $sinhVien->khoa->ten_khoa }}</td></tr>
          <tr><td class="text-muted">Lớp</td><td>{{ $sinhVien->lop }}</td></tr>
          <tr><td class="text-muted">Niên khóa</td><td>{{ $sinhVien->nien_khoa }}</td></tr>
          <tr><td class="text-muted">Hệ đào tạo</td><td>{{ Str::title(str_replace('_',' ',$sinhVien->he_dao_tao)) }}</td></tr>
          <tr><td class="text-muted">Diện MG</td><td>
            @if($sinhVien->dien_mien_giam !== 'binh_thuong')
              <span class="badge bg-info text-dark">{{ Str::title(str_replace('_',' ',$sinhVien->dien_mien_giam)) }}</span>
            @else <span class="text-muted">Bình thường</span> @endif
          </td></tr>
          <tr><td class="text-muted">Email</td><td>{{ $sinhVien->email ?? '—' }}</td></tr>
          <tr><td class="text-muted">SĐT</td><td>{{ $sinhVien->so_dien_thoai ?? '—' }}</td></tr>
        </table>
      </div>
    </div>

    {{-- Tổng công nợ --}}
    <div class="card" style="border-color:{{ $conNo['tong_con_no']>0?'#dc3545':'#198754' }}">
      <div class="card-body text-center py-4">
        <div class="text-muted mb-1">Tổng công nợ hiện tại</div>
        <div style="font-size:28px;font-weight:700;color:{{ $conNo['tong_con_no']>0?'#dc3545':'#198754' }}">
          {{ number_format($conNo['tong_con_no'],0,',','.') }}đ
        </div>
        @if($conNo['tong_con_no'] == 0)
          <div class="text-success mt-1"><i class="bi bi-check-circle me-1"></i>Đã đóng đầy đủ</div>
        @endif
      </div>
    </div>
  </div>

  {{-- Lịch sử học phí --}}
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><i class="bi bi-receipt me-2 text-primary"></i>Lịch sử học phí</div>
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead><tr>
            <th>Đợt thu</th><th class="text-end">Phải đóng</th>
            <th class="text-end">Đã đóng</th><th class="text-end">Còn nợ</th>
            <th>Trạng thái</th><th></th>
          </tr></thead>
          <tbody>
            @forelse($sinhVien->hocPhis as $hp)
            <tr>
              <td>
                <div class="fw-500">{{ $hp->dotThu->ten_dot }}</div>
                <small class="text-muted">{{ $hp->so_tin_chi }} TC × {{ number_format($hp->don_gia_tin_chi,0,',','.') }}đ</small>
                @if($hp->phan_tram_giam > 0)
                  <br><small class="text-info">Giảm {{ $hp->phan_tram_giam }}%</small>
                @endif
              </td>
              <td class="text-end">{{ number_format($hp->tong_phai_dong,0,',','.') }}đ</td>
              <td class="text-end text-success">{{ number_format($hp->da_dong,0,',','.') }}đ</td>
              <td class="text-end fw-600 {{ $hp->con_no>0?'text-danger':'' }}">{{ number_format($hp->con_no,0,',','.') }}đ</td>
              <td>
                @php $map=['chua_dong'=>['danger','Chưa đóng'],'dong_mot_phan'=>['warning','Một phần'],'da_dong_du'=>['success','Đủ'],'mien_hoan_toan'=>['info','Miễn']] @endphp
                @php [$c,$l] = $map[$hp->trang_thai]??['secondary','—'] @endphp
                <span class="badge bg-{{ $c }} {{ $c==='warning'?'text-dark':'' }}">{{ $l }}</span>
              </td>
              <td>
                @if($hp->thanhToans->count())
                  <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#tt-{{ $hp->id }}">
                    <i class="bi bi-list-ul"></i> {{ $hp->thanhToans->count() }}
                  </button>
                @endif
              </td>
            </tr>
            {{-- Giao dịch con --}}
            @if($hp->thanhToans->count())
            <tr class="collapse" id="tt-{{ $hp->id }}">
              <td colspan="6" class="bg-light py-2 px-4">
                @foreach($hp->thanhToans as $tt)
                <div class="d-flex justify-content-between align-items-center py-1">
                  <span class="text-muted" style="font-size:12px">
                    <i class="bi bi-arrow-right-short"></i>
                    {{ $tt->thoi_gian_thu->format('d/m/Y H:i') }} —
                    {{ ['tien_mat'=>'Tiền mặt','chuyen_khoan'=>'Chuyển khoản','the_ngan_hang'=>'Thẻ NH','vi_dien_tu'=>'Ví điện tử'][$tt->hinh_thuc]??$tt->hinh_thuc }}
                  </span>
                  <span class="fw-600 text-success">+{{ number_format($tt->so_tien,0,',','.') }}đ</span>
                  <a href="{{ route('ketoan.thanh-toan.bien-lai',$tt->ma_giao_dich) }}" class="btn btn-outline-secondary btn-sm py-0" style="font-size:11px">
                    <i class="bi bi-printer"></i> Biên lai
                  </a>
                </div>
                @endforeach
              </td>
            </tr>
            @endif
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu học phí</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
