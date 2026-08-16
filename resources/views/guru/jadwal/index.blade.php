@extends('layouts.app')
@section('title', 'Jadwal Kelas')
@section('page-title', 'Jadwal Kelas')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Jadwal Kelas</h2>
        <div class="breadcrumb">Guru / <span>Jadwal Kelas</span></div>
    </div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="month" name="bulan" class="form-control form-control-sm" style="width:auto"
                   value="{{ $bulan }}" onchange="this.form.submit()"/>
        </form>
    </div>
</div>

{{-- Open KPI Strips --}}
<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalJadwal }}</div>
            <div class="stat-label">Total Jadwal Bulan Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $jadwalHariIni }}</div>
            <div class="stat-label">Jadwal Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $sudahPresensi }}</div>
            <div class="stat-label">Sudah Presensi</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $belumPresensi }}</div>
            <div class="stat-label">Belum Presensi</div>
        </div>
    </div>
</div>

{{-- Daftar Jadwal dikelompokkan per Hari --}}
@if($jadwalGrouped->isEmpty())
    <div class="empty-state">
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
            <div style="width:3rem;height:3rem;border-radius:0.25rem;
                        background:{{ $isToday ? 'var(--primary-navy)' : 'var(--bg-light)' }};
                        color:{{ $isToday ? '#fff' : 'var(--text-dark)' }};
                        display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;border:1px solid var(--topbar-border)">
                <span style="font-size:.65rem;font-weight:700;line-height:1;text-transform:uppercase;letter-spacing:0.04em">
                    {{ $dt->translatedFormat('M') }}
                </span>
                <span style="font-size:1.2rem;font-weight:700;line-height:1.2;font-variant-numeric:tabular-nums">{{ $dt->format('d') }}</span>
            </div>
            <div>
                <div style="font-weight:700;font-size:.95rem;color:var(--text-dark)">
                    {{ $dt->translatedFormat('l') }}
                    @if($isToday)
                        <span class="badge badge-success" style="margin-left:0.25rem">HARI INI</span>
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
        <div class="card" style="margin-bottom:0.5rem">
            <div style="padding:0.75rem 1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">

                {{-- Jam --}}
                <div style="min-width:5rem;text-align:center">
                    <div style="font-size:1rem;font-weight:700;color:var(--text-dark);font-variant-numeric:tabular-nums">
                        {{ substr($j->jam_mulai, 0, 5) }}
                    </div>
                    <div style="font-size:.7rem;color:var(--text-light)">—</div>
                    <div style="font-size:.85rem;font-weight:600;color:var(--text-light);font-variant-numeric:tabular-nums">{{ substr($j->jam_selesai, 0, 5) }}</div>
                </div>

                <div style="width:1px;height:3rem;background:var(--topbar-border)"></div>

                {{-- Info Murid --}}
                <div style="flex:1;min-width:10rem">
                    <div style="font-weight:700;font-size:.95rem;color:var(--text-dark)">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                    <div style="font-size:.78rem;color:var(--text-light);margin-top:0.125rem">
                        {{ $j->spp->programKursus->nama_program ?? '-' }}
                        · <span class="badge {{ $j->spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}">{{ strtoupper($j->spp->tipe_les ?? '') }}</span>
                    </div>
                    @if($j->status_jadwal === 'Reschedule')
                        <span class="badge badge-warning" style="margin-top:0.25rem">
                            RESCHEDULE
                        </span>
                    @endif
                </div>

                {{-- Status presensi --}}
                <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
                    @if($sudahPresensiItem)
                        <span class="badge badge-success">
                            {{ strtoupper($statusMurid ?? 'TERCATAT') }}
                        </span>
                    @elseif($isPast)
                        <span class="badge badge-danger">
                            BELUM DIISI
                        </span>
                    @elseif($isToday)
                        <span class="badge badge-warning">
                            MENUNGGU INPUT
                        </span>
                    @else
                        <span class="badge badge-gray">AKAN DATANG</span>
                    @endif

                    @if(!$sudahPresensiItem && ($isToday || $isPast))
                        <a href="{{ route('guru.presensi.index', ['jadwal' => $j->id_jadwal]) }}"
                           class="btn btn-primary btn-sm">
                            Input Presensi
                        </a>
                    @elseif($sudahPresensiItem && !$j->progresMurid)
                        <a href="{{ route('guru.progres.create', ['id_jadwal' => $j->id_jadwal]) }}"
                           class="btn btn-outline btn-sm">
                            Input KBM
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
