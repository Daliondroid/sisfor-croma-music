@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Selamat Datang, {{ $guru->nama_guru }}</h2>
        <div class="breadcrumb">Guru / <span>Dashboard</span></div>
    </div>
</div>

{{-- Jadwal Hari Ini --}}
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-header">
        <h3>Jadwal Mengajar Hari Ini</h3>
        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-outline btn-sm">
            Lihat Semua
        </a>
    </div>

    @if($jadwalHariIni->isEmpty())
        <div class="empty-state" style="border:none">
            <div class="empty-state-title">Tidak ada jadwal mengajar hari ini.</div>
        </div>
    @else
        <div style="padding:0">
            @foreach($jadwalHariIni as $j)
            @php $sudahPresensi = $j->waktu_presensi_diisi !== null; @endphp
            <div style="padding:0.75rem 1.5rem;border-bottom:1px solid var(--topbar-border);display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
                {{-- Jam --}}
                <div style="min-width:4.375rem;text-align:center">
                    <div style="font-size:1rem;font-weight:700;color:var(--text-dark);font-variant-numeric:tabular-nums">{{ substr($j->jam_mulai,0,5) }}</div>
                    <div style="font-size:.7rem;color:var(--text-light)">—</div>
                    <div style="font-size:.85rem;font-weight:600;color:var(--text-light);font-variant-numeric:tabular-nums">{{ substr($j->jam_selesai,0,5) }}</div>
                </div>

                <div style="width:1px;height:3rem;background:var(--topbar-border)"></div>

                {{-- Info --}}
                <div style="flex:1;min-width:8.75rem">
                    <div style="font-weight:700;color:var(--text-dark)">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                    <div style="font-size:.78rem;color:var(--text-light);margin-top:0.125rem">
                        {{ $j->spp->programKursus->nama_program ?? '-' }}
                        · <span class="badge {{ $j->spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}">{{ strtoupper($j->spp->tipe_les ?? '') }}</span>
                    </div>
                </div>

                {{-- Status + Aksi --}}
                <div style="display:flex;align-items:center;gap:0.5rem">
                    @if($sudahPresensi)
                        <span class="badge badge-success">
                            PRESENSI TERCATAT
                        </span>
                        @if(!$j->progresMurid)
                            <a href="{{ route('guru.progres.create', ['id_jadwal' => $j->id_jadwal]) }}"
                               class="btn btn-outline btn-sm">
                                Input KBM
                            </a>
                        @endif
                    @else
                        <a href="{{ route('guru.presensi.index', ['jadwal' => $j->id_jadwal]) }}"
                           class="btn btn-primary btn-sm">
                            Input Presensi
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Quick Links --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(11.25rem,1fr));gap:1rem">
    <a href="{{ route('guru.jadwal.index') }}"
       class="card"
       style="display:flex;flex-direction:column;align-items:flex-start;gap:0.375rem;padding:1rem 2rem;
              text-decoration:none;">
        <span style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light)">Menu</span>
        <div style="font-weight:700;font-size:1rem;color:var(--text-dark)">Jadwal Kelas</div>
    </a>
    <a href="{{ route('guru.absensi.index') }}"
       class="card"
       style="display:flex;flex-direction:column;align-items:flex-start;gap:0.375rem;padding:1rem 2rem;
              text-decoration:none;">
        <span style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light)">Menu</span>
        <div style="font-weight:700;font-size:1rem;color:var(--text-dark)">Data Absensi</div>
    </a>
    <a href="{{ route('guru.presensi.index') }}"
       class="card"
       style="display:flex;flex-direction:column;align-items:flex-start;gap:0.375rem;padding:1rem 2rem;
              text-decoration:none;">
        <span style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light)">Menu</span>
        <div style="font-weight:700;font-size:1rem;color:var(--text-dark)">Input Presensi</div>
    </a>
    <a href="{{ route('guru.progres.index') }}"
       class="card"
       style="display:flex;flex-direction:column;align-items:flex-start;gap:0.375rem;padding:1rem 2rem;
              text-decoration:none;">
        <span style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light)">Menu</span>
        <div style="font-weight:700;font-size:1rem;color:var(--text-dark)">Laporan KBM</div>
    </a>
    <a href="{{ route('guru.monthly-report.index') }}"
       class="card"
       style="display:flex;flex-direction:column;align-items:flex-start;gap:0.375rem;padding:1rem 2rem;
              text-decoration:none;">
        <span style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light)">Menu</span>
        <div style="font-weight:700;font-size:1rem;color:var(--text-dark)">Laporan Bulanan</div>
    </a>
    <a href="{{ route('guru.profil.edit') }}"
       class="card"
       style="display:flex;flex-direction:column;align-items:flex-start;gap:0.375rem;padding:1rem 2rem;
              text-decoration:none;">
        <span style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-light)">Pengaturan</span>
        <div style="font-weight:700;font-size:1rem;color:var(--text-dark)">Profil Saya</div>
    </a>
</div>
@endsection
