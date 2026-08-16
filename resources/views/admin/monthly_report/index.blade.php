@extends('layouts.app')
@section('title', 'Monthly Report')
@section('page-title', 'Monthly Report')

@section('breadcrumb')
    <span class="crumb-root">Gaji & Laporan</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Monthly Report Murid</span>
@endsection

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Monthly Report Murid</h2>
    </div>
    <div style="display:flex;gap:0.625rem;align-items:center;flex-wrap:wrap">
        <form method="GET" action="{{ route('admin.monthly_report.index') }}" style="display:inline-flex;gap:0.5rem;align-items:center">
            <input type="month" name="bulan" value="{{ $bulan }}" class="form-control form-control-sm" onchange="this.form.submit()">
        </form>
        <form method="POST" action="{{ route('admin.report.generate') }}" style="display:inline">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <button type="submit" class="btn btn-secondary btn-sm">
                Generate Report
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Laporan Bulanan &mdash; {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</h3>
    </div>
    <div class="table-wrap">
        <table style="table-layout:fixed;width:100%">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:35%">Nama Murid</th>
                    <th style="width:20%;text-align:center">Kehadiran</th>
                    <th style="width:15%;text-align:center">% Kehadiran</th>
                    <th style="width:15%;text-align:center">Skor</th>
                    <th style="width:10%;text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($murids as $i => $m)
                <tr>
                    <td style="color:var(--text-light)">{{ $i + 1 }}</td>
                    <td><strong style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block">{{ $m->nama_murid }}</strong></td>
                    <td style="text-align:center;font-variant-numeric:tabular-nums">{{ $m->total_hadir }} / {{ $m->total_sesi }} sesi</td>
                    <td style="text-align:center">
                        <span class="badge {{ $m->persen_hadir >= 80 ? 'badge-success' : ($m->persen_hadir >= 60 ? 'badge-warning' : 'badge-danger') }}">
                            {{ $m->persen_hadir }}%
                        </span>
                    </td>
                    <td style="text-align:center">
                        <span style="font-weight:700;font-size:1.05rem;color:var(--primary-navy);font-variant-numeric:tabular-nums">
                            {{ $m->report->skor ?? '-' }}
                        </span>
                    </td>
                    <td style="text-align:right">
                        <div class="action-dropdown-wrap">
                            <button type="button" class="btn-action-dropdown" onclick="toggleActionDropdown(this, event)">
                                Kelola ▾
                            </button>
                            <div class="action-dropdown-menu">
                                <a href="{{ route('admin.report.show', ['murid' => $m->id_murid, 'bulan' => $bulan]) }}" class="action-dropdown-item">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:1.5rem;color:var(--text-light)">
                        Tidak ada data murid aktif.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
