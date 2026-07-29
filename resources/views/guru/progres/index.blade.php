@extends('layouts.app')
@section('title', 'Laporan KBM Harian')
@section('page-title', 'Laporan KBM Harian')

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
    <h2>Laporan KBM Harian</h2>
    <div class="breadcrumb">Guru / <span>Laporan KBM</span></div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <select name="id_spp" class="form-control form-control-sm" style="width:auto" onchange="this.form.submit()">
                <option value="">Semua Murid</option>
                @foreach($muridDiajar as $spp)
                    <option value="{{ $spp->id_spp }}" {{ request('id_spp') == $spp->id_spp ? 'selected' : '' }}>
                        {{ $spp->murid->nama_murid ?? '-' }}
                    </option>
                @endforeach
            </select>
            <input type="month" name="bulan" class="form-control form-control-sm" style="width:auto"
                   value="{{ request('bulan', now()->format('Y-m')) }}" onchange="this.form.submit()"/>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-book-open" style="color:var(--primary-blue);margin-right:0.5rem"></i>Riwayat Laporan KBM</h3>
        <span style="font-size:.8rem;color:var(--text-light)">{{ $progres->total() }} record</span>
    </div>

    @if($progres->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="8" width="48" height="64" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 24h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 56V36l8 8 6-4 10 12" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity=".6"/></svg>
            </div>
            <div class="empty-state-title">Belum ada laporan KBM.</div>
            <div class="empty-state-description">Belum ada laporan KBM yang diinput.</div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Murid</th>
                        <th>Program</th>
                        <th>Materi Diajarkan</th>
                        <th>Catatan Perkembangan</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($progres as $p)
                    <tr>
                        <td style="white-space:nowrap">
                            <div style="font-weight:600">{{ $p->jadwal->tanggal->translatedFormat('d M Y') }}</div>
                            <div style="font-size:.75rem;color:var(--text-light)">
                                Sesi {{ $p->jadwal->sesi_ke }}
                            </div>
                        </td>
                        <td>{{ $p->jadwal->spp->murid->nama_murid ?? '-' }}</td>
                        <td>{{ $p->jadwal->spp->programKursus->nama_program ?? '-' }}</td>
                        <td style="max-width:12.5rem">{{ Str::limit($p->materi_diajarkan, 60) }}</td>
                        <td style="max-width:12.5rem;color:var(--text-light);font-size:.82rem">
                            {{ Str::limit($p->catatan_perkembangan, 60) }}
                        </td>
                        <td style="text-align:center">
                            <a href="{{ route('guru.progres.edit', $p->id_progres) }}" class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.5rem">
            {{ $progres->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
