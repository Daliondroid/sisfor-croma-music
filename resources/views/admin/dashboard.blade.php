{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <span class="crumb-root">Utama</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Dashboard</span>
@endsection

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Dashboard Admin</h2>
        <div style="font-size:.825rem;color:var(--text-light);margin-top:0.25rem">
            Selamat datang kembali, <strong style="color:var(--text-dark)">{{ auth()->user()->name ?? auth()->user()->username }}</strong>
        </div>
    </div>
    <span style="font-size:.8rem;color:var(--text-light);font-weight:600">{{ now()->translatedFormat('l, d F Y') }}</span>
</div>

<!-- Open KPI Metric Strips -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $totalMurid ?? 0 }}</div>
        <div class="stat-label">Total Murid Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $totalGuru ?? 0 }}</div>
        <div class="stat-label">Total Guru Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $belumBayar ?? 0 }}</div>
        <div class="stat-label">SPP Belum Bayar</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">Rp {{ number_format($totalPemasukanBulanIni ?? 0, 0, ',', '.') }}</div>
        <div class="stat-label">Pemasukan Bulan Ini</div>
    </div>
</div>

<div class="dashboard-grid-2">
    <!-- SPP Belum Bayar -->
    <div class="card">
        <div class="card-header">
            <h3>SPP Belum Bayar</h3>
            <a href="{{ route('admin.spp.index', ['status'=>'belum_bayar']) }}" class="btn btn-sm btn-outline" style="height:1.85rem;min-height:1.85rem;font-size:0.78rem">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table style="table-layout:fixed;width:100%">
                <thead>
                    <tr>
                        <th style="width:35%">Murid</th>
                        <th style="width:25%">Bulan</th>
                        <th style="width:22%">Nominal</th>
                        <th style="width:18%">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($sppBelumBayar ?? [] as $spp)
                    <tr>
                        <td><strong style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block">{{ $spp->murid->nama_murid }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($spp->bulan_tagihan.'-01')->translatedFormat('F Y') }}</td>
                        <td style="font-weight:600">Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}</td>
                        <td>
                            @if($spp->tanggal_jatuh_tempo < now())
                                <span class="badge badge-danger">{{ $spp->tanggal_jatuh_tempo->format('d/m') }} LEWAT</span>
                            @else
                                <span class="badge badge-warning">{{ $spp->tanggal_jatuh_tempo->format('d/m') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:1.25rem">Semua SPP sudah lunas</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="card">
        <div class="card-header">
            <h3>Jadwal Hari Ini</h3>
            <a href="{{ route('admin.jadwals.index') }}" class="btn btn-sm btn-outline" style="height:1.85rem;min-height:1.85rem;font-size:0.78rem">Kelola</a>
        </div>
        <div class="table-wrap">
            <table style="table-layout:fixed;width:100%">
                <thead>
                    <tr>
                        <th style="width:32%">Murid</th>
                        <th style="width:30%">Guru</th>
                        <th style="width:22%">Jam</th>
                        <th style="width:16%">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($jadwalHariIni ?? [] as $j)
                    <tr>
                        <td><strong style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block">{{ $j->murid->nama_murid }}</strong></td>
                        <td style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $j->guru->nama_guru }}</td>
                        <td>{{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</td>
                        <td>
                            <span class="badge {{ $j->id_kelas && $j->kelas->tipe_les=='onsite' ? 'badge-info' : 'badge-warning' }}">
                                {{ strtoupper($j->kelas->tipe_les ?? '-') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:1.25rem">Tidak ada jadwal hari ini</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
