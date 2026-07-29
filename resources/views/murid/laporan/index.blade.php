@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('murid.dashboard') }}"     class="nav-item {{ request()->routeIs('murid.dashboard')  ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('murid.jadwal.index') }}"  class="nav-item {{ request()->routeIs('murid.jadwal*')    ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('murid.laporan.index') }}" class="nav-item {{ request()->routeIs('murid.laporan*') ? 'active' : '' }}">
        <i class="fa-solid fa-book-open"></i> Laporan Bulanan
    </a>
    <a href="{{ route('murid.spp.index') }}"     class="nav-item {{ request()->routeIs('murid.spp*')       ? 'active' : '' }}"><i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya</a>
@endsection

@push('styles')
<style>
/* ─── Skor badge ────────────────────────────────────── */
.skor-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem; height: 3rem;
    border-radius: 0.75rem;
    font-size: 1.05rem;
    font-weight: 800;
    flex-shrink: 0;
    letter-spacing: -.0.3125rem;
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
.skor-none    { background: var(--bg-light); color: var(--text-light); }
[data-theme="dark"] .skor-A-plus, [data-theme="dark"] .skor-A, [data-theme="dark"] .skor-A-minus
    { background: #14312a; color: #4ade80; }
[data-theme="dark"] .skor-B-plus, [data-theme="dark"] .skor-B, [data-theme="dark"] .skor-B-minus
    { background: #1e3a5f; color: #60a5fa; }
[data-theme="dark"] .skor-C-plus, [data-theme="dark"] .skor-C, [data-theme="dark"] .skor-C-minus
    { background: #3d2e0a; color: #fbbf24; }

/* ─── Report card ───────────────────────────────────── */
.report-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem 1.5rem;
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
    font-size: .78rem;
    color: var(--text-light);
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
}
.report-card-meta span { display: flex; align-items: center; gap: 0.25rem; }
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
    font-size: .8rem;
    flex-shrink: 0;
    align-self: center;
}

/* ─── Empty state ───────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 4rem 1.5rem;
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
                        <span style="font-size:.65rem;font-weight:700;padding:0.125rem 0.5rem;border-radius:0.25rem;background:var(--bg-light);color:var(--text-light);text-transform:uppercase;letter-spacing:0.025rem">
                            {{ $r->spp->programKursus->nama_program }}
                        </span>
                    @endif
                </div>
                <div class="report-card-meta">
                    @if($r->url_video)
                        <span style="color:var(--primary-blue)"><i class="fa-solid fa-film"></i> Ada video</span>
                    @endif
                    @if($r->evaluasi_bulanan)
                        <span><i class="fa-solid fa-comment-dots"></i> Ada evaluasi guru</span>
                    @endif
                </div>
                @php
                    // catatan_perkembangan dari jadwal pertama yang punya progres
                    $firstProgres = $r->jadwals->first(fn($j) => $j->progresMurid?->catatan_perkembangan);
                @endphp
                @if($firstProgres?->progresMurid?->catatan_perkembangan)
                    <div class="report-card-eval">
                        <span style="font-size:.68rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:0.025rem">Catatan: </span>
                        {{ $firstProgres->progresMurid->catatan_perkembangan }}
                    </div>
                @elseif($r->evaluasi_bulanan)
                    <div class="report-card-eval">{{ $r->evaluasi_bulanan }}</div>
                @else
                    <div class="report-card-eval" style="font-style:italic">Belum ada evaluasi dari guru.</div>
                @endif
            </div>

            <i class="fa-solid fa-chevron-right report-chevron"></i>
        </a>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="12" width="48" height="56" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 28h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 40h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 52h16" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div class="empty-state-title">Belum ada laporan bulanan.</div>
            <div class="empty-state-description">Laporan akan muncul setelah bulan berjalan selesai dan dikompilasi oleh guru.</div>
        </div>
    @endforelse
</div>

@endsection