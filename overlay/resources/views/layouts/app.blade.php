<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','Si Kursus') · Si Kursus</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body { background:#f5f7fb; }
  .sidebar { min-height:100vh; background:#ffffff; border-right:1px solid #e6e9ef; }
  .sidebar a { color:#374151; text-decoration:none; display:block; padding:.55rem .9rem; border-radius:.5rem; }
  .sidebar a.active, .sidebar a:hover { background:#eef2ff; color:#4338ca; }
  .sidebar .group { font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; padding:.6rem .9rem .25rem; }
  .brand { font-weight:700; color:#4338ca; padding:1rem .9rem; font-size:1.15rem; }
  .navbar-app { background:#fff; border-bottom:1px solid #e6e9ef; }
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
</style>
@stack('head')
</head>
<body>
@auth
<div class="d-flex">
  <aside class="sidebar p-2" style="width:240px;">
    <div class="brand"><i class="bi bi-mortarboard-fill"></i> Si Kursus</div>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>

    @if(auth()->user()->isAdmin())
    <div class="group">Master</div>
    <a href="{{ route('siswa.index') }}" class="{{ request()->routeIs('siswa.*') ? 'active' : '' }}"><i class="bi bi-people me-2"></i>Siswa</a>
    <a href="{{ route('kelas.index') }}" class="{{ request()->routeIs('kelas.*') ? 'active' : '' }}"><i class="bi bi-easel me-2"></i>Kelas & Tarif</a>
    <a href="{{ route('guru.index') }}" class="{{ request()->routeIs('guru.*') ? 'active' : '' }}"><i class="bi bi-person-badge me-2"></i>Guru</a>

    <div class="group">Keuangan</div>
    <a href="{{ route('tagihan.index') }}" class="{{ request()->routeIs('tagihan.*') ? 'active' : '' }}"><i class="bi bi-receipt me-2"></i>Tagihan</a>
    <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}"><i class="bi bi-bar-chart me-2"></i>Laporan</a>

    <div class="group">Pengingat</div>
    <a href="{{ route('pengingat.template.index') }}" class="{{ request()->routeIs('pengingat.template.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text me-2"></i>Template</a>
    <a href="{{ route('pengingat.setting.index') }}" class="{{ request()->routeIs('pengingat.setting.*') ? 'active' : '' }}"><i class="bi bi-sliders me-2"></i>Pengaturan</a>
    <a href="{{ route('pengingat.kirim.form') }}" class="{{ request()->routeIs('pengingat.kirim.*') ? 'active' : '' }}"><i class="bi bi-send me-2"></i>Kirim Manual</a>
    <a href="{{ route('pengingat.log.index') }}" class="{{ request()->routeIs('pengingat.log.*') ? 'active' : '' }}"><i class="bi bi-clock-history me-2"></i>Log Pengingat</a>
    @endif

    @if(auth()->user()->isSiswa() || auth()->user()->isGuru())
    <div class="group">Akun Saya</div>
    <a href="{{ route('notifikasi.index') }}" class="{{ request()->routeIs('notifikasi.*') ? 'active' : '' }}"><i class="bi bi-bell me-2"></i>Notifikasi</a>
    @endif
  </aside>

  <div class="flex-grow-1">
    <nav class="navbar navbar-app px-3 py-2">
      <div class="d-flex align-items-center w-100">
        <h6 class="mb-0">@yield('title','Dashboard')</h6>
        <div class="ms-auto d-flex align-items-center gap-2">
          @php $unread = auth()->user()->unreadNotifications->count(); @endphp
          <a href="{{ route('notifikasi.index') }}" class="btn btn-light position-relative nav-bell" title="Notifikasi">
            <i class="bi bi-bell"></i>
            @if($unread > 0)
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unread }}</span>
            @endif
          </a>
          <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
              <span class="badge bg-secondary ms-1">{{ ucfirst(auth()->user()->role) }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item"><i class="bi bi-box-arrow-right me-1"></i>Logout</button></form></li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <main class="p-4">
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
@stack('scripts')
</body>
</html>
