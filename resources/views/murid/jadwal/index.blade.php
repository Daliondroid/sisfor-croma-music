@extends('layouts.app')

@section('title', 'Jadwal Kelas Saya')
@section('page-title', 'Jadwal Kelas')

@section('sidebar-menu') @include('murid.partials.sidebar') @endsection

@push('styles')
<style>
/* ─── Filter pills ──────────────────────────────────── */
.filter-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.filter-pills { display: flex; gap: 0.25rem; flex-wrap: wrap; }
.filter-pill {
    font-size: .72rem;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    border: 1px solid var(--topbar-border);
    background: transparent;
    color: var(--text-light);
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    transition: .15s;
    white-space: nowrap;
}
.filter-pill.active {
    background: var(--primary-navy);
    color: #fff;
    border-color: var(--primary-navy);
}
.filter-pill:hover:not(.active) { border-color: var(--text-dark); color: var(--text-dark); }

/* ─── Section header (Senin, Selasa, …) ────────────── */
.day-section { margin-bottom: 0.75rem; }
.day-section-header {
    font-size: .75rem;
    font-weight: 700;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.5rem 1rem;
    background: var(--bg-light);
    border-radius: 0.25rem 0.25rem 0 0;
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
.session-item:last-child { border-radius: 0 0 0.25rem 0.25rem; }

.session-main {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1rem;
    cursor: pointer;
    user-select: none;
}
.session-main:hover { background: var(--bg-light); }

/* Left accent bar */
.session-accent {
    width: 3px;
    height: 2.5rem;
    border-radius: 0.125rem;
    flex-shrink: 0;
    background: var(--primary-navy);
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
    gap: 0.5rem;
    flex-wrap: wrap;
}
.session-sub {
    font-size: .72rem;
    color: var(--text-light);
    margin-top: 0.125rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    font-variant-numeric: tabular-nums;
}

/* Status badge */
.s-badge {
    font-size: .68rem;
    font-weight: 700;
    padding: 0.1875rem 0.5rem;
    border-radius: 0.25rem;
    white-space: nowrap;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.s-confirmed { background: #dcfce7; color: #15803d; }
.s-pending   { background: #fef9c3; color: #a16207; }
.s-tidak     { background: #fee2e2; color: #b91c1c; }
.s-future    { background: var(--bg-light); color: var(--text-light); border: 1px solid var(--topbar-border); }
.s-belum     { background: var(--bg-light); color: var(--text-light); border: 1px solid var(--topbar-border); }

/* Indicator */
.sess-indicator {
    font-size: .75rem;
    color: var(--text-light);
    font-weight: 700;
    flex-shrink: 0;
}

/* ─── Session detail (dropdown) ─────────────────────── */
.session-detail {
    display: none;
    padding: 0 1rem 1rem 1rem;
    border-top: 1px solid var(--topbar-border);
}
.session-detail.open { display: block; }

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 1rem;
}
@media (max-width: 37.5rem) { .detail-grid { grid-template-columns: 1fr; } }

.detail-block {
    background: var(--bg-light);
    border: 1px solid var(--topbar-border);
    border-radius: 0.25rem;
    padding: 1rem 1rem;
    font-size: .8rem;
}
.detail-block .db-label {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-light);
    margin-bottom: 0.5rem;
}
.detail-block .db-value {
    color: var(--text-dark);
    line-height: 1.6;
}
.detail-block.full { grid-column: 1 / -1; }

/* Progres section */
.progres-section-title {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-light);
    margin-bottom: 0.25rem;
    margin-top: 1rem;
}
.progres-section-title:first-child { margin-top: 0; }

.materi-box {
    background: var(--card-bg);
    border: 1px solid var(--topbar-border);
    border-radius: 0.25rem;
    padding: 0.5rem 1rem;
    font-size: .82rem;
    line-height: 1.6;
    color: var(--text-dark);
}

.catatan-box {
    background: var(--card-bg);
    border: 1px solid var(--topbar-border);
    border-radius: 0.25rem;
    padding: 0.5rem 1rem;
    font-size: .82rem;
    line-height: 1.6;
    color: var(--text-dark);
}

/* Foto bukti — thumbnail */
.foto-thumb-wrap { margin-top: 0.5rem; position: relative; display: inline-block; }
.foto-thumb {
    width: 7.5rem;
    height: 5rem;
    object-fit: cover;
    border-radius: 0.25rem;
    border: 1px solid var(--topbar-border);
    cursor: pointer;
    transition: opacity .15s;
    display: block;
}
.foto-thumb:hover { opacity: .85; }

/* ─── Lightbox ──────────────────────────────────────── */
.lightbox-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.85);
    z-index: 500;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}
.lightbox-backdrop.open { display: flex; }
.lightbox-img {
    max-width: 90vw;
    max-height: 88vh;
    object-fit: contain;
    border-radius: 0.25rem;
}
.lightbox-close {
    position: absolute;
    top: 1rem;
    right: 1.5rem;
    color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.25rem;
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
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    font-size: .8rem;
    font-weight: 700;
    background: var(--primary-navy);
    color: #fff;
    border: none;
    cursor: pointer;
    font-family: inherit;
    margin-top: 1rem;
    transition: background .15s;
}
.btn-isi-presensi:hover { opacity: 0.9; }

