@extends('layouts.app')
@section('title', 'Jadwal Kelas')
@section('page-title', 'Jadwal Kelas')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}"       class="nav-item {{ request()->routeIs('guru.dashboard')       ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('guru.jadwal.index') }}"    class="nav-item {{ request()->routeIs('guru.jadwal*')         ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('guru.absensi.index') }}"   class="nav-item {{ request()->routeIs('guru.absensi*')        ? 'active' : '' }}"><i class="fa-solid fa-chart-bar"></i> Data Absensi</a>
    <a href="{{ route('guru.presensi.index') }}"  class="nav-item {{ request()->routeIs('guru.presensi*')       ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Input Presensi</a>
    <a href="{{ route('guru.progres.index') }}"   class="nav-item {{ request()->routeIs('guru.progres*')        ? 'active' : '' }}"><i class="fa-solid fa-book-open"></i> Laporan KBM</a>
    <a href="{{ route('guru.monthly-report.index') }}" class="nav-item {{ request()->routeIs('guru.monthly-report*') ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i> Laporan Bulanan</a>
@endsection

@section('content')
<div class="page-header">
    <h2>Jadwal Kelas</h2>
    <div class="breadcrumb">Guru / <span>Jadwal Kelas</span></div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="month" name="bulan" class="form-control form-control-sm" style="width:auto"
                   value="{{ $bulan }}" onchange="this.form.submit()"/>
        </form>
    </div>
</div>

{{-- Stat Cards --}}
<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-calendar-days"></i></div>
        <div><div class="stat-value">{{ $totalJadwal }}</div><div class="stat-label">Total Jadwal Bulan Ini</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fa-solid fa-sun"></i></div>
        <div><div class="stat-value">{{ $jadwalHariIni }}</div><div class="stat-label">Jadwal Hari Ini</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div><div class="stat-value">{{ $sudahPresensi }}</div><div class="stat-label">Sudah Presensi</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-clock"></i></div>
        <div><div class="stat-value">{{ $belumPresensi }}</div><div class="stat-label">Belum Presensi</div></div>
    </div>
</div>

{{-- Daftar Jadwal dikelompokkan per Hari --}}
@if($jadwalGrouped->isEmpty())
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="12" y="20" width="56" height="48" rx="6" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M12 32h56" stroke="var(--primary-blue)" stroke-width="2"/><rect x="24" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><rect x="52" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><circle cx="30" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="40" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="50" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="30" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="40" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="50" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/></svg>
        </div>
        <div class="empty-state-title">Tidak ada jadwal mengajar.</div>
        <div class="empty-state-description">Tidak ada jadwal mengajar pada bulan ini.</div>
    </div>
@else
    @foreach($jadwalGrouped as $tanggal => $items)
    @php
        $dt       = \Carbon\Carbon::parse($tanggal);
        $isToday  = $dt->isToday();
        $isPast   = $dt->isPast() && !$isToday;
    @endphp
    <div style="margin-bottom:1.5rem">
        {{-- Header tanggal --}}
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.5rem">
            <div style="width:3rem;height:3rem;border-radius:0.75rem;
                        background:{{ $isToday ? 'var(--primary-blue)' : 'var(--bg-light)' }};
                        color:{{ $isToday ? '#fff' : 'var(--text-dark)' }};
                        display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0">
                <span style="font-size:.65rem;font-weight:600;line-height:1;text-transform:uppercase">
                    {{ $dt->translatedFormat('M') }}
                </span>
                <span style="font-size:1.2rem;font-weight:700;line-height:1.2">{{ $dt->format('d') }}</span>
            </div>
            <div>
                <div style="font-weight:700;font-size:.95rem">
                    {{ $dt->translatedFormat('l') }}
                    @if($isToday)
                        <span class="badge badge-success" style="font-size:.68rem;margin-left:0.25rem">Hari Ini</span>
                    @endif
                </div>
                <div style="font-size:.75rem;color:var(--text-light)">{{ $items->count() }} sesi</div>
            </div>
            <hr style="flex:1;border:none;border-top:1px solid var(--topbar-border)">
        </div>

        {{-- Item jadwal --}}
        @foreach($items as $j)
        @php
            $sudahPresensiItem = $j->waktu_presensi_diisi !== null;
            $statusMurid = $j->status_kehadiran_murid;
        @endphp
        <div class="card" style="margin-bottom:0.5rem;{{ $isToday ? 'border-left:0.1875rem solid var(--primary-blue)' : '' }}">
            <div style="padding:1rem 1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">

                {{-- Jam --}}
                <div style="min-width:5rem;text-align:center">
                    <div style="font-size:1rem;font-weight:700;color:var(--primary-blue)">
                        {{ substr($j->jam_mulai, 0, 5) }}
                    </div>
                    <div style="font-size:.7rem;color:var(--text-light)">—</div>
                    <div style="font-size:.85rem;font-weight:600">{{ substr($j->jam_selesai, 0, 5) }}</div>
                </div>

                <div style="width:1px;height:3rem;background:var(--topbar-border)"></div>

                {{-- Info Murid --}}
                <div style="flex:1;min-width:10rem">
                    <div style="font-weight:700;font-size:.95rem">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                    <div style="font-size:.78rem;color:var(--text-light);margin-top:0.125rem">
                        {{ $j->spp->programKursus->nama_program ?? '-' }}
                        · <span class="badge {{ $j->spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}" style="font-size:.68rem">{{ $j->spp->tipe_les ?? '' }}</span>
                    </div>
                    @if($j->status_jadwal === 'Reschedule')
                        <span class="badge badge-warning" style="font-size:.68rem;margin-top:0.25rem;display:inline-flex">
                            <i class="fa-solid fa-rotate" style="margin-right:0.25rem"></i>Reschedule
                        </span>
                    @endif
                </div>

                {{-- Status presensi --}}
                <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
                    @if($sudahPresensiItem)
                        <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.25rem 1rem;border-radius:0.5rem;
                                     background:#f0fdf4;color:#16a34a;font-size:.78rem;font-weight:600">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ $statusMurid ?? 'Tercatat' }}
                        </span>
                    @elseif($isPast)
                        <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.25rem 1rem;border-radius:0.5rem;
                                     background:#fef2f2;color:#dc2626;font-size:.78rem;font-weight:600">
                            <i class="fa-solid fa-triangle-exclamation"></i> Belum Diisi
                        </span>
                    @elseif($isToday)
                        <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.25rem 1rem;border-radius:0.5rem;
                                     background:#fffbeb;color:#d97706;font-size:.78rem;font-weight:600">
                            <i class="fa-solid fa-clock"></i> Menunggu Input
                        </span>
                    @else
                        <span class="badge badge-gray" style="font-size:.75rem">Akan Datang</span>
                    @endif

                    @if(!$sudahPresensiItem && ($isToday || $isPast))
                        <a href="{{ route('guru.presensi.index', ['jadwal' => $j->id_jadwal]) }}"
                           class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-clipboard-check"></i> Input Presensi
                        </a>
                    @elseif($sudahPresensiItem && !$j->progresMurid)
                        <a href="{{ route('guru.progres.create', ['id_jadwal' => $j->id_jadwal]) }}"
                           class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-book-open"></i> Input KBM
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
@endif
@endsection
