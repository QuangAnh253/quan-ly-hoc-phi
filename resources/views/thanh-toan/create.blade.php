@extends('layouts.app')
@section('title','Thu học phí')
@section('page-title','Thu học phí')

@section('content')
<div class="row g-4">
  {{-- Tìm sinh viên --}}
  <div class="col-md-4">
    <div class="card">
      <div class="card-header"><i class="bi bi-search me-2 text-primary"></i>Tìm sinh viên</div>
      <div class="card-body">
        <form method="GET" action="{{ route('ketoan.thanh-toan.create') }}">
          <label class="form-label">Mã sinh viên</label>
          <div class="input-group">
            <input type="text" name="ma_sv" class="form-control" value="{{ request('ma_sv') }}"
                   placeholder="B21DCCN001" autofocus>
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
          </div>
        </form>

        @if($sinhVien)
        <hr>
        <div class="text-center mb-3">
          <div style="width:52px;height:52px;border-radius:50%;background:#e7f1ff;
                      display:flex;align-items:center;justify-content:center;
                      font-size:20px;font-weight:700;color:#0d6efd;margin:0 auto">
            {{ strtoupper(substr($sinhVien->ho_ten,0,1)) }}
          </div>
          <div class="fw-600 mt-2">{{ $sinhVien->ho_ten }}</div>
          <div class="text-muted" style="font-size:13px">{{ $sinhVien->ma_sv }} — {{ $sinhVien->lop }}</div>
          <div class="text-muted" style="font-size:12px">{{ $sinhVien->khoa->ten_khoa }}</div>
          @if($sinhVien->dien_mien_giam !== 'binh_thuong')
            <span class="badge bg-info text-dark mt-1">{{ Str::title(str_replace('_',' ',$sinhVien->dien_mien_giam)) }}</span>
          @endif
        </div>
        <div class="text-center py-3 rounded" style="background:#fff3f3;border:1px solid #ffcdd2">
          <div style="font-size:12px;color:#6c757d">Tổng còn nợ</div>
          <div style="font-size:24px;font-weight:700;color:#dc3545">
            {{ number_format($conNo['tong_con_no'],0,',','.') }}đ
          </div>
        </div>
        @elseif(request('ma_sv'))
        <div class="alert alert-warning mt-3 py-2" style="font-size:13px">
          <i class="bi bi-exclamation-triangle me-1"></i>Không tìm thấy sinh viên "{{ request('ma_sv') }}"
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Form thu tiền --}}
  <div class="col-md-8">
    @if($sinhVien && $conNo['chi_tiet']->count())
    <div class="card">
      <div class="card-header"><i class="bi bi-credit-card me-2 text-primary"></i>Ghi nhận thanh toán</div>
      <div class="card-body">
        <form method="POST" action="{{ route('ketoan.thanh-toan.store') }}">
          @csrf
          {{-- Chọn đợt thu --}}
          <div class="mb-3">
            <label class="form-label">Đợt thu cần đóng <span class="text-danger">*</span></label>
            <div class="row g-2">
              @foreach($conNo['chi_tiet'] as $hp)
              <div class="col-12">
                <label class="d-block border rounded p-3 cursor-pointer {{ old('hoc_phi_id')==$hp->id?'border-primary bg-light':'' }}"
                       style="cursor:pointer" onclick="selectHocPhi({{ $hp->id }},{{ $hp->con_no }})">
                  <div class="d-flex align-items-center gap-2">
                    <input type="radio" name="hoc_phi_id" value="{{ $hp->id }}"
                           {{ old('hoc_phi_id')==$hp->id || $loop->first?'checked':'' }}
                           onchange="selectHocPhi({{ $hp->id }},{{ $hp->con_no }})">
                    <div class="flex-fill">
                      <div class="fw-500">{{ $hp->dotThu->ten_dot }}</div>
                      <div class="d-flex gap-3 mt-1" style="font-size:12px;color:#6c757d">
                        <span>Phải đóng: <strong>{{ number_format($hp->tong_phai_dong,0,',','.') }}đ</strong></span>
                        <span>Đã đóng: <strong class="text-success">{{ number_format($hp->da_dong,0,',','.') }}đ</strong></span>
                        <span>Còn nợ: <strong class="text-danger">{{ number_format($hp->con_no,0,',','.') }}đ</strong></span>
                      </div>
                    </div>
                  </div>
                </label>
              </div>
              @endforeach
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Số tiền thu <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" name="so_tien" id="so_tien" class="form-control fw-600"
                       value="{{ old('so_tien', $conNo['chi_tiet']->first()?->con_no ?? '') }}"
                       min="1000" step="1000" required>
                <span class="input-group-text">đ</span>
              </div>
              <button type="button" class="btn btn-link btn-sm p-0 mt-1 text-muted"
                      onclick="document.getElementById('so_tien').value=document.getElementById('con_no_val').value">
                Đóng hết công nợ
              </button>
              <input type="hidden" id="con_no_val" value="{{ $conNo['chi_tiet']->first()?->con_no ?? 0 }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Hình thức thanh toán <span class="text-danger">*</span></label>
              <select name="hinh_thuc" class="form-select" required>
                <option value="tien_mat" {{ old('hinh_thuc','tien_mat')==='tien_mat'?'selected':'' }}>💵 Tiền mặt</option>
                <option value="chuyen_khoan" {{ old('hinh_thuc')==='chuyen_khoan'?'selected':'' }}>🏦 Chuyển khoản</option>
                <option value="the_ngan_hang" {{ old('hinh_thuc')==='the_ngan_hang'?'selected':'' }}>💳 Thẻ ngân hàng</option>
                <option value="vi_dien_tu" {{ old('hinh_thuc')==='vi_dien_tu'?'selected':'' }}>📱 Ví điện tử</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Ngân hàng <small class="text-muted">(nếu CK/thẻ)</small></label>
              <input type="text" name="ngan_hang" class="form-control" value="{{ old('ngan_hang') }}" placeholder="VCB, Vietinbank...">
            </div>
            <div class="col-md-6">
              <label class="form-label">Số tham chiếu / mã CK</label>
              <input type="text" name="so_tham_chieu" class="form-control" value="{{ old('so_tham_chieu') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Ghi chú</label>
              <input type="text" name="ghi_chu" class="form-control" value="{{ old('ghi_chu') }}">
            </div>
          </div>

          <hr class="my-3">
          <div class="d-flex gap-2 justify-content-end">
            <button type="reset" class="btn btn-outline-secondary">Nhập lại</button>
            <button type="submit" class="btn btn-success btn-lg px-4">
              <i class="bi bi-check-circle me-2"></i>Xác nhận thu tiền
            </button>
          </div>
        </form>
      </div>
    </div>
    @elseif($sinhVien)
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="bi bi-check-circle-fill text-success" style="font-size:48px"></i>
          <p class="mt-3 mb-0 fw-500">Sinh viên <strong>{{ $sinhVien->ho_ten }}</strong> đã đóng đầy đủ học phí!</p>
        </div>
      </div>
    @else
      <div class="card">
        <div class="card-body text-center py-5 text-muted">
          <i class="bi bi-search" style="font-size:48px"></i>
          <p class="mt-3 mb-0">Nhập mã sinh viên bên trái để bắt đầu thu học phí</p>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
function selectHocPhi(id, conNo) {
  document.querySelector('input[name=hoc_phi_id][value="'+id+'"]').checked = true;
  document.getElementById('so_tien').value = conNo;
  document.getElementById('con_no_val').value = conNo;
}
</script>
@endpush