/* ─── Confirm modal ─────────────────────────────────── */
.confirm-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 300;
    align-items: center; justify-content: center;
}
.confirm-backdrop.open { display: flex; }
.confirm-box {
    background: var(--card-bg);
    border-radius: 0.25rem;
    padding: 1.5rem; width: 25rem; max-width: 92vw;
    border: 1px solid var(--topbar-border);
}
.confirm-detail {
    background: var(--bg-light); border-radius: 0.25rem;
    padding: 1rem 1rem; margin: 1rem 0;
    font-size: .82rem; line-height: 1.8;
    border: 1px solid var(--topbar-border);
}
.confirm-actions { display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem; }
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
    <div class="page-header-filters">
        <form method="GET" action="{{ route('murid.jadwal.index') }}" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="month" name="bulan" id="month-picker" class="form-control form-control-sm" style="width:auto" value="{{ $selectedMonth }}" onchange="this.form.submit()" @if($availableMonths->isNotEmpty()) min="{{ $availableMonths->last() }}" max="{{ $availableMonths->first() }}" @endif>
        </form>
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
    <span style="font-size:.72rem;color:var(--text-light);font-variant-numeric:tabular-nums">{{ $jadwalsFiltered->count() }} sesi</span>
</div>

{{-- ── DAFTAR JADWAL ── --}}
<div id="jadwal-list">
@if($jadwalsFiltered->isEmpty())
    <div class="empty-state">
        <div class="empty-state-title">Belum ada jadwal ditemukan.</div>
        <div class="empty-state-description">Tidak ada jadwal yang sesuai dengan filter/pencarian Anda.</div>
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
            if ($isToday) $hariLabel .= ' — Hari Ini';
        @endphp

        <div class="day-section">
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
                                <span class="badge {{ $tipe === 'Onsite' ? 'badge-info' : 'badge-warning' }}">
                                    {{ strtoupper($tipe) }}
                                </span>
                                @if($j->status_jadwal !== 'Sesuai Jadwal')
                                    <span class="badge badge-danger">{{ strtoupper($j->status_jadwal) }}</span>
                                @endif
                            </div>
                            <div class="session-sub">
                                <span>Sesi ke-{{ $j->sesi_ke }}</span>
                                <span>{{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</span>
                                <span>{{ $j->guru->nama_guru }}</span>
                            </div>
                        </div>

                        {{-- Status badge --}}
                        @if($isHadir && $guruKonfirm)
                            <span class="s-badge s-confirmed">Terkonfirmasi</span>
                        @elseif($isHadir && !$guruKonfirm)
                            <span class="s-badge s-pending">Menunggu Guru</span>
                        @elseif($isTidak)
                            <span class="s-badge s-tidak">Tidak Hadir</span>
                        @elseif($isFuture)
                            <span class="s-badge s-future">Akan Datang</span>
                        @elseif($bisaPresensi)
                            <span class="s-badge" style="background:#eff6ff;color:var(--primary-navy);border:1px solid #bfdbfe">
                                Isi Presensi
                            </span>
                        @else
                            <span class="s-badge s-belum">Belum Diisi</span>
                        @endif

                        <span class="sess-indicator" id="chev-{{ $sessId }}">▾</span>
                    </div>

                    {{-- Detail (dropdown) --}}
                    <div class="session-detail" id="{{ $sessId }}">
                        <div class="detail-grid">
                            {{-- Info Sesi --}}
                            <div class="detail-block">
                                <div class="db-label">Info Sesi</div>
                                <div class="db-value" style="font-variant-numeric:tabular-nums">
                                    <div>{{ $j->tanggal->translatedFormat('l, d F Y') }}</div>
                                    <div>{{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</div>
                                    <div>Sesi ke-{{ $j->sesi_ke }}</div>
                                </div>
                            </div>

                            {{-- Guru --}}
                            <div class="detail-block">
                                <div class="db-label">Guru & Program</div>
                                <div class="db-value">
                                    <div style="font-weight:600">{{ $j->guru->nama_guru }}</div>
                                    <div>{{ $j->spp?->programKursus?->nama_program ?? '—' }}</div>
                                    <div>{{ $tipe }}</div>
                                </div>
                            </div>

                            {{-- Progres / Materi --}}
                            @if($progres)
                                <div class="detail-block full">
                                    <div class="db-label">
                                        Materi &amp; Progres
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
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @else
                                <div class="detail-block full" style="color:var(--text-light);font-size:.8rem">
                                    <div class="db-label">Materi &amp; Progres</div>
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
                                Isi Presensi
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
        ✕
    </span>
    <img src="" alt="Foto progres" class="lightbox-img" id="lightbox-img">
</div>

{{-- ── MODAL KONFIRMASI PRESENSI ── --}}
<div class="confirm-backdrop" id="confirm-backdrop">
    <div class="confirm-box">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:0.25rem;color:var(--text-dark)">Isi Presensi Kehadiran</h3>
        <p style="font-size:.82rem;color:var(--text-light)">Konfirmasi bahwa kamu hadir pada sesi berikut:</p>
        <div class="confirm-detail" id="confirm-detail"></div>
        <p style="font-size:.72rem;color:var(--text-light)">
            Pengajuan ini akan diteruskan ke guru untuk dikonfirmasi.
        </p>
        <div class="confirm-actions">
            <button class="btn btn-outline btn-sm" onclick="tutupKonfirmasi()">
                Batal
            </button>
            <form method="POST" action="{{ route('murid.presensi.store') }}" id="confirm-form">
                @csrf
                <input type="hidden" name="id_jadwal" id="confirm-id-jadwal"/>
                <button type="submit" class="btn btn-primary btn-sm">
                    Ya, Saya Hadir
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
    if (chev) {
        chev.textContent = detail.classList.contains('open') ? '▴' : '▾';
    }
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
        `<div><strong>${tanggal}</strong></div>` +
        `<div style="font-variant-numeric:tabular-nums">${waktu}</div>` +
        `<div>${program}</div>` +
        `<div>Guru: ${guru}</div>`;
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
    window.location.href = url.toString();
});
</script>
@endpush