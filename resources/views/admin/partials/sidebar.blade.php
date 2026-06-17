<div class="nav-section-label">Utama</div>
<a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-gauge"></i> Dashboard
</a>

<div class="nav-section-label">Akademik</div>
<a href="{{ route('admin.murids.index') }}" class="nav-item {{ request()->routeIs('admin.murids*') ? 'active' : '' }}">
    <i class="fa-solid fa-user-graduate"></i> Data Murid
</a>
<a href="{{ route('admin.gurus.index') }}" class="nav-item {{ request()->routeIs('admin.gurus*') ? 'active' : '' }}">
    <i class="fa-solid fa-chalkboard-user"></i> Data Guru
</a>
<a href="{{ route('admin.jadwals.index') }}" class="nav-item {{ request()->routeIs('admin.jadwals*') ? 'active' : '' }}">
    <i class="fa-solid fa-calendar-days"></i> Jadwal KBM
</a>
{{-- <a href="{{ route('admin.kelas.index') }}" class="nav-item {{ request()->routeIs('admin.kelas*') ? 'active' : '' }}">
    <i class="fa-solid fa-door-open"></i> Data Kelas
</a> --}}
<a href="{{ route('admin.program-kursus.index') }}" class="nav-item {{ request()->routeIs('admin.program-kursus*') ? 'active' : '' }}">
    <i class="fa-solid fa-layer-group"></i> Program Kursus
</a>

<div class="nav-section-label">Keuangan</div>
<a href="{{ route('admin.spp.index') }}" class="nav-item {{ request()->routeIs('admin.spp*') ? 'active' : '' }}">
    <i class="fa-solid fa-file-invoice-dollar"></i> Tagihan SPP
</a>

<a href="{{ route('admin.laporan.keuangan') }}" class="nav-item">
    <i class="fa-solid fa-chart-line"></i> Laporan Keuangan
</a>

<div class="nav-section-label">Gaji & Laporan</div>
<a href="{{ route('admin.honor-guru.index') }}" class="nav-item {{ request()->routeIs('admin.honor-guru*') ? 'active' : '' }}">
    <i class="fa-solid fa-file-invoice-dollar"></i> Manajemen Gaji Guru
</a>