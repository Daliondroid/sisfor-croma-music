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
    <a href="{{ route('murid.laporan.index') }}" class="nav-item {{ request()->routeIs('murid.laporan*') ? 'active' : '' }}">
        <i class="fa-solid fa-book-open"></i> Laporan Bulanan
    </a>
    <a href="{{ route('murid.spp.index') }}" class="nav-item {{ request()->routeIs('murid.spp*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya
    </a>
@endsection

@push('styles')
<style>
    /* ── Greeting ── */
    .db-greeting { margin-bottom: 1.5rem; }
    .db-greeting h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0.25rem; }
    .db-greeting .date-sub { font-size: .78rem; color: var(--text-light); }

    /* ── Metric cards ── */
    .db-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(9.375rem, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .db-metric {
        background: var(--card-bg);
        border: 1px solid var(--topbar-border);
        border-radius: 0.5rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .db-metric-label {
        font-size: .72rem;
        color: var(--text-light);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03125rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .db-metric-value { font-size: 1.6rem; font-weight: 700; line-height: 1; }
    .db-metric-sub   { font-size: .72rem; color: var(--text-light); }
    .val-green { color: #16a34a; }
    .val-red   { color: #dc2626; }
    .val-blue  { color: var(--primary-blue); }

    /* ── SPP Banner ── */
    .spp-banner {
        border-radius: var(--radius);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .spp-banner.ok   { background: #dcfce7; border-left: 0.25rem solid #16a34a; }
    .spp-banner.warn { background: #fff7ed; border-left: 0.25rem solid #f97316; }
    [data-theme="dark"] .spp-banner.ok   { background: #14312a; border-left-color: #4ade80; }
    [data-theme="dark"] .spp-banner.warn { background: #3d2009; border-left-color: #fb923c; }
    .spp-banner-icon {
        width: 2.5rem; height: 2.5rem; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 1rem;
    }
    .spp-banner.ok .spp-banner-icon   { background: #bbf7d0; color: #15803d; }
    .spp-banner.warn .spp-banner-icon { background: #fed7aa; color: #c2410c; }
    [data-theme="dark"] .spp-banner.ok .spp-banner-icon   { background: #166534; color: #4ade80; }
    [data-theme="dark"] .spp-banner.warn .spp-banner-icon { background: #7c2d12; color: #fb923c; }
    .spp-banner-body { flex: 1; }
    .spp-banner-title { font-size: .875rem; font-weight: 700; }
    .spp-banner.ok .spp-banner-title   { color: #15803d; }
    .spp-banner.warn .spp-banner-title { color: #c2410c; }
    [data-theme="dark"] .spp-banner.ok .spp-banner-title   { color: #4ade80; }
    [data-theme="dark"] .spp-banner.warn .spp-banner-title { color: #fb923c; }
    .spp-banner-sub   { font-size: .75rem; color: var(--text-light); margin-top: 0.125rem; }


    /* ── Jadwal list ── */
    .jadwal-row {
        padding: 1rem 1rem;
        border-bottom: 1px solid var(--topbar-border);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .jadwal-row:last-child { border-bottom: none; }
    .jadwal-date-box {
        width: 3rem; height: 3rem;
        border-radius: 0.625rem;
        background: var(--bg-light);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .jadwal-date-box .day-name { font-size: .6rem; text-transform: uppercase; letter-spacing: 0.025rem; color: var(--text-light); font-weight: 600; }
    .jadwal-date-box .day-num  { font-size: 1.15rem; font-weight: 700; line-height: 1.1; }
    .jadwal-info { flex: 1; min-width: 0; }
    .jadwal-program { font-size: .875rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .jadwal-meta    { font-size: .72rem; color: var(--text-light); margin-top: 0.125rem; }

    /* ── Bottom grid ── */
    .db-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
    @media (max-width: 40rem) { .db-bottom { grid-template-columns: 1fr; } }

    /* ── Presensi list ── */
    .presensi-row {
        padding: 0.5rem 1rem;
        border-bottom: 1px solid var(--topbar-border);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .presensi-row:last-child { border-bottom: none; }
    .presensi-dot { width: 0.5rem; height: 0.5rem; border-radius: 50%; flex-shrink: 0; }
    .dot-hadir { background: #16a34a; }
    .dot-izin  { background: #d97706; }
    .dot-absen { background: #dc2626; }
    .presensi-text  { flex: 1; min-width: 0; }
    .presensi-prog  { font-size: .8rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .presensi-tgl   { font-size: .7rem; color: var(--text-light); }
    .presensi-badge { font-size: .68rem; font-weight: 600; }
    .p-hadir { color: #16a34a; }
    .p-izin  { color: #d97706; }
    .p-absen { color: #dc2626; }

    /* ── Score ring ── */
    .laporan-inner { padding: 1.5rem; }
    .laporan-top { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
    .score-ring-wrap { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; flex-shrink: 0; }
    .score-ring-outer { position: relative; width: 4rem; height: 4rem; }
    .score-ring-outer svg { transform: rotate(-90deg); }
    .score-ring-num {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; font-weight: 700;
    }
    .score-ring-label { font-size: .68rem; color: var(--text-light); }
    .laporan-stats { display: flex; gap: 0.5rem; flex: 1; }
    .laporan-stat { flex: 1; background: var(--bg-light); border-radius: 0.5rem; padding: 0.5rem 1rem; }
    .laporan-stat-label { font-size: .68rem; color: var(--text-light); margin-bottom: 0.125rem; }
    .laporan-stat-value { font-size: 1.1rem; font-weight: 700; }
    .eval-box {
        background: var(--bg-light);
        border-left: 0.1875rem solid var(--primary-blue);
        border-radius: 0 0.5rem 0.5rem 0;
        padding: 0.5rem 1rem;
        font-size: .8rem;
        line-height: 1.6;
        color: var(--text-dark);
    }
    .eval-label { font-size: .68rem; color: var(--text-light); margin-bottom: 0.25rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025rem; }
</style>
@endpush

@section('content')

{{-- Greeting --}}
<div class="db-greeting">
    <h2>Halo, {{ $murid->nama_murid }} 👋</h2>
    <div class="date-sub">{{ now()->translatedFormat('l, d F Y') }}</div>
</div>

{{-- Metric Cards --}}
@php
    $totalJadwalBulanIni = $murid->jadwals()
        ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [now()->format('Y-m')])
        ->count();
    $totalHadir = $presensiBulanIni->where('status_kehadiran_murid', 'hadir')->count();
    $totalSesi  = $presensiBulanIni->count();
    $pctHadir   = $totalSesi > 0 ? round($totalHadir / $totalSesi * 100) : 0;
@endphp
<div class="db-metrics">
    <div class="db-metric">
        <div class="db-metric-label"><i class="fa-solid fa-calendar-check"></i> Sesi bulan ini</div>
        <div class="db-metric-value val-blue">{{ $totalSesi }}</div>
        <div class="db-metric-sub">dari {{ $totalJadwalBulanIni }} jadwal</div>
    </div>
    <div class="db-metric">
        <div class="db-metric-label"><i class="fa-solid fa-user-check"></i> Kehadiran</div>
        <div class="db-metric-value {{ $pctHadir >= 80 ? 'val-green' : 'val-red' }}">{{ $pctHadir }}%</div>
        <div class="db-metric-sub">{{ $totalHadir }} hadir · {{ $totalSesi - $totalHadir }} tidak hadir</div>
    </div>
    <div class="db-metric">
        <div class="db-metric-label"><i class="fa-solid fa-receipt"></i> Status SPP</div>
        @if($sppBulanIni)
            <div class="db-metric-value {{ $sppBulanIni->sudahBayar() ? 'val-green' : 'val-red' }}">
                {{ $sppBulanIni->sudahBayar() ? 'Lunas' : 'Belum' }}
            </div>
            <div class="db-metric-sub">
                {{ $sppBulanIni->sudahBayar()
                    ? 'Terbayar bulan ini'
                    : 'Jatuh tempo ' . $sppBulanIni->tanggal_jatuh_tempo->format('d M') }}
            </div>
        @else
            <div class="db-metric-value" style="color:var(--text-light)">—</div>
            <div class="db-metric-sub">Tidak ada tagihan</div>
        @endif
    </div>
    @if($reportTerakhir && $reportTerakhir->skor)
    <div class="db-metric">
        <div class="db-metric-label"><i class="fa-solid fa-star"></i> Skor terakhir</div>
        <div class="db-metric-value val-blue">{{ $reportTerakhir->skor }}</div>
        <div class="db-metric-sub">{{ \Carbon\Carbon::parse($reportTerakhir->periode_bulan)->translatedFormat('F Y') }}</div>
    </div>
    @endif
</div>

{{-- SPP Banner --}}
@if($sppBulanIni)
<div class="spp-banner {{ $sppBulanIni->sudahBayar() ? 'ok' : 'warn' }}">
    <div class="spp-banner-icon">
        <i class="fa-solid {{ $sppBulanIni->sudahBayar() ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
    </div>
    <div class="spp-banner-body">
        <div class="spp-banner-title">
            SPP {{ \Carbon\Carbon::parse($sppBulanIni->periode_tagihan)->translatedFormat('F Y') }}
            — {{ $sppBulanIni->sudahBayar() ? 'Sudah Lunas ✅' : 'Belum Dibayar' }}
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
        <a href="{{ route('murid.spp.index') }}" class="btn btn-sm btn-yellow">
            <i class="fa-solid fa-upload"></i> Upload Bukti
        </a>
    @endif
</div>
@endif

{{-- Jadwal Card --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-calendar-week" style="color:var(--primary-blue);margin-right:0.5rem"></i>Jadwal Les Mendatang</h3>
        <a href="{{ route('murid.jadwal.index') }}" class="btn btn-sm btn-outline">Semua <i class="fa-solid fa-arrow-right" style="font-size:.7rem"></i></a>
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
            <span class="badge {{ ($j->spp?->tipe_les ?? 'onsite') == 'onsite' ? 'badge-info' : 'badge-warning' }}"
                  style="font-size:.68rem;gap:0.25rem">
                <i class="fa-solid {{ ($j->spp?->tipe_les ?? 'onsite') == 'onsite' ? 'fa-building' : 'fa-house' }}" style="font-size:.65rem"></i>
                {{ ($j->spp?->tipe_les ?? 'onsite') == 'onsite' ? 'Onsite' : 'Home' }}
            </span>
        </div>
        @empty
        <div class="empty-state" style="border:none;padding:2rem">
            <div class="empty-state-icon" style="width:3rem;height:3rem;margin-bottom:1rem">
                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="12" y="20" width="56" height="48" rx="6" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M12 32h56" stroke="var(--primary-blue)" stroke-width="2"/><rect x="24" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><rect x="52" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><circle cx="30" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="40" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="50" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="30" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="40" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="50" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/></svg>
            </div>
            <div class="empty-state-title" style="font-size:1rem">Tidak ada jadwal mendatang.</div>
        </div>
        @endforelse
    </div>
</div>

{{-- Bottom: Presensi + Laporan --}}
<div class="db-bottom">

    {{-- Presensi Bulan Ini --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-clipboard-list" style="color:var(--primary-blue);margin-right:0.5rem"></i>Presensi Bulan Ini</h3>
        </div>
        <div style="padding:0">
            @forelse($presensiBulanIni->take(5) as $p)
            <div class="presensi-row">
                @php
                    $st = $p->status_kehadiran_murid;
                    $dotClass = match($st) { 'hadir' => 'dot-hadir', 'izin' => 'dot-izin', default => 'dot-absen' };
                    $lblClass = match($st) { 'hadir' => 'p-hadir', 'izin' => 'p-izin', default => 'p-absen' };
                @endphp
                <div class="presensi-dot {{ $dotClass }}"></div>
                <div class="presensi-text">
                    <div class="presensi-prog">{{ $p->spp?->programKursus?->nama_program ?? 'Les' }} — {{ $p->guru->nama_guru }}</div>
                    <div class="presensi-tgl">{{ $p->tanggal->translatedFormat('l, d M') }}</div>
                </div>
                <div class="presensi-badge {{ $lblClass }}">{{ ucfirst($st) }}</div>
            </div>
            @empty
            <div class="empty-state" style="border:none;padding:2rem">
                <div class="empty-state-icon" style="width:3rem;height:3rem;margin-bottom:1rem">
                    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="12" width="48" height="56" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 28h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 40h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 52h16" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <div class="empty-state-title" style="font-size:1rem">Belum ada presensi bulan ini.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Laporan Terakhir --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-file-lines" style="color:var(--primary-blue);margin-right:0.5rem"></i>
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
                @php
                    $skor   = (int) $reportTerakhir->skor;
                    $r      = 26;
                    $circ   = 2 * M_PI * $r;
                    $offset = $circ * (1 - $skor / 100);
                @endphp
                <div class="score-ring-wrap">
                    <div class="score-ring-outer">
                        <svg width="64" height="64" viewBox="0 0 64 64">
                            <circle cx="32" cy="32" r="{{ $r }}" fill="none" stroke="var(--topbar-border)" stroke-width="6"/>
                            <circle cx="32" cy="32" r="{{ $r }}" fill="none" stroke="var(--primary-blue)" stroke-width="6"
                                stroke-dasharray="{{ round($circ, 2) }}"
                                stroke-dashoffset="{{ round($offset, 2) }}"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="score-ring-num">{{ $skor }}</div>
                    </div>
                    <div class="score-ring-label">Skor</div>
                </div>
                @endif
                <div class="laporan-stats">
                    <div class="laporan-stat">
                        <div class="laporan-stat-label">Total Hadir</div>
                        <div class="laporan-stat-value val-green">
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
                        <div class="laporan-stat-value val-blue">{{ $pct }}%</div>
                    </div>
                </div>
            </div>

            @if($reportTerakhir->evaluasi_bulanan)
            <div>
                <div class="eval-label">Evaluasi guru</div>
                <div class="eval-box">{{ \Str::limit($reportTerakhir->evaluasi_bulanan, 180) }}</div>
            </div>
            @endif
        </div>
        @else
        <div class="empty-state" style="border:none;padding:2rem">
            <div class="empty-state-icon" style="width:3rem;height:3rem;margin-bottom:1rem">
                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="12" width="48" height="56" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 28h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 40h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 52h16" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div class="empty-state-title" style="font-size:1rem">Belum ada laporan bulanan.</div>
        </div>
        @endif
    </div>

</div>
@endsection