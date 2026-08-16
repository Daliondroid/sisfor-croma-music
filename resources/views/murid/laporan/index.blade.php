@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar-menu') @include('murid.partials.sidebar') @endsection

@push('styles')
<style>
/* ─── Skor badge ────────────────────────────────────── */
.skor-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem; height: 3rem;
    border-radius: 0.25rem;
    font-size: 1.1rem;
    font-weight: 700;
    flex-shrink: 0;
}
.skor-A-plus  { background: #dcfce7; color: #15803d; }
.skor-A       { background: #dcfce7; color: #16a34a; }
.skor-A-minus { background: #d1fae5; color: #059669; }
.skor-B-plus  { background: #dbeafe; color: #1d4ed8; }
.skor-B       { background: #dbeafe; color: #2563eb; }
.skor-B-minus { background: #e0e7ff; color: #4f46e5; }
.skor-C-plus  { background: #fef9c3; color: #a16207; }
.skor-C       { background: #fef9c3; color: #b45309; }
.skor-C-minus { background: #ffedd5; color: #c2410c; }
.skor-none    { background: var(--bg-light); color: var(--text-light); border: 1px solid var(--topbar-border); }

/* ─── Report card ───────────────────────────────────── */
.report-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--topbar-border);
    text-decoration: none;
    color: inherit;
    transition: background .15s;
    cursor: pointer;
}
.report-card:last-child { border-bottom: none; }
.report-card:hover { background: var(--bg-light); }

.report-card-body { flex: 1; min-width: 0; }
.report-card-title {
    font-size: .95rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.report-card-meta {
    font-size: .75rem;
    color: var(--text-light);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
}
.report-card-eval {
    font-size: .82rem;
    color: var(--text-light);
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.report-chevron {
    color: var(--text-light);
    font-size: .85rem;
    font-weight: 700;
    flex-shrink: 0;
    align-self: center;
}

/* ─── Empty state ───────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    color: var(--text-light);
}
</style>
@endpush

@section('content')

@php
    use Carbon\Carbon;
    $skorClass = fn($s) => match($s) {
        'A+' => 'skor-A-plus',  'A'  => 'skor-A',       'A-' => 'skor-A-minus',
        'B+' => 'skor-B-plus',  'B'  => 'skor-B',       'B-' => 'skor-B-minus',
        'C+' => 'skor-C-plus',  'C'  => 'skor-C',       'C-' => 'skor-C-minus',
        default => 'skor-none',
    };
@endphp

<div class="page-header">
    <div>
        <h2>Laporan Bulanan</h2>
        <div class="breadcrumb">Murid / <span>Laporan Bulanan</span></div>
    </div>
</div>

<div class="card">
    @forelse($reports as $r)
        @php
            $bulanLabel = Carbon::parse($r->periode_bulan)->translatedFormat('F Y');
        @endphp
        <a href="{{ route('murid.laporan.show', $r) }}" class="report-card">
            {{-- Skor badge --}}
            <div class="skor-badge {{ $skorClass($r->skor) }}">
                {{ $r->skor ?? '—' }}
            </div>

            <div class="report-card-body">
                <div class="report-card-title">
                    {{ $bulanLabel }}
                    @if($r->spp?->programKursus)
                        <span class="badge badge-info">
                            {{ $r->spp->programKursus->nama_program }}
                        </span>
                    @endif
                </div>
                <div class="report-card-meta">
                    @if($r->url_video)
                        <span class="badge badge-info">VIDEO TERSEDIA</span>
                    @endif
                    @if($r->evaluasi_bulanan)
                        <span class="badge badge-success">EVALUASI GURU</span>
                    @endif
                </div>
                @php
                    $firstProgres = $r->jadwals->first(fn($j) => $j->progresMurid?->catatan_perkembangan);
                @endphp
                @if($firstProgres?->progresMurid?->catatan_perkembangan)
                    <div class="report-card-eval">
                        <strong style="font-size:.68rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em">Catatan: </strong>
                        {{ $firstProgres->progresMurid->catatan_perkembangan }}
                    </div>
                @elseif($r->evaluasi_bulanan)
                    <div class="report-card-eval">{{ $r->evaluasi_bulanan }}</div>
                @else
                    <div class="report-card-eval" style="font-style:italic">Belum ada evaluasi dari guru.</div>
                @endif
            </div>

            <span class="report-chevron">›</span>
        </a>
    @empty
        <div class="empty-state">
            <div class="empty-state-title">Belum ada laporan bulanan.</div>
            <div class="empty-state-description">Laporan akan muncul setelah bulan berjalan selesai dan dikompilasi oleh guru.</div>
        </div>
    @endforelse
</div>

@endsection