@extends('layouts.app')
@section('title', 'Laporan Gaji & Honor Guru')
@section('page-title', 'Laporan Gaji & Honor Guru')

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Laporan Gaji & Honor Guru</h2>
        <div class="breadcrumb">Admin / Laporan / <span>Honor Guru</span></div>
    </div>
    <div style="display:flex;gap:0.5rem">
        <a href="{{ route('admin.laporan.export.pdf', ['jenis' => 'gaji', 'bulan' => $bulan]) }}" class="btn btn-outline btn-sm" target="_blank">
            <i class="fa-solid fa-file-pdf" style="color:#dc2626"></i> Export PDF
        </a>
        <a href="{{ route('admin.laporan.export.xlsx', ['jenis' => 'gaji', 'bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-file-excel" style="color:#16a34a"></i> Export XLSX
        </a>
    </div>
</div>

<div class="card mb-4" style="padding:1rem 1.5rem">
    <form method="GET" action="{{ route('admin.laporan.gaji') }}" style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
        <div>
            <label style="font-size:.78rem;font-weight:600;color:var(--text-light);display:block;margin-bottom:0.25rem">Bulan Periode</label>
            <input type="month" name="bulan" value="{{ $bulan }}" class="form-control" onchange="this.form.submit()">
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-money-bill-wave" style="color:var(--primary-blue);margin-right:0.5rem"></i>Ringkasan Honor Guru</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Guru</th>
                    <th style="text-align:center">Total Pertemuan</th>
                    <th style="text-align:right">Total Honor</th>
                    <th style="text-align:right">Sudah Dibayar</th>
                    <th style="text-align:right">Pending</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ringkasanGuru as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $item['guru']->nama_guru ?? '-' }}</strong></td>
                    <td style="text-align:center">{{ $item['total_pertemuan'] }}</td>
                    <td style="text-align:right;font-weight:600">Rp {{ number_format($item['total_honor'], 0, ',', '.') }}</td>
                    <td style="text-align:right;color:#16a34a">Rp {{ number_format($item['total_lunas'], 0, ',', '.') }}</td>
                    <td style="text-align:right;color:#d97706">Rp {{ number_format($item['total_pending'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:2rem;color:var(--text-light)">
                        Tidak ada data honor guru untuk periode ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
