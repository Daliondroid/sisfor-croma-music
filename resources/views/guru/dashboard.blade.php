@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard')

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
        <h2>Selamat Datang, {{ $guru->nama_guru }} 👋</h2>
        <div class="breadcrumb">Guru / <span>Dashboard</span></div>
    </div>
</div>

{{-- Jadwal Hari Ini --}}
<div class="card" style="margin-bottom:24px">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-calendar-day" style="color:var(--primary-blue);margin-right:8px"></i>
            Jadwal Mengajar Hari Ini
        </h3>
        <a href="{{ route('guru.jadwal.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-calendar-days"></i> Lihat Semua
        </a>
    </div>

    @if($jadwalHariIni->isEmpty())
        <div style="text-align:center;padding:48px;color:var(--text-light)">
            <i class="fa-solid fa-mug-hot" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px"></i>
            <p>Tidak ada jadwal mengajar hari ini.</p>
        </div>
    @else
        <div style="padding:0">
            @foreach($jadwalHariIni as $j)
            @php $sudahPresensi = $j->waktu_presensi_diisi !== null; @endphp
            <div style="padding:16px 24px;border-bottom:1px solid var(--topbar-border);display:flex;align-items:center;gap:16px;flex-wrap:wrap">
                {{-- Jam --}}
                <div style="min-width:70px;text-align:center">
                    <div style="font-size:1rem;font-weight:700;color:var(--primary-blue)">{{ substr($j->jam_mulai,0,5) }}</div>
                    <div style="font-size:.7rem;color:var(--text-light)">—</div>
                    <div style="font-size:.85rem;font-weight:600">{{ substr($j->jam_selesai,0,5) }}</div>
                </div>

                <div style="width:1px;height:44px;background:var(--topbar-border)"></div>

                {{-- Info --}}
                <div style="flex:1;min-width:140px">
                    <div style="font-weight:700">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                    <div style="font-size:.78rem;color:var(--text-light);margin-top:2px">
                        {{ $j->spp->programKursus->nama_program ?? '-' }}
                        · <span class="badge {{ $j->spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}" style="font-size:.68rem">{{ $j->spp->tipe_les ?? '' }}</span>
                    </div>
                </div>

                {{-- Status + Aksi --}}
                <div style="display:flex;align-items:center;gap:8px">
                    @if($sudahPresensi)
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;
                                     background:#f0fdf4;color:#16a34a;font-size:.78rem;font-weight:600">
                            <i class="fa-solid fa-circle-check"></i> Presensi Tercatat
                        </span>
                        @if(!$j->progresMurid)
                            <a href="{{ route('guru.progres.create', ['id_jadwal' => $j->id_jadwal]) }}"
                               class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-book-open"></i> Input KBM
                            </a>
                        @endif
                    @else
                        <a href="{{ route('guru.presensi.index', ['jadwal' => $j->id_jadwal]) }}"
                           class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-clipboard-check"></i> Input Presensi
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Quick Links --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px">
    <a href="{{ route('guru.jadwal.index') }}"
       style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px 16px;
              background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow-sm);
              color:var(--text-dark);text-decoration:none;transition:.2s;text-align:center"
       onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div style="width:48px;height:48px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center">
            <i class="fa-solid fa-calendar-days" style="color:var(--primary-blue);font-size:1.2rem"></i>
        </div>
        <div style="font-weight:600;font-size:.9rem">Jadwal Kelas</div>
    </a>
    <a href="{{ route('guru.absensi.index') }}"
       style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px 16px;
              background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow-sm);
              color:var(--text-dark);text-decoration:none;transition:.2s;text-align:center"
       onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div style="width:48px;height:48px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center">
            <i class="fa-solid fa-chart-bar" style="color:#16a34a;font-size:1.2rem"></i>
        </div>
        <div style="font-weight:600;font-size:.9rem">Data Absensi</div>
    </a>
    <a href="{{ route('guru.presensi.index') }}"
       style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px 16px;
              background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow-sm);
              color:var(--text-dark);text-decoration:none;transition:.2s;text-align:center"
       onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div style="width:48px;height:48px;border-radius:12px;background:#fffbeb;display:flex;align-items:center;justify-content:center">
            <i class="fa-solid fa-clipboard-check" style="color:#d97706;font-size:1.2rem"></i>
        </div>
        <div style="font-weight:600;font-size:.9rem">Input Presensi</div>
    </a>
    <a href="{{ route('guru.progres.index') }}"
       style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px 16px;
              background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow-sm);
              color:var(--text-dark);text-decoration:none;transition:.2s;text-align:center"
       onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div style="width:48px;height:48px;border-radius:12px;background:#fdf4ff;display:flex;align-items:center;justify-content:center">
            <i class="fa-solid fa-book-open" style="color:#9333ea;font-size:1.2rem"></i>
        </div>
        <div style="font-weight:600;font-size:.9rem">Laporan KBM</div>
    </a>
    <a href="{{ route('guru.monthly-report.index') }}"
       style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px 16px;
              background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow-sm);
              color:var(--text-dark);text-decoration:none;transition:.2s;text-align:center"
       onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div style="width:48px;height:48px;border-radius:12px;background:#fef2f2;display:flex;align-items:center;justify-content:center">
            <i class="fa-solid fa-file-lines" style="color:#dc2626;font-size:1.2rem"></i>
        </div>
        <div style="font-weight:600;font-size:.9rem">Laporan Bulanan</div>
    </a>
    <a href="{{ route('guru.profil.edit') }}"
       style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px 16px;
              background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow-sm);
              color:var(--text-dark);text-decoration:none;transition:.2s;text-align:center"
       onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div style="width:48px;height:48px;border-radius:12px;background:#f0f9ff;display:flex;align-items:center;justify-content:center">
            <i class="fa-solid fa-id-card" style="color:#0284c7;font-size:1.2rem"></i>
        </div>
        <div style="font-weight:600;font-size:.9rem">Profil Saya</div>
    </a>
</div>
@endsection
