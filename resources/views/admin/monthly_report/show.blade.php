@extends('layouts.app')
@section('title', 'Monthly Report')
@section('page-title', 'Monthly Report')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>{{ $murid->nama_murid }}</h2>
        <div class="breadcrumb">Admin / Rekap Absensi / <span>{{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</span></div>
    </div>
    <a href="{{ route('admin.laporan.absensi', ['bulan' => $bulan]) }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<!-- Summary -->
<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div>
        <div><div class="stat-value">{{ $report->total_hadir }}</div><div class="stat-label">Total Hadir</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fa-solid fa-clock"></i></div>
        <div><div class="stat-value">{{ $report->total_izin }}</div><div class="stat-label">Total Izin</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fa-solid fa-times-circle"></i></div>
        <div><div class="stat-value">{{ $report->total_alpa }}</div><div class="stat-label">Total Alpa</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fa-solid fa-percent"></i></div>
        <div><div class="stat-value">{{ $report->persentase_kehadiran }}%</div><div class="stat-label">Kehadiran</div></div>
    </div>
</div>

@if($report->catatan_guru)
<div class="card" style="margin-bottom:20px">
    <div class="card-header"><h3><i class="fa-solid fa-note-sticky" style="margin-right:8px;color:var(--primary-blue)"></i>Catatan Guru</h3></div>
    <div class="card-body">
        <div style="background:#f8faff;padding:14px 18px;border-radius:8px;border-left:3px solid var(--primary-blue);font-size:.9rem">
            {{ $report->catatan_guru }}
        </div>
    </div>
</div>
@endif

<!-- Detail per sesi -->
<div class="card">
    <div class="card-header"><h3>Detail Sesi</h3></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Sesi</th><th>Status</th><th>Materi</th><th>Progres</th><th>Video</th></tr></thead>
            <tbody>
            @forelse($presensis as $p)
                <tr>
                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td>Sesi {{ $p->sesi_ke }}</td>
                    <td>
                        <span class="badge {{ $p->status_murid=='hadir'?'badge-success':($p->status_murid=='izin'?'badge-warning':'badge-danger') }}">
                            {{ ucfirst($p->status_murid) }}
                        </span>
                    </td>
                    <td>{{ $p->materiKbm?->materi_diajarkan ?? '-' }}</td>
                    <td>
                        @if($p->materiKbm)
                            <div style="display:flex;align-items:center;gap:6px">
                                <div style="width:60px;height:5px;background:#e5e7eb;border-radius:99px;overflow:hidden">
                                    <div style="width:{{ $p->materiKbm->tingkat_progres }}%;height:100%;background:var(--primary-blue);border-radius:99px"></div>
                                </div>
                                <span style="font-size:.72rem">{{ $p->materiKbm->tingkat_progres }}%</span>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($p->videoProgres)
                            <a href="{{ $p->videoProgres->url_video }}" target="_blank" class="btn btn-sm btn-outline">
                                <i class="fa-solid fa-play"></i>
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-light)">Tidak ada sesi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection