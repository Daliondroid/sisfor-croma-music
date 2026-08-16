@extends('layouts.app')
@section('title', 'Laporan Gaji & Honor Guru')
@section('page-title', 'Laporan Honor Guru')

@section('breadcrumb')
    <span class="crumb-root">Keuangan</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Laporan Honor Guru</span>
@endsection

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Laporan Gaji & Honor Guru</h2>
    </div>
    <div style="display:flex;gap:0.5rem">
        <a href="{{ route('admin.laporan.export.pdf', ['jenis' => 'gaji', 'bulan' => $bulan]) }}" class="btn btn-outline btn-sm" target="_blank">
            Export PDF
        </a>
        <a href="{{ route('admin.laporan.export.xlsx', ['jenis' => 'gaji', 'bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
            Export Excel
        </a>
    </div>
</div>

<div class="card" style="padding:0.875rem 1.25rem;margin-bottom:1rem">
    <form method="GET" action="{{ route('admin.laporan.gaji') }}" style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
        <div>
            <label style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-light);display:block;margin-bottom:0.25rem">Bulan Periode</label>
            <input type="month" name="bulan" value="{{ $bulan }}" class="form-control form-control-sm" onchange="this.form.submit()">
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Ringkasan Honor Guru</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:30%">Nama Guru</th>
                    <th style="text-align:center;width:20%">Total Pertemuan</th>
                    <th style="text-align:right;width:15%">Total Honor</th>
                    <th style="text-align:right;width:15%">Sudah Dibayar</th>
                    <th style="text-align:right;width:15%">Pending</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ringkasanGuru as $i => $item)
                <tr>
                    <td style="color:var(--text-light)">{{ $i + 1 }}</td>
                    <td><strong>{{ $item['guru']->nama_guru ?? '-' }}</strong></td>
                    <td style="text-align:center;font-variant-numeric:tabular-nums">{{ $item['total_pertemuan'] }}</td>
                    <td style="text-align:right;font-weight:600;font-variant-numeric:tabular-nums">Rp {{ number_format($item['total_honor'], 0, ',', '.') }}</td>
                    <td style="text-align:right;color:#15803d;font-variant-numeric:tabular-nums">Rp {{ number_format($item['total_lunas'], 0, ',', '.') }}</td>
                    <td style="text-align:right;color:#b45309;font-variant-numeric:tabular-nums">Rp {{ number_format($item['total_pending'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:1.5rem;color:var(--text-light)">
                        Tidak ada data honor guru untuk periode ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
