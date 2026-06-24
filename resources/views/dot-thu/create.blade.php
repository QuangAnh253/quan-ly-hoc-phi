@extends('layouts.app')
@section('title','Tạo đợt thu mới')
@section('page-title','Tạo đợt thu học phí')

@section('content')
<div class="row justify-content-center">
<div class="col-md-7">
<div class="card">
  <div class="card-header"><i class="bi bi-calendar-plus me-2 text-primary"></i>Thông tin đợt thu</div>
  <div class="card-body">
  <form method="POST" action="{{ route('admin.dot-thu.store') }}">
    @csrf
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Tên đợt thu <span class="text-danger">*</span></label>
        <input type="text" name="ten_dot" class="form-control" value="{{ old('ten_dot') }}"
               placeholder="Đợt 1 - Học kỳ 1 năm học 2024-2025" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Học kỳ <span class="text-danger">*</span></label>
        <select name="hoc_ky" class="form-select" required>
          <option value="1" {{ old('hoc_ky','1')==='1'?'selected':'' }}>Học kỳ 1</option>
          <option value="2" {{ old('hoc_ky')==='2'?'selected':'' }}>Học kỳ 2</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Năm học <span class="text-danger">*</span></label>
        <input type="text" name="nam_hoc" class="form-control" value="{{ old('nam_hoc','2024-2025') }}"
               placeholder="2024-2025" pattern="\d{4}-\d{4}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Đơn giá tín chỉ (VNĐ) <span class="text-danger">*</span></label>
        <div class="input-group">
          <input type="number" name="don_gia_tin_chi" class="form-control" value="{{ old('don_gia_tin_chi',440000) }}" min="1000" required>
          <span class="input-group-text">đ/TC</span>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Phí phạt mỗi ngày quá hạn</label>
        <div class="input-group">
          <input type="number" name="phi_phat_ngay" class="form-control" value="{{ old('phi_phat_ngay',5000) }}" min="0">
          <span class="input-group-text">đ/ngày</span>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
        <input type="date" name="ngay_bat_dau" class="form-control" value="{{ old('ngay_bat_dau') }}" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Hạn đóng học phí <span class="text-danger">*</span></label>
        <input type="date" name="han_dong" class="form-control" value="{{ old('han_dong') }}" required>
      </div>
      <div class="col-12">
        <label class="form-label">Ghi chú</label>
        <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Thông tin bổ sung (nếu có)">{{ old('ghi_chu') }}</textarea>
      </div>
    </div>

    <div class="alert alert-info mt-3 py-2" style="font-size:13px">
      <i class="bi bi-info-circle me-1"></i>
      Sau khi tạo, đợt thu ở trạng thái <strong>Sắp mở</strong>. Nhấn <strong>Mở đợt</strong> để hệ thống tự tính học phí và gửi thông báo email cho toàn bộ sinh viên.
    </div>

    <hr class="my-3">
    <div class="d-flex gap-2 justify-content-end">
      <a href="{{ route('admin.dot-thu.index') }}" class="btn btn-outline-secondary">Hủy</a>
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Tạo đợt thu</button>
    </div>
  </form>
  </div>
</div>
</div>
</div>
@endsection
