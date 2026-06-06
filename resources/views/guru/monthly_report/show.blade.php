@extends('layouts.app')
@section('title', 'Detail Laporan Bulanan')
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
        <h2>Detail Laporan Bulanan</h2>
        <div class="breadcrumb">Guru / Laporan Bulanan / <span>Detail</span></div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('guru.monthly-report.create', ['id_spp' => $spp->id_spp, 'bulan' => $bulan]) }}"
           class="btn btn-outline btn-sm">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
        <a href="{{ route('guru.monthly-report.pdf', $monthlyReport->id_report) }}"
           class="btn btn-primary btn-sm" target="_blank">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('guru.monthly-report.index', ['bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">

    {{-- Konten utama --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- Header laporan --}}
        <div class="card">
            <div style="padding:24px">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
                    <div style="width:52px;height:52px;border-radius:12px;background:var(--primary-blue);
                                color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;flex-shrink:0">
                        {{ $monthlyReport->skor }}
                    </div>
                    <div>
                        <div style="font-size:1.1rem;font-weight:700">{{ $murid->nama_murid ?? '-' }}</div>
                        <div style="font-size:.82rem;color:var(--text-light)">
                            {{ $program->nama_program ?? '-' }} · {{ $spp->tipe_les ?? '' }}
                        </div>
                        <div style="font-size:.78rem;color:var(--text-light);margin-top:2px">
                            <i class="fa-regular fa-calendar" style="margin-right:4px"></i>
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                        </div>
                    </div>
                    <div style="margin-left:auto;text-align:right">
                        <div style="font-size:.72rem;color:var(--text-light)">Dibuat</div>
                        <div style="font-size:.82rem;font-weight:600">
                            {{ $monthlyReport->created_at->translatedFormat('d M Y') }}
                        </div>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--topbar-border);margin:16px 0">

                <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:10px">
                    Evaluasi Bulanan
                </div>
                <div style="font-size:.9rem;line-height:1.7;white-space:pre-wrap">{{ $monthlyReport->evaluasi_bulanan }}</div>

                @if($monthlyReport->url_video)
                <div style="margin-top:16px;padding:14px;background:#f8faff;border:1px solid #dbeafe;border-radius:8px">
                    <div style="font-size:.78rem;font-weight:600;color:var(--primary-blue);margin-bottom:6px">
                        <i class="fa-brands fa-youtube" style="margin-right:6px"></i>Video KBM
                    </div>
                    <a href="{{ $monthlyReport->url_video }}" target="_blank"
                       style="font-size:.85rem;color:var(--primary-blue);word-break:break-all">
                        {{ $monthlyReport->url_video }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Tabel Rincian Sesi --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-list-check" style="color:var(--primary-blue);margin-right:8px"></i>Rincian Sesi</h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="text-align:center">Sesi</th>
                            <th>Tanggal</th>
                            <th style="text-align:center">Kehadiran</th>
                            <th>Materi KBM</th>
                            <th>Catatan Perkembangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwals as $j)
                        @php
                            $sm = $j->status_kehadiran_murid;
                            $badgeClass = match($sm) {
                                'Hadir'       => 'badge-success',
                                'Tidak Hadir' => 'badge-danger',
                                default       => 'badge-warning',
                            };
                        @endphp
                        <tr>
                            <td style="text-align:center;font-weight:700">{{ $j->sesi_ke }}</td>
                            <td style="white-space:nowrap">
                                {{ $j->tanggal->translatedFormat('d M Y') }}<br>
                                <span style="font-size:.72rem;color:var(--text-light)">
                                    {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge {{ $badgeClass }}" style="font-size:.72rem">
                                    {{ $sm ?? 'Belum' }}
                                </span>
                            </td>
                            <td style="font-size:.83rem">{{ $j->progresMurid->materi_diajarkan ?? '—' }}</td>
                            <td style="font-size:.8rem;color:var(--text-light)">
                                {{ $j->progresMurid->catatan_perkembangan ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar statistik --}}
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="card" style="padding:20px">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:14px">
                Ringkasan Kehadiran
            </div>
            @php
                $totalSesi  = $jadwals->count();
                $totalHadir = $jadwals->where('status_kehadiran_murid','Hadir')->count();
                $totalAbsen = $jadwals->where('status_kehadiran_murid','Tidak Hadir')->count();
                $persen     = $totalSesi > 0 ? round(($totalHadir/$totalSesi)*100) : 0;
                $barColor   = $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626');
            @endphp

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <span style="font-size:.85rem">Kehadiran</span>
                <span style="font-size:1.1rem;font-weight:700;color:{{ $barColor }}">{{ $persen }}%</span>
            </div>
            <div style="height:8px;background:#f3f4f6;border-radius:4px;overflow:hidden;margin-bottom:16px">
                <div style="width:{{ $persen }}%;height:100%;background:{{ $barColor }};border-radius:4px"></div>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px">
                <div style="display:flex;justify-content:space-between;font-size:.85rem">
                    <span style="color:var(--text-light)">Total Sesi</span>
                    <span style="font-weight:600">{{ $totalSesi }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.85rem">
                    <span style="color:#16a34a">Hadir</span>
                    <span style="font-weight:600;color:#16a34a">{{ $totalHadir }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.85rem">
                    <span style="color:#dc2626">Tidak Hadir</span>
                    <span style="font-weight:600;color:#dc2626">{{ $totalAbsen }}</span>
                </div>
                <hr style="border:none;border-top:1px solid var(--topbar-border);margin:4px 0">
                <div style="display:flex;justify-content:space-between;font-size:.9rem">
                    <span style="color:var(--text-light)">Skor</span>
                    <span style="font-size:1.2rem;font-weight:700;color:var(--primary-blue)">{{ $monthlyReport->skor }}</span>
                </div>
            </div>
        </div>

        <div class="card" style="padding:20px">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:12px">
                Guru Pengajar
            </div>
            <div style="font-weight:600">{{ $guru->nama_guru }}</div>
            @if($guru->spesialisasis->count())
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px">
                    @foreach($guru->spesialisasis as $s)
                        <span class="badge badge-info" style="font-size:.68rem">{{ $s->nama_spesialisasi }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
