<div class="nav-section-label">Menu</div>
<a href="{{ route('murid.dashboard') }}" class="nav-item {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}">
    Dashboard
</a>
<a href="{{ route('murid.jadwal.index') }}" class="nav-item {{ request()->routeIs('murid.jadwal*') ? 'active' : '' }}">
    Jadwal Kelas
</a>
<a href="{{ route('murid.laporan.index') }}" class="nav-item {{ request()->routeIs('murid.laporan*') ? 'active' : '' }}">
    Laporan Bulanan
</a>
<a href="{{ route('murid.spp.index') }}" class="nav-item {{ request()->routeIs('murid.spp*') ? 'active' : '' }}">
    SPP Saya
</a>
