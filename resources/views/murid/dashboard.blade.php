@extends('layouts.app')
@section('title', 'Dashboard Murid')
@section('page-title', 'Dashboard')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('murid.dashboard') }}" class="nav-item active"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('murid.spp.index') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Halo, {{ $murid->nama_murid }} 👋</h2>
        <div class="breadcrumb">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
</div>

<!-- Status SPP -->
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

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <!-- Jadwal Les -->
    <div class="card">
        <div class="card-header"><h3><i class="fa-solid fa-calendar-week" style="color:var(--primary-blue);margin-right:8px"></i>Jadwal Les Saya</h3></div>
        @forelse($murid->jadwals->where('is_active', true) as $j)
            <div style="padding:16px 20px;border-bottom:1px solid #f0f0f0">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <div style="font-weight:600">{{ $j->hari }}, {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</div>
                        <div style="font-size:.78rem;color:var(--text-light)">{{ $j->guru->nama_guru }} · {{ $j->kelas->nama_kelas ?? '-' }}</div>
                        @if($j->lokasi)<div style="font-size:.75rem;color:var(--text-light)"><i class="fa-solid fa-location-dot"></i> {{ $j->lokasi }}</div>@endif
                    </div>
                    <span class="badge {{ $j->kelas->tipe_les=='onsite' ? 'badge-info' : 'badge-warning' }}">
                        {{ $j->kelas->tipe_les=='onsite' ? 'Onsite' : 'Home' }}
                    </span>
                </div>
            </div>
        @empty
            <div style="padding:24px;text-align:center;color:var(--text-light)">Belum ada jadwal aktif.</div>
        @endforelse
    </div>

    <!-- Presensi Bulan Ini -->
    <div class="card">
        <div class="card-header"><h3><i class="fa-solid fa-clipboard-list" style="color:var(--primary-blue);margin-right:8px"></i>Presensi Bulan Ini</h3></div>
        <div class="card-body">
            @php
                $hadir = $presensiBulanIni->where('status_murid','hadir')->count();
                $alpa  = $presensiBulanIni->where('status_murid','alpa')->count();
                $izin  = $presensiBulanIni->where('status_murid','izin')->count();
                $total = $presensiBulanIni->count();
            @endphp
            <div style="display:flex;gap:16px;margin-bottom:20px">
                <div style="flex:1;text-align:center;padding:14px;background:#f0fdf4;border-radius:10px">
                    <div style="font-size:1.5rem;font-weight:700;color:#16a34a">{{ $hadir }}</div>
                    <div style="font-size:.72rem;color:#16a34a;font-weight:600">HADIR</div>
                </div>
                <div style="flex:1;text-align:center;padding:14px;background:#fef9c3;border-radius:10px">
                    <div style="font-size:1.5rem;font-weight:700;color:#a16207">{{ $izin }}</div>
                    <div style="font-size:.72rem;color:#a16207;font-weight:600">IZIN</div>
                </div>
                <div style="flex:1;text-align:center;padding:14px;background:#fef2f2;border-radius:10px">
                    <div style="font-size:1.5rem;font-weight:700;color:#dc2626">{{ $alpa }}</div>
                    <div style="font-size:.72rem;color:#dc2626;font-weight:600">ALPA</div>
                </div>
            </div>
            @if($total > 0)
            <div style="margin-bottom:6px;font-size:.8rem;font-weight:500">
                Kehadiran: {{ round(($hadir/$total)*100) }}%
            </div>
            <div style="height:8px;background:#e5e7eb;border-radius:99px;overflow:hidden">
                <div style="width:{{ round(($hadir/$total)*100) }}%;height:100%;background:var(--primary-blue);border-radius:99px"></div>
            </div>
            @endif

            <!-- Riwayat sesi -->
            @if($presensiBulanIni->count())
            <div style="margin-top:20px">
                <div style="font-weight:600;font-size:.85rem;margin-bottom:12px">Riwayat Sesi</div>
                @foreach($presensiBulanIni->take(5) as $p)
                <div style="padding:10px 0;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px">
                    <span class="badge {{ $p->status_murid=='hadir'?'badge-success':($p->status_murid=='izin'?'badge-warning':'badge-danger') }}">
                        {{ ucfirst($p->status_murid) }}
                    </span>
                    <div style="flex:1">
                        <div style="font-size:.8rem;font-weight:500">Sesi {{ $p->sesi_ke }} · {{ $p->tanggal->format('d/m') }}</div>
                        @if($p->materiKbm)
                            <div style="font-size:.72rem;color:var(--text-light)">{{ $p->materiKbm->materi_diajarkan }}</div>
                        @endif
                    </div>
                    @if($p->videoProgres)
                        <a href="{{ $p->videoProgres->url_video }}" target="_blank" class="btn btn-sm btn-outline" title="Tonton video">
                            <i class="fa-solid fa-play"></i>
                        </a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Monthly Report Terakhir -->
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