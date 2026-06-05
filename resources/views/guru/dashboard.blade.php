@extends('layouts.app')
@section('title', 'Laporan Absensi')
@section('page-title', 'Laporan Absensi')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}" class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('guru.presensi.index') }}" class="nav-item {{ request()->routeIs('guru.presensi*') ? 'active' : '' }}">
        <i class="fa-solid fa-clipboard-check"></i> Input Presensi
    </a>
    <a href="{{ route('guru.absensi.index') }}" class="nav-item {{ request()->routeIs('guru.absensi*') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-bar"></i> Laporan Absensi
    </a>
    <a href="{{ route('guru.profil.edit') }}" class="nav-item {{ request()->routeIs('guru.profil*') ? 'active' : '' }}">
        <i class="fa-solid fa-id-card"></i> Profil Saya
    </a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Laporan Absensi Murid</h2>
        <div class="breadcrumb">Guru / <span>Laporan Absensi</span></div>
    </div>
</div>

{{-- ── Filter Bulan ──────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" action="{{ route('guru.absensi.index') }}" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            @if(request('id_spp'))
                <input type="hidden" name="id_spp" value="{{ request('id_spp') }}">
            @endif
            <label style="font-weight:600;font-size:.875rem;white-space:nowrap">
                <i class="fa-regular fa-calendar" style="color:var(--primary-blue);margin-right:6px"></i>Pilih Bulan:
            </label>
            <input type="month" name="bulan" class="form-control" style="width:auto"
                   value="{{ $bulan }}" onchange="this.form.submit()"/>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Tampilkan
            </button>
            @if(request('id_spp'))
                <a href="{{ route('guru.absensi.index', ['bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-xmark"></i> Reset Filter Murid
                </a>
            @endif
        </form>
    </div>
</div>

{{-- ── Ringkasan Statistik ──────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;margin-bottom:24px">
    {{-- Total Sesi --}}
    <div class="card" style="padding:20px 24px;display:flex;align-items:center;gap:16px">
        <div style="width:46px;height:46px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-calendar-days" style="color:var(--primary-blue);font-size:1.1rem"></i>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:700;line-height:1">{{ $totalSesiAll }}</div>
            <div style="font-size:.75rem;color:var(--text-light);margin-top:2px">Total Sesi</div>
        </div>
    </div>

    {{-- Hadir --}}
    <div class="card" style="padding:20px 24px;display:flex;align-items:center;gap:16px">
        <div style="width:46px;height:46px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-circle-check" style="color:#16a34a;font-size:1.1rem"></i>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:700;line-height:1;color:#16a34a">{{ $totalHadirAll }}</div>
            <div style="font-size:.75rem;color:var(--text-light);margin-top:2px">Hadir</div>
        </div>
    </div>

    {{-- Tidak Hadir --}}
    <div class="card" style="padding:20px 24px;display:flex;align-items:center;gap:16px">
        <div style="width:46px;height:46px;border-radius:12px;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-circle-xmark" style="color:#dc2626;font-size:1.1rem"></i>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:700;line-height:1;color:#dc2626">{{ $totalAbsenAll }}</div>
            <div style="font-size:.75rem;color:var(--text-light);margin-top:2px">Tidak Hadir</div>
        </div>
    </div>

    {{-- Belum Diisi --}}
    <div class="card" style="padding:20px 24px;display:flex;align-items:center;gap:16px">
        <div style="width:46px;height:46px;border-radius:12px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-clock-rotate-left" style="color:#d97706;font-size:1.1rem"></i>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:700;line-height:1;color:#d97706">{{ $totalBelumAll }}</div>
            <div style="font-size:.75rem;color:var(--text-light);margin-top:2px">Belum Diisi</div>
        </div>
    </div>
</div>

{{-- ── Layout dua kolom: tabel rekap | detail sesi ─────────── --}}
<div style="display:grid;grid-template-columns:{{ $selectedSpp ? '1fr 1fr' : '1fr' }};gap:20px;align-items:start">

    {{-- ── Tabel Rekap Per Murid ── --}}
    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fa-solid fa-users" style="color:var(--primary-blue);margin-right:8px"></i>
                Rekap per Murid
                <span style="font-size:.78rem;color:var(--text-light);font-weight:400;margin-left:6px">
                    — {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                </span>
            </h3>
        </div>

        @if($rekapAbsensi->isEmpty())
            <div style="text-align:center;padding:48px;color:var(--text-light)">
                <i class="fa-solid fa-folder-open" style="font-size:2rem;opacity:.3;margin-bottom:12px;display:block"></i>
                <p>Tidak ada data jadwal pada bulan ini.</p>
            </div>
        @else
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:.875rem">
                    <thead>
                        <tr style="background:#f8faff;border-bottom:2px solid #e5e7eb">
                            <th style="padding:12px 20px;text-align:left;font-weight:600;color:var(--text-light);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Murid</th>
                            <th style="padding:12px 16px;text-align:center;font-weight:600;color:var(--text-light);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Sesi</th>
                            <th style="padding:12px 16px;text-align:center;font-weight:600;color:#16a34a;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Hadir</th>
                            <th style="padding:12px 16px;text-align:center;font-weight:600;color:#dc2626;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Absen</th>
                            <th style="padding:12px 16px;text-align:center;font-weight:600;color:#d97706;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Belum</th>
                            <th style="padding:12px 20px;text-align:left;font-weight:600;color:var(--text-light);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Kehadiran</th>
                            <th style="padding:12px 16px;text-align:center;font-weight:600;color:var(--text-light);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapAbsensi as $rekap)
                        @php
                            $isSelected = $selectedSpp && $selectedSpp->id_spp == $rekap->spp->id_spp;
                            $persen     = $rekap->persen_hadir;
                            $barColor   = $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626');
                        @endphp
                        <tr style="border-bottom:1px solid #f3f4f6;{{ $isSelected ? 'background:#eff6ff;' : '' }}transition:.15s"
                            class="absensi-row">
                            <td style="padding:14px 20px">
                                <div style="font-weight:600">{{ $rekap->murid->nama_murid ?? '-' }}</div>
                                <div style="font-size:.75rem;color:var(--text-light)">
                                    {{ $rekap->program->nama_program ?? '-' }}
                                    · <span class="badge {{ $rekap->spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}" style="font-size:.68rem">{{ $rekap->spp->tipe_les }}</span>
                                </div>
                            </td>
                            <td style="padding:14px 16px;text-align:center;font-weight:600">{{ $rekap->total_sesi }}</td>
                            <td style="padding:14px 16px;text-align:center">
                                <span style="color:#16a34a;font-weight:700">{{ $rekap->hadir }}</span>
                            </td>
                            <td style="padding:14px 16px;text-align:center">
                                <span style="color:{{ $rekap->tidak_hadir > 0 ? '#dc2626' : 'var(--text-light)' }};font-weight:{{ $rekap->tidak_hadir > 0 ? '700' : '400' }}">
                                    {{ $rekap->tidak_hadir }}
                                </span>
                            </td>
                            <td style="padding:14px 16px;text-align:center">
                                <span style="color:{{ $rekap->belum_diisi > 0 ? '#d97706' : 'var(--text-light)' }}">
                                    {{ $rekap->belum_diisi }}
                                </span>
                            </td>
                            <td style="padding:14px 20px;min-width:140px">
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="flex:1;height:6px;background:#f3f4f6;border-radius:99px;overflow:hidden">
                                        <div style="height:100%;width:{{ $persen }}%;background:{{ $barColor }};border-radius:99px;transition:.4s"></div>
                                    </div>
                                    <span style="font-size:.8rem;font-weight:700;color:{{ $barColor }};min-width:36px">{{ $persen }}%</span>
                                </div>
                            </td>
                            <td style="padding:14px 16px;text-align:center">
                                <a href="{{ route('guru.absensi.index', ['bulan' => $bulan, 'id_spp' => $rekap->spp->id_spp]) }}"
                                   class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-outline' }}"
                                   style="font-size:.78rem;padding:5px 12px">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Detail Sesi Per Murid (muncul jika ada filter id_spp) ── --}}
    @if($selectedSpp)
    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fa-solid fa-list-check" style="color:var(--primary-blue);margin-right:8px"></i>
                Detail Sesi — {{ $selectedSpp->murid->nama_murid ?? '-' }}
            </h3>
            <span style="font-size:.78rem;color:var(--text-light)">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
            </span>
        </div>

        @if($detailJadwals->isEmpty())
            <div style="text-align:center;padding:40px;color:var(--text-light)">
                <p>Tidak ada jadwal ditemukan.</p>
            </div>
        @else
            <div style="padding:0">
                @foreach($detailJadwals as $j)
                @php
                    $statusMurid = $j->status_kehadiran_murid;
                    $statusGuru  = $j->status_kehadiran_guru;

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
                    $labelMurid = $statusMurid ?? 'Belum Diisi';
                @endphp
                <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;gap:14px;align-items:flex-start">
                    {{-- Nomor sesi --}}
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-blue);color:#fff;
                                display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;flex-shrink:0">
                        {{ $j->sesi_ke }}
                    </div>

                    {{-- Info --}}
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;flex-wrap:wrap">
                            <div>
                                <div style="font-weight:600;font-size:.875rem">
                                    {{ $j->tanggal->translatedFormat('l, d M Y') }}
                                </div>
                                <div style="font-size:.78rem;color:var(--text-light);margin-top:2px">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}
                                    @if($j->status_jadwal === 'Reschedule')
                                        &nbsp;<span class="badge badge-warning" style="font-size:.68rem">Reschedule</span>
                                    @endif
                                </div>
                            </div>
                            {{-- Status kehadiran murid --}}
                            <div style="background:{{ $bgMurid }};color:{{ $colorMurid }};border-radius:8px;
                                        padding:5px 10px;font-size:.75rem;font-weight:700;white-space:nowrap;display:flex;align-items:center;gap:5px">
                                <i class="fa-solid {{ $iconMurid }}"></i>
                                {{ $labelMurid }}
                            </div>
                        </div>

                        {{-- Status Guru --}}
                        <div style="margin-top:8px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                            <span style="font-size:.75rem;color:var(--text-light)">Guru:</span>
                            @if($statusGuru === 'Hadir')
                                <span style="font-size:.75rem;color:#16a34a;font-weight:600">
                                    <i class="fa-solid fa-circle-check"></i> Hadir
                                </span>
                            @elseif($statusGuru === 'Tidak Hadir')
                                <span style="font-size:.75rem;color:#dc2626;font-weight:600">
                                    <i class="fa-solid fa-circle-xmark"></i> Tidak Hadir
                                </span>
                            @else
                                <span style="font-size:.75rem;color:#d97706">
                                    <i class="fa-solid fa-circle-question"></i> Belum Diisi
                                </span>
                            @endif

                            {{-- Waktu input presensi --}}
                            @if($j->waktu_presensi_diisi)
                                <span style="font-size:.72rem;color:var(--text-light)">
                                    · Dicatat {{ $j->waktu_presensi_diisi->translatedFormat('d M, H:i') }}
                                    oleh {{ $j->presensi_diisi_oleh }}
                                </span>
                            @endif
                        </div>

                        {{-- Progres KBM (jika sudah diisi) --}}
                        @if($j->progresMurid)
                            <div style="margin-top:10px;background:#f8faff;border:1px solid #dbeafe;border-radius:8px;padding:10px 12px">
                                <div style="font-size:.75rem;font-weight:600;color:var(--primary-blue);margin-bottom:4px">
                                    <i class="fa-solid fa-book-open" style="margin-right:4px"></i>Materi KBM
                                </div>
                                <div style="font-size:.8rem">{{ $j->progresMurid->materi_diajarkan }}</div>
                                @if($j->progresMurid->catatan_perkembangan)
                                    <div style="font-size:.78rem;color:var(--text-light);margin-top:4px;font-style:italic">
                                        {{ Str::limit($j->progresMurid->catatan_perkembangan, 80) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif

</div>
@endsection