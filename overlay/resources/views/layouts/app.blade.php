<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
<title>@yield('title','Si Kursus') · Si Kursus</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root { --sb-w: 240px; --sb-w-mini: 64px; }
  body { background:#f5f7fb; }

  /* Layout shell */
  .app-shell { display:flex; min-height:100vh; }
  .sidebar {
    width: var(--sb-w);
    min-height:100vh;
    background:#ffffff;
    border-right:1px solid #e6e9ef;
    transition: width .2s ease, transform .2s ease;
    position:relative;
    z-index: 1030;
  }
  .content-wrap { flex:1 1 auto; min-width:0; }

  /* Sidebar links */
  .sidebar a, .sidebar .menu-toggle {
    color:#374151;
    text-decoration:none;
    display:flex;
    align-items:center;
    padding:.55rem .9rem;
    border-radius:.5rem;
    width:100%;
    background:transparent;
    border:0;
    text-align:left;
    font-size:.92rem;
  }
  .sidebar a.active, .sidebar a:hover, .sidebar .menu-toggle:hover { background:#eef2ff; color:#4338ca; }
  .sidebar .menu-toggle.has-active { color:#4338ca; }
  .sidebar .menu-toggle .caret { margin-left:auto; transition:transform .2s; font-size:.7rem; }
  .sidebar .menu-toggle.open .caret { transform:rotate(180deg); }
  .sidebar .submenu { padding-left:.5rem; display:none; }
  .sidebar .submenu.show { display:block; }
  .sidebar .submenu a { padding-left:2.4rem; font-size:.88rem; }
  .sidebar .group { font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; padding:.6rem .9rem .25rem; }
  .brand { font-weight:700; color:#4338ca; padding:1rem .9rem; font-size:1.15rem; display:flex; align-items:center; gap:.5rem; }
  .brand .label { white-space:nowrap; overflow:hidden; }

  /* Mini (collapsed) sidebar — desktop */
  .sidebar.mini { width: var(--sb-w-mini); }
  .sidebar.mini .label, .sidebar.mini .group, .sidebar.mini .caret, .sidebar.mini .submenu { display:none !important; }
  .sidebar.mini a, .sidebar.mini .menu-toggle { justify-content:center; padding:.55rem .25rem; }
  .sidebar.mini a i, .sidebar.mini .menu-toggle i { margin:0 !important; font-size:1.1rem; }

  /* Mobile drawer */
  .sidebar-backdrop {
    display:none;
    position:fixed; inset:0;
    background:rgba(15,23,42,.45);
    z-index:1029;
  }
  @media (max-width: 991.98px) {
    .sidebar {
      position:fixed; top:0; left:0; bottom:0;
      transform: translateX(-100%);
      box-shadow: 2px 0 12px rgba(0,0,0,.12);
    }
    .sidebar.open { transform: translateX(0); }
    .sidebar.mini { width: var(--sb-w); } /* di mobile, mini tidak berlaku */
    .sidebar.mini .label, .sidebar.mini .group, .sidebar.mini .caret, .sidebar.mini .submenu { display:initial !important; }
    .sidebar.mini a, .sidebar.mini .menu-toggle { justify-content:flex-start; padding:.55rem .9rem; }
    .sidebar-backdrop.show { display:block; }
  }

  /* Topbar */
  .navbar-app { background:#fff; border-bottom:1px solid #e6e9ef; position:sticky; top:0; z-index:1020; }
  .navbar-app .topbar-title { font-weight:600; font-size:1rem; margin:0; }
  .btn-icon { width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center; padding:0; }

  /* Cards & UI */
  .card-stat { border:0; border-radius:.75rem; box-shadow:0 1px 2px rgba(0,0,0,.04); }
  .card-stat .stat-num { font-size:1.6rem; font-weight:700; }
  .table > :not(caption) > * > * { padding:.6rem .75rem; }
  .badge-soft { padding:.35em .55em; border-radius:.4rem; font-weight:500; }
  .badge-belum { background:#fee2e2; color:#991b1b; }
  .badge-sebagian { background:#fef3c7; color:#92400e; }
  .badge-lunas { background:#dcfce7; color:#166534; }
  .badge-aktif { background:#dbeafe; color:#1e40af; }
  .badge-selesai { background:#e0e7ff; color:#3730a3; }
  .badge-berhenti { background:#f3f4f6; color:#4b5563; }
  .nav-bell .badge { font-size:.65rem; }

  /* Responsive table */
  .table-responsive-sticky { overflow-x:auto; -webkit-overflow-scrolling:touch; }
  @media (max-width: 575.98px) {
    main.app-main { padding: 1rem !important; }
    .stat-num { font-size:1.3rem !important; }
  }

  /* Pagination wrapper */
  .pagination-bar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; }
  .pagination-bar .pagination { margin:0; }
</style>
@stack('head')
</head>
<body>
@auth
<div class="app-shell">
  <div class="sidebar-backdrop" id="sbBackdrop"></div>

  <aside class="sidebar p-2" id="appSidebar">
    <div class="brand"><i class="bi bi-mortarboard-fill"></i> <span class="label">Si Kursus</span></div>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="bi bi-speedometer2 me-2"></i><span class="label">Dashboard</span>
    </a>

    @if(auth()->user()->isAdmin())
      @php
        $isMaster = request()->routeIs('siswa.*') || request()->routeIs('kelas.*') || request()->routeIs('guru.*');
        $isKeu    = request()->routeIs('tagihan.*') || request()->routeIs('laporan.*');
        $isPeng   = request()->routeIs('pengingat.*');
        $isJadwal = request()->routeIs('admin.pesan_template.*') || request()->routeIs('admin.jadwal_notif.*');
      @endphp

      <button type="button" class="menu-toggle {{ $isMaster ? 'open has-active' : '' }}" data-target="#sm-master">
        <i class="bi bi-collection me-2"></i><span class="label">Master</span>
        <span class="caret label"><i class="bi bi-chevron-down"></i></span>
      </button>
      <div class="submenu {{ $isMaster ? 'show' : '' }}" id="sm-master">
        <a href="{{ route('siswa.index') }}" class="{{ request()->routeIs('siswa.*') ? 'active' : '' }}"><i class="bi bi-people me-2"></i><span class="label">Siswa</span></a>
        <a href="{{ route('kelas.index') }}" class="{{ request()->routeIs('kelas.*') ? 'active' : '' }}"><i class="bi bi-easel me-2"></i><span class="label">Kelas & Tarif</span></a>
        <a href="{{ route('guru.index') }}" class="{{ request()->routeIs('guru.*') ? 'active' : '' }}"><i class="bi bi-person-badge me-2"></i><span class="label">Guru</span></a>
      </div>

      <button type="button" class="menu-toggle {{ $isKeu ? 'open has-active' : '' }}" data-target="#sm-keu">
        <i class="bi bi-cash-coin me-2"></i><span class="label">Keuangan</span>
        <span class="caret label"><i class="bi bi-chevron-down"></i></span>
      </button>
      <div class="submenu {{ $isKeu ? 'show' : '' }}" id="sm-keu">
        <a href="{{ route('tagihan.index') }}" class="{{ request()->routeIs('tagihan.*') ? 'active' : '' }}"><i class="bi bi-receipt me-2"></i><span class="label">Tagihan</span></a>
        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}"><i class="bi bi-bar-chart me-2"></i><span class="label">Laporan</span></a>
      </div>

      <button type="button" class="menu-toggle {{ $isPeng ? 'open has-active' : '' }}" data-target="#sm-peng">
        <i class="bi bi-bell me-2"></i><span class="label">Pengingat Tagihan</span>
        <span class="caret label"><i class="bi bi-chevron-down"></i></span>
      </button>
      <div class="submenu {{ $isPeng ? 'show' : '' }}" id="sm-peng">
        <a href="{{ route('pengingat.template.index') }}" class="{{ request()->routeIs('pengingat.template.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text me-2"></i><span class="label">Template</span></a>
        <a href="{{ route('pengingat.setting.index') }}" class="{{ request()->routeIs('pengingat.setting.*') ? 'active' : '' }}"><i class="bi bi-sliders me-2"></i><span class="label">Pengaturan</span></a>
        <a href="{{ route('pengingat.kirim.form') }}" class="{{ request()->routeIs('pengingat.kirim.*') ? 'active' : '' }}"><i class="bi bi-send me-2"></i><span class="label">Kirim Manual</span></a>
        <a href="{{ route('pengingat.log.index') }}" class="{{ request()->routeIs('pengingat.log.*') ? 'active' : '' }}"><i class="bi bi-clock-history me-2"></i><span class="label">Log</span></a>
      </div>

      <button type="button" class="menu-toggle {{ $isJadwal ? 'open has-active' : '' }}" data-target="#sm-jadwal">
        <i class="bi bi-calendar2-event me-2"></i><span class="label">Notif Jadwal</span>
        <span class="caret label"><i class="bi bi-chevron-down"></i></span>
      </button>
      <div class="submenu {{ $isJadwal ? 'show' : '' }}" id="sm-jadwal">
        <a href="{{ route('admin.pesan_template.index') }}" class="{{ request()->routeIs('admin.pesan_template.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text me-2"></i><span class="label">Template Pesan</span></a>
        <a href="{{ route('admin.jadwal_notif.index') }}" class="{{ request()->routeIs('admin.jadwal_notif.*') ? 'active' : '' }}"><i class="bi bi-clock me-2"></i><span class="label">Pengaturan H-N</span></a>
      </div>
    @endif

    @if(auth()->user()->isGuru())
      <div class="group">Mengajar</div>
      <a href="{{ route('guru.kelas.index') }}" class="{{ request()->routeIs('guru.kelas.*') ? 'active' : '' }}"><i class="bi bi-easel me-2"></i><span class="label">Kelas Saya</span></a>
      <a href="{{ route('guru.siswa.index') }}" class="{{ request()->routeIs('guru.siswa.*') ? 'active' : '' }}"><i class="bi bi-people me-2"></i><span class="label">Siswa Saya</span></a>
      <a href="{{ route('guru.pesan.form') }}" class="{{ request()->routeIs('guru.pesan.*') ? 'active' : '' }}"><i class="bi bi-send me-2"></i><span class="label">Kirim Pesan</span></a>
    @endif

    @if(auth()->user()->isSiswa())
      <div class="group">Belajar</div>
      <a href="{{ route('siswa.kelas.index') }}" class="{{ request()->routeIs('siswa.kelas.*') ? 'active' : '' }}"><i class="bi bi-easel me-2"></i><span class="label">Kelas Saya</span></a>
      <a href="{{ route('siswa.tagihan.index') }}" class="{{ request()->routeIs('siswa.tagihan.*') ? 'active' : '' }}"><i class="bi bi-receipt me-2"></i><span class="label">Tagihan Saya</span></a>
    @endif

    @if(auth()->user()->isSiswa() || auth()->user()->isGuru())
      <div class="group">Akun Saya</div>
      <a href="{{ route('notifikasi.index') }}" class="{{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
        <i class="bi bi-bell me-2"></i><span class="label">Notifikasi</span>
      </a>
    @endif
  </aside>

  <div class="content-wrap">
    <nav class="navbar navbar-app px-3 py-2">
      <div class="d-flex align-items-center w-100 gap-2">
        <button type="button" class="btn btn-light btn-icon" id="btnSidebar" title="Menu">
          <i class="bi bi-list"></i>
        </button>
        <h6 class="topbar-title d-none d-sm-block">@yield('title','Dashboard')</h6>
        <div class="ms-auto d-flex align-items-center gap-2">
          @php $unread = auth()->user()->unreadNotifications->count(); @endphp
          <a href="{{ route('notifikasi.index') }}" class="btn btn-light btn-icon position-relative nav-bell" title="Notifikasi">
            <i class="bi bi-bell"></i>
            @if($unread > 0)
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unread }}</span>
            @endif
          </a>
          <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i>
              <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
              <span class="badge bg-secondary ms-1">{{ ucfirst(auth()->user()->role) }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><span class="dropdown-item-text small text-muted d-md-none">{{ auth()->user()->name }}</span></li>
              <li><hr class="dropdown-divider d-md-none"></li>
              <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item"><i class="bi bi-box-arrow-right me-1"></i>Logout</button></form></li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <main class="p-4 app-main">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif

      @yield('content')
    </main>
  </div>
</div>
@else
  @yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  const sb = document.getElementById('appSidebar');
  const bd = document.getElementById('sbBackdrop');
  const btn = document.getElementById('btnSidebar');
  if (!sb || !btn) return;

  const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;

  // Restore desktop preference
  if (!isMobile() && localStorage.getItem('sb-mini') === '1') sb.classList.add('mini');

  btn.addEventListener('click', () => {
    if (isMobile()) {
      sb.classList.toggle('open');
      bd.classList.toggle('show');
    } else {
      sb.classList.toggle('mini');
      localStorage.setItem('sb-mini', sb.classList.contains('mini') ? '1' : '0');
    }
  });

  bd.addEventListener('click', () => {
    sb.classList.remove('open');
    bd.classList.remove('show');
  });

  // Submenu accordion
  document.querySelectorAll('.menu-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      if (sb.classList.contains('mini') && !isMobile()) {
        sb.classList.remove('mini');
        localStorage.setItem('sb-mini','0');
      }
      const sel = btn.getAttribute('data-target');
      const target = document.querySelector(sel);
      const willOpen = !target.classList.contains('show');
      // close others
      document.querySelectorAll('.sidebar .submenu').forEach(s => s.classList.remove('show'));
      document.querySelectorAll('.menu-toggle').forEach(b => b.classList.remove('open'));
      if (willOpen) {
        target.classList.add('show');
        btn.classList.add('open');
      }
    });
  });
})();
</script>
@stack('scripts')
</body>
</html>
