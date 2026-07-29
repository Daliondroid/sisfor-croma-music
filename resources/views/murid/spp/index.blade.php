@extends('layouts.app')
@section('title', 'SPP Saya')
@section('page-title', 'SPP Saya')
@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('murid.dashboard') }}" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('murid.jadwal.index') }}" class="nav-item {{ request()->routeIs('murid.jadwal*') ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('murid.laporan.index') }}" class="nav-item {{ request()->routeIs('murid.laporan*') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> Laporan Bulanan</a>
    <a href="{{ route('murid.spp.index') }}" class="nav-item active"><i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya</a>@endsection
@push('styles')
<style>
    /* ── SPP Card List ── */
    .spp-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .spp-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        border: 0.09375rem solid var(--topbar-border);
        overflow: hidden;
        transition: box-shadow .2s;
    }
    .spp-card:hover { box-shadow: var(--shadow-md); }

    /* Border accent per status */
    .spp-card.status-lunas     { border-color: #86efac; }
    .spp-card.status-menunggu  { border-color: #93c5fd; }
    .spp-card.status-belum     { border-color: #fca5a5; }

    /* ── Card Header ── */
    .spp-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1rem 1.5rem 1rem;
        gap: 1rem;
    }
    .spp-period {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .spp-meta {
        font-size: .82rem;
        color: var(--text-light);
    }
    .spp-meta strong { color: var(--text-dark); font-weight: 600; }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: .75rem;
        font-weight: 700;
        padding: 0.25rem 1rem;
        border-radius: 62.4375rem;
        white-space: nowrap;
        flex-shrink: 0;
        letter-spacing: 0.01875rem;
    }
    .pill-lunas    { background: #dcfce7; color: #15803d; }
    .pill-menunggu { background: #dbeafe; color: #1d4ed8; }
    .pill-belum    { background: #fee2e2; color: #b91c1c; }

    [data-theme="dark"] .pill-lunas    { background: #14312a; color: #4ade80; }
    [data-theme="dark"] .pill-menunggu { background: #1e3a5f; color: #60a5fa; }
    [data-theme="dark"] .pill-belum    { background: #3d1515; color: #f87171; }

    /* ── Card Body ── */
    .spp-card-body { padding: 0 1.5rem 1rem; }

    /* Divider sebelum body */
    .spp-divider {
        height: 1px;
        background: var(--topbar-border);
        margin: 0 1.5rem 1rem;
    }

    /* ── Status Lunas: konfirmasi row ── */
    .confirm-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: .83rem;
        color: #15803d;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
    }
    [data-theme="dark"] .confirm-row {
        background: #14312a;
        border-color: #166534;
        color: #4ade80;
    }
    .confirm-row i { font-size: 1rem; }

    /* ── Status Menunggu: info row ── */
    .waiting-row {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 0.5rem;
        padding: 1rem 1rem;
        margin-bottom: 1rem;
    }
    [data-theme="dark"] .waiting-row {
        background: #1e3a5f;
        border-color: #1e40af;
    }
    .waiting-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: .83rem;
        font-weight: 600;
        color: #1d4ed8;
    }
    [data-theme="dark"] .waiting-title { color: #60a5fa; }
    .waiting-sub {
        font-size: .78rem;
        color: #3b82f6;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        padding-left: 1.5rem;
    }
    [data-theme="dark"] .waiting-sub { color: #93c5fd; }
    .waiting-sub a { color: #1d4ed8; font-weight: 600; }
    [data-theme="dark"] .waiting-sub a { color: #60a5fa; }

    /* ── Upload Box ── */
    .upload-box {
        border: 1px solid var(--input-border);
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .upload-box-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--bg-light);
        border-bottom: 1px solid var(--input-border);
        font-size: .82rem;
        font-weight: 600;
        color: var(--text-dark);
    }
    .upload-box-header i { color: var(--primary-blue); }
    .upload-box-body { padding: 1rem; }
    .upload-field-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .upload-field-label {
        display: block;
        font-size: .72rem;
        font-weight: 600;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.03125rem;
        margin-bottom: 0.25rem;
    }

    /* Tombol kirim per status */
    .btn-send-primary {
        background: #d97706;
        color: #fffbeb;
        border: none;
    }
    .btn-send-primary:hover { background: #b45309; color: #fffbeb; }
    .btn-send-secondary {
        background: var(--primary-blue);
        color: #fff;
        border: none;
    }
    .btn-send-secondary:hover { background: var(--primary-dark); color: #fff; }

    /* Empty state */
    .spp-empty {
        text-align: center;
        padding: 3rem;
        color: var(--text-light);
    }
    .spp-empty i { font-size: 2.2rem; opacity: .25; display: block; margin-bottom: 1rem; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>Riwayat SPP</h2>
        <div class="breadcrumb">Murid / <span>SPP</span></div>
    </div>
</div>

<div class="spp-list">
    @forelse($spps as $spp)

    @php
        $isLunas    = $spp->sudahBayar();
        $hasBukti   = !$isLunas && $spp->transaksi;
        $statusClass = $isLunas ? 'status-lunas' : ($hasBukti ? 'status-menunggu' : 'status-belum');
    @endphp

    <div class="spp-card {{ $statusClass }}">

        {{-- ── Header ── --}}
        <div class="spp-card-header">
            <div>
                <div class="spp-period">
                    {{ \Carbon\Carbon::parse($spp->periode_tagihan)->translatedFormat('F Y') }}
                </div>
                <div class="spp-meta">
                    Nominal: <strong>Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}</strong>
                    &nbsp;·&nbsp;
                    Jatuh Tempo: {{ $spp->tanggal_jatuh_tempo->translatedFormat('d F Y') }}
                </div>
            </div>

            @if($isLunas)
                <span class="status-pill pill-lunas">
                    <i class="fa-solid fa-circle-check"></i> Lunas
                </span>
            @elseif($hasBukti)
                <span class="status-pill pill-menunggu">
                    <i class="fa-solid fa-clock"></i> Menunggu Konfirmasi
                </span>
            @else
                <span class="status-pill pill-belum">
                    <i class="fa-solid fa-circle-exclamation"></i> Belum Upload Bukti
                </span>
            @endif
        </div>

        {{-- ── Divider ── --}}
        <div class="spp-divider"></div>

        {{-- ── Body ── --}}
        <div class="spp-card-body">

            {{-- LUNAS: tampilkan info konfirmasi saja --}}
            @if($isLunas)
                <div class="confirm-row">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>
                        Dikonfirmasi admin pada
                        <strong>{{ $spp->transaksi?->tanggal_konfirmasi?->translatedFormat('d F Y') ?? '—' }}</strong>
                    </span>
                    @if($spp->transaksi?->catatan_admin)
                        &nbsp;·&nbsp; <em>{{ $spp->transaksi->catatan_admin }}</em>
                    @endif
                </div>

            {{-- MENUNGGU KONFIRMASI: info bukti + form kirim ulang --}}
            @elseif($hasBukti)
                <div class="waiting-row">
                    <div class="waiting-title">
                        <i class="fa-solid fa-file-arrow-up"></i>
                        Bukti transfer sudah dikirim
                    </div>
                    <div class="waiting-sub">
                        <i class="fa-regular fa-calendar"></i>
                        Dikirim
                        {{ $spp->transaksi->created_at->translatedFormat('d F Y') }}
                        &nbsp;·&nbsp;
                        <a href="{{ asset('storage/' . $spp->transaksi->file_bukti_transfer) }}" target="_blank">
                            Lihat bukti →
                        </a>
                    </div>
                </div>

                <div class="upload-box">
                    <div class="upload-box-header">
                        <i class="fa-solid fa-rotate-right"></i> Kirim Ulang Bukti
                    </div>
                    <div class="upload-box-body">
                        <form method="POST" action="{{ route('murid.spp.bukti', $spp) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="upload-field-grid">
                                <div class="form-group" style="margin-bottom:0">
                                    <label class="upload-field-label">File Bukti (JPG/PNG/PDF maks 5MB)</label>
                                    <input type="file" name="bukti_transfer" class="form-control"
                                           accept=".jpg,.jpeg,.png,.pdf" required/>
                                </div>
                                <div class="form-group" style="margin-bottom:0">
                                    <label class="upload-field-label">Nominal Dibayar (Rp)</label>
                                    <input type="number" name="nominal_bayar" class="form-control"
                                           value="{{ $spp->nominal_tagihan }}" required/>
                                </div>
                            </div>
                            <div class="form-group" style="max-width:16.25rem;margin-bottom:1rem">
                                <label class="upload-field-label">Tanggal Bayar</label>
                                <input type="date" name="tanggal_bayar" class="form-control"
                                       value="{{ now()->format('Y-m-d') }}" required/>
                            </div>
                            <button type="submit" class="btn btn-sm btn-send-secondary">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Ulang
                            </button>
                        </form>
                    </div>
                </div>

            {{-- BELUM UPLOAD: form upload pertama kali --}}
            @else
                <div class="upload-box">
                    <div class="upload-box-header">
                        <i class="fa-solid fa-upload"></i> Upload Bukti Transfer
                    </div>
                    <div class="upload-box-body">
                        <form method="POST" action="{{ route('murid.spp.bukti', $spp) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="upload-field-grid">
                                <div class="form-group" style="margin-bottom:0">
                                    <label class="upload-field-label">File Bukti (JPG/PNG/PDF maks 5MB)</label>
                                    <input type="file" name="bukti_transfer" class="form-control"
                                           accept=".jpg,.jpeg,.png,.pdf" required/>
                                </div>
                                <div class="form-group" style="margin-bottom:0">
                                    <label class="upload-field-label">Nominal Dibayar (Rp)</label>
                                    <input type="number" name="nominal_bayar" class="form-control"
                                           value="{{ $spp->nominal_tagihan }}" required/>
                                </div>
                            </div>
                            <div class="form-group" style="max-width:16.25rem;margin-bottom:1rem">
                                <label class="upload-field-label">Tanggal Bayar</label>
                                <input type="date" name="tanggal_bayar" class="form-control"
                                       value="{{ now()->format('Y-m-d') }}" required/>
                            </div>
                            <button type="submit" class="btn btn-sm btn-send-primary">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Bukti
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>{{-- /spp-card-body --}}
    </div>{{-- /spp-card --}}

    @empty
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="16" width="48" height="56" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M16 32h48" stroke="var(--primary-blue)" stroke-width="2"/><circle cx="40" cy="48" r="8" stroke="var(--primary-blue)" stroke-width="2"/><path d="M40 44v8" stroke="var(--primary-blue)" stroke-width="2"/><path d="M38 46h4" stroke="var(--primary-blue)" stroke-width="2"/></svg>
                </div>
                <div class="empty-state-title">Tidak ada tagihan SPP.</div>
                <div class="empty-state-description">Belum ada tagihan SPP untuk saat ini.</div>
            </div>
        </div>
    @endforelse

    @if($spps->hasPages())
        <div style="padding: 0.5rem 0">{{ $spps->links() }}</div>
    @endif
</div>
@endsection