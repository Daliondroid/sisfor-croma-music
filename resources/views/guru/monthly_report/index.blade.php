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
    <div>
        <h2>Laporan Bulanan</h2>
        <div class="breadcrumb">Guru / <span>Laporan Bulanan</span></div>
    </div>
</div>

{{-- Filter Bulan --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <label style="font-weight:600;font-size:.875rem;white-space:nowrap">
                <i class="fa-regular fa-calendar" style="color:var(--primary-blue);margin-right:6px"></i>Pilih Bulan:
            </label>
            <input type="month" name="bulan" class="form-control" style="width:auto"
                   value="{{ $bulan }}" onchange="this.form.submit()"/>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-file-lines" style="color:var(--primary-blue);margin-right:8px"></i>
            Daftar Murid — {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
        </h3>
        <span style="font-size:.8rem;color:var(--text-light)">{{ $spps->count() }} murid</span>
    </div>

    @if($spps->isEmpty())
        <div style="text-align:center;padding:60px;color:var(--text-light)">
            <i class="fa-solid fa-folder-open" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:14px"></i>
            <p>Tidak ada murid yang diajar pada bulan ini.</p>
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
                        <td style="text-align:center;min-width:100px">
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="flex:1;height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden">
                                    <div style="width:{{ $persen }}%;height:100%;background:{{ $barColor }};border-radius:3px"></div>
                                </div>
                                <span style="font-size:.78rem;font-weight:700;color:{{ $barColor }};min-width:34px">{{ $persen }}%</span>
                            </div>
                        </td>
                        <td style="text-align:center">
                            <span style="font-size:1rem;font-weight:700;color:var(--primary-blue)">{{ $skorOtomatis }}</span>
                        </td>
                        <td style="text-align:center">
                            @if($sudahAda)
                                <span class="badge badge-success">
                                    <i class="fa-solid fa-circle-check" style="margin-right:4px"></i>Selesai
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fa-solid fa-clock" style="margin-right:4px"></i>Belum Dibuat
                                </span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap">
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
