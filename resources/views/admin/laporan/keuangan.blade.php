@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Laporan Keuangan Bulanan</h2>
        <div class="breadcrumb">Admin / Laporan / <span>Keuangan</span></div>
    </div>
    {{-- Tombol ekspor --}}
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.laporan.export.pdf', ['jenis' => 'keuangan']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
           class="btn btn-outline" target="_blank">
            <i class="fa-solid fa-file-pdf" style="color:#dc2626"></i> Ekspor PDF
        </a>
        <a href="{{ route('admin.laporan.export.xlsx', ['jenis' => 'keuangan']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
           class="btn btn-outline">
            <i class="fa-solid fa-file-excel" style="color:#16a34a"></i> Ekspor Excel
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="margin:0">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}"/>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}"/>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-search"></i> Tampilkan
            </button>
            <a href="{{ route('admin.laporan.keuangan') }}" class="btn btn-outline">Reset</a>
        </form>
        @error('end_date')
            <div style="color:#dc2626;font-size:.8rem;margin-top:8px">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
            </div>
        @enderror
    </div>
</div>

{{-- Ringkasan stats --}}
<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
            <div class="stat-label">Total Masuk (Lunas)</div>
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
            <div class="stat-value">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
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

{{-- Label periode --}}
<div class="card">
    <div class="card-header">
        <h3>
            Detail Tagihan &mdash;
            @if(\Carbon\Carbon::parse($startDate)->isSameMonth(\Carbon\Carbon::parse($endDate)))
                {{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}
            @else
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @endif
        </h3>
        <span style="font-size:.8rem;color:var(--text-light)">{{ $spps->count() }} tagihan</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Murid</th>
                    <th>Program Kursus</th>
                    <th>Periode</th>
                    <th>Nominal</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Tgl Konfirmasi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($spps as $i => $spp)
                <tr>
                    <td style="color:var(--text-light)">{{ $i + 1 }}</td>
                    <td><strong>{{ $spp->murid->nama_murid ?? '-' }}</strong></td>
                    <td style="font-size:.8rem">{{ $spp->programKursus->nama_program ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($spp->periode_tagihan)->translatedFormat('F Y') }}</td>
                    <td>Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}">
                            {{ $spp->tipe_les ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $spp->sudahBayar() ? 'badge-success' : 'badge-danger' }}">
                            {{ $spp->status_bayar }}
                        </span>
                    </td>
                    <td style="font-size:.82rem;color:var(--text-light)">
                        {{ $spp->transaksi?->tanggal_konfirmasi
                            ? \Carbon\Carbon::parse($spp->transaksi->tanggal_konfirmasi)->format('d/m/Y')
                            : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:32px;color:var(--text-light)">
                        Tidak ada data tagihan untuk periode ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
            @if($spps->count())
            <tfoot>
                <tr style="background:#f8f9fa;font-weight:600">
                    <td colspan="4" style="padding:12px 16px;text-align:right">Total</td>
                    <td style="padding:12px 16px">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection