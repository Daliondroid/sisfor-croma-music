<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Croma Music')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        /* ── Theme Variables ── */
        :root,
        [data-theme="light"] {
            --primary-blue: #0056b3;
            --primary-dark: #003d80;
            --primary-yellow: #ffcc00;
            --accent-yellow: #ffdb4d;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-white: #ffffff;
            --bg-light: #f3f4f6;
            --sidebar-bg: #003d80;
            --sidebar-nav-hover: rgba(255,255,255,.08);
            --sidebar-text: rgba(255,255,255,.75);
            --sidebar-section-label: rgba(255,255,255,.4);
            --sidebar-border: rgba(255,255,255,.1);
            --topbar-bg: #ffffff;
            --topbar-border: #e5e7eb;
            --card-bg: #ffffff;
            --table-hover: #fafafa;
            --th-bg: #f8f9fa;
            --input-border: #e5e7eb;
            --sidebar-width: 260px;
            --header-height: 64px;
            --radius: 12px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
        }
        [data-theme="dark"] {
            --primary-blue: #4da3ff;
            --primary-dark: #2d8ae0;
            --primary-yellow: #ffcc00;
            --accent-yellow: #ffdb4d;
            --text-dark: #f1f5f9;
            --text-light: #94a3b8;
            --bg-white: #1e2433;
            --bg-light: #141927;
            --sidebar-bg: #0d1221;
            --sidebar-nav-hover: rgba(255,255,255,.06);
            --sidebar-text: rgba(255,255,255,.65);
            --sidebar-section-label: rgba(255,255,255,.3);
            --sidebar-border: rgba(255,255,255,.08);
            --topbar-bg: #1e2433;
            --topbar-border: #2d3748;
            --card-bg: #1e2433;
            --table-hover: #252d3d;
            --th-bg: #252d3d;
            --input-border: #2d3748;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; color:var(--text-dark); background:var(--bg-light); transition: background .3s, color .3s; }
        a { text-decoration:none; color:inherit; transition:.2s; }

        /* ── Sidebar ── */
        .sidebar {
            position:fixed; top:0; left:0; width:var(--sidebar-width);
            height:100vh; background:var(--sidebar-bg); color:#fff;
            display:flex; flex-direction:column; z-index:100; transition:transform .3s;
        }
        .sidebar-brand {
            display:flex; align-items:center; gap:12px;
            padding:20px 24px; border-bottom:1px solid var(--sidebar-border);
            flex-shrink:0;
        }
        .sidebar-brand img { width:36px; height:36px; border-radius:8px; object-fit:cover; }
        .sidebar-brand span { font-weight:700; font-size:1rem; letter-spacing:.5px; }
        .sidebar-nav {
            flex:1; padding:16px 0;
            overflow-y:auto;
            overflow-x:hidden;
            scrollbar-width:none; /* Firefox */
            -ms-overflow-style:none; /* IE/Edge */
        }
        .sidebar-nav::-webkit-scrollbar { display:none; } /* Chrome/Safari */
        .nav-section-label {
            font-size:.65rem; font-weight:600; letter-spacing:1.2px;
            color:var(--sidebar-section-label); padding:16px 24px 6px; text-transform:uppercase;
        }
        .nav-item {
            display:flex; align-items:center; gap:12px;
            padding:11px 24px; font-size:.875rem; font-weight:500;
            color:var(--sidebar-text); border-left:3px solid transparent; transition:.2s;
        }
        .nav-item:hover, .nav-item.active {
            background:var(--sidebar-nav-hover); color:#fff;
            border-left-color:var(--primary-yellow);
        }
        .nav-item i { width:18px; text-align:center; font-size:.9rem; }
        .sidebar-footer { padding:16px 24px; border-top:1px solid var(--sidebar-border); flex-shrink:0; }

        /* ── Topbar ── */
        .main-wrapper { margin-left:var(--sidebar-width); min-height:100vh; display:flex; flex-direction:column; }
        .topbar {
            height:var(--header-height); background:var(--topbar-bg); border-bottom:1px solid var(--topbar-border);
            display:flex; align-items:center; justify-content:space-between;
            padding:0 28px; position:sticky; top:0; z-index:50; transition:background .3s, border-color .3s;
        }
        .topbar-title { font-size:1.1rem; font-weight:600; }
        .topbar-right { display:flex; align-items:center; gap:10px; }

        /* Notif button */
        .notif-btn {
            position:relative; width:38px; height:38px; border-radius:50%;
            background:var(--bg-light); border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center; color:var(--text-dark);
            transition:.2s;
        }
        .notif-btn:hover { background:var(--input-border); }
        .notif-badge {
            position:absolute; top:6px; right:6px; width:8px; height:8px;
            background:#ef4444; border-radius:50%; border:2px solid var(--topbar-bg);
        }

        /* ── Theme toggle ── */
        .theme-toggle-btn {
            width:38px; height:38px; border-radius:50%;
            background:var(--bg-light); border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            color:var(--text-dark); transition:.2s; font-size:.95rem;
        }
        .theme-toggle-btn:hover { background:var(--input-border); }

        /* ── User dropdown ── */
        .user-dropdown-wrap { position:relative; }
        .user-trigger {
            display:flex; align-items:center; gap:10px; cursor:pointer;
            padding:6px 10px; border-radius:8px; border:none; background:transparent;
            font-family:inherit; transition:.2s;
        }
        .user-trigger:hover { background:var(--bg-light); }
        .avatar {
            width:34px; height:34px; border-radius:50%;
            background:var(--primary-blue); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:.8rem; flex-shrink:0;
        }
        .user-trigger-name { font-size:.85rem; font-weight:600; color:var(--text-dark); }
        .user-trigger-role { font-size:.7rem; color:var(--text-light); line-height:1; }
        .user-trigger-caret { color:var(--text-light); font-size:.7rem; margin-left:2px; }

        .user-dropdown {
            position:absolute; top:calc(100% + 8px); right:0;
            background:var(--card-bg); border-radius:10px;
            box-shadow:0 8px 24px rgba(0,0,0,.18);
            border:1px solid var(--topbar-border);
            width:230px; z-index:200;
            display:none; flex-direction:column; overflow:hidden;
        }
        .user-dropdown.open { display:flex; }
        .dropdown-header {
            padding:14px 16px; border-bottom:1px solid var(--topbar-border);
            background:var(--bg-light);
        }
        .dropdown-header-name { font-weight:700; font-size:.9rem; color:var(--text-dark); }
        .dropdown-header-email { font-size:.72rem; color:var(--text-light); margin-top:2px; }
        .dropdown-item {
            display:flex; align-items:center; gap:10px;
            padding:11px 16px; font-size:.85rem; color:var(--text-dark);
            transition:.15s; cursor:pointer; border:none; background:transparent;
            width:100%; text-align:left; font-family:inherit;
        }
        .dropdown-item:hover { background:var(--bg-light); }
        .dropdown-item i { width:16px; color:var(--text-light); font-size:.85rem; }
        .dropdown-divider { height:1px; background:var(--topbar-border); margin:4px 0; }

        /* Theme toggle row inside dropdown */
        .dropdown-theme-row {
            display:flex; align-items:center; justify-content:space-between;
            padding:10px 16px; font-size:.85rem; color:var(--text-dark);
        }
        .dropdown-theme-row-left { display:flex; align-items:center; gap:10px; }
        .dropdown-theme-row-left i { width:16px; color:var(--text-light); font-size:.85rem; }

        /* Toggle switch */
        .theme-switch { position:relative; width:40px; height:22px; }
        .theme-switch input { opacity:0; width:0; height:0; }
        .theme-switch-slider {
            position:absolute; inset:0; cursor:pointer;
            background:#cbd5e1; border-radius:22px; transition:.3s;
        }
        .theme-switch-slider::before {
            content:''; position:absolute;
            width:16px; height:16px; left:3px; top:3px;
            background:#fff; border-radius:50%; transition:.3s;
        }
        input:checked + .theme-switch-slider { background:var(--primary-blue); }
        input:checked + .theme-switch-slider::before { transform:translateX(18px); }

        .dropdown-item.logout { color:#dc2626; }
        .dropdown-item.logout i { color:#dc2626; }
        .dropdown-item.logout:hover { background:#fef2f2; }
        [data-theme="dark"] .dropdown-item.logout:hover { background:#3d1515; }

        /* ── Main content ── */
        .main-content { flex:1; padding:28px; }

        /* ── Cards ── */
        .card { background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--shadow-sm); }
        .card-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:18px 24px; border-bottom:1px solid var(--topbar-border);
        }
        .card-header h3 { font-size:1rem; font-weight:600; }
        .card-body { padding:24px; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:24px; }
        .stat-card {
            background:var(--card-bg); border-radius:var(--radius); padding:20px 24px;
            box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;
        }
        .stat-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; }
        .stat-icon.blue  { background:#eff6ff; color:var(--primary-blue); }
        .stat-icon.yellow{ background:#fffbeb; color:#d97706; }
        .stat-icon.green { background:#f0fdf4; color:#16a34a; }
        .stat-icon.red   { background:#fef2f2; color:#dc2626; }
        [data-theme="dark"] .stat-icon.blue  { background:#1e3a5f; }
        [data-theme="dark"] .stat-icon.yellow{ background:#3d2e0a; }
        [data-theme="dark"] .stat-icon.green { background:#14312a; }
        [data-theme="dark"] .stat-icon.red   { background:#3d1515; }
        .stat-value { font-size:1.6rem; font-weight:700; line-height:1; }
        .stat-label { font-size:.78rem; color:var(--text-light); margin-top:4px; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:.875rem; }
        th { background:var(--th-bg); padding:12px 16px; text-align:left; font-weight:600; font-size:.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:.5px; }
        td { padding:13px 16px; border-bottom:1px solid var(--topbar-border); color:var(--text-dark); }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:var(--table-hover); }
        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:50px; font-size:.72rem; font-weight:600; }
        .badge-success { background:#dcfce7; color:#16a34a; }
        .badge-warning { background:#fef9c3; color:#a16207; }
        .badge-danger  { background:#fee2e2; color:#dc2626; }
        .badge-info    { background:#dbeafe; color:#1d4ed8; }
        .badge-gray    { background:#f3f4f6; color:#6b7280; }
        [data-theme="dark"] .badge-success { background:#14312a; color:#4ade80; }
        [data-theme="dark"] .badge-warning { background:#3d2e0a; color:#fbbf24; }
        [data-theme="dark"] .badge-danger  { background:#3d1515; color:#f87171; }
        [data-theme="dark"] .badge-info    { background:#1e3a5f; color:#60a5fa; }
        [data-theme="dark"] .badge-gray    { background:#252d3d; color:#94a3b8; }
        .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 20px; border-radius:8px; font-weight:600; font-size:.875rem; cursor:pointer; border:none; transition:.2s; font-family:inherit; }
        .btn-primary { background:var(--primary-blue); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-yellow { background:var(--primary-yellow); color:#1a1a00; }
        .btn-yellow:hover { background:var(--accent-yellow); }
        .btn-sm { padding:6px 14px; font-size:.78rem; }
        .btn-outline { background:transparent; border:1.5px solid var(--primary-blue); color:var(--primary-blue); }
        .btn-outline:hover { background:var(--primary-blue); color:#fff; }
        .btn-danger { background:#dc2626; color:#fff; }
        .btn-danger:hover { background:#b91c1c; }
        .form-group { margin-bottom:18px; }
        .form-label { display:block; font-size:.85rem; font-weight:500; margin-bottom:6px; color:var(--text-dark); }
        .form-control {
            width:100%; padding:10px 14px; border:1.5px solid var(--input-border);
            border-radius:8px; font-size:.875rem; font-family:inherit; transition:.2s;
            background:var(--card-bg); color:var(--text-dark);
        }
        .form-control:focus { outline:none; border-color:var(--primary-blue); box-shadow:0 0 0 3px rgba(0,86,179,.1); }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .alert { padding:12px 16px; border-radius:8px; font-size:.875rem; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#dcfce7; color:#15803d; border-left:4px solid #16a34a; }
        .alert-danger  { background:#fee2e2; color:#b91c1c; border-left:4px solid #dc2626; }
        [data-theme="dark"] .alert-success { background:#14312a; color:#4ade80; }
        [data-theme="dark"] .alert-danger  { background:#3d1515; color:#f87171; }
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
        .page-header h2 { font-size:1.4rem; font-weight:700; }
        .breadcrumb { font-size:.78rem; color:var(--text-light); margin-top:2px; }
        .breadcrumb span { color:var(--primary-blue); }

        /* ── Delete modal ── */
        .delete-modal-backdrop {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);
            z-index:300; align-items:center; justify-content:center;
        }
        .delete-modal-backdrop.open { display:flex; }
        .delete-modal {
            background:var(--card-bg); border-radius:12px; padding:28px;
            width:420px; max-width:92vw; box-shadow:0 20px 48px rgba(0,0,0,.25);
        }
        .delete-modal-icon {
            width:52px; height:52px; border-radius:50%;
            background:#fee2e2; display:flex; align-items:center; justify-content:center;
            margin-bottom:16px;
        }
        [data-theme="dark"] .delete-modal-icon { background:#3d1515; }
        .delete-modal-icon i { color:#dc2626; font-size:1.3rem; }
        .delete-modal h3 { font-size:1.05rem; font-weight:700; margin-bottom:8px; }
        .delete-modal p { font-size:.875rem; color:var(--text-light); margin-bottom:24px; line-height:1.6; }
        .delete-modal-actions { display:flex; gap:10px; justify-content:flex-end; }

        /* Mobile */
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:99; }
        @media(max-width:768px) {
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .sidebar-overlay.open { display:block; }
            .main-wrapper { margin-left:0; }
            .form-grid { grid-template-columns:1fr; }
            .stats-grid { grid-template-columns:1fr 1fr; }
            .mobile-menu-btn { display:flex !important; }
            .user-trigger-name, .user-trigger-role, .user-trigger-caret { display:none; }
        }
        .mobile-menu-btn {
            display:none; width:38px; height:38px; border-radius:8px;
            background:var(--bg-light); border:none; cursor:pointer;
            align-items:center; justify-content:center; font-size:1.1rem;
            color:var(--text-dark);
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/croma_logo.jpg') }}" alt="Croma Music"/>
        <span>CROMA MUSIC</span>
    </div>
    <nav class="sidebar-nav">
        @yield('sidebar-menu')
    </nav>
    <div class="sidebar-footer">
        <div style="font-size:.65rem;color:var(--sidebar-section-label);text-align:center">
            Croma Music &copy; {{ date('Y') }}
        </div>
    </div>
</aside>

<div class="main-wrapper">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:12px">
            <button class="mobile-menu-btn" id="mobile-menu-btn"><i class="fa-solid fa-bars"></i></button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            {{-- Notifikasi --}}
            @php $unread = auth()->user()->notifikasis()->where('status_baca','belum_dibaca')->count(); @endphp
            <a href="{{ route('notifikasi.index') }}" class="notif-btn" title="Notifikasi">
                <i class="fa-regular fa-bell"></i>
                @if($unread) <span class="notif-badge"></span> @endif
            </a>

            {{-- User dropdown --}}
            <div class="user-dropdown-wrap" id="user-dropdown-wrap">
                <button class="user-trigger" id="user-trigger" type="button">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? auth()->user()->username, 0, 1)) }}</div>
                    <div style="text-align:left">
                        <div class="user-trigger-name">{{ auth()->user()->name ?? auth()->user()->username }}</div>
                        <div class="user-trigger-role">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <i class="fa-solid fa-chevron-down user-trigger-caret"></i>
                </button>

                <div class="user-dropdown" id="user-dropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-header-name">{{ auth()->user()->name ?? auth()->user()->username }}</div>
                        <div class="dropdown-header-email">{{ auth()->user()->email }}</div>
                    </div>

                    {{-- Data Akun (menggantikan Dashboard) --}}
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                            <i class="fa-solid fa-id-card"></i> Data Akun
                        </a>
                    @elseif(auth()->user()->role === 'guru')
                        <a href="{{ route('guru.profil.edit') }}" class="dropdown-item">
                            <i class="fa-solid fa-id-card"></i> Data Akun
                        </a>
                    @elseif(auth()->user()->role === 'murid')
                        <a href="{{ route('murid.profil.edit') }}" class="dropdown-item">
                            <i class="fa-solid fa-id-card"></i> Data Akun
                        </a>
                    @endif

                    <a href="{{ route('notifikasi.index') }}" class="dropdown-item">
                        <i class="fa-regular fa-bell"></i> Notifikasi
                        @if($unread)
                            <span style="margin-left:auto;background:#ef4444;color:#fff;border-radius:50px;padding:1px 7px;font-size:.65rem;font-weight:700">{{ $unread }}</span>
                        @endif
                    </a>

                    {{-- Dark/Light Theme Toggle --}}
                    <div class="dropdown-theme-row">
                        <div class="dropdown-theme-row-left">
                            <i class="fa-solid fa-moon" id="theme-icon"></i>
                            <span id="theme-label">Mode Gelap</span>
                        </div>
                        <label class="theme-switch">
                            <input type="checkbox" id="theme-toggle-checkbox"/>
                            <span class="theme-switch-slider"></span>
                        </label>
                    </div>

                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
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
    // ── Sidebar mobile ──
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });

    // ── User dropdown ──
    const trigger  = document.getElementById('user-trigger');
    const dropdown = document.getElementById('user-dropdown');
    trigger?.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
        if (!document.getElementById('user-dropdown-wrap')?.contains(e.target)) {
            dropdown?.classList.remove('open');
        }
    });

    // ── Dark/Light Theme ──
    const html      = document.documentElement;
    const checkbox  = document.getElementById('theme-toggle-checkbox');
    const themeIcon = document.getElementById('theme-icon');
    const themeLabel= document.getElementById('theme-label');

    function applyTheme(isDark) {
        html.setAttribute('data-theme', isDark ? 'dark' : 'light');
        checkbox.checked = isDark;
        themeIcon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        themeLabel.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
        localStorage.setItem('croma-theme', isDark ? 'dark' : 'light');
    }

    // Load saved theme
    const saved = localStorage.getItem('croma-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(saved ? saved === 'dark' : prefersDark);

    checkbox?.addEventListener('change', () => applyTheme(checkbox.checked));
</script>
@stack('scripts')
</body>
</html>