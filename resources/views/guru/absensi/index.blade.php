@extends('layouts.app')
@section('title', 'Data Absensi')
@section('page-title', 'Data Absensi')

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
    <h2>Data Absensi Murid</h2>
    <div class="breadcrumb">Guru / <span>Data Absensi</span></div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            @if(request('id_spp'))
                <input type="hidden" name="id_spp" value="{{ request('id_spp') }}">
            @endif
            <input type="month" name="bulan" class="form-control form-control-sm" style="width:auto"
                   value="{{ $bulan }}" onchange="this.form.submit()"/>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Tampilkan
            </button>
            @if(request('id_spp'))
                <a href="{{ route('guru.absensi.index', ['bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-xmark"></i> Reset Filter
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Statistik --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(10rem,1fr));gap:1rem;margin-bottom:1.5rem">
    <div class="card" style="padding:1rem 1.5rem;display:flex;align-items:center;gap:1rem">
        <div style="width:2.5rem;height:2.5rem;border-radius:0.625rem;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-calendar-days" style="color:var(--primary-blue)"></i>
        </div>
        <div><div style="font-size:1.5rem;font-weight:700">{{ $totalSesiAll }}</div><div style="font-size:.72rem;color:var(--text-light)">Total Sesi</div></div>
    </div>
    <div class="card" style="padding:1rem 1.5rem;display:flex;align-items:center;gap:1rem">
        <div style="width:2.5rem;height:2.5rem;border-radius:0.625rem;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-circle-check" style="color:#16a34a"></i>
        </div>
        <div><div style="font-size:1.5rem;font-weight:700;color:#16a34a">{{ $totalHadirAll }}</div><div style="font-size:.72rem;color:var(--text-light)">Hadir</div></div>
    </div>
    <div class="card" style="padding:1rem 1.5rem;display:flex;align-items:center;gap:1rem">
        <div style="width:2.5rem;height:2.5rem;border-radius:0.625rem;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-circle-xmark" style="color:#dc2626"></i>
        </div>
        <div><div style="font-size:1.5rem;font-weight:700;color:#dc2626">{{ $totalAbsenAll }}</div><div style="font-size:.72rem;color:var(--text-light)">Tidak Hadir</div></div>
    </div>
    <div class="card" style="padding:1rem 1.5rem;display:flex;align-items:center;gap:1rem">
        <div style="width:2.5rem;height:2.5rem;border-radius:0.625rem;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-clock-rotate-left" style="color:#d97706"></i>
        </div>
        <div><div style="font-size:1.5rem;font-weight:700;color:#d97706">{{ $totalBelumAll }}</div><div style="font-size:.72rem;color:var(--text-light)">Belum Diisi</div></div>
    </div>
    @if($totalMenunggu > 0)
    <div class="card" style="padding:1rem 1.5rem;display:flex;align-items:center;gap:1rem;border:1px solid #fbbf24">
        <div style="width:2.5rem;height:2.5rem;border-radius:0.625rem;background:#fef9c3;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-bell" style="color:#a16207"></i>
        </div>
        <div><div style="font-size:1.5rem;font-weight:700;color:#a16207">{{ $totalMenunggu }}</div><div style="font-size:.72rem;color:var(--text-light)">Perlu Verifikasi</div></div>
    </div>
    @endif
</div>

