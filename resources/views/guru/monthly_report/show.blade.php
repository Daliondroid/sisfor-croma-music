@extends('layouts.app')
@section('title', 'Detail Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Detail Laporan Bulanan</h2>
        <div class="breadcrumb">Guru / Laporan Bulanan / <span>Detail</span></div>
    </div>
    <div style="display:flex;gap:0.5rem">
        <a href="{{ route('guru.monthly-report.create', ['id_spp' => $spp->id_spp, 'bulan' => $bulan]) }}"
           class="btn btn-outline btn-sm">
            Edit
        </a>
        <a href="{{ route('guru.monthly-report.pdf', $monthlyReport->id_report) }}"
           class="btn btn-primary btn-sm" target="_blank">
            Export PDF
        </a>
        <a href="{{ route('guru.monthly-report.index', ['bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
            Kembali
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 20rem;gap:1.5rem;align-items:start">

    {{-- Konten utama --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem">

        {{-- Header laporan --}}
        <div class="card">
            <div style="padding:1.5rem">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem">
                    <div style="width:3.5rem;height:3.5rem;border-radius:0.25rem;background:var(--primary-navy);
                                color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;flex-shrink:0">
                        {{ $monthlyReport->skor }}
                    </div>
                    <div>
                        <div style="font-size:1.1rem;font-weight:700;color:var(--text-dark)">{{ $murid->nama_murid ?? '-' }}</div>
                        <div style="font-size:.82rem;color:var(--text-light)">
                            {{ $program->nama_program ?? '-' }} · {{ $spp->tipe_les ?? '' }}
                        </div>
                        <div style="font-size:.78rem;color:var(--text-light);margin-top:0.125rem">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                        </div>
                    </div>
                    <div style="margin-left:auto;text-align:right">
                        <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700">Dibuat</div>
                        <div style="font-size:.82rem;font-weight:600;font-variant-numeric:tabular-nums;color:var(--text-dark)">
                            {{ $monthlyReport->created_at->translatedFormat('d M Y') }}
                        </div>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--topbar-border);margin:1rem 0">

                <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700;margin-bottom:0.5rem">
                    Evaluasi Bulanan
                </div>
                <div style="font-size:.9rem;line-height:1.7;white-space:pre-wrap;color:var(--text-dark)">{{ $monthlyReport->evaluasi_bulanan }}</div>

                @if($monthlyReport->url_video)
                <div style="margin-top:1rem;padding:1rem;background:var(--bg-light);border:1px solid var(--topbar-border);border-radius:0.25rem">
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-light);margin-bottom:0.25rem">
                        Video KBM
                    </div>
                    <a href="{{ $monthlyReport->url_video }}" target="_blank"
                       style="font-size:.85rem;color:var(--primary-navy);word-break:break-all;font-weight:600">
                        {{ $monthlyReport->url_video }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Tabel Rincian Sesi --}}
        <div class="card">
            <div class="card-header">
                <h3>Rincian Sesi</h3>
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
                            <td style="text-align:center;font-weight:700;font-variant-numeric:tabular-nums">{{ $j->sesi_ke }}</td>
                            <td style="white-space:nowrap">
                                <span style="font-weight:600;color:var(--text-dark)">{{ $j->tanggal->translatedFormat('d M Y') }}</span><br>
                                <span style="font-size:.72rem;color:var(--text-light);font-variant-numeric:tabular-nums">
                                    {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span class="badge {{ $badgeClass }}">
                                    {{ strtoupper($sm ?? 'BELUM') }}
                                </span>
                            </td>
                            <td style="font-size:.83rem;color:var(--text-dark)">{{ $j->progresMurid->materi_diajarkan ?? '—' }}</td>
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
    <div style="display:flex;flex-direction:column;gap:1rem">
        <div class="card" style="padding:1.5rem">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700;margin-bottom:1rem">
                Ringkasan Kehadiran
            </div>
            @php
                $totalSesi  = $jadwals->count();
                $totalHadir = $jadwals->where('status_kehadiran_murid','Hadir')->count();
                $totalAbsen = $jadwals->where('status_kehadiran_murid','Tidak Hadir')->count();
                $persen     = $totalSesi > 0 ? round(($totalHadir/$totalSesi)*100) : 0;
                $barColor   = $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626');
            @endphp

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
                <span style="font-size:.85rem;color:var(--text-dark)">Kehadiran</span>
                <span style="font-size:1.1rem;font-weight:700;color:{{ $barColor }};font-variant-numeric:tabular-nums">{{ $persen }}%</span>
            </div>
            <div style="height:0.25rem;background:#f3f4f6;border-radius:0.125rem;overflow:hidden;margin-bottom:1rem">
                <div style="width:{{ $persen }}%;height:100%;background:{{ $barColor }};border-radius:0.125rem"></div>
            </div>

            <div style="display:flex;flex-direction:column;gap:0.5rem">
                <div style="display:flex;justify-content:space-between;font-size:.85rem">
                    <span style="color:var(--text-light)">Total Sesi</span>
                    <span style="font-weight:600;font-variant-numeric:tabular-nums">{{ $totalSesi }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.85rem">
                    <span style="color:#16a34a">Hadir</span>
                    <span style="font-weight:600;color:#16a34a;font-variant-numeric:tabular-nums">{{ $totalHadir }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.85rem">
                    <span style="color:#dc2626">Tidak Hadir</span>
                    <span style="font-weight:600;color:#dc2626;font-variant-numeric:tabular-nums">{{ $totalAbsen }}</span>
                </div>
                <hr style="border:none;border-top:1px solid var(--topbar-border);margin:0.25rem 0">
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:.9rem">
                    <span style="color:var(--text-light)">Skor</span>
                    <span style="font-size:1.2rem;font-weight:700;color:var(--text-dark)">{{ $monthlyReport->skor }}</span>
                </div>
            </div>
        </div>

        <div class="card" style="padding:1.5rem">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700;margin-bottom:1rem">
                Guru Pengajar
            </div>
            <div style="font-weight:700;color:var(--text-dark)">{{ $guru->nama_guru }}</div>
            @if($guru->spesialisasis->count())
                <div style="display:flex;flex-wrap:wrap;gap:0.25rem;margin-top:0.375rem">
                    @foreach($guru->spesialisasis as $s)
                        <span class="badge badge-info">{{ $s->nama_spesialisasi }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
