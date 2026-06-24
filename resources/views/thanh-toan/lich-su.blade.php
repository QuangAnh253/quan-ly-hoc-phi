@extends('layouts.app')
@section('title','Lịch sử thanh toán')
@section('page-title','Lịch sử thanh toán')

@section('content')
@php
  $userRole = auth()->user()->role ?? null;
  $backUrl = $userRole === 'sinhvien'
      ? route('sinhvien.dashboard')
      : route('ketoan.sinh-vien.show', $sinhVien);
@endphp

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-clock-history me-2 text-primary"></i>
      Lịch sử giao dịch — {{ $sinhVien->ho_ten }} ({{ $sinhVien->ma_sv }})
    </span>
    <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
  </div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead><tr>
        <th>Mã GD</th><th>Đợt thu</th><th>Thời gian</th>
        <th class="text-end">Số tiền</th><th>Hình thức</th><th>Thu bởi</th><th></th>
      </tr></thead>
      <tbody>
        @forelse($lichSu as $tt)
        <tr>
          <td class="fw-600 text-primary font-monospace" style="font-size:12px">{{ $tt->ma_giao_dich }}</td>
          <td>{{ $tt->hocPhi->dotThu->ten_dot }}</td>
          <td>{{ $tt->thoi_gian_thu->format('H:i d/m/Y') }}</td>
          <td class="text-end fw-600 text-success">+{{ number_format($tt->so_tien,0,',','.') }}đ</td>
          <td>
            @php $icons=['tien_mat'=>'💵','chuyen_khoan'=>'🏦','the_ngan_hang'=>'💳','vi_dien_tu'=>'📱'] @endphp
            @php $labels=['tien_mat'=>'Tiền mặt','chuyen_khoan'=>'Chuyển khoản','the_ngan_hang'=>'Thẻ NH','vi_dien_tu'=>'Ví điện tử'] @endphp
            {{ $icons[$tt->hinh_thuc]??'' }} {{ $labels[$tt->hinh_thuc]??$tt->hinh_thuc }}
          </td>
          <td class="text-muted">{{ $tt->nguoiThu->name }}</td>
          <td>
            @if($userRole !== 'sinhvien')
              <a href="{{ route('ketoan.thanh-toan.bien-lai',$tt->ma_giao_dich) }}"
                 class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-receipt"></i>
              </a>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-5">Chưa có giao dịch nào</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
