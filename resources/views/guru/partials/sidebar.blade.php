<div class="nav-section-label">Menu</div>
<a href="{{ route('guru.dashboard') }}" class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
    Dashboard
</a>
<a href="{{ route('guru.jadwal.index') }}" class="nav-item {{ request()->routeIs('guru.jadwal*') ? 'active' : '' }}">
    Jadwal Kelas
</a>
<a href="{{ route('guru.absensi.index') }}" class="nav-item {{ request()->routeIs('guru.absensi*') ? 'active' : '' }}">
    Data Absensi
</a>
<a href="{{ route('guru.presensi.index') }}" class="nav-item {{ request()->routeIs('guru.presensi*') ? 'active' : '' }}">
    Input Presensi
</a>
<a href="{{ route('guru.progres.index') }}" class="nav-item {{ request()->routeIs('guru.progres*') ? 'active' : '' }}">
    Laporan KBM
</a>
<a href="{{ route('guru.monthly-report.index') }}" class="nav-item {{ request()->routeIs('guru.monthly-report*') ? 'active' : '' }}">
    Laporan Bulanan
</a>
