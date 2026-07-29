@extends('layouts.app')
@section('title', 'Monthly Report')
@section('page-title', 'Monthly Report')

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Monthly Report Murid</h2>
        <div class="breadcrumb">Admin / Laporan / <span>Monthly Report</span></div>
    </div>
    <div>
        <form method="POST" action="{{ route('admin.report.generate') }}" style="display:inline">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-arrows-rotate"></i> Generate Report
            </button>
        </form>
    </div>
</div>

<div class="card mb-4" style="padding:1rem 1.5rem">
    <form method="GET" action="{{ route('admin.monthly_report.index') }}" style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
        <div>
            <label style="font-size:.78rem;font-weight:600;color:var(--text-light);display:block;margin-bottom:0.25rem">Pilih Bulan</label>
            <input type="month" name="bulan" value="{{ $bulan }}" class="form-control" onchange="this.form.submit()">
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-file-lines" style="color:var(--primary-blue);margin-right:0.5rem"></i>Daftar Laporan Bulanan</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Murid</th>
                    <th style="text-align:center">Kehadiran</th>
                    <th style="text-align:center">% Kehadiran</th>
                    <th style="text-align:center">Skor</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($murids as $i => $m)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $m->nama_murid }}</strong></td>
                    <td style="text-align:center">{{ $m->total_hadir }} / {{ $m->total_sesi }} sesi</td>
                    <td style="text-align:center">
                        <span class="badge {{ $m->persen_hadir >= 80 ? 'badge-success' : ($m->persen_hadir >= 60 ? 'badge-warning' : 'badge-danger') }}">
                            {{ $m->persen_hadir }}%
                        </span>
                    </td>
                    <td style="text-align:center">
                        <span style="font-weight:700;font-size:1.1rem;color:var(--primary-blue)">
                            {{ $m->report->skor ?? '-' }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <a href="{{ route('admin.report.show', ['murid' => $m->id_murid, 'bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:2rem;color:var(--text-light)">
                        Tidak ada data murid aktif.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
