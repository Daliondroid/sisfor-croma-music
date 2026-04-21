{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Admin — Croma Music')
@section('page-title', 'Dashboard')

@section('sidebar-menu')
    <div class="nav-section-label">Utama</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item active">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>

    <div class="nav-section-label">Akademik</div>
    <a href="{{ route('admin.murids.index') }}" class="nav-item">
        <i class="fa-solid fa-user-graduate"></i> Data Murid
    </a>
    <a href="{{ route('admin.gurus.index') }}" class="nav-item">
        <i class="fa-solid fa-chalkboard-user"></i> Data Guru
    </a>
    <a href="{{ route('admin.jadwals.index') }}" class="nav-item">
        <i class="fa-solid fa-calendar-days"></i> Jadwal KBM
    </a>

    <div class="nav-section-label">Keuangan</div>
    <a href="{{ route('admin.spp.index') }}" class="nav-item">
        <i class="fa-solid fa-file-invoice-dollar"></i> Tagihan SPP
    </a>

    <div class="nav-section-label">Laporan</div>
    <a href="{{ route('admin.laporan.absensi') }}" class="nav-item">
        <i class="fa-solid fa-clipboard-list"></i> Rekap Absensi
    </a>
    <a href="{{ route('admin.laporan.keuangan') }}" class="nav-item">
        <i class="fa-solid fa-chart-line"></i> Lap. Keuangan
    </a>
    <a href="{{ route('admin.laporan.gaji') }}" class="nav-item">
        <i class="fa-solid fa-money-bill-wave"></i> Gaji Guru
    </a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Dashboard Admin</h2>
        <div class="breadcrumb">Selamat datang, <span>{{ auth()->user()->name ?? auth()->user()->username }}</span></div>
    </div>
    <span style="font-size:.8rem;color:var(--text-light)">{{ now()->translatedFormat('l, d F Y') }}</span>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-user-graduate"></i></div>
        <div>
            <div class="stat-value">{{ $totalMurid ?? 0 }}</div>
            <div class="stat-label">Total Murid Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fa-solid fa-chalkboard-user"></i></div>
        <div>
            <div class="stat-value">{{ $totalGuru ?? 0 }}</div>
            <div class="stat-label">Total Guru Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        <div>
            <div class="stat-value">{{ $belumBayar ?? 0 }}</div>
            <div class="stat-label">SPP Belum Bayar</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format($totalPemasukanBulanIni ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Pemasukan Bulan Ini</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <!-- SPP Belum Bayar -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b;margin-right:6px"></i>SPP Belum Bayar</h3>
            <a href="{{ route('admin.spp.index', ['status'=>'belum_bayar']) }}" class="btn btn-sm btn-outline">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Murid</th><th>Bulan</th><th>Nominal</th><th>Jatuh Tempo</th></tr></thead>
                <tbody>
                @forelse($sppBelumBayar ?? [] as $spp)
                    <tr>
                        <td><strong>{{ $spp->murid->nama_murid }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($spp->bulan_tagihan.'-01')->translatedFormat('F Y') }}</td>
                        <td>Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}</td>
                        <td>
                            @if($spp->tanggal_jatuh_tempo < now())
                                <span class="badge badge-danger">{{ $spp->tanggal_jatuh_tempo->format('d/m') }} Lewat</span>
                            @else
                                <span class="badge badge-warning">{{ $spp->tanggal_jatuh_tempo->format('d/m') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:24px">Semua SPP sudah lunas 🎉</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-calendar-day" style="color:var(--primary-blue);margin-right:6px"></i>Jadwal Hari Ini</h3>
            <a href="{{ route('admin.jadwals.index') }}" class="btn btn-sm btn-outline">Kelola</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Murid</th><th>Guru</th><th>Jam</th><th>Tipe</th></tr></thead>
                <tbody>
                @forelse($jadwalHariIni ?? [] as $j)
                    <tr>
                        <td><strong>{{ $j->murid->nama_murid }}</strong></td>
                        <td>{{ $j->guru->nama_guru }}</td>
                        <td>{{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</td>
                        <td>
                            <span class="badge {{ $j->id_kelas && $j->kelas->tipe_les=='onsite' ? 'badge-info' : 'badge-warning' }}">
                                {{ $j->kelas->tipe_les ?? '-' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:24px">Tidak ada jadwal hari ini</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
