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
    padding-bottom: 56.25%;
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

/* ── Skor badge besar ── */
.skor-big {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 64px; height: 64px;
    border-radius: 16px;
    font-size: 1.6rem;
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

/* ── Skor card ── */
.skor-card {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px 24px;
}
.skor-card-info { flex: 1; }
.skor-label { font-size: .72rem; color: var(--text-light); font-weight: 600; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; }
.skor-period { font-size: 1.05rem; font-weight: 700; }
.skor-sub { font-size: .78rem; color: var(--text-light); margin-top: 2px; }
</style>
@endpush

@section('content')

@php
    use Carbon\Carbon;
    $bulanLabel = Carbon::parse($report->periode_bulan)->translatedFormat('F Y');

    $skorClass = match($report->skor) {
        'A+' => 'skor-A-plus',  'A'  => 'skor-A',       'A-' => 'skor-A-minus',
        'B+' => 'skor-B-plus',  'B'  => 'skor-B',       'B-' => 'skor-B-minus',
        'C+' => 'skor-C-plus',  'C'  => 'skor-C',       'C-' => 'skor-C-minus',
        default => 'skor-none',
    };

    // Parse embed URL — support YouTube & Google Drive
    $videoEmbed = null;
    if ($report->url_video) {
        $url = $report->url_video;
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            $videoEmbed = 'https://www.youtube.com/embed/' . $m[1];
        } elseif (str_contains($url, 'drive.google.com')) {
            // Ubah /view atau /view?usp=... ke /preview agar bisa di-embed
            $videoEmbed = preg_replace('/\/view(\?.*)?$/', '/preview', $url);
            // Kalau format /file/d/{id}/... belum punya /view, pastikan pakai /preview
            if (!str_contains($videoEmbed, '/preview')) {
                $videoEmbed = rtrim($videoEmbed, '/') . '/preview';
            }
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
    <a href="{{ route('murid.laporan.pdf', $report) }}" class="btn btn-primary" target="_blank">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </a>
</div>

<div style="display:flex;flex-direction:column;gap:20px">

    {{-- Skor --}}
    <div class="card">
        <div class="skor-card">
            <div class="skor-big {{ $skorClass }}">{{ $report->skor ?? '—' }}</div>
            <div class="skor-card-info">
                <div class="skor-label">Skor Bulanan</div>
                <div class="skor-period">{{ $bulanLabel }}</div>
                <div class="skor-sub">
                    {{ $report->spp?->programKursus?->nama_program ?? '-' }}
                    @php $guruNama = $report->jadwals->first()?->guru?->nama_guru; @endphp
                    @if($guruNama) · {{ $guruNama }} @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Video Pembelajaran --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-film" style="color:var(--primary-blue);margin-right:8px"></i>Video Pembelajaran</h3>
            @if($report->url_video)
                <a href="{{ $report->url_video }}" target="_blank" class="btn btn-sm btn-outline">
                    <i class="fa-solid fa-external-link-alt"></i> Buka di tab baru
                </a>
            @endif
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

    {{-- Kesimpulan / Evaluasi --}}
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

@endsection