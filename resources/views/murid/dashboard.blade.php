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
    .db-greeting { margin-bottom: 24px; }
    .db-greeting h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 4px; }
    .db-greeting .date-sub { font-size: .78rem; color: var(--text-light); }

    /* ── Metric cards ── */
    .db-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .db-metric {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 16px 18px;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .db-metric-label {
        font-size: .72rem;
        color: var(--text-light);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .db-metric-value { font-size: 1.6rem; font-weight: 700; line-height: 1; }
    .db-metric-sub   { font-size: .72rem; color: var(--text-light); }
    .val-green { color: #16a34a; }
    .val-red   { color: #dc2626; }
    .val-blue  { color: var(--primary-blue); }

    /* ── SPP Banner ── */
    .spp-banner {
        border-radius: var(--radius);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }
    .spp-banner.ok   { background: #dcfce7; border-left: 4px solid #16a34a; }
    .spp-banner.warn { background: #fff7ed; border-left: 4px solid #f97316; }
    .spp-banner-icon {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 1rem;
    }
    .spp-banner.ok .spp-banner-icon   { background: #bbf7d0; color: #15803d; }
    .spp-banner.warn .spp-banner-icon { background: #fed7aa; color: #c2410c; }
    .spp-banner-body { flex: 1; }
    .spp-banner-title { font-size: .875rem; font-weight: 700; }
    .spp-banner.ok .spp-banner-title   { color: #15803d; }
    .spp-banner.warn .spp-banner-title { color: #c2410c; }
    .spp-banner-sub   { font-size: .75rem; color: var(--text-light); margin-top: 2px; }

    /* ── Jadwal list ── */
    .jadwal-row {
        padding: 14px 18px;
        border-bottom: 1px solid var(--topbar-border);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .jadwal-row:last-child { border-bottom: none; }
    .jadwal-date-box {
        width: 46px; height: 46px;
        border-radius: 10px;
        background: var(--bg-light);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .jadwal-date-box .day-name { font-size: .6rem; text-transform: uppercase; letter-spacing: .4px; color: var(--text-light); font-weight: 600; }
    .jadwal-date-box .day-num  { font-size: 1.15rem; font-weight: 700; line-height: 1.1; }
    .jadwal-info { flex: 1; min-width: 0; }
    .jadwal-program { font-size: .875rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .jadwal-meta    { font-size: .72rem; color: var(--text-light); margin-top: 2px; }

    /* ── Bottom grid ── */
    .db-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
    @media (max-width: 640px) { .db-bottom { grid-template-columns: 1fr; } }

    /* ── Presensi list ── */
    .presensi-row {
        padding: 11px 18px;
        border-bottom: 1px solid var(--topbar-border);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .presensi-row:last-child { border-bottom: none; }
    .presensi-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
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
    .laporan-inner { padding: 16px 18px; }
    .laporan-top { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
    .score-ring-wrap { display: flex; flex-direction: column; align-items: center; gap: 4px; flex-shrink: 0; }
    .score-ring-outer { position: relative; width: 64px; height: 64px; }
    .score-ring-outer svg { transform: rotate(-90deg); }
    .score-ring-num {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; font-weight: 700;
    }
    .score-ring-label { font-size: .68rem; color: var(--text-light); }
    .laporan-stats { display: flex; gap: 10px; flex: 1; }
    .laporan-stat { flex: 1; background: var(--bg-light); border-radius: 8px; padding: 10px 12px; }
    .laporan-stat-label { font-size: .68rem; color: var(--text-light); margin-bottom: 2px; }
    .laporan-stat-value { font-size: 1.1rem; font-weight: 700; }
    .eval-box {
        background: var(--bg-light);
        border-left: 3px solid var(--primary-blue);
        border-radius: 0 8px 8px 0;
        padding: 10px 14px;
        font-size: .8rem;
        line-height: 1.6;
        color: var(--text-dark);
    }
    .eval-label { font-size: .68rem; color: var(--text-light); margin-bottom: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
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
        <h3><i class="fa-solid fa-calendar-week" style="color:var(--primary-blue);margin-right:8px"></i>Jadwal Les Mendatang</h3>
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
                  style="font-size:.68rem;gap:4px">
                <i class="fa-solid {{ ($j->spp?->tipe_les ?? 'onsite') == 'onsite' ? 'fa-building' : 'fa-house' }}" style="font-size:.65rem"></i>
                {{ ($j->spp?->tipe_les ?? 'onsite') == 'onsite' ? 'Onsite' : 'Home' }}
            </span>
        </div>
        @empty
        <div style="padding:28px;text-align:center;color:var(--text-light)">
            <i class="fa-solid fa-calendar-xmark" style="font-size:1.5rem;opacity:.3;display:block;margin-bottom:8px"></i>
            Tidak ada jadwal mendatang.
        </div>
        @endforelse
    </div>
</div>

{{-- Bottom: Presensi + Laporan --}}
<div class="db-bottom">

    {{-- Presensi Bulan Ini --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-clipboard-list" style="color:var(--primary-blue);margin-right:8px"></i>Presensi Bulan Ini</h3>
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
            <div style="padding:24px;text-align:center;color:var(--text-light);font-size:.8rem">
                Belum ada presensi bulan ini.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Laporan Terakhir --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-file-lines" style="color:var(--primary-blue);margin-right:8px"></i>
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
        <div style="padding:32px;text-align:center;color:var(--text-light);font-size:.8rem">
            <i class="fa-solid fa-file-circle-xmark" style="font-size:1.5rem;opacity:.3;display:block;margin-bottom:8px"></i>
            Belum ada laporan bulanan.
        </div>
        @endif
    </div>

</div>
@endsection