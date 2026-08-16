<div class="nav-section-label">Utama</div>
<a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    Dashboard
</a>

<div class="nav-section-label">Akademik</div>
<a href="{{ route('admin.murids.index') }}" class="nav-item {{ request()->routeIs('admin.murids*') ? 'active' : '' }}">
    Data Murid
</a>
<a href="{{ route('admin.gurus.index') }}" class="nav-item {{ request()->routeIs('admin.gurus*') ? 'active' : '' }}">
    Data Guru
</a>
<a href="{{ route('admin.jadwals.index') }}" class="nav-item {{ request()->routeIs('admin.jadwals*') ? 'active' : '' }}">
    Jadwal KBM
</a>
<a href="{{ route('admin.program-kursus.index') }}" class="nav-item {{ request()->routeIs('admin.program-kursus*') ? 'active' : '' }}">
    Program Kursus
</a>

<div class="nav-section-label">Keuangan</div>
<a href="{{ route('admin.spp.index') }}" class="nav-item {{ request()->routeIs('admin.spp*') ? 'active' : '' }}">
    Tagihan SPP
</a>
<a href="{{ route('admin.laporan.keuangan') }}" class="nav-item {{ request()->routeIs('admin.laporan.keuangan*') ? 'active' : '' }}">
    Laporan Keuangan
</a>

<div class="nav-section-label">Gaji & Laporan</div>
<a href="{{ route('admin.honor-guru.index') }}" class="nav-item {{ request()->routeIs('admin.honor-guru*') ? 'active' : '' }}">
    Manajemen Gaji Guru
</a>
<a href="{{ route('admin.monthly_report.index') }}" class="nav-item {{ request()->routeIs('admin.monthly_report*') || request()->routeIs('admin.report*') ? 'active' : '' }}">
    Monthly Report Murid
</a>