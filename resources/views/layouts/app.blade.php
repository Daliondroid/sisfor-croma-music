<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Croma Music')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <style>
        :root {
            --primary-blue: #0056b3;
            --primary-dark: #003d80;
            --primary-yellow: #ffcc00;
            --accent-yellow: #ffdb4d;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-white: #ffffff;
            --bg-light: #f3f4f6;
            --sidebar-width: 260px;
            --header-height: 64px;
            --radius: 12px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; color:var(--text-dark); background:var(--bg-light); }
        a { text-decoration:none; color:inherit; transition:.2s; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top:0; left:0; width:var(--sidebar-width);
            height:100vh; background:var(--primary-dark); color:#fff;
            display:flex; flex-direction:column; z-index:100;
            transition: transform .3s;
        }
        .sidebar-brand {
            display:flex; align-items:center; gap:12px;
            padding:20px 24px; border-bottom:1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand img { width:36px; height:36px; border-radius:8px; object-fit:cover; }
        .sidebar-brand span { font-weight:700; font-size:1rem; letter-spacing:.5px; }
        .sidebar-nav { flex:1; padding:16px 0; overflow-y:auto; }
        .nav-section-label {
            font-size:.65rem; font-weight:600; letter-spacing:1.2px;
            color:rgba(255,255,255,.4); padding:16px 24px 6px; text-transform:uppercase;
        }
        .nav-item {
            display:flex; align-items:center; gap:12px;
            padding:11px 24px; font-size:.875rem; font-weight:500;
            color:rgba(255,255,255,.75); border-left:3px solid transparent;
            transition:.2s;
        }
        .nav-item:hover, .nav-item.active {
            background:rgba(255,255,255,.08); color:#fff;
            border-left-color:var(--primary-yellow);
        }
        .nav-item i { width:18px; text-align:center; font-size:.9rem; }
        .sidebar-footer {
            padding:16px 24px; border-top:1px solid rgba(255,255,255,.1);
        }
        .sidebar-user { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
        .avatar {
            width:36px; height:36px; border-radius:50%;
            background:var(--primary-yellow); color:var(--primary-dark);
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:.875rem; flex-shrink:0;
        }
        .sidebar-user-info { flex:1; min-width:0; }
        .sidebar-user-name { font-size:.8rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .sidebar-user-role { font-size:.7rem; color:rgba(255,255,255,.5); }
        .btn-logout {
            display:flex; align-items:center; gap:8px; width:100%;
            padding:9px 14px; border-radius:8px; font-size:.8rem; font-weight:500;
            background:rgba(255,255,255,.08); color:rgba(255,255,255,.75);
            border:none; cursor:pointer; transition:.2s;
        }
        .btn-logout:hover { background:rgba(220,53,69,.2); color:#ff6b6b; }

        /* ── Main ── */
        .main-wrapper { margin-left:var(--sidebar-width); min-height:100vh; display:flex; flex-direction:column; }
        .topbar {
            height:var(--header-height); background:#fff; border-bottom:1px solid #e5e7eb;
            display:flex; align-items:center; justify-content:space-between;
            padding:0 28px; position:sticky; top:0; z-index:50;
        }
        .topbar-title { font-size:1.1rem; font-weight:600; }
        .topbar-right { display:flex; align-items:center; gap:16px; }
        .notif-btn {
            position:relative; width:38px; height:38px; border-radius:50%;
            background:var(--bg-light); border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center; color:var(--text-dark);
        }
        .notif-badge {
            position:absolute; top:6px; right:6px; width:8px; height:8px;
            background:#ef4444; border-radius:50%; border:2px solid #fff;
        }
        .main-content { flex:1; padding:28px; }

        /* ── Cards ── */
        .card { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow-sm); }
        .card-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:18px 24px; border-bottom:1px solid #f0f0f0;
        }
        .card-header h3 { font-size:1rem; font-weight:600; }
        .card-body { padding:24px; }

        /* ── Stats ── */
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:24px; }
        .stat-card {
            background:#fff; border-radius:var(--radius); padding:20px 24px;
            box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;
        }
        .stat-icon {
            width:52px; height:52px; border-radius:12px;
            display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0;
        }
        .stat-icon.blue  { background:#eff6ff; color:var(--primary-blue); }
        .stat-icon.yellow{ background:#fffbeb; color:#d97706; }
        .stat-icon.green { background:#f0fdf4; color:#16a34a; }
        .stat-icon.red   { background:#fef2f2; color:#dc2626; }
        .stat-value { font-size:1.6rem; font-weight:700; line-height:1; }
        .stat-label { font-size:.78rem; color:var(--text-light); margin-top:4px; }

        /* ── Table ── */
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:.875rem; }
        th { background:#f8f9fa; padding:12px 16px; text-align:left; font-weight:600; font-size:.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px; }
        td { padding:13px 16px; border-bottom:1px solid #f3f4f6; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#fafafa; }

        /* ── Badges ── */
        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:50px; font-size:.72rem; font-weight:600; }
        .badge-success { background:#dcfce7; color:#16a34a; }
        .badge-warning { background:#fef9c3; color:#a16207; }
        .badge-danger  { background:#fee2e2; color:#dc2626; }
        .badge-info    { background:#dbeafe; color:#1d4ed8; }
        .badge-gray    { background:#f3f4f6; color:#6b7280; }

        /* ── Buttons ── */
        .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 20px; border-radius:8px; font-weight:600; font-size:.875rem; cursor:pointer; border:none; transition:.2s; }
        .btn-primary { background:var(--primary-blue); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-yellow { background:var(--primary-yellow); color:var(--primary-dark); }
        .btn-yellow:hover { background:var(--accent-yellow); }
        .btn-sm { padding:6px 14px; font-size:.78rem; }
        .btn-outline { background:transparent; border:1.5px solid var(--primary-blue); color:var(--primary-blue); }
        .btn-outline:hover { background:var(--primary-blue); color:#fff; }
        .btn-danger { background:#dc2626; color:#fff; }
        .btn-danger:hover { background:#b91c1c; }

        /* ── Forms ── */
        .form-group { margin-bottom:18px; }
        .form-label { display:block; font-size:.85rem; font-weight:500; margin-bottom:6px; }
        .form-control {
            width:100%; padding:10px 14px; border:1.5px solid #e5e7eb;
            border-radius:8px; font-size:.875rem; font-family:inherit;
            transition:.2s; background:#fff;
        }
        .form-control:focus { outline:none; border-color:var(--primary-blue); box-shadow:0 0 0 3px rgba(0,86,179,.1); }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }

        /* ── Alert ── */
        .alert { padding:12px 16px; border-radius:8px; font-size:.875rem; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#dcfce7; color:#15803d; border-left:4px solid #16a34a; }
        .alert-danger  { background:#fee2e2; color:#b91c1c; border-left:4px solid #dc2626; }
        .alert-warning { background:#fef9c3; color:#92400e; border-left:4px solid #f59e0b; }

        /* ── Page header ── */
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
        .page-header h2 { font-size:1.4rem; font-weight:700; }
        .breadcrumb { font-size:.78rem; color:var(--text-light); margin-top:2px; }
        .breadcrumb span { color:var(--primary-blue); }

        /* ── Mobile ── */
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:99; }
        @media(max-width:768px) {
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .sidebar-overlay.open { display:block; }
            .main-wrapper { margin-left:0; }
            .form-grid, .form-grid-3 { grid-template-columns:1fr; }
            .stats-grid { grid-template-columns:1fr 1fr; }
            .mobile-menu-btn { display:flex !important; }
        }
        .mobile-menu-btn {
            display:none; width:38px; height:38px; border-radius:8px;
            background:var(--bg-light); border:none; cursor:pointer;
            align-items:center; justify-content:center; font-size:1.1rem;
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/croma_logo.jpg') }}" alt="Croma Music"/>
        <span>CROMA MUSIC</span>
    </div>
    <nav class="sidebar-nav">
        @yield('sidebar-menu')
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? auth()->user()->username, 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? auth()->user()->username }}</div>
                <div class="sidebar-user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </button>
        </form>
    </div>
</aside>

<!-- Main -->
<div class="main-wrapper">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:12px">
            <button class="mobile-menu-btn" id="mobile-menu-btn"><i class="fa-solid fa-bars"></i></button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <button class="notif-btn" title="Notifikasi">
                <i class="fa-regular fa-bell"></i>
                @if(auth()->user()->notifikasis()->where('status_baca','belum_dibaca')->count())
                    <span class="notif-badge"></span>
                @endif
            </button>
        </div>
    </header>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });
</script>
@stack('scripts')
</body>
</html>
