@extends('layouts.app')
@section('title', 'Detail Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('murid.dashboard') }}"     class="nav-item {{ request()->routeIs('murid.dashboard')  ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('murid.jadwal.index') }}"  class="nav-item {{ request()->routeIs('murid.jadwal*')    ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('murid.laporan.index') }}" class="nav-item {{ request()->routeIs('murid.laporan*')   ? 'active' : '' }}"><i class="fa-solid fa-book-open"></i> Laporan Bulanan</a>
    <a href="{{ route('murid.spp.index') }}"     class="nav-item {{ request()->routeIs('murid.spp*')       ? 'active' : '' }}"><i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya</a>
@endsection

@push('styles')
<style>
/* ── Video embed ── */
.video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 */
    background: #000;
    border-radius: 0 0 var(--radius) var(--radius);
    overflow: hidden;
}
.video-container iframe {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: none;
}
.video-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: rgba(255,255,255,.5);
    font-size: .875rem;
}
.video-placeholder i { font-size: 2.5rem; opacity: .4; }

/* ── Info rows ── */
.info-row {
    display: flex;
    gap: 8px;
    padding: 11px 0;
    border-bottom: 1px solid var(--topbar-border);
    font-size: .875rem;
    align-items: flex-start;
}
.info-row:last-child { border-bottom: none; }
.info-label {
    width: 140px;
    flex-shrink: 0;
    color: var(--text-light);
    font-weight: 600;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    padding-top: 1px;
}
.info-value { flex: 1; color: var(--text-dark); line-height: 1.6; }

