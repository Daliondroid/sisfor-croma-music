@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('breadcrumb')
    <span class="crumb-root">Keuangan</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Laporan Keuangan</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <h2>Laporan Keuangan Bulanan</h2>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.625rem;align-items:center;flex-wrap:wrap">
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}"/>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}"/>
            <button type="submit" class="btn btn-secondary btn-sm">
                Tampilkan
            </button>
            <a href="{{ route('admin.laporan.keuangan') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
        <a href="{{ route('admin.laporan.export.pdf', ['jenis' => 'keuangan']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
           class="btn btn-outline btn-sm" target="_blank">
            PDF
        </a>
        <a href="{{ route('admin.laporan.export.xlsx', ['jenis' => 'keuangan']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
           class="btn btn-outline btn-sm">
            Excel
        </a>
    </div>
    @error('end_date')
        <div style="color:#dc2626;font-size:.8rem;margin-top:0.5rem;width:100%">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- Open KPI Metric Strips --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(13rem,1fr));margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-value">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
        <div class="stat-label">Total Masuk (Lunas)</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
        <div class="stat-label">Total Tagihan</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
        <div class="stat-label">Total Tunggakan</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $totalTagihan > 0 ? round(($totalMasuk / $totalTagihan) * 100) : 0 }}%</div>
        <div class="stat-label">Tingkat Pembayaran</div>
    </div>
</div>

{{-- Detail Tagihan --}}
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
        <span style="font-size:.78rem;color:var(--text-light);font-weight:600;font-variant-numeric:tabular-nums">{{ $spps->count() }} tagihan</span>
    </div>
    <div class="table-wrap">
        <table style="table-layout:fixed;width:100%">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:20%">Murid</th>
                    <th style="width:18%">Program Kursus</th>
                    <th style="width:14%">Periode</th>
                    <th style="width:14%">Nominal</th>
                    <th style="width:10%">Tipe</th>
                    <th style="width:9%">Status</th>
                    <th style="width:10%">Tgl Konfirmasi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($spps as $i => $spp)
                <tr>
                    <td style="color:var(--text-light)">{{ $i + 1 }}</td>
                    <td><strong style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block">{{ $spp->murid->nama_murid ?? '-' }}</strong></td>
                    <td style="font-size:.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $spp->programKursus->nama_program ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($spp->periode_tagihan)->translatedFormat('F Y') }}</td>
                    <td style="font-weight:600;font-variant-numeric:tabular-nums">Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}">
                            {{ strtoupper($spp->tipe_les ?? '-') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $spp->sudahBayar() ? 'badge-success' : 'badge-danger' }}">
                            {{ strtoupper($spp->status_bayar) }}
                        </span>
                    </td>
                    <td style="font-size:.8rem;color:var(--text-light);font-variant-numeric:tabular-nums">
                        {{ $spp->transaksi?->tanggal_konfirmasi
                            ? \Carbon\Carbon::parse($spp->transaksi->tanggal_konfirmasi)->format('d/m/Y')
                            : '—' }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-title">Tidak ada data tagihan.</div>
                        <div class="empty-state-description">Tidak ada data tagihan untuk periode yang dipilih.</div>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
            @if($spps->count())
            <tfoot>
                <tr style="background:var(--th-bg);font-weight:700">
                    <td colspan="4" style="padding:0.65rem 0.875rem;text-align:right">Total</td>
                    <td style="padding:0.65rem 0.875rem;font-variant-numeric:tabular-nums">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection