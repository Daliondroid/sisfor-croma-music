@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}"            class="nav-item {{ request()->routeIs('guru.dashboard')       ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('guru.jadwal.index') }}"         class="nav-item {{ request()->routeIs('guru.jadwal*')         ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('guru.absensi.index') }}"        class="nav-item {{ request()->routeIs('guru.absensi*')        ? 'active' : '' }}"><i class="fa-solid fa-chart-bar"></i> Data Absensi</a>
    <a href="{{ route('guru.presensi.index') }}"       class="nav-item {{ request()->routeIs('guru.presensi*')       ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Input Presensi</a>
    <a href="{{ route('guru.progres.index') }}"        class="nav-item {{ request()->routeIs('guru.progres*')        ? 'active' : '' }}"><i class="fa-solid fa-book-open"></i> Laporan KBM</a>
    <a href="{{ route('guru.monthly-report.index') }}" class="nav-item {{ request()->routeIs('guru.monthly-report*') ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i> Laporan Bulanan</a>
@endsection

@section('content')
<div class="page-header">
    <h2>Laporan Bulanan</h2>
    <div class="breadcrumb">Guru / <span>Laporan Bulanan</span></div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="month" name="bulan" class="form-control form-control-sm" style="width:auto"
                   value="{{ $bulan }}" onchange="this.form.submit()"/>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-file-lines" style="color:var(--primary-blue);margin-right:0.5rem"></i>
            Daftar Murid — {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
        </h3>
        <span style="font-size:.8rem;color:var(--text-light)">{{ $spps->count() }} murid</span>
    </div>

    @if($spps->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="8" width="48" height="64" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 24h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 56V36l8 8 6-4 10 12" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity=".6"/></svg>
            </div>
            <div class="empty-state-title">Tidak ada murid ditemukan.</div>
            <div class="empty-state-description">Tidak ada murid yang diajar pada bulan ini.</div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Murid</th>
                        <th>Program</th>
                        <th style="text-align:center">Total Sesi</th>
                        <th style="text-align:center">Kehadiran</th>
                        <th style="text-align:center">% Hadir</th>
                        <th style="text-align:center">Skor Otomatis</th>
                        <th style="text-align:center">Status Laporan</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($spps as $spp)
                    @php
                        $persen     = $spp->persen;
                        $barColor   = $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626');
                        $sudahAda   = $spp->report !== null;

                        $skorOtomatis = match(true) {
                            $persen >= 95 => 'A+',
                            $persen >= 90 => 'A',
                            $persen >= 85 => 'A-',
                            $persen >= 80 => 'B+',
                            $persen >= 75 => 'B',
                            $persen >= 70 => 'B-',
                            $persen >= 65 => 'C+',
                            $persen >= 60 => 'C',
                            default       => 'C-',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $spp->murid->nama_murid ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-size:.85rem">{{ $spp->programKursus->nama_program ?? '-' }}</div>
                            <span class="badge {{ $spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}" style="font-size:.68rem">{{ $spp->tipe_les }}</span>
                        </td>
                        <td style="text-align:center;font-weight:600">{{ $spp->total_sesi }}</td>
                        <td style="text-align:center">
                            <span style="color:#16a34a;font-weight:700">{{ $spp->hadir_murid }}</span>
                            <span style="color:var(--text-light)"> / {{ $spp->total_sesi }}</span>
                        </td>
                        <td style="text-align:center;min-width:6.25rem">
                            <div style="display:flex;align-items:center;gap:0.5rem">
                                <div style="flex:1;height:0.25rem;background:#f3f4f6;border-radius:0.1875rem;overflow:hidden">
                                    <div style="width:{{ $persen }}%;height:100%;background:{{ $barColor }};border-radius:0.1875rem"></div>
                                </div>
                                <span style="font-size:.78rem;font-weight:700;color:{{ $barColor }};min-width:2.125rem">{{ $persen }}%</span>
                            </div>
                        </td>
                        <td style="text-align:center">
                            <span style="font-size:1rem;font-weight:700;color:var(--primary-blue)">{{ $skorOtomatis }}</span>
                        </td>
                        <td style="text-align:center">
                            @if($sudahAda)
                                <span class="badge badge-success">
                                    <i class="fa-solid fa-circle-check" style="margin-right:0.25rem"></i>Selesai
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fa-solid fa-clock" style="margin-right:0.25rem"></i>Belum Dibuat
                                </span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:0.25rem;justify-content:center;flex-wrap:wrap">
                                @if($sudahAda)
                                    <a href="{{ route('guru.monthly-report.show', $spp->report->id_report) }}"
                                       class="btn btn-outline btn-sm">
                                        <i class="fa-solid fa-eye"></i> Lihat
                                    </a>
                                    <a href="{{ route('guru.monthly-report.create', ['id_spp' => $spp->id_spp, 'bulan' => $bulan]) }}"
                                       class="btn btn-outline btn-sm">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                @else
                                    <a href="{{ route('guru.monthly-report.create', ['id_spp' => $spp->id_spp, 'bulan' => $bulan]) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-plus"></i> Buat Laporan
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
