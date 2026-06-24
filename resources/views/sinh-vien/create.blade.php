@extends('layouts.app')
@section('title','Thêm sinh viên')
@section('page-title','Thêm sinh viên mới')

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
  <div class="card-header"><i class="bi bi-person-plus me-2 text-primary"></i>Thông tin sinh viên</div>
  <div class="card-body">
  <form method="POST" action="{{ route('ketoan.sinh-vien.store') }}">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
        <input type="text" name="ma_sv" class="form-control" value="{{ old('ma_sv') }}" placeholder="B21DCCN001" required>
      </div>
      <div class="col-md-8">
        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
        <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten') }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Khoa <span class="text-danger">*</span></label>
        <select name="khoa_id" class="form-select" required>
          <option value="">-- Chọn khoa --</option>
          @foreach($khoas as $k)
            <option value="{{ $k->id }}" {{ old('khoa_id')==$k->id?'selected':'' }}>{{ $k->ten_khoa }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Lớp <span class="text-danger">*</span></label>
        <input type="text" name="lop" class="form-control" value="{{ old('lop') }}" placeholder="D21CQCN01" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Ngày sinh</label>
        <input type="date" name="ngay_sinh" class="form-control" value="{{ old('ngay_sinh') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label">Giới tính <span class="text-danger">*</span></label>
        <select name="gioi_tinh" class="form-select" required>
          <option value="nam" {{ old('gioi_tinh','nam')==='nam'?'selected':'' }}>Nam</option>
          <option value="nu" {{ old('gioi_tinh')==='nu'?'selected':'' }}>Nữ</option>
          <option value="khac" {{ old('gioi_tinh')==='khac'?'selected':'' }}>Khác</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Niên khóa <span class="text-danger">*</span></label>
        <input type="number" name="nien_khoa" class="form-control" value="{{ old('nien_khoa',date('Y')) }}" min="2000" max="2099" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Hệ đào tạo <span class="text-danger">*</span></label>
        <select name="he_dao_tao" class="form-select" required>
          <option value="chinh_quy" {{ old('he_dao_tao','chinh_quy')==='chinh_quy'?'selected':'' }}>Chính quy</option>
          <option value="lien_thong" {{ old('he_dao_tao')==='lien_thong'?'selected':'' }}>Liên thông</option>
          <option value="tu_xa" {{ old('he_dao_tao')==='tu_xa'?'selected':'' }}>Từ xa</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Diện miễn giảm <span class="text-danger">*</span></label>
        <select name="dien_mien_giam" class="form-select" required>
          <option value="binh_thuong" {{ old('dien_mien_giam','binh_thuong')==='binh_thuong'?'selected':'' }}>Bình thường</option>
          <option value="ho_ngheo" {{ old('dien_mien_giam')==='ho_ngheo'?'selected':'' }}>Hộ nghèo (miễn 100%)</option>
          <option value="ho_can_ngheo" {{ old('dien_mien_giam')==='ho_can_ngheo'?'selected':'' }}>Hộ cận nghèo (giảm 50%)</option>
          <option value="chinh_sach" {{ old('dien_mien_giam')==='chinh_sach'?'selected':'' }}>Chính sách (giảm 50%)</option>
          <option value="thuong_binh" {{ old('dien_mien_giam')==='thuong_binh'?'selected':'' }}>Thương binh (giảm 70%)</option>
          <option value="mo_coi" {{ old('dien_mien_giam')==='mo_coi'?'selected':'' }}>Mồ côi (miễn 100%)</option>
          <option value="khuyet_tat" {{ old('dien_mien_giam')==='khuyet_tat'?'selected':'' }}>Khuyết tật (miễn 100%)</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="so_dien_thoai" class="form-control" value="{{ old('so_dien_thoai') }}">
      </div>
      <div class="col-12">
        <label class="form-label">Địa chỉ</label>
        <textarea name="dia_chi" class="form-control" rows="2">{{ old('dia_chi') }}</textarea>
      </div>
    </div>
    <hr class="my-4">
    <div class="d-flex gap-2 justify-content-end">
      <a href="{{ route('ketoan.sinh-vien.index') }}" class="btn btn-outline-secondary">Hủy</a>
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Lưu sinh viên</button>
    </div>
  </form>
  </div>
</div>
</div>
</div>
@endsection
