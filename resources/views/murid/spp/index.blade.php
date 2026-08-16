@extends('layouts.app')
@section('title', 'SPP Saya')
@section('page-title', 'SPP Saya')

@section('sidebar-menu') @include('murid.partials.sidebar') @endsection

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
        border-radius: 0.25rem;
        border: 1px solid var(--topbar-border);
        overflow: hidden;
    }

    /* ── Card Header ── */
    .spp-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.25rem 1.5rem 1rem;
        gap: 1rem;
    }
    .spp-period {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
    }
    .spp-meta {
        font-size: .82rem;
        color: var(--text-light);
        font-variant-numeric: tabular-nums;
    }
    .spp-meta strong { color: var(--text-dark); font-weight: 600; }

    /* ── Card Body ── */
    .spp-card-body { padding: 0 1.5rem 1.25rem; }

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
        border-radius: 0.25rem;
        padding: 0.75rem 1rem;
    }

    /* ── Status Menunggu: info row ── */
    .waiting-row {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 0.25rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
    }
    .waiting-title {
        font-size: .83rem;
        font-weight: 700;
        color: #1e3a8a;
    }
    .waiting-sub {
        font-size: .78rem;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .waiting-sub a { color: var(--primary-navy); font-weight: 600; text-decoration: underline; }

    /* ── Upload Box ── */
    .upload-box {
        border: 1px solid var(--topbar-border);
        border-radius: 0.25rem;
        overflow: hidden;
    }
    .upload-box-header {
        padding: 0.5rem 1rem;
        background: var(--bg-light);
        border-bottom: 1px solid var(--topbar-border);
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-dark);
    }
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
        font-weight: 700;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.25rem;
    }
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
                <span class="badge badge-success">
                    LUNAS
                </span>
            @elseif($hasBukti)
                <span class="badge badge-info">
                    MENUNGGU KONFIRMASI
                </span>
            @else
                <span class="badge badge-danger">
                    BELUM UPLOAD BUKTI
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
                    <span>
                        Dikonfirmasi admin pada
                        <strong style="font-variant-numeric:tabular-nums">{{ $spp->transaksi?->tanggal_konfirmasi?->translatedFormat('d F Y') ?? '—' }}</strong>
                    </span>
                    @if($spp->transaksi?->catatan_admin)
                        &nbsp;·&nbsp; <em>{{ $spp->transaksi->catatan_admin }}</em>
                    @endif
                </div>

            {{-- MENUNGGU KONFIRMASI: info bukti + form kirim ulang --}}
            @elseif($hasBukti)
                <div class="waiting-row">
                    <div class="waiting-title">
                        Bukti transfer sudah dikirim
                    </div>
                    <div class="waiting-sub">
                        <span>Dikirim {{ $spp->transaksi->created_at->translatedFormat('d F Y') }}</span>
                        <span>·</span>
                        <a href="{{ route('murid.spp.view-bukti', $spp) }}" target="_blank">
                            Lihat Bukti
                        </a>
                    </div>
                </div>

                <div class="upload-box">
                    <div class="upload-box-header">
                        Kirim Ulang Bukti
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
                            <button type="submit" class="btn btn-outline btn-sm">
                                Kirim Ulang
                            </button>
                        </form>
                    </div>
                </div>

            {{-- BELUM UPLOAD: form upload pertama kali --}}
            @else
                <div class="upload-box">
                    <div class="upload-box-header">
                        Upload Bukti Transfer
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
                            <button type="submit" class="btn btn-primary btn-sm">
                                Kirim Bukti
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