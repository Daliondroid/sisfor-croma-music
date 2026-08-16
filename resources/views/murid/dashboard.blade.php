@extends('layouts.app')
@section('title', 'Dashboard Murid')
@section('page-title', 'Dashboard')

@section('sidebar-menu') @include('murid.partials.sidebar') @endsection

@push('styles')
<style>
    /* ── Greeting ── */
    .db-greeting { margin-bottom: 1.5rem; }
    .db-greeting h2 { font-size: 1.4rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem; }
    .db-greeting .date-sub { font-size: .78rem; color: var(--text-light); }

    /* ── SPP Banner ── */
    .spp-banner {
        border-radius: 0.25rem;
        padding: 1rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--topbar-border);
    }
    .spp-banner.ok   { background: #f0fdf4; border-color: #bbf7d0; }
    .spp-banner.warn { background: #fffbeb; border-color: #fde68a; }
    .spp-banner-body { flex: 1; }
    .spp-banner-title { font-size: .875rem; font-weight: 700; }
    .spp-banner.ok .spp-banner-title   { color: #15803d; }
    .spp-banner.warn .spp-banner-title { color: #b45309; }
    .spp-banner-sub   { font-size: .75rem; color: var(--text-light); margin-top: 0.125rem; }

    /* ── Jadwal list ── */
    .jadwal-row {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid var(--topbar-border);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .jadwal-row:last-child { border-bottom: none; }
    .jadwal-date-box {
        width: 3rem; height: 3rem;
        border-radius: 0.25rem;
        background: var(--bg-light);
        border: 1px solid var(--topbar-border);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .jadwal-date-box .day-name { font-size: .6rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-light); font-weight: 700; }
    .jadwal-date-box .day-num  { font-size: 1.15rem; font-weight: 700; line-height: 1.1; font-variant-numeric: tabular-nums; color: var(--text-dark); }
    .jadwal-info { flex: 1; min-width: 0; }
    .jadwal-program { font-size: .875rem; font-weight: 600; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .jadwal-meta    { font-size: .72rem; color: var(--text-light); margin-top: 0.125rem; font-variant-numeric: tabular-nums; }

    /* ── Bottom grid ── */
    .db-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
    @media (max-width: 40rem) { .db-bottom { grid-template-columns: 1fr; } }

    /* ── Presensi list ── */
    .presensi-row {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid var(--topbar-border);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .presensi-row:last-child { border-bottom: none; }
    .presensi-text  { flex: 1; min-width: 0; }
    .presensi-prog  { font-size: .8rem; font-weight: 600; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .presensi-tgl   { font-size: .7rem; color: var(--text-light); font-variant-numeric: tabular-nums; }

    /* ── Laporan ── */
    .laporan-inner { padding: 1.25rem 2.5rem; }
    .laporan-top { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
    .score-tile {
        width: 3.5rem; height: 3.5rem; border-radius: 0.25rem;
        background: var(--primary-navy); color: #fff;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .score-tile-num { font-size: 1.25rem; font-weight: 700; line-height: 1; }
    .score-tile-label { font-size: .6rem; text-transform: uppercase; letter-spacing: 0.04em; opacity: 0.8; }
    .laporan-stats { display: flex; gap: 0.5rem; flex: 1; }
    .laporan-stat { flex: 1; background: var(--bg-light); border: 1px solid var(--topbar-border); border-radius: 0.25rem; padding: 0.5rem 1rem; }
    .laporan-stat-label { font-size: .68rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-light); margin-bottom: 0.125rem; font-weight: 700; }
    .laporan-stat-value { font-size: 1.1rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--text-dark); }
    .eval-box {
        background: var(--bg-light);
        border: 1px solid var(--topbar-border);
        border-radius: 0.25rem;
        padding: 0.75rem 1.5rem;
        font-size: .8rem;
        line-height: 1.6;
        color: var(--text-dark);
    }
    .eval-label { font-size: .68rem; color: var(--text-light); margin-bottom: 0.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
</style>
@endpush

@section('content')

{{-- Greeting --}}
<div class="db-greeting">
    <h2>Halo, {{ $murid->nama_murid }}</h2>
    <div class="date-sub">{{ now()->translatedFormat('l, d F Y') }}</div>
</div>

{{-- Open KPI Metric Strips --}}
@php
    $totalJadwalBulanIni = $murid->jadwals()
        ->whereYear('tanggal', now()->year)
        ->whereMonth('tanggal', now()->month)
        ->count();
    $totalHadir = $presensiBulanIni->where('status_kehadiran_murid', 'hadir')->count();
    $totalSesi  = $presensiBulanIni->count();
    $pctHadir   = $totalSesi > 0 ? round($totalHadir / $totalSesi * 100) : 0;
@endphp
<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalSesi }}</div>
            <div class="stat-label">Sesi Bulan Ini (dari {{ $totalJadwalBulanIni }})</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $pctHadir }}%</div>
            <div class="stat-label">Kehadiran ({{ $totalHadir }} Hadir)</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">
                {{ $sppBulanIni ? ($sppBulanIni->sudahBayar() ? 'LUNAS' : 'BELUM') : '—' }}
            </div>
            <div class="stat-label">Status SPP Bulan Ini</div>
        </div>
    </div>
    @if($reportTerakhir && $reportTerakhir->skor)
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums;color:var(--text-dark)">{{ $reportTerakhir->skor }}</div>
            <div class="stat-label">Skor Terakhir ({{ \Carbon\Carbon::parse($reportTerakhir->periode_bulan)->translatedFormat('F Y') }})</div>
        </div>
    </div>
    @endif
</div>

{{-- SPP Banner --}}
@if($sppBulanIni)
<div class="spp-banner {{ $sppBulanIni->sudahBayar() ? 'ok' : 'warn' }}">
    <div class="spp-banner-body">
        <div class="spp-banner-title">
            SPP {{ \Carbon\Carbon::parse($sppBulanIni->periode_tagihan)->translatedFormat('F Y') }}
            &mdash; {{ $sppBulanIni->sudahBayar() ? 'Sudah Lunas' : 'Belum Dibayar' }}
        </div>
        <div class="spp-banner-sub">
            @if($sppBulanIni->sudahBayar())
                Nominal Rp {{ number_format($sppBulanIni->nominal_tagihan, 0, ',', '.') }}
            @else
                Nominal Rp {{ number_format($sppBulanIni->nominal_tagihan, 0, ',', '.') }}
                · Jatuh tempo {{ $sppBulanIni->tanggal_jatuh_tempo->translatedFormat('d F Y') }}
            @endif
        </div>
    </div>
    @if(!$sppBulanIni->sudahBayar())
        <a href="{{ route('murid.spp.index') }}" class="btn btn-sm btn-primary">
            Upload Bukti
        </a>
    @endif
</div>
@endif

{{-- Jadwal Card --}}
<div class="card">
    <div class="card-header">
        <h3>Jadwal Les Mendatang</h3>
        <a href="{{ route('murid.jadwal.index') }}" class="btn btn-sm btn-outline">Semua Jadwal</a>
    </div>
    <div style="padding:0">
        @forelse($murid->jadwals->where('is_active', true)->where('tanggal', '>=', now()->toDateString())->sortBy('tanggal')->take(4) as $j)
        <div class="jadwal-row">
            <div class="jadwal-date-box">
                <div class="day-name">{{ $j->tanggal->translatedFormat('D') }}</div>
                <div class="day-num">{{ $j->tanggal->format('d') }}</div>
            </div>
            <div class="jadwal-info">
                <div class="jadwal-program">{{ $j->spp?->programKursus?->nama_program ?? 'Program Kursus' }}</div>
                <div class="jadwal-meta">
                    {{ $j->guru->nama_guru }} &nbsp;·&nbsp; {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}
                </div>
            </div>
            @php $tipeLes = $j->spp?->tipe_les ?? $j->spp?->programKursus?->tipe_les ?? 'Onsite'; @endphp
            <span class="badge {{ strtolower($tipeLes) === 'onsite' ? 'badge-info' : 'badge-warning' }}">
                {{ strtoupper($tipeLes) }}
            </span>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-state-title">Tidak ada jadwal mendatang.</div>
        </div>
        @endforelse
    </div>
</div>

{{-- Bottom: Presensi + Laporan --}}
<div class="db-bottom">

    {{-- Presensi Bulan Ini --}}
    <div class="card">
        <div class="card-header">
            <h3>Presensi Bulan Ini</h3>
        </div>
        <div style="padding:0">
            @forelse($presensiBulanIni->take(5) as $p)
            <div class="presensi-row">
                @php
                    $st = strtolower($p->status_kehadiran_murid);
                    $badgeClass = match($st) { 'hadir' => 'badge-success', 'izin' => 'badge-warning', default => 'badge-danger' };
                @endphp
                <div class="presensi-text">
                    <div class="presensi-prog">{{ $p->spp?->programKursus?->nama_program ?? 'Les' }} — {{ $p->guru->nama_guru }}</div>
                    <div class="presensi-tgl">{{ $p->tanggal->translatedFormat('l, d M') }}</div>
                </div>
                <span class="badge {{ $badgeClass }}">{{ strtoupper($st) }}</span>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-state-title">Belum ada presensi bulan ini.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Laporan Terakhir --}}
    <div class="card">
        <div class="card-header">
            <h3>
                @if($reportTerakhir)
                    Laporan {{ \Carbon\Carbon::parse($reportTerakhir->periode_bulan)->translatedFormat('F Y') }}
                @else
                    Laporan Bulanan
                @endif
            </h3>
            @if($reportTerakhir)
                <a href="{{ route('murid.laporan.show', $reportTerakhir) }}" class="btn btn-sm btn-outline">Detail</a>
            @endif
        </div>

        @if($reportTerakhir)
        <div class="laporan-inner">
            <div class="laporan-top">
                @if($reportTerakhir->skor)
                <div class="score-tile">
                    <div class="score-tile-num">{{ $reportTerakhir->skor }}</div>
                    <div class="score-tile-label">Skor</div>
                </div>
                @endif
                <div class="laporan-stats">
                    <div class="laporan-stat">
                        <div class="laporan-stat-label">Total Hadir</div>
                        <div class="laporan-stat-value" style="color:#16a34a">
                            {{ $reportTerakhir->jadwals->where('status_kehadiran_murid','hadir')->count() }}x
                        </div>
                    </div>
                    <div class="laporan-stat">
                        <div class="laporan-stat-label">Persentase</div>
                        @php
                            $tot = $reportTerakhir->jadwals->count();
                            $hdr = $reportTerakhir->jadwals->where('status_kehadiran_murid','hadir')->count();
                            $pct = $tot > 0 ? round($hdr/$tot*100) : 0;
                        @endphp
                        <div class="laporan-stat-value" style="color:var(--text-dark)">{{ $pct }}%</div>
                    </div>
                </div>
            </div>

            @if($reportTerakhir->evaluasi_bulanan)
            <div>
                <div class="eval-label">Evaluasi Guru</div>
                <div class="eval-box">{{ \Str::limit($reportTerakhir->evaluasi_bulanan, 180) }}</div>
            </div>
            @endif
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-title">Belum ada laporan bulanan.</div>
        </div>
        @endif
    </div>

</div>
@endsection