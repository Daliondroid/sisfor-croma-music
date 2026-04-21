@extends('layouts.app')
@section('title', 'Laporan Gaji Guru')
@section('page-title', 'Laporan Gaji Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Rekap Gaji Guru</h2><div class="breadcrumb">Admin / Laporan / <span>Gaji</span></div></div>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
            <div class="form-group" style="margin:0">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}"/>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Gaji per Sesi (Rp)</label>
                <input type="number" name="gaji_per_sesi" class="form-control" value="{{ $gajiPerSesi }}" style="width:160px"/>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-calculator"></i> Hitung</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>{{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</h3>
        <div style="font-size:.8rem;color:var(--text-light)">
            Rp {{ number_format($gajiPerSesi, 0, ',', '.') }} / sesi
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Nama Guru</th><th>Spesialisasi</th><th>Jumlah Sesi</th><th>Total Gaji</th></tr></thead>
            <tbody>
            @forelse($gurus as $i => $g)
                <tr>
                    <td style="color:var(--text-light)">{{ $i+1 }}</td>
                    <td><strong>{{ $g->nama_guru }}</strong></td>
                    <td>{{ $g->spesialisasi ?? '-' }}</td>
                    <td>
                        <span class="badge badge-info">{{ $g->jumlah_sesi }} sesi</span>
                    </td>
                    <td style="font-weight:600;color:var(--primary-blue)">
                        Rp {{ number_format($g->total_gaji, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text-light)">Tidak ada data sesi untuk bulan ini.</td></tr>
            @endforelse
            </tbody>
            @if($gurus->count())
            <tfoot>
                <tr style="background:#f8f9fa;font-weight:600">
                    <td colspan="3" style="padding:13px 16px">Total</td>
                    <td style="padding:13px 16px">{{ $gurus->sum('jumlah_sesi') }} sesi</td>
                    <td style="padding:13px 16px;color:var(--primary-blue)">
                        Rp {{ number_format($gurus->sum('total_gaji'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection