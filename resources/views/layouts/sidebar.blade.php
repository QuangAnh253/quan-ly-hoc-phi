<aside class="app-sidebar-shell" id="appSidebar">
  <div class="app-sidebar">
    <div class="sidebar-top d-flex align-items-center justify-content-between">
      <div class="sidebar-brand d-flex align-items-center gap-2">
        <img src="{{ asset('images/logo.png') }}" alt="UTT Logo" class="sidebar-avatar-logo">
        <div class="sidebar-brand-text">
          <div class="sidebar-brand-title">UTT</div>
          <div class="sidebar-brand-subtitle">Quản lý thu học phí</div>
        </div>
      </div>
      <button type="button" class="sidebar-toggle btn btn-sm btn-light" id="sidebarToggle" aria-label="Thu gọn/mở rộng sidebar" aria-expanded="true">
        <i class="bi bi-layout-sidebar-inset"></i>
      </button>
    </div>

    @auth
      @php
        $role = auth()->user()->role;
        $roleLabel = [
          'admin' => 'Quản trị viên',
          'ketoan' => 'Kế toán',
          'sinhvien' => 'Sinh viên',
        ][$role] ?? '';
        $menu = [
          'admin' => [
            ['section' => 'Tổng quan'],
            ['route' => 'admin.dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Dashboard'],
            ['section' => 'Quản lý'],
            ['route' => 'admin.dot-thu.index', 'icon' => 'bi-calendar-event', 'label' => 'Đợt thu học phí'],
            ['route' => 'ketoan.sinh-vien.index', 'icon' => 'bi-people', 'label' => 'Sinh viên'],
            ['route' => 'ketoan.thanh-toan.create', 'icon' => 'bi-credit-card', 'label' => 'Thu học phí'],
            ['section' => 'Báo cáo'],
            ['route' => 'admin.bao-cao.nam-hoc', 'icon' => 'bi-bar-chart-line', 'label' => 'Báo cáo năm học'],
          ],
          'ketoan' => [
            ['section' => 'Tổng quan'],
            ['route' => 'ketoan.dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Dashboard'],
            ['section' => 'Nghiệp vụ'],
            ['route' => 'ketoan.thanh-toan.create', 'icon' => 'bi-credit-card', 'label' => 'Thu học phí'],
            ['route' => 'ketoan.sinh-vien.index', 'icon' => 'bi-people', 'label' => 'Sinh viên'],
          ],
          'sinhvien' => [
            ['section' => 'Của tôi'],
            ['route' => 'sinhvien.dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Tổng quan'],
            ['route' => 'sinhvien.hoc-phi', 'icon' => 'bi-receipt', 'label' => 'Học phí của tôi'],
          ],
        ];
      @endphp

      <nav class="sidebar-nav" aria-label="Sidebar navigation">
        @foreach($menu[$role] ?? [] as $item)
          @if(isset($item['section']))
            <div class="sidebar-section">{{ $item['section'] }}</div>
          @else
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
              <i class="bi {{ $item['icon'] }}"></i>
              <span class="sidebar-label">{{ $item['label'] }}</span>
            </a>
          @endif
        @endforeach
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-user d-flex align-items-center gap-2">
          <div class="sidebar-user-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
          <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-user-role">{{ $roleLabel }}</div>
          </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
          @csrf
          <button type="submit" class="sidebar-link sidebar-logout w-100 border-0">
            <i class="bi bi-box-arrow-right"></i>
            <span class="sidebar-label">Đăng xuất</span>
          </button>
        </form>
      </div>
    @endauth
  </div>
</aside>

