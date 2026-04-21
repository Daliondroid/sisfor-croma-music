@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Laporan Keuangan Bulanan</h2><div class="breadcrumb">Admin / Laporan / <span>Keuangan</span></div></div>
    <a href="{{ route('admin.laporan.export', ['jenis'=>'keuangan', 'bulan'=>$bulan]) }}" class="btn btn-outline">
        <i class="fa-solid fa-file-pdf"></i> Export PDF
    </a>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end">
            <div class="form-group" style="margin:0">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}"/>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Tampilkan</button>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
            <div class="stat-label">Total Masuk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-file-invoice"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
            <div class="stat-label">Total Tagihan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-clock"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format($totalTagihan - $totalMasuk, 0, ',', '.') }}</div>
            <div class="stat-label">Total Tunggakan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fa-solid fa-percent"></i></div>
        <div>
            <div class="stat-value">{{ $totalTagihan > 0 ? round(($totalMasuk / $totalTagihan) * 100) : 0 }}%</div>
            <div class="stat-label">Tingkat Pembayaran</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Detail Tagihan — {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Murid</th><th>Nominal</th><th>Jatuh Tempo</th><th>Status</th><th>Tanggal Bayar</th></tr></thead>
            <tbody>
            @forelse($spps as $i => $spp)
                <tr>
                    <td style="color:var(--text-light)">{{ $i+1 }}</td>
                    <td><strong>{{ $spp->murid->nama_murid }}</strong></td>
                    <td>Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}</td>
                    <td>{{ $spp->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $spp->sudahBayar() ? 'badge-success' : 'badge-danger' }}">
                            {{ $spp->sudahBayar() ? 'Lunas' : 'Belum Bayar' }}
                        </span>
                    </td>
                    <td>{{ $spp->transaksi?->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-light)">Tidak ada data untuk bulan ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection