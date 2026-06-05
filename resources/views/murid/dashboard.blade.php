@extends('layouts.app')
@section('title', 'Dashboard Murid')
@section('page-title', 'Dashboard')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    
    <a href="{{ route('murid.dashboard') }}" class="nav-item {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    
    <a href="{{ route('murid.jadwal.index') }}" class="nav-item {{ request()->routeIs('murid.jadwal*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-days"></i> Jadwal Kelas
    </a>
    
    <a href="{{ route('murid.spp.index') }}" class="nav-item {{ request()->routeIs('murid.spp*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya
    </a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Halo, {{ $murid->nama_murid }} 👋</h2>
        <div class="breadcrumb">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
</div>

@if($sppBulanIni)
<div class="alert {{ $sppBulanIni->sudahBayar() ? 'alert-success' : 'alert-danger' }}" style="margin-bottom:24px">
    <i class="fa-solid {{ $sppBulanIni->sudahBayar() ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
    <div>
        <strong>SPP {{ \Carbon\Carbon::parse($sppBulanIni->bulan_tagihan.'-01')->translatedFormat('F Y') }}</strong>
        @if($sppBulanIni->sudahBayar())
            — Sudah Lunas ✅
        @else
            — Belum Bayar. Jatuh tempo: {{ $sppBulanIni->tanggal_jatuh_tempo->format('d F Y') }}
            <a href="{{ route('murid.spp.index') }}" style="margin-left:8px;font-weight:600;text-decoration:underline">Upload Bukti →</a>
        @endif
    </div>
</div>
@endif

<div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
            <h3>
                <i class="fa-solid fa-calendar-week" style="color:var(--primary-blue);margin-right:8px"></i>Jadwal Les Saya
            </h3>
            <a href="{{ route('murid.jadwal.index') }}" class="btn btn-sm btn-outline">Selebihnya</a>
        </div>
        <div class="card-body" style="padding:0">
            @forelse($murid->jadwals->where('is_active', true)->take(5) as $j)
                <div style="padding:16px 20px;border-bottom:1px solid #f0f0f0">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <div style="font-weight:600">{{ $j->tanggal->translatedFormat('l') }}, {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</div>
                            <div style="font-size:.78rem;color:var(--text-light)">
                                {{ $j->guru->nama_guru }} · {{ $j->spp?->programKursus?->nama_program ?? 'Program Kursus' }}
                            </div>
                            @if($j->lokasi)<div style="font-size:.75rem;color:var(--text-light)"><i class="fa-solid fa-location-dot"></i> {{ $j->lokasi }}</div>@endif
                        </div>
                        <span class="badge {{ ($j->spp?->tipe_les ?? 'onsite') == 'onsite' ? 'badge-info' : 'badge-warning' }}">
                            {{ ($j->spp?->tipe_les ?? 'onsite') == 'onsite' ? 'Onsite' : 'Home' }}
                        </span>
                    </div>
                </div>
            @empty
                <div style="padding:24px;text-align:center;color:var(--text-light)">Belum ada jadwal aktif.</div>
            @endforelse
        </div>
    </div>

@if($reportTerakhir)
<div class="card" style="margin-top:20px">
    <div class="card-header">
        <h3><i class="fa-solid fa-file-lines" style="color:var(--primary-blue);margin-right:8px"></i>
            Laporan Bulanan — {{ \Carbon\Carbon::parse($reportTerakhir->bulan.'-01')->translatedFormat('F Y') }}
        </h3>
    </div>
    <div class="card-body">
        <div style="display:flex;gap:20px;flex-wrap:wrap">
            <div>
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:2px">Total Hadir</div>
                <div style="font-size:1.3rem;font-weight:700;color:#16a34a">{{ $reportTerakhir->total_hadir }}x</div>
            </div>
            <div>
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:2px">Persentase</div>
                <div style="font-size:1.3rem;font-weight:700;color:var(--primary-blue)">{{ $reportTerakhir->persentase_kehadiran }}%</div>
            </div>
            @if($reportTerakhir->catatan_guru)
            <div style="flex:1;min-width:200px">
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:4px">Catatan Guru</div>
                <div style="font-size:.875rem;background:#f8faff;padding:10px 14px;border-radius:8px;border-left:3px solid var(--primary-blue)">
                    {{ $reportTerakhir->catatan_guru }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@endsection