<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <script>
        (function() {
            const saved = localStorage.getItem('croma-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-theme', saved ? saved : (prefersDark ? 'dark' : 'light'));
        })();
    </script>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Croma Music')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        /* ── Theme Variables (Aligned with Landing Page Design System) ── */
        :root,
        [data-theme="light"] {
            --primary-navy: #0f2c59;
            --primary-navy-dark: #07132a;
            --primary-blue: #0f2c59;
            --primary-dark: #07132a;
            --accent-gold: #ffb703;
            --accent-gold-hover: #fb8500;
            --accent-glow: rgba(255, 183, 3, 0.35);
            --primary-yellow: #ffb703;
            --accent-yellow: #ffb703;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --text-light: #94a3b8;
            --bg-white: #ffffff;
            --bg-light: #f8fafc;
            --sidebar-bg: #ffffff;
            --sidebar-nav-hover: #f1f5f9;
            --sidebar-text: #475569;
            --sidebar-section-label: #94a3b8;
            --sidebar-border: rgba(15, 23, 42, 0.08);
            --sidebar-active-bg: #eff6ff;
            --sidebar-active-text: var(--primary-navy);
            --topbar-bg: #ffffff; /* Solid White Topbar */
            --topbar-border: rgba(15, 23, 42, 0.08);
            --card-bg: #ffffff;
            --table-hover: #f8fafc;
            --th-bg: #f1f5f9;
            --input-border: #cbd5e1;
            --sidebar-width: 16rem;
            --header-height: 4.25rem;
            --radius: 0.875rem;
            --font-heading: "Outfit", sans-serif;
            --font-body: "Plus Jakarta Sans", sans-serif;
            --shadow-sm: 0 0.125rem 0.25rem rgba(15, 23, 42, 0.04);
            --shadow-md: 0 0.625rem 1.5625rem -0.3125rem rgba(15, 23, 42, 0.08);
        }
        [data-theme="dark"] {
            --primary-navy: #38bdf8;
            --primary-navy-dark: #0f172a;
            --primary-blue: #38bdf8;
            --primary-dark: #0284c7;
            --accent-gold: #ffb703;
            --accent-gold-hover: #fb8500;
            --accent-glow: rgba(255, 183, 3, 0.35);
            --primary-yellow: #ffb703;
            --accent-yellow: #ffb703;
            --text-dark: #f8fafc;
            --text-muted: #cbd5e1;
            --text-light: #94a3b8;
            --bg-white: #1e293b;
            --bg-light: #0f172a;
            --sidebar-bg: #1e293b;
            --sidebar-nav-hover: rgba(255,255,255,.06);
            --sidebar-text: rgba(255,255,255,.7);
            --sidebar-section-label: rgba(255,255,255,.35);
            --sidebar-border: rgba(255,255,255,.08);
            --sidebar-active-bg: rgba(56,189,248,.12);
            --sidebar-active-text: #38bdf8;
            --topbar-bg: #1e293b; /* Solid Dark Navy Topbar */
            --topbar-border: rgba(255,255,255,.08);
            --card-bg: #1e293b;
            --table-hover: #334155;
            --th-bg: #334155;
            --input-border: #475569;
            --font-heading: "Outfit", sans-serif;
            --font-body: "Plus Jakarta Sans", sans-serif;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: var(--font-body); color:var(--text-dark); background:var(--bg-light); transition: background .3s, color .3s; }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); font-weight: 700; color: var(--text-dark); }
        a { text-decoration:none; color:inherit; transition:.2s; }
        :focus-visible { outline: 3px solid var(--accent-gold); outline-offset: 2px; border-radius: 0.375rem; }

        /* ── Sidebar ── */
        .sidebar {
            position:fixed; top:0; left:0; width:var(--sidebar-width);
            height:100vh; background:var(--sidebar-bg);
            display:flex; flex-direction:column; z-index:100; transition:transform .3s, background .3s, border-color .3s;
        }
        .sidebar::after {
            content:''; position:absolute; top:var(--header-height); right:0; bottom:0;
            width:1px; background:var(--sidebar-border); transition:background .3s;
        }
        .sidebar-brand {
            display:flex; align-items:center; gap:0.75rem;
            height:var(--header-height);
            padding:0 1.5rem; border-bottom:1px solid var(--sidebar-border);
            flex-shrink:0;
        }
        .sidebar-brand img { width:2rem; height:2rem; border-radius:0.5rem; object-fit:cover; }
        .sidebar-brand span { font-weight:700; font-size:1rem; letter-spacing:0.03125rem; color:var(--text-dark); }
        .sidebar-nav {
            flex:1; padding:0.5rem 0;
            overflow-y:auto;
            overflow-x:hidden;
            scrollbar-width:none; /* Firefox */
            -ms-overflow-style:none; /* IE/Edge */
        }
        .sidebar-nav::-webkit-scrollbar { display:none; } /* Chrome/Safari */
        .nav-section-label {
            font-size:.65rem; font-weight:600; letter-spacing:0.075rem;
            color:var(--sidebar-section-label); padding:1.5rem 1.5rem 0.5rem; text-transform:uppercase;
        }
        .nav-item {
            display:flex; align-items:center; gap:0.75rem;
            padding:0.5rem 1rem; margin:0.125rem 0.75rem; font-size:.875rem; font-weight:500;
            color:var(--sidebar-text); border-radius:0.5rem; transition:.2s;
        }
        .nav-item:hover {
            background:var(--sidebar-nav-hover); color:var(--text-dark);
        }
        .nav-item.active {
            background:var(--sidebar-active-bg); color:var(--sidebar-active-text); font-weight:600;
        }
        .nav-item i { width:1.25rem; text-align:center; font-size:.9rem; }
        .sidebar-footer { padding:1rem 1.5rem; border-top:1px solid var(--sidebar-border); flex-shrink:0; }

        /* ── Topbar ── */
        .main-wrapper { margin-left:var(--sidebar-width); min-height:100vh; display:flex; flex-direction:column; }
        .topbar {
            height:var(--header-height); background:var(--topbar-bg); border-bottom:1px solid var(--topbar-border);
            display:flex; align-items:center; justify-content:flex-end;
            padding:0 2rem; position:sticky; top:0; z-index:50; transition:background .3s, border-color .3s;
        }
        .topbar-left { display:flex; align-items:center; gap:1rem; }
        .topbar-right { display:flex; align-items:center; gap:0.5rem; }

        /* Notif button */
        .notif-btn {
            position:relative; width:2.5rem; height:2.5rem; border-radius:50%;
            background:var(--bg-light); border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center; color:var(--text-dark);
            transition:.2s;
        }
        .notif-btn:hover { background:var(--input-border); }
        .notif-badge {
            position:absolute; top:0.25rem; right:0.25rem; width:0.5rem; height:0.5rem;
            background:#ef4444; border-radius:50%; border:0.125rem solid var(--topbar-bg);
        }

        /* ── Theme toggle ── */
        .theme-toggle-btn {
            width:2.5rem; height:2.5rem; border-radius:50%;
            background:var(--bg-light); border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            color:var(--text-dark); transition:.2s; font-size:.95rem;
        }
        .theme-toggle-btn:hover { background:var(--input-border); }

        /* ── User dropdown ── */
        .user-dropdown-wrap { position:relative; }
        .user-trigger {
            display:flex; align-items:center; gap:0.5rem; cursor:pointer;
            padding:0.25rem 0.5rem; border-radius:0.5rem; border:none; background:transparent;
            font-family:inherit; transition:.2s;
        }
        .user-trigger:hover { background:var(--bg-light); }
        .avatar {
            width:2rem; height:2rem; border-radius:50%;
            background:var(--primary-blue); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:.8rem; flex-shrink:0;
        }
        .user-trigger-name { font-size:.85rem; font-weight:600; color:var(--text-dark); }
        .user-trigger-role { font-size:.7rem; color:var(--text-light); line-height:1; }
        .user-trigger-caret { color:var(--text-light); font-size:.7rem; margin-left:0.125rem; }

        .user-dropdown {
            position:absolute; top:calc(100% + 0.5rem); right:0;
            background:var(--card-bg); border-radius:0.625rem;
            box-shadow:var(--shadow-md);
            border:1px solid var(--topbar-border);
            width:14.5rem; z-index:200;
            display:none; flex-direction:column; overflow:hidden;
        }
        .user-dropdown.open { display:flex; }
        .dropdown-header {
            padding:1rem 1rem; border-bottom:1px solid var(--topbar-border);
            background:var(--bg-light);
        }
        .dropdown-header-name { font-weight:700; font-size:.9rem; color:var(--text-dark); }
        .dropdown-header-email { font-size:.72rem; color:var(--text-light); margin-top:0.125rem; }
        .dropdown-item {
            display:flex; align-items:center; gap:0.5rem;
            padding:0.5rem 1rem; font-size:.85rem; color:var(--text-dark);
            transition:.15s; cursor:pointer; border:none; background:transparent;
            width:100%; text-align:left; font-family:inherit;
        }
        .dropdown-item:hover { background:var(--bg-light); }
        .dropdown-item i { width:1rem; color:var(--text-light); font-size:.85rem; }
        .dropdown-divider { height:1px; background:var(--topbar-border); margin:0.25rem 0; }

        /* Theme toggle row inside dropdown */
        .dropdown-theme-row {
            display:flex; align-items:center; justify-content:space-between;
            padding:0.5rem 1rem; font-size:.85rem; color:var(--text-dark);
        }
        .dropdown-theme-row-left { display:flex; align-items:center; gap:0.5rem; }
        .dropdown-theme-row-left i { width:1rem; color:var(--text-light); font-size:.85rem; }

        /* Toggle switch */
        .theme-switch { position:relative; width:2.5rem; height:1.5rem; }
        .theme-switch input { opacity:0; width:0; height:0; }
        .theme-switch-slider {
            position:absolute; inset:0; cursor:pointer;
            background:#cbd5e1; border-radius:1.375rem; transition:.3s;
        }
        .theme-switch-slider::before {
            content:''; position:absolute;
            width:1rem; height:1rem; left:0.1875rem; top:0.1875rem;
            background:#fff; border-radius:50%; transition:.3s;
        }
        input:checked + .theme-switch-slider { background:var(--primary-blue); }
        input:checked + .theme-switch-slider::before { transform:translateX(1.125rem); }

        .dropdown-item.logout { color:#dc2626; }
        .dropdown-item.logout i { color:#dc2626; }
        .dropdown-item.logout:hover { background:#fef2f2; }
        [data-theme="dark"] .dropdown-item.logout:hover { background:#3d1515; }

        /* ── Main content ── */
        .main-content { flex:1; padding:2.5rem; }

        /* ── Cards ── */
        .card { background:var(--card-bg); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; }
        .card-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:1.5rem; border-bottom:1px solid var(--topbar-border);
        }
        .card-header h3 { font-size:1rem; font-weight:600; }
        .card-body { padding:1.5rem; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(12.5rem,1fr)); gap:1.5rem; margin-bottom:2rem; }
        .stat-card {
            background:var(--card-bg); border-radius:var(--radius); padding:1.5rem 1.5rem;
            box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:1rem;
        }
        .stat-icon { width:3.5rem; height:3.5rem; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex-shrink:0; }
        .stat-icon.blue  { background:#eff6ff; color:var(--primary-blue); }
        .stat-icon.yellow{ background:#fffbeb; color:#d97706; }
        .stat-icon.green { background:#f0fdf4; color:#16a34a; }
        .stat-icon.red   { background:#fef2f2; color:#dc2626; }
        [data-theme="dark"] .stat-icon.blue  { background:#1e3a5f; }
        [data-theme="dark"] .stat-icon.yellow{ background:#3d2e0a; }
        [data-theme="dark"] .stat-icon.green { background:#14312a; }
        [data-theme="dark"] .stat-icon.red   { background:#3d1515; }
        .stat-value { font-size:1.6rem; font-weight:700; line-height:1; }
        .stat-label { font-size:.78rem; color:var(--text-light); margin-top:0.25rem; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:.875rem; }
        th { background:var(--th-bg); padding:0.5rem 1rem; text-align:left; font-weight:600; font-size:.78rem; color:var(--text-light); text-transform:uppercase; letter-spacing:0.03125rem; }
        td { padding:0.5rem 1rem; border-bottom:1px solid var(--topbar-border); color:var(--text-dark); }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:var(--table-hover); }
        .badge { display:inline-flex; align-items:center; padding:0.25rem 0.5rem; border-radius:3.125rem; font-size:.72rem; font-weight:600; }
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
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:0.625rem; padding:0.75rem 1.75rem; border-radius:0.625rem; font-weight:600; font-size:.95rem; cursor:pointer; border:none; transition:.2s; font-family:inherit; min-height:2.875rem; }
        .btn i { font-size:0.95rem; }
        .btn-primary { background:var(--primary-blue); color:#fff; }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-yellow { background:var(--primary-yellow); color:#1a1a00; font-weight:700; }
        .btn-yellow:hover { background:var(--accent-yellow); }
        .btn-sm { padding:0.5rem 1.25rem; font-size:.875rem; min-height:2.375rem; border-radius:0.5rem; }
        .btn-sm i { font-size:0.85rem; }
        .btn-outline { background:transparent; border:0.09375rem solid var(--primary-blue); color:var(--primary-blue); }
        .btn-outline:hover { background:var(--primary-blue); color:#fff; }
        .btn-danger { background:#dc2626; color:#fff; }
        .btn-danger:hover { background:#b91c1c; }
        .form-group { margin-bottom:1rem; }
        .form-label { display:block; font-size:.85rem; font-weight:500; margin-bottom:0.25rem; color:var(--text-dark); }
        .form-control {
            width:100%; padding:0.5rem 1rem; border:0.09375rem solid var(--input-border);
            border-radius:0.5rem; font-size:.875rem; font-family:inherit; transition:.2s;
            background:var(--card-bg); color:var(--text-dark);
        }
        .form-control:focus { outline:none; border-color:var(--primary-blue); box-shadow:0 0 0 1px var(--primary-blue); }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .alert { padding:1rem 1rem; border-radius:0.5rem; font-size:.875rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem; }
        .alert-success { background:#dcfce7; color:#15803d; border-left:0.25rem solid #16a34a; }
        .alert-danger  { background:#fee2e2; color:#b91c1c; border-left:0.25rem solid #dc2626; }
        [data-theme="dark"] .alert-success { background:#14312a; color:#4ade80; }
        [data-theme="dark"] .alert-danger  { background:#3d1515; color:#f87171; }
        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
        .page-header h2 { font-size:1.4rem; font-weight:700; }
        .page-header-filters { display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap; }
        .page-header-filters .form-control { min-width:10rem; width:auto; }
        .form-control-sm { padding:0.5rem 1rem; font-size:.8rem; }

        /* ── Empty states ── */
        .empty-state {
            display:flex; flex-direction:column; align-items:center;
            justify-content:center; padding:4rem 2rem; text-align:center;
        }
        .empty-state-icon {
            width:5rem; height:5rem; margin-bottom:1.5rem;
        }
        .empty-state-icon svg { width:100%; height:100%; }
        .empty-state-title {
            font-size:1rem; font-weight:600; color:var(--text-dark);
            margin-bottom:0.5rem;
        }
        .empty-state-description {
            font-size:.875rem; color:var(--text-light);
            margin-bottom:1.5rem; max-width:22rem; line-height:1.6;
        }

        /* Legacy breadcrumb (hidden in redesign but kept for compatibility) */
        .breadcrumb { display:none; }

        /* ── Delete modal ── */
        .delete-modal-backdrop {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);
            z-index:300; align-items:center; justify-content:center;
        }
        .delete-modal-backdrop.open { display:flex; }
        .delete-modal {
            background:var(--card-bg); border-radius:0.75rem; padding:2rem;
            width:26.5rem; max-width:92vw; box-shadow:var(--shadow-md);
        }
        .delete-modal-icon {
            width:3.5rem; height:3.5rem; border-radius:50%;
            background:#fee2e2; display:flex; align-items:center; justify-content:center;
            margin-bottom:1rem;
        }
        [data-theme="dark"] .delete-modal-icon { background:#3d1515; }
        .delete-modal-icon i { color:#dc2626; font-size:1.3rem; }
        .delete-modal h3 { font-size:1.05rem; font-weight:700; margin-bottom:0.5rem; }
        .delete-modal p { font-size:.875rem; color:var(--text-light); margin-bottom:1.5rem; line-height:1.6; }
        .delete-modal-actions { display:flex; gap:0.5rem; justify-content:flex-end; }

        /* Mobile */
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:99; }
        @media(max-width:48rem) {
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .sidebar-overlay.open { display:block; }
            .main-wrapper { margin-left:0; }
            .topbar { justify-content:space-between; }
            .form-grid { grid-template-columns:1fr; }
            .stats-grid { grid-template-columns:1fr 1fr; }
            .mobile-menu-btn { display:flex !important; }
            .user-trigger-name, .user-trigger-role, .user-trigger-caret { display:none; }
            .page-header { flex-direction:column; align-items:flex-start; }
            .page-header-filters { width:100%; }
            .page-header-filters .form-control { flex:1; min-width:0; }
        }
        .mobile-menu-btn {
            display:none; width:2.5rem; height:2.5rem; border-radius:0.5rem;
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
        <span>Croma Music</span>
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
        <div class="topbar-left">
            <button class="mobile-menu-btn" id="mobile-menu-btn"><i class="fa-solid fa-bars"></i></button>
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
                        <a href="{{ route('admin.profil.edit') }}" class="dropdown-item">
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
