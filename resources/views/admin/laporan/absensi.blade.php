@extends('layouts.app')
@section('title', 'Rekap Absensi')
@section('page-title', 'Rekap Absensi')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Rekap Absensi Bulanan</h2><div class="breadcrumb">Admin / Laporan / <span>Absensi</span></div></div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.laporan.export', ['jenis'=>'absensi', 'bulan'=>$bulan]) }}" class="btn btn-outline">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
    </div>
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

<div class="card">
    <div class="card-header">
        <h3>{{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Nama Murid</th><th>Hadir</th><th>Alpa</th><th>Izin</th><th>Total Sesi</th><th>Kehadiran</th><th>Report</th></tr>
            </thead>
            <tbody>
            @foreach($murids as $i => $m)
                <tr>
                    <td style="color:var(--text-light)">{{ $i+1 }}</td>
                    <td><strong>{{ $m->nama_murid }}</strong></td>
                    <td><span class="badge badge-success">{{ $m->total_hadir }}</span></td>
                    <td><span class="badge badge-danger">{{ $m->total_alpa }}</span></td>
                    <td><span class="badge badge-warning">{{ $m->total_izin }}</span></td>
                    <td>{{ $m->total_hadir + $m->total_alpa + $m->total_izin }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="flex:1;height:6px;background:#e5e7eb;border-radius:99px;overflow:hidden">
                                <div style="width:{{ $m->persen_hadir }}%;height:100%;background:{{ $m->persen_hadir >= 75 ? '#16a34a' : ($m->persen_hadir >= 50 ? '#f59e0b' : '#dc2626') }};border-radius:99px"></div>
                            </div>
                            <span style="font-size:.78rem;font-weight:600">{{ $m->persen_hadir }}%</span>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.report.show', [$m, $bulan]) }}" class="btn btn-sm btn-outline">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
