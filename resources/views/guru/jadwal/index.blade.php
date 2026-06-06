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
    <div>
        <h2>Jadwal Kelas</h2>
        <div class="breadcrumb">Guru / <span>Jadwal Kelas</span></div>
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

{{-- Stat Cards --}}
<div class="stats-grid" style="margin-bottom:24px">
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
    <div class="card">
        <div style="text-align:center;padding:60px;color:var(--text-light)">
            <i class="fa-solid fa-calendar-xmark" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:14px"></i>
            <p>Tidak ada jadwal mengajar pada bulan ini.</p>
        </div>
    </div>
@else
    @foreach($jadwalGrouped as $tanggal => $items)
    @php
        $dt       = \Carbon\Carbon::parse($tanggal);
        $isToday  = $dt->isToday();
        $isPast   = $dt->isPast() && !$isToday;
    @endphp
    <div style="margin-bottom:20px">
        {{-- Header tanggal --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
            <div style="width:46px;height:46px;border-radius:12px;
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
                        <span class="badge badge-success" style="font-size:.68rem;margin-left:6px">Hari Ini</span>
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
        <div class="card" style="margin-bottom:10px;{{ $isToday ? 'border-left:3px solid var(--primary-blue)' : '' }}">
            <div style="padding:16px 20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap">

                {{-- Jam --}}
                <div style="min-width:80px;text-align:center">
                    <div style="font-size:1rem;font-weight:700;color:var(--primary-blue)">
                        {{ substr($j->jam_mulai, 0, 5) }}
                    </div>
                    <div style="font-size:.7rem;color:var(--text-light)">—</div>
                    <div style="font-size:.85rem;font-weight:600">{{ substr($j->jam_selesai, 0, 5) }}</div>
                </div>

                <div style="width:1px;height:50px;background:var(--topbar-border)"></div>

                {{-- Info Murid --}}
                <div style="flex:1;min-width:160px">
                    <div style="font-weight:700;font-size:.95rem">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                    <div style="font-size:.78rem;color:var(--text-light);margin-top:2px">
                        {{ $j->spp->programKursus->nama_program ?? '-' }}
                        · <span class="badge {{ $j->spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}" style="font-size:.68rem">{{ $j->spp->tipe_les ?? '' }}</span>
                    </div>
                    @if($j->status_jadwal === 'Reschedule')
                        <span class="badge badge-warning" style="font-size:.68rem;margin-top:4px;display:inline-flex">
                            <i class="fa-solid fa-rotate" style="margin-right:4px"></i>Reschedule
                        </span>
                    @endif
                </div>

                {{-- Status presensi --}}
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    @if($sudahPresensiItem)
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;
                                     background:#f0fdf4;color:#16a34a;font-size:.78rem;font-weight:600">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ $statusMurid ?? 'Tercatat' }}
                        </span>
                    @elseif($isPast)
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;
                                     background:#fef2f2;color:#dc2626;font-size:.78rem;font-weight:600">
                            <i class="fa-solid fa-triangle-exclamation"></i> Belum Diisi
                        </span>
                    @elseif($isToday)
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;
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
