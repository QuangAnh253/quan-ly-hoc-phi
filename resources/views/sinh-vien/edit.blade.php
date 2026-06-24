@extends('layouts.app')
@section('title','Chỉnh sửa — '.$sinhVien->ho_ten)
@section('page-title','Chỉnh sửa sinh viên')

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
  <div class="card-header"><i class="bi bi-pencil me-2 text-primary"></i>Chỉnh sửa: {{ $sinhVien->ma_sv }} — {{ $sinhVien->ho_ten }}</div>
  <div class="card-body">
  <form method="POST" action="{{ route('ketoan.sinh-vien.update',$sinhVien) }}">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Mã sinh viên</label>
        <input type="text" class="form-control bg-light" value="{{ $sinhVien->ma_sv }}" disabled>
      </div>
      <div class="col-md-8">
        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
        <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten',$sinhVien->ho_ten) }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Khoa <span class="text-danger">*</span></label>
        <select name="khoa_id" class="form-select" required>
          @foreach($khoas as $k)
            <option value="{{ $k->id }}" {{ old('khoa_id',$sinhVien->khoa_id)==$k->id?'selected':'' }}>{{ $k->ten_khoa }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Lớp <span class="text-danger">*</span></label>
        <input type="text" name="lop" class="form-control" value="{{ old('lop',$sinhVien->lop) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Ngày sinh</label>
        <input type="date" name="ngay_sinh" class="form-control" value="{{ old('ngay_sinh',$sinhVien->ngay_sinh?->format('Y-m-d')) }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Giới tính</label>
        <select name="gioi_tinh" class="form-select">
          <option value="nam" {{ old('gioi_tinh',$sinhVien->gioi_tinh)==='nam'?'selected':'' }}>Nam</option>
          <option value="nu" {{ old('gioi_tinh',$sinhVien->gioi_tinh)==='nu'?'selected':'' }}>Nữ</option>
          <option value="khac" {{ old('gioi_tinh',$sinhVien->gioi_tinh)==='khac'?'selected':'' }}>Khác</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Diện miễn giảm</label>
        <select name="dien_mien_giam" class="form-select">
          @foreach(['binh_thuong'=>'Bình thường','ho_ngheo'=>'Hộ nghèo (100%)','ho_can_ngheo'=>'Cận nghèo (50%)','chinh_sach'=>'Chính sách (50%)','thuong_binh'=>'Thương binh (70%)','mo_coi'=>'Mồ côi (100%)','khuyet_tat'=>'Khuyết tật (100%)'] as $v=>$l)
            <option value="{{ $v }}" {{ old('dien_mien_giam',$sinhVien->dien_mien_giam)===$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email',$sinhVien->email) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="so_dien_thoai" class="form-control" value="{{ old('so_dien_thoai',$sinhVien->so_dien_thoai) }}">
      </div>
      <div class="col-12">
        <label class="form-label">Địa chỉ</label>
        <textarea name="dia_chi" class="form-control" rows="2">{{ old('dia_chi',$sinhVien->dia_chi) }}</textarea>
      </div>
    </div>
    <hr class="my-4">
    <div class="d-flex gap-2 justify-content-end">
      <a href="{{ route('ketoan.sinh-vien.show',$sinhVien) }}" class="btn btn-outline-secondary">Hủy</a>
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Cập nhật</button>
    </div>
  </form>
  </div>
</div>
</div>
</div>
@endsection
