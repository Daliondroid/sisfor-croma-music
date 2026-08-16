@extends('layouts.app')
@section('title', 'Detail Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar-menu') @include('murid.partials.sidebar') @endsection

@push('styles')
<style>
/* ── Video embed ── */
.video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #000;
    border-radius: 0 0 0.25rem 0.25rem;
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
    gap: 0.5rem;
    color: rgba(255,255,255,.6);
    font-size: .875rem;
}

/* ── Skor badge besar ── */
.skor-big {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 4rem; height: 4rem;
    border-radius: 0.25rem;
    font-size: 1.6rem;
    font-weight: 800;
    flex-shrink: 0;
}
.skor-A-plus, .skor-A, .skor-A-minus { background:#dcfce7; color:#15803d; }
.skor-B-plus, .skor-B, .skor-B-minus { background:#dbeafe; color:#1d4ed8; }
.skor-C-plus, .skor-C, .skor-C-minus { background:#fef9c3; color:#a16207; }
.skor-none { background:var(--bg-light); color:var(--text-light); border: 1px solid var(--topbar-border); }

/* ── Evaluasi box ── */
.eval-box {
    background: var(--bg-light);
    border: 1px solid var(--topbar-border);
    border-radius: 0.25rem;
    padding: 1rem 1rem;
    font-size: .875rem;
    line-height: 1.7;
    color: var(--text-dark);
}

/* ── Skor card ── */
.skor-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem 1.5rem;
}
.skor-card-info { flex: 1; }
.skor-label { font-size: .72rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.25rem; }
.skor-period { font-size: 1.05rem; font-weight: 700; color: var(--text-dark); }
.skor-sub { font-size: .78rem; color: var(--text-light); margin-top: 0.125rem; }
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
            $videoEmbed = preg_replace('/\/view(\?.*)?$/', '/preview', $url);
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
            <a href="{{ route('murid.laporan.index') }}" style="color:var(--text-dark);font-weight:600">Laporan Bulanan</a>
            / <span>{{ $bulanLabel }}</span>
        </div>
    </div>
    <div style="display:flex;gap:0.5rem">
        <a href="{{ route('murid.laporan.pdf', $report) }}" class="btn btn-primary btn-sm" target="_blank">
            Export PDF
        </a>
        <a href="{{ route('murid.laporan.index') }}" class="btn btn-outline btn-sm">
            Kembali
        </a>
    </div>
</div>

<div style="display:flex;flex-direction:column;gap:1.5rem">

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
            <h3>Video Pembelajaran</h3>
            @if($report->url_video)
                <a href="{{ $report->url_video }}" target="_blank" class="btn btn-sm btn-outline">
                    Buka di Tab Baru
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
                    <span>Video belum tersedia untuk bulan ini.</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Kesimpulan / Evaluasi --}}
    @if($report->evaluasi_bulanan)
    <div class="card">
        <div class="card-header">
            <h3>Kesimpulan Pembelajaran</h3>
        </div>
        <div class="card-body">
            <div class="eval-box">{{ $report->evaluasi_bulanan }}</div>
        </div>
    </div>
    @endif

</div>

@endsection