/* ── Skor badge besar ── */
.skor-big {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px; height: 56px;
    border-radius: 14px;
    font-size: 1.4rem;
    font-weight: 800;
    flex-shrink: 0;
}
.skor-A-plus, .skor-A, .skor-A-minus { background:#dcfce7; color:#15803d; }
.skor-B-plus, .skor-B, .skor-B-minus { background:#dbeafe; color:#1d4ed8; }
.skor-C-plus, .skor-C, .skor-C-minus { background:#fef9c3; color:#a16207; }
.skor-none { background:var(--bg-light); color:var(--text-light); }
[data-theme="dark"] .skor-A-plus,[data-theme="dark"] .skor-A,[data-theme="dark"] .skor-A-minus{ background:#14312a;color:#4ade80; }
[data-theme="dark"] .skor-B-plus,[data-theme="dark"] .skor-B,[data-theme="dark"] .skor-B-minus{ background:#1e3a5f;color:#60a5fa; }
[data-theme="dark"] .skor-C-plus,[data-theme="dark"] .skor-C,[data-theme="dark"] .skor-C-minus{ background:#3d2e0a;color:#fbbf24; }

/* ── Pertemuan table ── */
.pertemuan-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.pertemuan-table th {
    background: var(--th-bg); padding: 10px 14px;
    text-align:left; font-size:.72rem; font-weight:600;
    color:var(--text-light); text-transform:uppercase; letter-spacing:.5px;
}
.pertemuan-table td { padding:12px 14px; border-bottom:1px solid var(--topbar-border); color:var(--text-dark); }
.pertemuan-table tr:last-child td { border-bottom:none; }
.pertemuan-table tr:hover td { background:var(--table-hover); }

/* ── Evaluasi box ── */
.eval-box {
    background: var(--bg-light);
    border-left: 4px solid var(--primary-blue);
    border-radius: 0 8px 8px 0;
    padding: 14px 18px;
    font-size: .875rem;
    line-height: 1.7;
    color: var(--text-dark);
}
</style>
@endpush

@section('content')

@php
    use Carbon\Carbon;
    $bulanLabel = Carbon::parse($report->periode_bulan)->translatedFormat('F Y');
    $totalSesi  = $report->jadwals->count();
    $hadirSesi  = $report->jadwals->where('status_kehadiran_murid', 'Hadir')->count();
    $pct        = $totalSesi > 0 ? round($hadirSesi / $totalSesi * 100) : 0;

    $skorClass = match($report->skor) {
        'A+' => 'skor-A-plus',  'A'  => 'skor-A',       'A-' => 'skor-A-minus',
        'B+' => 'skor-B-plus',  'B'  => 'skor-B',       'B-' => 'skor-B-minus',
        'C+' => 'skor-C-plus',  'C'  => 'skor-C',       'C-' => 'skor-C-minus',
        default => 'skor-none',
    };

    // Parse YouTube embed URL
    $videoEmbed = null;
    if ($report->url_video) {
        $url = $report->url_video;
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            $videoEmbed = 'https://www.youtube.com/embed/' . $m[1];
        } elseif (str_contains($url, 'drive.google.com')) {
            // Google Drive: ubah ke embed
            $videoEmbed = preg_replace('/\/view(\?.*)?$/', '/preview', $url);
        } else {
            $videoEmbed = $url;
        }
    }
@endphp

<div class="page-header">
    <div>
        <h2>Laporan {{ $bulanLabel }}</h2>
        <div class="breadcrumb">
            <a href="{{ route('murid.laporan.index') }}" style="color:var(--primary-blue)">Laporan Bulanan</a>
            / <span>{{ $bulanLabel }}</span>
        </div>
    </div>
    {{-- Tombol Download PDF --}}
    <a href="{{ route('murid.laporan.pdf', $report) }}"
       class="btn btn-primary" target="_blank">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    {{-- ── Kolom Kiri ── --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- Video Pembelajaran --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-film" style="color:var(--primary-blue);margin-right:8px"></i>Video Pembelajaran</h3>
            </div>
            <div class="video-container">
                @if($videoEmbed)
                    <iframe
                        src="{{ $videoEmbed }}"
                        title="Video Pembelajaran {{ $bulanLabel }}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                @else
                    <div class="video-placeholder">
                        <i class="fa-solid fa-circle-play"></i>
                        <span>Video belum tersedia untuk bulan ini.</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabel Pertemuan --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-list-check" style="color:var(--primary-blue);margin-right:8px"></i>Detail Pertemuan</h3>
            </div>
            <div class="table-wrap">
                <table class="pertemuan-table">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th style="width:130px">Tanggal</th>
                            <th>Materi Pembelajaran</th>
                            <th style="width:100px;text-align:center">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report->jadwals->sortBy('tanggal') as $i => $j)
                        <tr>
                            <td style="text-align:center;color:var(--text-light);font-weight:600">{{ $i + 1 }}</td>
                            <td>{{ $j->tanggal->translatedFormat('d M Y') }}</td>
                            <td>
                                {{ $j->progresMurid?->materi_diajarkan ?? '-' }}
                                @if($j->progresMurid?->catatan_perkembangan)
                                    <div style="font-size:.72rem;color:var(--text-light);margin-top:2px">
                                        {{ $j->progresMurid->catatan_perkembangan }}
                                    </div>
                                @endif
                            </td>
                            <td style="text-align:center">
                                @php $st = $j->status_kehadiran_murid; @endphp
                                <span class="badge {{ match($st) {
                                    'Hadir' => 'badge-success',
                                    'Izin'  => 'badge-warning',
                                    default => 'badge-danger',
                                } }}">{{ $st ?? '—' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:var(--text-light);padding:20px">
                                Belum ada data pertemuan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Evaluasi --}}
        @if($report->evaluasi_bulanan)
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-comment-dots" style="color:var(--primary-blue);margin-right:8px"></i>Kesimpulan Pembelajaran</h3>
            </div>
            <div class="card-body">
                <div class="eval-box">{{ $report->evaluasi_bulanan }}</div>
            </div>
        </div>
        @endif

    </div>

    {{-- ── Kolom Kanan ── --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- Ringkasan --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-chart-pie" style="color:var(--primary-blue);margin-right:8px"></i>Ringkasan</h3>
            </div>
            <div class="card-body" style="padding:20px">

                {{-- Skor --}}
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--topbar-border)">
                    <div class="skor-big {{ $skorClass }}">{{ $report->skor ?? '—' }}</div>
                    <div>
                        <div style="font-size:.72rem;color:var(--text-light);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Skor Bulanan</div>
                        <div style="font-size:1rem;font-weight:700;margin-top:2px">{{ $bulanLabel }}</div>
                    </div>
                </div>

                {{-- Stat rows --}}
                <div class="info-row">
                    <div class="info-label">Kehadiran</div>
                    <div class="info-value">
                        <span style="font-weight:700;font-size:1.1rem;color:{{ $pct >= 80 ? '#16a34a' : '#dc2626' }}">{{ $pct }}%</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Total Hadir</div>
                    <div class="info-value" style="font-weight:700">{{ $hadirSesi }} / {{ $totalSesi }} sesi</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Program</div>
                    <div class="info-value">{{ $report->spp?->programKursus?->nama_program ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Guru</div>
                    <div class="info-value">
                        {{ $report->jadwals->first()?->guru?->nama_guru ?? '-' }}
                    </div>
                </div>
                @if($report->url_video)
                <div class="info-row">
                    <div class="info-label">Video</div>
                    <div class="info-value">
                        <a href="{{ $report->url_video }}" target="_blank" style="color:var(--primary-blue);font-weight:600;font-size:.8rem">
                            <i class="fa-solid fa-external-link-alt"></i> Buka di tab baru
                        </a>
                    </div>
                </div>
                @endif

                {{-- Tombol Download ulang --}}
                <div style="margin-top:20px">
                    <a href="{{ route('murid.laporan.pdf', $report) }}"
                       class="btn btn-primary" style="width:100%;justify-content:center" target="_blank">
                        <i class="fa-solid fa-file-pdf"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection