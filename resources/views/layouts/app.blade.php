<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Quản lý Thu Học Phí')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root{
  --header-h:120px;
  --sidebar-expanded:260px;
  --sidebar-collapsed:80px;
  --sidebar-w:var(--sidebar-expanded);
  --sidebar-gap:24px;
  --main-top-offset:calc(var(--header-h) + 20px);
  --brand-blue:#262262;
  --brand-orange:#F79421;
  --page-bg:#f4f7fb;
}
body{
  background:var(--page-bg);
  font-size:14px;
  overflow-x:hidden;
}
.app-shell{
  min-height:100vh;
}
#appShell{
  --sidebar-w:var(--sidebar-expanded);
}
#appShell[data-sidebar-state="collapsed"]{
  --sidebar-w:var(--sidebar-collapsed);
}
#appShell[data-sidebar-state="expanded"]{
  --sidebar-w:var(--sidebar-expanded);
}
.app-header-space{height:var(--header-h);}
.app-body{
  position:relative;
  min-height:calc(100vh - var(--header-h));
}
.app-sidebar-shell{
  position:fixed;
  top:var(--header-h);
  left:0;
  width:var(--sidebar-w);
  height:calc(100vh - var(--header-h));
  z-index:1040;
  transition:width .25s ease;
}
.app-sidebar-shell.is-collapsed{
  width:var(--sidebar-collapsed);
}
.app-main-shell{
  margin-left:var(--sidebar-w);
  padding-top:var(--sidebar-gap);
  min-height:calc(100vh - var(--header-h));
  transition:margin-left .25s ease;
}
.app-header-frost{
  position:fixed;
  top:var(--header-h);
  left:0;
  right:0;
  height:16px;
  z-index:1035;
  pointer-events:none;
  background:linear-gradient(to bottom, rgba(255,255,255,.35), rgba(255,255,255,.06));
  backdrop-filter:blur(10px);
  -webkit-backdrop-filter:blur(10px);
  box-shadow:0 10px 28px rgba(10,18,36,.16);
}
.app-main-card{
  margin:0 24px 24px;
  padding:24px 28px;
  background:rgba(255,255,255,.72);
  border:1px solid rgba(255,255,255,.42);
  border-radius:22px;
  box-shadow:0 18px 50px rgba(17,24,39,.08);
  backdrop-filter:blur(16px);
  -webkit-backdrop-filter:blur(16px);
}
.topbar{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:16px;
  margin-bottom:18px;
}
.topbar-title{font-weight:700;font-size:16px;color:#1f2a44}
.topbar-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.card{border:1px solid #e9ecef;border-radius:12px;box-shadow:none}
.card-header{background:#fff;border-bottom:1px solid #e9ecef;padding:14px 20px;font-weight:600;border-radius:12px 12px 0 0!important}
.stat-card{background:#fff;border-radius:12px;border:1px solid #e9ecef;padding:20px;display:flex;align-items:center;gap:16px}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px}
.stat-label{font-size:12px;color:#6c757d;margin-bottom:2px}
.stat-value{font-size:22px;font-weight:700;line-height:1}
.table{font-size:13.5px}
.table thead th{background:#f8f9fa;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:#6c757d;border-bottom:1px solid #dee2e6;padding:10px 12px}
.table td{padding:10px 12px;vertical-align:middle}
.badge{font-weight:500;font-size:11px}
.btn{border-radius:8px;font-size:13.5px}
.btn-sm{font-size:12px}
.form-control,.form-select{border-radius:8px;font-size:13.5px}
.form-label{font-weight:500;font-size:13px;color:#495057}
.alert{border-radius:10px;font-size:13.5px}

@media (max-width: 991.98px){
  :root{--sidebar-expanded:260px;--sidebar-collapsed:260px;}
  .app-sidebar-shell{transform:translateX(-100%);width:var(--sidebar-expanded)}
  .app-sidebar-shell.is-mobile-open{transform:translateX(0)}
  .app-main-shell{margin-left:0}
}

@media (max-width: 575.98px){
  .app-main-card{margin:0 12px 12px;padding:18px 16px}
}
</style>
@stack('styles')
</head>
<body>
<div class="app-shell" id="appShell" data-sidebar-state="expanded">
  @include('layouts.header')
  <div class="app-header-space"></div>
  <div class="app-header-frost"></div>

  <div class="app-body">
    @include('layouts.sidebar')

    <main class="app-main-shell" id="appMain">
      <div class="app-main-card">
        <div class="topbar">
          <div class="topbar-title">@yield('page-title','Trang chủ')</div>
          <div class="topbar-actions">@yield('topbar-actions')</div>
        </div>

        @if(session('success'))<div class="alert alert-success d-flex align-items-center gap-2 mb-4"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger d-flex align-items-center gap-2 mb-4"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Lỗi:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        @yield('content')
      </div>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
  const shell = document.getElementById('appShell');
  const sidebar = document.getElementById('appSidebar');
  const toggle = document.getElementById('sidebarToggle');
  if (!shell || !sidebar || !toggle) return;

  const storageKey = 'utt.sidebar.desktop';
  const mobileQuery = window.matchMedia('(max-width: 991.98px)');

  function applyDesktopState(state) {
    const desktopState = state === 'collapsed' ? 'collapsed' : 'expanded';
    shell.dataset.sidebarState = desktopState;
    sidebar.classList.toggle('is-collapsed', desktopState === 'collapsed' && !mobileQuery.matches);
    toggle.setAttribute('aria-expanded', String(desktopState !== 'collapsed'));
    localStorage.setItem(storageKey, desktopState);
  }

  function applyMobileState(open) {
    sidebar.classList.toggle('is-mobile-open', open);
    toggle.setAttribute('aria-expanded', String(open));
  }

  const savedState = localStorage.getItem(storageKey) || 'expanded';
  applyDesktopState(savedState);

  toggle.addEventListener('click', () => {
    if (mobileQuery.matches) {
      applyMobileState(!sidebar.classList.contains('is-mobile-open'));
      return;
    }

    const next = sidebar.classList.contains('is-collapsed') ? 'expanded' : 'collapsed';
    applyDesktopState(next);
  });

  mobileQuery.addEventListener('change', () => {
    applyDesktopState(localStorage.getItem(storageKey) || 'expanded');
    applyMobileState(false);
  });
})();
</script>
@stack('scripts')
<script>
(function () {
  const banner = document.getElementById('appBanner');
  if (!banner) return;
  function syncHeaderH() {
    const h = banner.offsetHeight || 130;
    document.documentElement.style.setProperty('--header-h', h + 'px');
  }
  if (banner.complete) {
    syncHeaderH();
  } else {
    banner.addEventListener('load', syncHeaderH);
  }
  window.addEventListener('resize', syncHeaderH);
})();
</script>
</body>
</html>
