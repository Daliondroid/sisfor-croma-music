@extends('layouts.app')

@section('title', 'Jadwal Kelas Saya')
@section('page-title', 'Jadwal Kelas')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('murid.dashboard') }}" class="nav-item {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('murid.jadwal.index') }}" class="nav-item {{ request()->routeIs('murid.jadwal*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-days"></i> Jadwal Kelas
    </a>
    <a href="{{ route('murid.laporan.index') }}" class="nav-item {{ request()->routeIs('murid.laporan*') ? 'active' : '' }}">
        <i class="fa-solid fa-book-open"></i> Laporan Bulanan
    </a>
    <a href="{{ route('murid.spp.index') }}" class="nav-item {{ request()->routeIs('murid.spp*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya
    </a>
@endsection

@push('styles')
<style>
/* ─── Filter pills ──────────────────────────────────── */
.filter-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}
.filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.filter-pill {
    font-size: .72rem;
    font-weight: 600;
    padding: 4px 14px;
    border-radius: 20px;
    border: 1.5px solid var(--input-border);
    background: transparent;
    color: var(--text-light);
    text-decoration: none;
    transition: .15s;
    white-space: nowrap;
}
.filter-pill.active {
    background: var(--text-dark);
    color: var(--bg-white);
    border-color: var(--text-dark);
}
.filter-pill:hover:not(.active) { border-color: var(--text-dark); color: var(--text-dark); }

/* ─── Month picker ──────────────────────────────────── */
.month-picker-wrap {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.month-picker-label {
    font-size: .75rem;
    font-weight: 600;
    color: var(--text-light);
    white-space: nowrap;
}
.month-picker-input {
    font-family: inherit;
    font-size: .8rem;
    font-weight: 600;
    padding: 6px 12px;
    border: 1.5px solid var(--input-border);
    border-radius: 8px;
    background: var(--card-bg);
    color: var(--text-dark);
    cursor: pointer;
    transition: .15s;
}
.month-picker-input:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(0,86,179,.1);
}

/* ─── Section header (Senin, Selasa, …) ────────────── */
.day-section { margin-bottom: 8px; }
.day-section-header {
    font-size: .8rem;
    font-weight: 600;
    color: var(--text-light);
    padding: 10px 16px;
    background: var(--bg-light);
    border-radius: 10px 10px 0 0;
    border: 1px solid var(--topbar-border);
    border-bottom: none;
}

/* ─── Session item ──────────────────────────────────── */
.session-item {
    border: 1px solid var(--topbar-border);
    border-top: none;
    background: var(--card-bg);
    transition: background .15s;
}
.session-item:last-child { border-radius: 0 0 10px 10px; }

.session-main {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    cursor: pointer;
    user-select: none;
}
.session-main:hover { background: var(--bg-light); }

/* Left accent bar */
.session-accent {
    width: 3px;
    height: 36px;
    border-radius: 3px;
    flex-shrink: 0;
    background: var(--primary-blue);
}
.session-accent.confirmed { background: #16a34a; }
.session-accent.pending   { background: #d97706; }
.session-accent.tidak     { background: #dc2626; }
.session-accent.future    { background: #cbd5e1; }
.session-accent.belum     { background: #cbd5e1; }

.session-body { flex: 1; min-width: 0; }
.session-title {
    font-size: .875rem;
    font-weight: 600;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.session-sub {
    font-size: .72rem;
    color: var(--text-light);
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.session-sub span { display: flex; align-items: center; gap: 4px; }

/* Tipe tag */
.tipe-tag {
    font-size: .6rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.tipe-onsite { background: #dbeafe; color: #1d4ed8; }
.tipe-home   { background: #fef9c3; color: #a16207; }
[data-theme="dark"] .tipe-onsite { background: #1e3a5f; color: #60a5fa; }
[data-theme="dark"] .tipe-home   { background: #3d2e0a; color: #fbbf24; }

/* Status badge */
.s-badge {
    font-size: .68rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.s-confirmed { background: #dcfce7; color: #15803d; }
.s-pending   { background: #fef9c3; color: #a16207; }
.s-tidak     { background: #fee2e2; color: #b91c1c; }
.s-future    { background: var(--bg-light); color: var(--text-light); }
.s-belum     { background: var(--bg-light); color: var(--text-light); }
[data-theme="dark"] .s-confirmed { background: #14312a; color: #4ade80; }
[data-theme="dark"] .s-pending   { background: #3d2e0a; color: #fbbf24; }
[data-theme="dark"] .s-tidak     { background: #3d1515; color: #f87171; }
[data-theme="dark"] .s-future    { background: #252d3d; color: #94a3b8; }
[data-theme="dark"] .s-belum     { background: #252d3d; color: #94a3b8; }

/* Chevron */
.sess-chevron {
    font-size: .75rem;
    color: var(--text-light);
    transition: transform .2s;
    flex-shrink: 0;
}
.sess-chevron.open { transform: rotate(180deg); }

/* ─── Session detail (dropdown) ─────────────────────── */
.session-detail {
    display: none;
    padding: 0 16px 16px 16px;
    border-top: 1px solid var(--topbar-border);
}
.session-detail.open { display: block; }

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 14px;
}
@media (max-width: 600px) { .detail-grid { grid-template-columns: 1fr; } }

.detail-block {
    background: var(--bg-light);
    border-radius: 8px;
    padding: 12px 14px;
    font-size: .8rem;
}
.detail-block .db-label {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--text-light);
    margin-bottom: 5px;
}
.detail-block .db-value {
    color: var(--text-dark);
    line-height: 1.6;
}
.detail-block.full { grid-column: 1 / -1; }

/* Progres section */
.progres-section-title {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--text-light);
    margin-bottom: 6px;
    margin-top: 12px;
}
.progres-section-title:first-child { margin-top: 0; }

.materi-box {
    background: var(--card-bg);
    border: 1px solid var(--topbar-border);
    border-radius: 6px;
    padding: 10px 12px;
    font-size: .82rem;
    line-height: 1.6;
    color: var(--text-dark);
}

.catatan-box {
    background: var(--card-bg);
    border-left: 3px solid var(--primary-blue);
    border-radius: 0 6px 6px 0;
    padding: 10px 12px;
    font-size: .82rem;
    line-height: 1.6;
    color: var(--text-dark);
}

/* Foto bukti — thumbnail + lightbox trigger */
.foto-thumb-wrap { margin-top: 8px; position: relative; display: inline-block; }
.foto-thumb {
    width: 120px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid var(--topbar-border);
    cursor: pointer;
    transition: opacity .15s;
    display: block;
}
.foto-thumb:hover { opacity: .85; }
.foto-thumb-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,.3);
    border-radius: 6px;
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    gap: 4px;
    pointer-events: none;
    opacity: 0;
    transition: opacity .15s;
}
.foto-thumb-wrap:hover .foto-thumb-overlay { opacity: 1; }

/* ─── Lightbox ──────────────────────────────────────── */
.lightbox-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.85);
    z-index: 500;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.lightbox-backdrop.open { display: flex; }
.lightbox-img {
    max-width: 90vw;
    max-height: 88vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
}
.lightbox-close {
    position: absolute;
    top: 16px;
    right: 20px;
    color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
}
.lightbox-close:hover { background: rgba(255,255,255,.3); }

/* ─── Tombol Isi Presensi ───────────────────────────── */
.btn-isi-presensi {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: .8rem;
    font-weight: 700;
    background: var(--primary-blue);
    color: #fff;
    border: none;
    cursor: pointer;
    font-family: inherit;
    margin-top: 12px;
    transition: background .15s;
}
.btn-isi-presensi:hover { background: var(--primary-dark); }

/* ─── Empty state ───────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 48px 24px;
    color: var(--text-light);
    border: 1px solid var(--topbar-border);
    border-radius: 12px;
    background: var(--card-bg);
}

/* ─── Confirm modal ─────────────────────────────────── */
.confirm-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 300;
    align-items: center; justify-content: center;
}
.confirm-backdrop.open { display: flex; }
.confirm-box {
    background: var(--card-bg);
    border-radius: 14px;
    padding: 28px; width: 400px; max-width: 92vw;
    box-shadow: 0 20px 48px rgba(0,0,0,.22);
    border: 1px solid var(--topbar-border);
}
.confirm-icon {
    width: 46px; height: 46px; border-radius: 10px;
    background: #eff6ff; display: flex; align-items: center;
    justify-content: center; margin-bottom: 14px;
}
.confirm-icon i { color: var(--primary-blue); font-size: 1.2rem; }
.confirm-detail {
    background: var(--bg-light); border-radius: 8px;
    padding: 12px 14px; margin: 12px 0;
    font-size: .82rem; line-height: 1.9;
    border: 1px solid var(--topbar-border);
}
.confirm-detail i { width: 16px; color: var(--primary-blue); }
.confirm-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 16px; }
</style>
@endpush

@section('content')

@php
    use Carbon\Carbon;
    $namaHari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $namaBulanPanjang = ['Januari','Februari','Maret','April','Mei','Juni','Juli',
                         'Agustus','September','Oktober','November','Desember'];
    [$selYear, $selMonth] = explode('-', $selectedMonth);
@endphp

{{-- ── PAGE HEADER ── --}}
<div class="page-header">
    <div>
        <h2>Jadwal Kelas Saya</h2>
        <div class="breadcrumb">Murid / <span>Jadwal Kelas</span></div>
    </div>
</div>

{{-- ── FILTER PILLS ── --}}
<div class="filter-row">
    <div class="filter-pills">
        @foreach(['semua'=>'Semua', 'belum'=>'Perlu Diisi', 'hadir'=>'Hadir'] as $fk => $fl)
            <a href="{{ route('murid.jadwal.index', ['bulan' => $selectedMonth, 'filter' => $fk]) }}"
               class="filter-pill {{ $filter === $fk ? 'active' : '' }}">{{ $fl }}</a>
        @endforeach
    </div>
    <span style="font-size:.72rem;color:var(--text-light)">{{ $jadwalsFiltered->count() }} sesi</span>
</div>

{{-- ── MONTH PICKER ── --}}
<div class="month-picker-wrap">
    <span class="month-picker-label"><i class="fa-regular fa-calendar" style="margin-right:5px"></i>Bulan:</span>
    <input
        type="month"
        id="month-picker"
        class="month-picker-input"
        value="{{ $selectedMonth }}"
        @if($availableMonths->isNotEmpty())
            min="{{ $availableMonths->last() }}"
            max="{{ $availableMonths->first() }}"
        @endif
    />
</div>

{{-- ── DAFTAR JADWAL ── --}}
<div id="jadwal-list">
@if($jadwalsFiltered->isEmpty())
    <div class="empty-state">
        <i class="fa-solid fa-calendar-xmark" style="font-size:2rem;opacity:.2;display:block;margin-bottom:10px"></i>
        <p style="font-size:.875rem">Tidak ada jadwal ditemukan.</p>
    </div>
@else
    @php
        $byDay = $jadwalsFiltered->groupBy(fn($j) => $j->tanggal->format('Y-m-d'))->sortKeys();
    @endphp

    @foreach($byDay as $dateKey => $daySessions)
        @php
            $dt = Carbon::parse($dateKey);
            $isToday = $dt->isToday();
            $hariLabel = $namaHari[$dt->dayOfWeek] . ', ' . $dt->day . ' ' . $namaBulanPanjang[$dt->month - 1];
            if ($isToday) $hariLabel .= ' — Hari ini';
        @endphp

        <div class="day-section" style="margin-bottom:12px">
            <div class="day-section-header">{{ $hariLabel }}</div>

            @foreach($daySessions->sortBy('jam_mulai') as $j)
                @php
                    $tipe        = $j->spp?->tipe_les ?? 'Onsite';
                    $isHadir     = $j->status_kehadiran_murid === 'Hadir';
                    $isTidak     = $j->status_kehadiran_murid === 'Tidak Hadir';
                    $guruKonfirm = $j->status_kehadiran_guru  === 'Hadir';
                    $isFuture    = $j->tanggal->isFuture();
                    $bisaPresensi = $j->status_kehadiran_murid === null && !$isFuture && $j->is_active;
                    $progres     = $j->progresMurid;

                    if ($isHadir && $guruKonfirm)       $accentClass = 'confirmed';
                    elseif ($isHadir && !$guruKonfirm)  $accentClass = 'pending';
                    elseif ($isTidak)                   $accentClass = 'tidak';
                    elseif ($isFuture)                  $accentClass = 'future';
                    else                                $accentClass = 'belum';

                    $sessId = 'sess-' . $j->id_jadwal;
                @endphp

                <div class="session-item">
                    {{-- Row utama --}}
                    <div class="session-main" onclick="toggleSess('{{ $sessId }}')">
                        <div class="session-accent {{ $accentClass }}"></div>
                        <div class="session-body">
                            <div class="session-title">
                                {{ $j->spp?->programKursus?->nama_program ?? 'Program Musik' }}
                                <span class="tipe-tag {{ $tipe === 'Onsite' ? 'tipe-onsite' : 'tipe-home' }}">
                                    {{ $tipe === 'Onsite' ? 'Onsite' : 'Home' }}
                                </span>
                                @if($j->status_jadwal !== 'Sesuai Jadwal')
                                    <span class="tipe-tag" style="background:#fee2e2;color:#b91c1c">{{ $j->status_jadwal }}</span>
                                @endif
                            </div>
                            <div class="session-sub">
                                <span><i class="fa-solid fa-hashtag"></i> Sesi ke-{{ $j->sesi_ke }}</span>
                                <span><i class="fa-regular fa-clock"></i> {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</span>
                                <span><i class="fa-solid fa-chalkboard-user"></i> {{ $j->guru->nama_guru }}</span>
                            </div>
                        </div>

                        {{-- Status badge --}}
                        @if($isHadir && $guruKonfirm)
                            <span class="s-badge s-confirmed"><i class="fa-solid fa-circle-check"></i> Terkonfirmasi</span>
                        @elseif($isHadir && !$guruKonfirm)
                            <span class="s-badge s-pending"><i class="fa-solid fa-clock"></i> Menunggu Guru</span>
                        @elseif($isTidak)
                            <span class="s-badge s-tidak"><i class="fa-solid fa-circle-xmark"></i> Tidak Hadir</span>
                        @elseif($isFuture)
                            <span class="s-badge s-future"><i class="fa-regular fa-calendar"></i> Akan Datang</span>
                        @elseif($bisaPresensi)
                            <span class="s-badge" style="background:#eff6ff;color:var(--primary-blue);border:1px solid #bfdbfe">
                                <i class="fa-solid fa-hand-point-up"></i> Isi Presensi
                            </span>
                        @else
                            <span class="s-badge s-belum"><i class="fa-regular fa-circle"></i> Belum Diisi</span>
                        @endif

                        <i class="fa-solid fa-chevron-down sess-chevron" id="chev-{{ $sessId }}"></i>
                    </div>

                    {{-- Detail (dropdown) --}}
                    <div class="session-detail" id="{{ $sessId }}">
                        <div class="detail-grid">
                            {{-- Info Sesi --}}
                            <div class="detail-block">
                                <div class="db-label">Info Sesi</div>
                                <div class="db-value">
                                    <div><i class="fa-regular fa-calendar" style="width:16px;color:var(--primary-blue)"></i>
                                        {{ $j->tanggal->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div><i class="fa-regular fa-clock" style="width:16px;color:var(--primary-blue)"></i>
                                        {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}
                                    </div>
                                    <div><i class="fa-solid fa-hashtag" style="width:16px;color:var(--primary-blue)"></i>
                                        Sesi ke-{{ $j->sesi_ke }}
                                    </div>
                                </div>
                            </div>

                            {{-- Guru --}}
                            <div class="detail-block">
                                <div class="db-label">Guru</div>
                                <div class="db-value">
                                    <div><i class="fa-solid fa-chalkboard-user" style="width:16px;color:var(--primary-blue)"></i>
                                        {{ $j->guru->nama_guru }}
                                    </div>
                                    <div style="margin-top:4px"><i class="fa-solid fa-music" style="width:16px;color:var(--primary-blue)"></i>
                                        {{ $j->spp?->programKursus?->nama_program ?? '—' }}
                                    </div>
                                    <div style="margin-top:4px"><i class="fa-solid fa-location-dot" style="width:16px;color:var(--primary-blue)"></i>
                                        {{ $tipe }}
                                    </div>
                                </div>
                            </div>

                            {{-- Progres / Materi --}}
                            @if($progres)
                                <div class="detail-block full">
                                    <div class="db-label">
                                        <i class="fa-solid fa-book-open" style="margin-right:4px"></i>Materi &amp; Progres
                                    </div>
                                    <div class="db-value">

                                        {{-- Materi diajarkan --}}
                                        @if($progres->materi_diajarkan)
                                            <div class="progres-section-title">Materi Diajarkan</div>
                                            <div class="materi-box">{{ $progres->materi_diajarkan }}</div>
                                        @endif

                                        {{-- Catatan perkembangan --}}
                                        @if($progres->catatan_perkembangan)
                                            <div class="progres-section-title">Catatan Guru</div>
                                            <div class="catatan-box">{{ $progres->catatan_perkembangan }}</div>
                                        @endif

                                        {{-- Foto dokumentasi --}}
                                        @if($progres->url_foto)
                                            <div class="progres-section-title">Dokumentasi</div>
                                            <div class="foto-thumb-wrap"
                                                 onclick="bukaLightbox('{{ asset('storage/' . $progres->url_foto) }}')">
                                                <img src="{{ asset('storage/' . $progres->url_foto) }}"
                                                     alt="Foto progres" class="foto-thumb">
                                                <div class="foto-thumb-overlay">
                                                    <i class="fa-solid fa-expand"></i> Lihat
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @else
                                <div class="detail-block full" style="color:var(--text-light);font-size:.8rem">
                                    <div class="db-label"><i class="fa-solid fa-book-open" style="margin-right:4px"></i>Materi &amp; Progres</div>
                                    <div class="db-value" style="color:var(--text-light);font-style:italic">
                                        Belum ada catatan materi dari guru untuk sesi ini.
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Isi Presensi --}}
                        @if($bisaPresensi)
                            <button class="btn-isi-presensi"
                                onclick="bukaKonfirmasi(
                                    {{ $j->id_jadwal }},
                                    '{{ $namaHari[$j->tanggal->dayOfWeek] }}, {{ $j->tanggal->day }} {{ $namaBulanPanjang[$j->tanggal->month - 1] }} {{ $j->tanggal->year }}',
                                    '{{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}',
                                    '{{ $j->spp?->programKursus?->nama_program ?? 'Program Musik' }}',
                                    '{{ $j->guru->nama_guru }}'
                                )">
                                <i class="fa-solid fa-clipboard-check"></i> Isi Presensi
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@endif
</div>

{{-- ── LIGHTBOX ── --}}
<div class="lightbox-backdrop" id="lightbox-backdrop" onclick="tutupLightbox(event)">
    <span class="lightbox-close" onclick="tutupLightbox(null, true)">
        <i class="fa-solid fa-xmark"></i>
    </span>
    <img src="" alt="Foto progres" class="lightbox-img" id="lightbox-img">
</div>

{{-- ── MODAL KONFIRMASI PRESENSI ── --}}
<div class="confirm-backdrop" id="confirm-backdrop">
    <div class="confirm-box">
        <div class="confirm-icon"><i class="fa-solid fa-clipboard-check"></i></div>
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:6px">Isi Presensi Kehadiran</h3>
        <p style="font-size:.82rem;color:var(--text-light)">Konfirmasi bahwa kamu hadir pada sesi berikut:</p>
        <div class="confirm-detail" id="confirm-detail"></div>
        <p style="font-size:.72rem;color:var(--text-light)">
            <i class="fa-solid fa-circle-info" style="color:var(--primary-blue)"></i>
            Pengajuan ini akan diteruskan ke guru untuk dikonfirmasi.
        </p>
        <div class="confirm-actions">
            <button class="btn btn-outline btn-sm" onclick="tutupKonfirmasi()">
                <i class="fa-solid fa-xmark"></i> Batal
            </button>
            <form method="POST" action="{{ route('murid.presensi.store') }}" id="confirm-form">
                @csrf
                <input type="hidden" name="id_jadwal" id="confirm-id-jadwal"/>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-check"></i> Ya, Saya Hadir
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Toggle session detail ── */
function toggleSess(id) {
    const detail = document.getElementById(id);
    const chev   = document.getElementById('chev-' + id);
    detail.classList.toggle('open');
    chev.classList.toggle('open');
}

/* ── Lightbox ── */
function bukaLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-backdrop').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function tutupLightbox(e, force) {
    if (force || (e && e.target === document.getElementById('lightbox-backdrop'))) {
        document.getElementById('lightbox-backdrop').classList.remove('open');
        document.body.style.overflow = '';
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        tutupLightbox(null, true);
        tutupKonfirmasi();
    }
});

/* ── Modal presensi ── */
function bukaKonfirmasi(idJadwal, tanggal, waktu, program, guru) {
    document.getElementById('confirm-id-jadwal').value = idJadwal;
    document.getElementById('confirm-detail').innerHTML =
        `<div><i class="fa-solid fa-calendar"></i> <strong>${tanggal}</strong></div>` +
        `<div><i class="fa-regular fa-clock"></i> ${waktu}</div>` +
        `<div><i class="fa-solid fa-music"></i> ${program}</div>` +
        `<div><i class="fa-solid fa-chalkboard-user"></i> ${guru}</div>`;
    document.getElementById('confirm-backdrop').classList.add('open');
}
function tutupKonfirmasi() {
    document.getElementById('confirm-backdrop').classList.remove('open');
}
document.getElementById('confirm-backdrop').addEventListener('click', function(e) {
    if (e.target === this) tutupKonfirmasi();
});

/* ── Month picker navigation ── */
document.getElementById('month-picker').addEventListener('change', function() {
    const bulan = this.value;
    if (!bulan) return;
    const url = new URL(window.location.href);
    url.searchParams.set('bulan', bulan);
    // pertahankan filter yang aktif
    window.location.href = url.toString();
});
</script>
@endpush