{{-- Layout dua kolom --}}
<div style="display:grid;grid-template-columns:{{ $selectedSpp ? '1fr 1fr' : '1fr' }};gap:1.5rem;align-items:start">

    {{-- Tabel Rekap Per Murid --}}
    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fa-solid fa-users" style="color:var(--primary-blue);margin-right:0.5rem"></i>
                Rekap per Murid
                <span style="font-size:.78rem;color:var(--text-light);font-weight:400;margin-left:0.25rem">
                    — {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                </span>
            </h3>
        </div>

        @if($rekapAbsensi->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="8" width="48" height="64" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 24h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 56V36l8 8 6-4 10 12" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity=".6"/></svg>
                </div>
                <div class="empty-state-title">Tidak ada data absensi.</div>
                <div class="empty-state-description">Tidak ada data jadwal pada bulan ini.</div>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Murid</th>
                            <th style="text-align:center">Sesi</th>
                            <th style="text-align:center;color:#16a34a">Hadir</th>
                            <th style="text-align:center;color:#dc2626">Absen</th>
                            <th style="text-align:center;color:#d97706">Belum</th>
                            <th>Kehadiran</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapAbsensi as $rekap)
                        @php
                            $isSelected = $selectedSpp && $selectedSpp->id_spp == $rekap->spp->id_spp;
                            $persen     = $rekap->persen_hadir;
                            $barColor   = $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626');
                        @endphp
                        <tr style="{{ $isSelected ? 'background:#eff6ff;' : '' }}">
                            <td>
                                <div style="font-weight:600">{{ $rekap->murid->nama_murid ?? '-' }}</div>
                                <div style="font-size:.75rem;color:var(--text-light)">
                                    {{ $rekap->program->nama_program ?? '-' }}
                                    · <span class="badge {{ $rekap->spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}" style="font-size:.68rem">{{ $rekap->spp->tipe_les }}</span>
                                    @if($rekap->menunggu > 0)
                                        <span class="badge badge-warning" style="font-size:.68rem;margin-left:0.25rem">
                                            <i class="fa-solid fa-bell"></i> {{ $rekap->menunggu }} perlu verifikasi
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align:center;font-weight:600">{{ $rekap->total_sesi }}</td>
                            <td style="text-align:center">
                                <span style="color:#16a34a;font-weight:700">{{ $rekap->hadir }}</span>
                            </td>
                            <td style="text-align:center">
                                <span style="color:{{ $rekap->tidak_hadir > 0 ? '#dc2626' : 'var(--text-light)' }};font-weight:{{ $rekap->tidak_hadir > 0 ? '700' : '400' }}">
                                    {{ $rekap->tidak_hadir }}
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span style="color:{{ $rekap->belum_diisi > 0 ? '#d97706' : 'var(--text-light)' }}">
                                    {{ $rekap->belum_diisi }}
                                </span>
                            </td>
                            <td style="min-width:7.5rem">
                                <div style="display:flex;align-items:center;gap:0.5rem">
                                    <div style="flex:1;height:0.25rem;background:#f3f4f6;border-radius:0.1875rem;overflow:hidden">
                                        <div style="width:{{ $persen }}%;height:100%;background:{{ $barColor }};border-radius:0.1875rem;transition:.4s"></div>
                                    </div>
                                    <span style="font-size:.78rem;font-weight:700;color:{{ $barColor }};min-width:2.25rem">{{ $persen }}%</span>
                                </div>
                            </td>
                            <td style="text-align:center">
                                <a href="{{ route('guru.absensi.index', ['bulan' => $bulan, 'id_spp' => $rekap->spp->id_spp]) }}"
                                   class="btn btn-outline btn-sm">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Detail Sesi Per Murid + Tombol Verifikasi --}}
    @if($selectedSpp)
    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fa-solid fa-list-check" style="color:var(--primary-blue);margin-right:0.5rem"></i>
                Detail — {{ $selectedSpp->murid->nama_murid ?? '-' }}
            </h3>
            <span style="font-size:.78rem;color:var(--text-light)">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
            </span>
        </div>

        @if($detailJadwals->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="14" y="14" width="52" height="52" rx="8" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M32 40h16M40 32v16" stroke="var(--primary-blue)" stroke-width="2.5" stroke-linecap="round" opacity=".5"/></svg>
                </div>
                <div class="empty-state-title">Tidak ada jadwal ditemukan.</div>
            </div>
        @else
            <div style="padding:0">
                @foreach($detailJadwals as $j)
                @php
                    $statusMurid = $j->status_kehadiran_murid;
                    $statusGuru  = $j->status_kehadiran_guru;
                    $perluVerif  = $j->presensi_diisi_oleh === 'Murid' && is_null($j->verified_at ?? null);

                    $colorMurid = match($statusMurid) {
                        'Hadir'       => '#16a34a',
                        'Tidak Hadir' => '#dc2626',
                        default       => '#d97706',
                    };
                    $bgMurid = match($statusMurid) {
                        'Hadir'       => '#f0fdf4',
                        'Tidak Hadir' => '#fef2f2',
                        default       => '#fffbeb',
                    };
                    $iconMurid = match($statusMurid) {
                        'Hadir'       => 'fa-circle-check',
                        'Tidak Hadir' => 'fa-circle-xmark',
                        default       => 'fa-circle-question',
                    };
                @endphp
                <div style="padding:1rem 1.5rem;border-bottom:1px solid #f3f4f6;{{ $perluVerif ? 'background:#fffbeb;' : '' }}">
                    <div style="display:flex;gap:1rem;align-items:flex-start">
                        {{-- Nomor sesi --}}
                        <div style="width:2rem;height:2rem;border-radius:50%;
                                    background:{{ $perluVerif ? '#d97706' : 'var(--primary-blue)' }};
                                    color:#fff;display:flex;align-items:center;justify-content:center;
                                    font-size:.8rem;font-weight:700;flex-shrink:0">
                            {{ $j->sesi_ke }}
                        </div>

                        <div style="flex:1;min-width:0">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem;flex-wrap:wrap">
                                <div>
                                    <div style="font-weight:600;font-size:.875rem">
                                        {{ $j->tanggal->translatedFormat('l, d M Y') }}
                                    </div>
                                    <div style="font-size:.78rem;color:var(--text-light);margin-top:0.125rem">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}
                                        @if($perluVerif)
                                            <span class="badge badge-warning" style="font-size:.68rem;margin-left:0.25rem">
                                                <i class="fa-solid fa-bell"></i> Diisi Murid
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div style="background:{{ $bgMurid }};color:{{ $colorMurid }};border-radius:0.5rem;
                                            padding:0.25rem 0.5rem;font-size:.75rem;font-weight:700;white-space:nowrap;display:flex;align-items:center;gap:0.25rem">
                                    <i class="fa-solid {{ $iconMurid }}"></i>
                                    {{ $statusMurid ?? 'Belum Diisi' }}
                                </div>
                            </div>

                            {{-- Status Guru --}}
                            <div style="margin-top:0.5rem;display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
                                <span style="font-size:.75rem;color:var(--text-light)">Guru:</span>
                                @if($statusGuru === 'Hadir')
                                    <span style="font-size:.75rem;color:#16a34a;font-weight:600"><i class="fa-solid fa-circle-check"></i> Hadir</span>
                                @elseif($statusGuru === 'Tidak Hadir')
                                    <span style="font-size:.75rem;color:#dc2626;font-weight:600"><i class="fa-solid fa-circle-xmark"></i> Tidak Hadir</span>
                                @else
                                    <span style="font-size:.75rem;color:#d97706"><i class="fa-solid fa-circle-question"></i> Belum Diisi</span>
                                @endif

                                @if($j->waktu_presensi_diisi)
                                    <span style="font-size:.72rem;color:var(--text-light)">
                                        · Dicatat {{ $j->waktu_presensi_diisi->translatedFormat('d M, H:i') }}
                                        oleh {{ $j->presensi_diisi_oleh }}
                                    </span>
                                @endif
                            </div>

                            {{-- Tombol verifikasi --}}
                            @if($perluVerif)
                                <form method="POST"
                                      action="{{ route('guru.absensi.verifikasi', $j->id_jadwal) }}"
                                      style="margin-top:0.5rem">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-badge-check"></i> Verifikasi Kehadiran
                                    </button>
                                </form>
                            @elseif(!is_null($j->verified_at ?? null))
                                <div style="margin-top:0.5rem;font-size:.72rem;color:#16a34a">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Terverifikasi {{ \Carbon\Carbon::parse($j->verified_at)->translatedFormat('d M Y, H:i') }}
                                </div>
                            @endif

                            {{-- Progres KBM --}}
                            @if($j->progresMurid)
                                <div style="margin-top:0.5rem;background:#f8faff;border:1px solid #dbeafe;border-radius:0.5rem;padding:0.5rem 1rem">
                                    <div style="font-size:.75rem;font-weight:600;color:var(--primary-blue);margin-bottom:0.25rem">
                                        <i class="fa-solid fa-book-open" style="margin-right:0.25rem"></i>Materi KBM
                                    </div>
                                    <div style="font-size:.8rem">{{ $j->progresMurid->materi_diajarkan }}</div>
                                    @if($j->progresMurid->catatan_perkembangan)
                                        <div style="font-size:.78rem;color:var(--text-light);margin-top:0.25rem;font-style:italic">
                                            {{ Str::limit($j->progresMurid->catatan_perkembangan, 80) }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif
</div>
@endsection
