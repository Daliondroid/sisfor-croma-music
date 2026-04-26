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
<a href="{{ route('admin.kelas.index') }}" class="nav-item {{ request()->routeIs('admin.kelas*') ? 'active' : '' }}">
    <i class="fa-solid fa-door-open"></i> Data Kelas
</a>
<a href="{{ route('admin.program-kursus.index') }}" class="nav-item {{ request()->routeIs('admin.program-kursus*') ? 'active' : '' }}">
    <i class="fa-solid fa-layer-group"></i> Program Kursus
</a>

<div class="nav-section-label">Keuangan</div>
<a href="{{ route('admin.spp.index') }}" class="nav-item {{ request()->routeIs('admin.spp*') ? 'active' : '' }}">
    <i class="fa-solid fa-file-invoice-dollar"></i> Tagihan SPP
</a>

<div class="nav-section-label">Laporan</div>
<a href="{{ route('admin.laporan.absensi') }}" class="nav-item {{ request()->routeIs('admin.laporan.absensi') ? 'active' : '' }}">
    <i class="fa-solid fa-clipboard-list"></i> Rekap Absensi
</a>
<a href="{{ route('admin.laporan.keuangan') }}" class="nav-item {{ request()->routeIs('admin.laporan.keuangan') ? 'active' : '' }}">
    <i class="fa-solid fa-chart-line"></i> Lap. Keuangan
</a>
<a href="{{ route('admin.laporan.gaji') }}" class="nav-item {{ request()->routeIs('admin.laporan.gaji') ? 'active' : '' }}">
    <i class="fa-solid fa-money-bill-wave"></i> Gaji Guru
</a>
