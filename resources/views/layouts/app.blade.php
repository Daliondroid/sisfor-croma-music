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
    <title>@yield('title', 'Dashboard') - CROMIS</title>
    <meta name="description" content="CROMIS - Croma Music Information System (Sistem Informasi Manajemen Kursus Musik)"/>
    <meta name="robots" content="noindex, nofollow"/>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/croma_logo.jpg') }}"/>
    <link rel="shortcut icon" href="{{ asset('images/croma_logo.jpg') }}"/>
    <link rel="apple-touch-icon" href="{{ asset('images/croma_logo.jpg') }}"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        /* ── Theme Variables ── */
        :root,
        [data-theme="light"] {
            --primary-navy: #0f172a;
            --primary-navy-dark: #070d19;
            --primary-blue: #0f2c59;
            --accent-gold: #f59e0b;
            --accent-gold-hover: #d97706;
            --primary-yellow: #f59e0b;
            --text-dark: #0f172a;
            --text-muted: #334155;
            --text-light: #64748b;
            --bg-white: #ffffff;
            --bg-light: #f8fafc;
            --sidebar-bg: #0f172a;
            --sidebar-nav-hover: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-brand-text: #ffffff;
            --sidebar-section-label: #64748b;
            --sidebar-border: #1e293b;
            --sidebar-active-bg: #f59e0b;
            --sidebar-active-text: #0f172a;
            --topbar-bg: #ffffff;
            --topbar-border: #e2e8f0;
            --card-bg: #ffffff;
            --table-hover: #f8fafc;
            --th-bg: #f8fafc;
            --input-border: #cbd5e1;
            --sidebar-width: 15.5rem;
            --header-height: 4rem;
            --radius: 0.25rem;
            --font-heading: "Outfit", sans-serif;
            --font-body: "Plus Jakarta Sans", sans-serif;
            --shadow-sm: none;
            --shadow-md: none;
            --shadow-lg: none;
        }
        [data-theme="dark"] {
            --primary-navy: #38bdf8;
            --primary-navy-dark: #0f172a;
            --primary-blue: #38bdf8;
            --accent-gold: #f59e0b;
            --accent-gold-hover: #d97706;
            --primary-yellow: #f59e0b;
            --text-dark: #f8fafc;
            --text-muted: #cbd5e1;
            --text-light: #94a3b8;
            --bg-white: #1e293b;
            --bg-light: #0b1120;
            --sidebar-bg: #0f172a;
            --sidebar-nav-hover: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-brand-text: #ffffff;
            --sidebar-section-label: #64748b;
            --sidebar-border: #1e293b;
            --sidebar-active-bg: #f59e0b;
            --sidebar-active-text: #0f172a;
            --topbar-bg: #0f172a;
            --topbar-border: #1e293b;
            --card-bg: #1e293b;
            --table-hover: #334155;
            --th-bg: #0f172a;
            --input-border: #334155;
            --font-heading: "Outfit", sans-serif;
            --font-body: "Plus Jakarta Sans", sans-serif;
            --shadow-sm: none;
            --shadow-md: none;
            --shadow-lg: none;
        }

        /* ── Global Reset & Zero-Shadow Elimination ── */
        * { margin:0; padding:0; box-sizing:border-box; box-shadow: none !important; }
        body { font-family: var(--font-body); color:var(--text-dark); background:var(--bg-light); transition: background .2s, color .2s; }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); font-weight: 700; color: var(--text-dark); letter-spacing: -0.01em; }
        a { text-decoration:none; color:inherit; transition:.15s; }
        :focus-visible { outline: 2px solid var(--accent-gold); outline-offset: 2px; border-radius: 0.25rem; }

        /* ── Sidebar ── */
        .sidebar {
            position:fixed; top:0; left:0; width:var(--sidebar-width);
            height:100vh; background:var(--sidebar-bg);
            display:flex; flex-direction:column; z-index:100; transition:transform .3s, background .2s;
            border-right: 1px solid var(--sidebar-border);
        }
        .sidebar-brand {
            display:flex; align-items:center; gap:0.75rem;
            height:var(--header-height);
            padding:0 1.25rem; border-bottom:1px solid var(--sidebar-border);
            flex-shrink:0;
        }
        .sidebar-brand img { width:2rem; height:2rem; border-radius:0.25rem; object-fit:cover; }
        .sidebar-brand span { font-weight:700; font-size:0.95rem; letter-spacing:0.02em; color:var(--sidebar-brand-text); }
        .sidebar-nav {
            flex:1; padding:0.5rem 0 1rem;
            overflow-y:auto;
            overflow-x:hidden;
            scrollbar-width:none;
            -ms-overflow-style:none;
        }
        .sidebar-nav::-webkit-scrollbar { display:none; }
        .nav-section-label {
            font-size:.65rem; font-weight:700; letter-spacing:0.08em;
            color:var(--sidebar-section-label); padding:1.25rem 1.25rem 0.45rem; text-transform:uppercase;
        }
        .nav-item {
            display:flex; align-items:center;
            padding:0.55rem 0.875rem; margin:0.1875rem 0.625rem 0.375rem; font-size:.85rem; font-weight:500;
            color:var(--sidebar-text); border-radius:0.25rem; transition:.15s;
            background: transparent;
        }
        .nav-item:hover {
            background:var(--sidebar-nav-hover); color:#ffffff;
        }
        .nav-item.active {
            background:var(--sidebar-active-bg) !important; color:var(--sidebar-active-text) !important;
            font-weight:700 !important;
        }
        .sidebar-footer { padding:0.875rem 1.25rem; border-top:1px solid var(--sidebar-border); flex-shrink:0; }

        /* ── Topbar ── */
        .main-wrapper { margin-left:var(--sidebar-width); min-height:100vh; display:flex; flex-direction:column; }
        .topbar {
            height:var(--header-height); background:var(--topbar-bg); border-bottom:1px solid var(--topbar-border);
            display:flex; align-items:center; justify-content:space-between;
            padding:0 1.75rem; position:sticky; top:0; z-index:50; transition:background .2s, border-color .2s;
        }
        .topbar-left { display:flex; align-items:center; gap:0.75rem; }
        .topbar-right { display:flex; align-items:center; gap:0.5rem; }
        .topbar-page-title {
            font-family: var(--font-heading);
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* Notif button */
        .notif-btn {
            position:relative; width:2.25rem; height:2.25rem; border-radius:0.25rem;
            background:var(--bg-light); border:1px solid var(--input-border); cursor:pointer;
            display:flex; align-items:center; justify-content:center; color:var(--text-dark);
            transition:.15s; font-size:0.9rem;
        }
        .notif-btn:hover { background:var(--th-bg); }
        .notif-badge {
            position:absolute; top:0.25rem; right:0.25rem; width:0.45rem; height:0.45rem;
            background:#ef4444; border-radius:50%; border:1px solid var(--topbar-bg);
        }

        /* ── Theme toggle ── */
        .theme-toggle-btn {
            width:2.25rem; height:2.25rem; border-radius:0.25rem;
            background:var(--bg-light); border:1px solid var(--input-border); cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            color:var(--text-dark); transition:.15s; font-size:.9rem;
        }
        .theme-toggle-btn:hover { background:var(--th-bg); }

        /* ── User dropdown ── */
        .user-dropdown-wrap { position:relative; }
        .user-trigger {
            display:flex; align-items:center; gap:0.5rem; cursor:pointer;
            padding:0.25rem 0.5rem; border-radius:0.25rem; border:1px solid transparent; background:transparent;
            font-family:inherit; transition:.15s;
        }
        .user-trigger:hover { background:var(--bg-light); border-color:var(--topbar-border); }
        .avatar {
            width:2rem; height:2rem; border-radius:0.25rem;
            background:var(--primary-navy); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:.8rem; flex-shrink:0;
        }
        .user-trigger-name { font-size:.825rem; font-weight:600; color:var(--text-dark); }
        .user-trigger-role { font-size:.68rem; color:var(--text-light); line-height:1; }
        .user-trigger-caret { color:var(--text-light); font-size:.68rem; margin-left:0.125rem; }

        .user-dropdown {
            position:absolute; top:calc(100% + 0.35rem); right:0;
            background:var(--card-bg); border-radius:0.25rem;
            border:1px solid var(--topbar-border);
            width:14rem; z-index:200;
            display:none; flex-direction:column; overflow:hidden;
        }
        .user-dropdown.open { display:flex; }
        .dropdown-header {
            padding:0.5rem 1rem; border-bottom:1px solid var(--topbar-border);
            background:var(--bg-light);
        }
        .dropdown-header-name { font-weight:700; font-size:.85rem; color:var(--text-dark); }
        .dropdown-header-email { font-size:.72rem; color:var(--text-light); margin-top:0.125rem; }
        .dropdown-item {
            display:block; padding:0.5rem 1rem; font-size:.825rem; color:var(--text-dark);
            transition:.15s; cursor:pointer; border:none; background:transparent;
            width:100%; text-align:left; font-family:inherit;
        }
        .dropdown-item:hover { background:var(--bg-light); }
        .dropdown-divider { height:1px; background:var(--topbar-border); margin:0.25rem 0; }

        .dropdown-item.logout { color:#dc2626; }
        .dropdown-item.logout:hover { background:#fef2f2; }
        [data-theme="dark"] .dropdown-item.logout:hover { background:#3d1515; }

        /* ── Main content (Restricted Max Width for wide viewports) ── */
        .main-content { flex:1; padding:1.75rem 1.75rem; max-width:90rem; width:100%; margin:0 auto; box-sizing:border-box; }

        /* ── Cards & Surfaces (Zero Shadow, 1px Hairline Border, 4px Radius, 1:2 Optical Spacing) ── */
        .card {
            background:var(--card-bg);
            border-radius:0.25rem;
            border:1px solid var(--topbar-border);
            overflow:hidden;
            margin-bottom:1.5rem;
        }
        .card-header {
            display:flex; align-items:center; justify-content:space-between;
            padding:0.75rem 1.5rem; border-bottom:1px solid var(--topbar-border); background:var(--card-bg);
        }
        .card-header h3 { font-size:0.95rem; font-weight:700; color:var(--text-dark); }
        .card-body { padding:1.25rem 2.5rem; background:var(--card-bg); }

        /* ── Streamlined Open KPI Strips (Neutral 1px Border, 28px Numerals, 11px Micro-Labels, 1:2 Ratio) ── */
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(13rem,1fr)); gap:1rem; margin-bottom:1.5rem; }
        .stat-card {
            background:var(--card-bg); border:1px solid var(--topbar-border);
            border-radius:0.25rem;
            padding:1rem 2rem; display:flex; flex-direction:column; justify-content:center; gap:0.375rem;
        }

        .stat-value {
            font-size:1.75rem; /* 28px */
            font-weight:700; line-height:1.1; color:var(--text-dark);
            font-variant-numeric: tabular-nums; font-family: var(--font-heading);
        }
        .stat-label {
            font-size:0.6875rem; /* 11px */
            color:var(--text-light); text-transform:uppercase; letter-spacing:0.08em; font-weight:700;
        }
        
        /* ── Open Table Architecture (Hairline Horizontal Rules, Tabular Figures, 1:2 Ratio) ── */
        .table-wrap { overflow-x:auto; width:100%; }
        table { width:100%; border-collapse:collapse; font-size:.85rem; text-align:left; }
        th {
            background:var(--th-bg); padding:0.625rem 1.25rem; text-align:left;
            font-weight:700; font-size:.6875rem; color:#64748b !important;
            text-transform:uppercase; letter-spacing:0.06em;
            border-bottom:1px solid var(--topbar-border);
        }
        [data-theme="dark"] th { color:#94a3b8 !important; }
        td {
            padding:0.75rem 1.5rem; border-bottom:1px solid var(--topbar-border);
            color:var(--text-dark); vertical-align:middle;
            font-variant-numeric: tabular-nums;
        }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:var(--table-hover); }
        
        /* ── Text-Only Pill Badges (4px Radius, 1:2 Ratio) ── */
        .badge {
            display:inline-flex; align-items:center; justify-content:center;
            padding:0.25rem 0.5rem; border-radius:0.25rem; font-size:.6875rem;
            font-weight:700; letter-spacing:0.04em; text-transform:uppercase; line-height:1.2;
        }
        .badge-success { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
        .badge-warning { background:#fef9c3; color:#a16207; border:1px solid #fef08a; }
        .badge-danger  { background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; }
        .badge-info    { background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; }
        .badge-gray    { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
        [data-theme="dark"] .badge-success { background:#14312a; color:#4ade80; border-color:#166534; }
        [data-theme="dark"] .badge-warning { background:#3d2e0a; color:#fbbf24; border-color:#854d0e; }
        [data-theme="dark"] .badge-danger  { background:#3d1515; color:#f87171; border-color:#991b1b; }
        [data-theme="dark"] .badge-info    { background:#1e3a5f; color:#38bdf8; border-color:#075985; }
        [data-theme="dark"] .badge-gray    { background:#252d3d; color:#94a3b8; border-color:#334155; }

        /* ── Buttons (4px Micro-Radius, 1:2 Ratio) ── */
        .btn {
            display:inline-flex; align-items:center; justify-content:center;
            padding:0.5rem 1rem; border-radius:0.25rem; font-weight:600; font-size:.85rem;
            cursor:pointer; border:1px solid transparent; transition:all .15s ease; font-family:inherit;
            min-height:2.25rem; height:2.25rem; box-sizing:border-box; line-height:1; white-space:nowrap;
        }
        .btn-primary { background:var(--primary-navy); color:#fff; }
        .btn-primary:hover { background:var(--primary-navy-dark); color:#fff; }
        .btn-secondary { background:#334155; color:#fff; }
        .btn-secondary:hover { background:#1e293b; color:#fff; }
        .btn-yellow, .btn-create { background:var(--primary-yellow); color:#0f172a; font-weight:700; border-color:var(--primary-yellow); }
        .btn-yellow:hover, .btn-create:hover { background:var(--accent-gold-hover); color:#0f172a; border-color:var(--accent-gold-hover); }
        .btn-sm { padding:0.375rem 0.75rem; font-size:.8rem; min-height:2.25rem; height:2.25rem; border-radius:0.25rem; }
        .btn-outline { background:transparent; border:1px solid var(--input-border); color:var(--text-dark); font-weight:600; }
        .btn-outline:hover { background:var(--bg-light); border-color:#94a3b8; }
        [data-theme="dark"] .btn-outline { border-color:#475569; color:#e2e8f0; }
        [data-theme="dark"] .btn-outline:hover { background:#334155; border-color:#64748b; color:#fff; }
        .btn-danger { background:#dc2626; color:#fff; }
        .btn-danger:hover { background:#b91c1c; }

        /* ── Table Action Consolidated Dropdown (Portal / Fixed Positioning, 1:2 Ratio) ── */
        .action-dropdown-wrap { position:relative; display:inline-block; text-align:left; }
        .btn-action-dropdown {
            display:inline-flex; align-items:center; justify-content:center; gap:0.25rem;
            height:2rem; min-height:2rem; padding:0.375rem 0.75rem; font-size:0.78rem; font-weight:600;
            color:var(--text-dark); background:var(--card-bg); border:1px solid var(--input-border);
            border-radius:0.25rem; cursor:pointer; transition:0.15s; font-family:inherit;
        }
        .btn-action-dropdown:hover { background:var(--bg-light); border-color:#94a3b8; }
        .btn-action-dropdown.active { background:var(--bg-light); border-color:var(--text-dark); }
        .action-dropdown-menu {
            position:fixed;
            background:var(--card-bg); border:1px solid var(--topbar-border);
            border-radius:0.25rem; width:10.5rem; z-index:9999;
            display:none; flex-direction:column; padding:0.25rem 0;
            box-sizing:border-box;
        }
        .action-dropdown-menu.open { display:flex; }
        .action-dropdown-item {
            display:block; width:100%; padding:0.375rem 0.75rem; font-size:0.8rem; font-weight:500;
            color:var(--text-dark); text-align:left; background:none; border:none; cursor:pointer;
            text-decoration:none; font-family:inherit; transition:0.15s;
        }
        .action-dropdown-item:hover { background:var(--bg-light); }
        .action-dropdown-item.danger { color:#dc2626; }
        .action-dropdown-item.danger:hover { background:#fef2f2; color:#b91c1c; }
        [data-theme="dark"] .action-dropdown-item.danger:hover { background:#3d1515; }
        .action-dropdown-divider { height:1px; background:var(--topbar-border); margin:0.25rem 0; }

        /* ── Topbar Breadcrumbs (Text-Only) ── */
        .topbar-breadcrumb { display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; font-weight:500; }
        .topbar-breadcrumb .crumb-root { color:#64748b; }
        [data-theme="dark"] .topbar-breadcrumb .crumb-root { color:#94a3b8; }
        .topbar-breadcrumb .crumb-sep { font-size:0.75rem; color:#94a3b8; }
        .topbar-breadcrumb .crumb-current { color:var(--text-dark); font-weight:700; }

        /* ── Form Controls & Filter Bars (4px Micro-Radius, 1:2 Ratio) ── */
        .form-group { margin-bottom:1.125rem; }
        .form-label { display:block; font-size:.825rem; font-weight:600; margin-bottom:0.35rem; color:var(--text-dark); }
        .form-control {
            width:100%; padding:0.5rem 1rem; border:1px solid var(--input-border);
            border-radius:0.25rem; font-size:.85rem; font-family:inherit; transition:.15s;
            background:var(--card-bg); color:var(--text-dark); height:2.25rem; min-height:2.25rem; box-sizing:border-box;
        }
        .form-control:focus { outline:none; border-color:var(--accent-gold); }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.125rem; }
        
        .alert { padding:0.75rem 1.5rem; border-radius:0.25rem; font-size:.85rem; margin-bottom:1.25rem; border:1px solid transparent; }
        .alert-success { background:#dcfce7; color:#15803d; border-color:#bbf7d0; }
        .alert-danger  { background:#fee2e2; color:#b91c1c; border-color:#fecaca; }
        [data-theme="dark"] .alert-success { background:#14312a; color:#4ade80; border-color:#166534; }
        [data-theme="dark"] .alert-danger  { background:#3d1515; color:#f87171; border-color:#991b1b; }

        .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem; }
        .page-header h2 { font-size:1.35rem; font-weight:700; }
        .page-header-filters { display:flex; gap:0.625rem; align-items:center; flex-wrap:wrap; }
        .page-header-filters .form-control,
        .page-header-filters select,
        .page-header-filters input,
        .page-header-filters .btn {
            height:2.25rem; min-height:2.25rem; border-radius:0.25rem;
            box-sizing:border-box; padding-top:0; padding-bottom:0;
            display:inline-flex; align-items:center;
        }
        .page-header-filters .form-control { min-width:9rem; width:auto; }
        .form-control-sm { padding:0.375rem 0.75rem; font-size:.825rem; height:2.25rem; }

        /* ── Empty states ── */
        .empty-state {
            display:flex; flex-direction:column; align-items:center;
            justify-content:center; padding:3.5rem 1.5rem; text-align:center;
        }
        .empty-state-title {
            font-size:0.95rem; font-weight:700; color:var(--text-dark);
            margin-bottom:0.35rem;
        }
        .empty-state-description {
            font-size:.825rem; color:var(--text-light);
            margin-bottom:1.25rem; max-width:22rem; line-height:1.5;
        }

        /* Legacy breadcrumb hidden */
        .breadcrumb { display:none !important; }

        /* ── Delete modal (4px Radius, Zero Shadow, 1px Border) ── */
        .delete-modal-backdrop {
            display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6);
            z-index:300; align-items:center; justify-content:center; backdrop-filter:blur(2px);
        }
        .delete-modal-backdrop.open { display:flex; }
        .delete-modal {
            background:var(--card-bg); border-radius:0.25rem; padding:1.75rem;
            width:25rem; max-width:92vw; border:1px solid var(--topbar-border);
        }
        .delete-modal h3 { font-size:1.1rem; font-weight:700; margin-bottom:0.5rem; }
        .delete-modal p { font-size:.85rem; color:var(--text-light); margin-bottom:1.25rem; line-height:1.5; }
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
        .dashboard-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        @media(max-width:60rem) {
            .dashboard-grid-2 { grid-template-columns: 1fr; }
        }

        .mobile-menu-btn {
            display:none; width:2.25rem; height:2.25rem; border-radius:0.25rem;
            background:var(--bg-light); border:1px solid var(--input-border); cursor:pointer;
            align-items:center; justify-content:center; font-size:1rem;
            color:var(--text-dark);
        }
    </style>
    @stack('styles')
</head>
<body>
<a href="#main-content" class="skip-to-content" style="position:absolute;top:-100%;left:1rem;z-index:9999;padding:.4rem 1rem;background:var(--primary-navy);color:#fff;font-family:var(--font-heading);font-weight:600;font-size:.85rem;border-radius:0 0 .25rem .25rem;text-decoration:none;transition:top .2s ease" onfocus="this.style.top='0'" onblur="this.style.top='-100%'">Lewati ke konten utama</a>

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
        <div style="font-size:.7rem;color:var(--sidebar-section-label);text-align:center">
            Croma Music &copy; {{ date('Y') }}
        </div>
    </div>
</aside>

<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-left">
            <button class="mobile-menu-btn" id="mobile-menu-btn"><i class="fa-solid fa-bars"></i></button>
            <nav class="topbar-breadcrumb" aria-label="Breadcrumb Navigasi">
                @hasSection('breadcrumb')
                    @yield('breadcrumb')
                @else
                    <span class="crumb-root">Portal</span>
                    <span class="crumb-sep">/</span>
                    <span class="crumb-current">@yield('page-title', 'Dashboard')</span>
                @endif
            </nav>
        </div>
        <div class="topbar-right">
            {{-- Persistent Dark / Light toggle --}}
            <button class="theme-toggle-btn" id="theme-toggle-btn" title="Ganti tema" aria-label="Ganti ke mode gelap">
                <i class="fa-solid fa-moon" id="theme-icon"></i>
            </button>
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

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.profil.edit') }}" class="dropdown-item">Data Akun</a>
                    @elseif(auth()->user()->role === 'guru')
                        <a href="{{ route('guru.profil.edit') }}" class="dropdown-item">Data Akun</a>
                    @elseif(auth()->user()->role === 'murid')
                        <a href="{{ route('murid.profil.edit') }}" class="dropdown-item">Data Akun</a>
                    @endif

                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item logout">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content" id="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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
    overlay?.addEventListener('click', () => {
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

    // ── Table Action Dropdowns (Fixed Positioning Portal Strategy) ──
    function positionActionDropdown(btn, menu) {
        const rect = btn.getBoundingClientRect();
        const menuWidth = menu.offsetWidth || 168;
        const gap = 4;
        
        // Align right edge of menu to right edge of trigger button
        let left = rect.right - menuWidth;
        let top = rect.bottom + gap;
        
        // Ensure within viewport boundaries horizontally
        if (left < 10) left = 10;
        if (left + menuWidth > window.innerWidth - 10) {
            left = window.innerWidth - menuWidth - 10;
        }
        
        // Ensure within viewport boundaries vertically (flip above button if overflowing bottom)
        const menuHeight = menu.offsetHeight || 150;
        if (top + menuHeight > window.innerHeight - 10) {
            top = Math.max(10, rect.top - menuHeight - gap);
        }
        
        menu.style.position = 'fixed';
        menu.style.top = top + 'px';
        menu.style.left = left + 'px';
        menu.style.right = 'auto';
        menu.style.zIndex = '9999';
    }

    function toggleActionDropdown(btn, event) {
        if (event) event.stopPropagation();
        const wrap = btn.closest('.action-dropdown-wrap');
        const menu = wrap ? wrap.querySelector('.action-dropdown-menu') : null;
        if (!menu) return;

        const isCurrentlyOpen = menu.classList.contains('open');

        // Close all other action dropdowns
        document.querySelectorAll('.action-dropdown-menu.open').forEach(el => {
            el.classList.remove('open');
        });
        document.querySelectorAll('.btn-action-dropdown.active').forEach(el => {
            el.classList.remove('active');
        });

        if (!isCurrentlyOpen) {
            menu.classList.add('open');
            btn.classList.add('active');
            positionActionDropdown(btn, menu);
        }
    }

    function closeAllActionDropdowns() {
        document.querySelectorAll('.action-dropdown-menu.open').forEach(el => {
            el.classList.remove('open');
        });
        document.querySelectorAll('.btn-action-dropdown.active').forEach(el => {
            el.classList.remove('active');
        });
    }

    // Close dropdowns on outside click
    document.addEventListener('click', (e) => {
        if (!document.getElementById('user-dropdown-wrap')?.contains(e.target)) {
            dropdown?.classList.remove('open');
        }
        if (!e.target.closest('.action-dropdown-wrap') && !e.target.closest('.action-dropdown-menu')) {
            closeAllActionDropdowns();
        }
    });

    // Close action dropdowns on window scroll or resize to prevent detached floating menus
    window.addEventListener('scroll', (e) => {
        if (!e.target || !e.target.closest || !e.target.closest('.action-dropdown-menu')) {
            closeAllActionDropdowns();
        }
    }, true);
    window.addEventListener('resize', closeAllActionDropdowns);

    // ── Dark/Light Theme (persistent topbar button) ──
    const html = document.documentElement;
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const themeIcon = document.getElementById('theme-icon');

    function applyTheme(isDark) {
        html.setAttribute('data-theme', isDark ? 'dark' : 'light');
        if (themeIcon) themeIcon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        if (themeToggleBtn) {
            themeToggleBtn.setAttribute('aria-label', isDark ? 'Ganti ke mode terang' : 'Ganti ke mode gelap');
            themeToggleBtn.setAttribute('title', isDark ? 'Mode Terang' : 'Mode Gelap');
        }
        localStorage.setItem('croma-theme', isDark ? 'dark' : 'light');
    }

    const saved = localStorage.getItem('croma-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(saved ? saved === 'dark' : prefersDark);

    themeToggleBtn?.addEventListener('click', () => {
        const isDark = html.getAttribute('data-theme') !== 'dark';
        applyTheme(isDark);
    });

    // ── Client-Side Error Boundary & Fault Guard ──
    window.addEventListener('error', function(e) {
        // Prevent silent failure cascading on critical UI interactions
        if (e && e.error && e.error.name !== 'ResizeObserverLoopError') {
            console.warn('[Croma Client Fault Trapped]', e.message, e.filename, e.lineno);
        }
    });

    window.addEventListener('unhandledrejection', function(e) {
        console.warn('[Croma Unhandled Promise Trapped]', e.reason);
    });
</script>
@stack('scripts')
</body>
</html>
