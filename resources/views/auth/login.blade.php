<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Đăng nhập — Quản lý Học Phí</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{
  background-color:#e9eef5;
  background-image:url('/images/login-background.png');
  background-position:center center;
  background-repeat:no-repeat;
  background-size:cover;
  background-attachment:fixed;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:14px;
}
.login-card{
  width:100%;
  max-width:440px;
  padding:34px 32px 28px;
  border-radius:20px;
  background:rgba(255,255,255,.18);
  border:1px solid rgba(255,255,255,.28);
  box-shadow:0 22px 60px rgba(13,32,67,.28);
  backdrop-filter:blur(18px);
  -webkit-backdrop-filter:blur(18px);
}
.brand-logo{
  display:block;
  height:100px;
  width:auto;
  max-width:100%;
  object-fit:contain;
  margin:0 auto 16px;
}
.page-title{
  color:#fff;
  font-weight:800;
  font-size:1.1rem;
  line-height:1.35;
  letter-spacing:.3px;
}
.page-subtitle{
  color:rgba(255,255,255,.82);
  font-size:.92rem;
}
.form-label{font-weight:600;font-size:13px;color:rgba(255,255,255,.92)}
.input-group-text{
  background:rgba(255,255,255,.78);
  border:1px solid rgba(255,255,255,.55);
  border-right:0;
  color:#262262;
  border-radius:14px 0 0 14px;
}
.form-control{
  height:50px;
  border-radius:0 14px 14px 0;
  border:1px solid rgba(255,255,255,.55);
  background:rgba(255,255,255,.93);
  font-size:14px;
}
.form-control:focus{
  border-color:#F79421;
  box-shadow:0 0 0 .2rem rgba(247,148,33,.18);
}
.btn-login{
  width:100%;
  height:50px;
  border-radius:14px;
  border:none;
  background:#F79421;
  color:#fff;
  font-weight:700;
  font-size:14.5px;
  box-shadow:0 10px 24px rgba(247,148,33,.28);
}
.btn-login:hover,
.btn-login:focus{
  background:#e68612;
  color:#fff;
}
.form-check-input{
  width:1rem;
  height:1rem;
  margin-top:.18rem;
  border-color:rgba(255,255,255,.65);
  background-color:rgba(255,255,255,.18);
}
.form-check-label,.text-muted-custom{color:rgba(255,255,255,.82)!important}
.alert{
  border:1px solid rgba(255,255,255,.25);
  background:rgba(255,255,255,.18);
  color:#fff;
}
.footer-note{
  color:rgba(255,255,255,.72);
  font-size:.78rem;
}
</style>
</head>
<body>
<div class="login-card">
  <div class="text-center mb-4">
    <img src="{{ asset('images/logo.png') }}" alt="UTT Logo" class="brand-logo">
    <div class="page-title text-uppercase">HỆ THỐNG QUẢN LÝ THU HỌC PHÍ</div>
    <div class="page-subtitle mt-1">Trường Đại học Công nghệ Giao thông Vận tải</div>
  </div>

  @if($errors->any())
  <div class="alert py-2 mb-3 rounded-4 small">
    <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
  </div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Email hoặc Tài khoản</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="text" name="email" class="form-control" placeholder="Nhập email hoặc tài khoản"
               value="{{ old('email') }}" required autofocus>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Mật khẩu</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
    </div>
    <div class="form-check mb-4 text-muted-custom">
      <input class="form-check-input" type="checkbox" name="remember" id="remember">
      <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
    </div>
    <button type="submit" class="btn btn-login">
      <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
    </button>
  </form>

  <div class="mt-4 pt-3 border-top border-light border-opacity-25 text-center">
    <div class="footer-note">© University of Transport Technology - UTT</div>
  </div>
</div>
</body>
</html>