<style>
.app-top-header{
  position:fixed;
  top:0;
  left:0;
  right:0;
  width:100%;
  z-index:1050;
  line-height:0;
  background:#12213b;
  max-height:130px;
  overflow:hidden;
}
.app-banner-image{
  width:100%;
  height:auto;
  max-height:130px;
  object-fit:cover;
  object-position:center center;
  display:block;
}
.app-sidebar{
  height:100%;
  background:linear-gradient(180deg, #262262 0%, #1f1d57 100%);
  color:#fff;
  display:flex;
  flex-direction:column;
  border-radius:0 24px 24px 0;
  box-shadow:0 16px 38px rgba(11,18,43,.25);
  overflow:hidden;
}
.sidebar-top{
  padding:16px 16px 12px;
  border-bottom:1px solid rgba(255,255,255,.08);
  min-height:72px;
}
.sidebar-brand{min-width:0;overflow:hidden}
.sidebar-avatar-logo{
  width:38px;
  height:38px;
  border-radius:10px;
  object-fit:contain;
  flex:0 0 auto;
  background:rgba(255,255,255,.10);
}
.sidebar-brand-text{min-width:0}
.sidebar-brand-title{font-weight:800;font-size:14px;line-height:1.1}
.sidebar-brand-subtitle{font-size:11px;color:rgba(255,255,255,.68);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sidebar-toggle{
  border:0;
  width:38px;height:38px;border-radius:12px;
  background:rgba(255,255,255,.12)!important;color:#fff!important;
}
.sidebar-nav{
  padding:12px 10px 8px;
  overflow-y:auto;
  flex:1 1 auto;
}
.sidebar-section{
  padding:14px 10px 6px;
  font-size:10px;
  text-transform:uppercase;
  letter-spacing:.08em;
  color:rgba(255,255,255,.38);
  font-weight:700;
}
.sidebar-link{
  display:flex;
  align-items:center;
  gap:12px;
  padding:11px 12px;
  color:rgba(255,255,255,.8);
  text-decoration:none;
  border-radius:14px;
  margin:2px 0;
  transition:all .25s ease;
  white-space:nowrap;
  overflow:hidden;
}
.sidebar-link:hover{
  background:rgba(255,255,255,.08);
  color:#fff;
}
.sidebar-link.active{
  background:rgba(247,148,33,.18);
  color:#fff;
  box-shadow:inset 0 0 0 1px rgba(247,148,33,.22);
}
.sidebar-link i{width:20px;flex:0 0 20px;text-align:center;font-size:16px}
.sidebar-label{transition:opacity .2s ease, transform .2s ease}
.sidebar-footer{
  padding:14px 12px 16px;
  border-top:1px solid rgba(255,255,255,.08);
}
.sidebar-user-avatar{
  width:36px;height:36px;border-radius:50%;
  background:rgba(255,255,255,.14);
  display:flex;align-items:center;justify-content:center;
  font-weight:700;
}
.sidebar-user-name{font-size:13px;font-weight:600;line-height:1.1}
.sidebar-user-role{font-size:11px;color:rgba(255,255,255,.58)}
.sidebar-logout{
  background:transparent;
}
.app-sidebar-shell.is-collapsed .sidebar-brand-text,
.app-sidebar-shell.is-collapsed .sidebar-label,
.app-sidebar-shell.is-collapsed .sidebar-user-info,
.app-sidebar-shell.is-collapsed .sidebar-section{
  opacity:0;
  transform:translateX(-6px);
  width:0;
  overflow:hidden;
  display:none;
}
.app-sidebar-shell.is-collapsed .sidebar-link{
  justify-content:center;
  padding-left:0;
  padding-right:0;
}
.app-sidebar-shell.is-collapsed .sidebar-link i{margin:0}
.app-sidebar-shell.is-collapsed .sidebar-top,
.app-sidebar-shell.is-collapsed .sidebar-footer{padding-left:12px;padding-right:12px}
.app-sidebar-shell.is-collapsed .sidebar-brand{justify-content:center;width:100%}
.app-sidebar-shell.is-collapsed .sidebar-toggle{margin-left:auto;margin-right:auto}

@media (max-width: 991.98px){
  .app-sidebar-shell{
    transform:translateX(-100%);
    transition:transform .25s ease, width .25s ease;
  }
  .app-sidebar-shell.is-mobile-open{
    transform:translateX(0);
  }
}
</style>
