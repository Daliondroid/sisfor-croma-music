@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <h2>Laporan Keuangan Bulanan</h2>
    <div class="breadcrumb">Admin / Laporan / <span>Keuangan</span></div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}"/>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}"/>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-search"></i> Tampilkan
            </button>
            <a href="{{ route('admin.laporan.keuangan') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
        <a href="{{ route('admin.laporan.export.pdf', ['jenis' => 'keuangan']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
           class="btn btn-outline btn-sm" target="_blank">
            <i class="fa-solid fa-file-pdf" style="color:#dc2626"></i> PDF
        </a>
        <a href="{{ route('admin.laporan.export.xlsx', ['jenis' => 'keuangan']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
           class="btn btn-outline btn-sm">
            <i class="fa-solid fa-file-excel" style="color:#16a34a"></i> Excel
        </a>
    </div>
    @error('end_date')
        <div style="color:#dc2626;font-size:.8rem;margin-top:0.5rem">
            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
        </div>
    @enderror
</div>

{{-- Ringkasan stats --}}
<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card" style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.25rem; padding: 1.5rem 1.5rem;">
        <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-blue);">Total Masuk (Lunas)</div>
        <div style="font-size: 1.05rem; font-weight: 600; color: var(--text-dark);">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.25rem; padding: 1.5rem 1.5rem;">
        <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-blue);">Total Tagihan</div>
        <div style="font-size: 1.05rem; font-weight: 600; color: var(--text-dark);">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.25rem; padding: 1.5rem 1.5rem;">
        <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-blue);">Total Tunggakan</div>
        <div style="font-size: 1.05rem; font-weight: 600; color: var(--text-dark);">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.25rem; padding: 1.5rem 1.5rem;">
        <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-blue);">Tingkat Pembayaran</div>
        <div style="font-size: 1.05rem; font-weight: 600; color: var(--text-dark);">{{ $totalTagihan > 0 ? round(($totalMasuk / $totalTagihan) * 100) : 0 }}%</div>
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
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="8" width="48" height="64" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 24h24" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round"/><path d="M28 56V36l8 8 6-4 10 12" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity=".6"/></svg>
                        </div>
                        <div class="empty-state-title">Tidak ada data tagihan.</div>
                        <div class="empty-state-description">Tidak ada data tagihan untuk periode yang dipilih.</div>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
            @if($spps->count())
            <tfoot>
                <tr style="background:#f8f9fa;font-weight:600">
                    <td colspan="4" style="padding:1rem 1rem;text-align:right">Total</td>
                    <td style="padding:1rem 1rem">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection