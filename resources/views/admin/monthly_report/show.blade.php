@extends('layouts.app')
@section('title', 'Detail Monthly Report')
@section('page-title', 'Detail Monthly Report')

@section('breadcrumb')
    <span class="crumb-root">Gaji & Laporan</span>
    <span class="crumb-sep">/</span>
    <a href="{{ route('admin.monthly_report.index') }}" class="crumb-root">Monthly Report</a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">{{ $murid->nama_murid }}</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>{{ $murid->nama_murid }} &mdash; <span style="font-size:1.1rem;color:var(--text-light);font-weight:400">{{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</span></h2>
    </div>
    <div style="display:flex;gap:0.625rem;align-items:center">
        <a href="{{ route('admin.monthly_report.index', ['bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
            Kembali
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0;padding-left:1.25rem">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Open KPI Metric Strips -->
<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total_sesi'] ?? 0 }}</div>
        <div class="stat-label">Total Sesi</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['hadir'] ?? 0 }}</div>
        <div class="stat-label">Total Hadir</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['tidak_hadir'] ?? 0 }}</div>
        <div class="stat-label">Total Tidak Hadir</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['persen_hadir'] ?? 0 }}%</div>
        <div class="stat-label">Persentase Kehadiran</div>
    </div>
</div>

@if($report)
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-header">
        <h3>Evaluasi & Skor Bulanan</h3>
        <span class="badge {{ in_array($report->skor, ['A+', 'A', 'A-']) ? 'badge-success' : (in_array($report->skor, ['B+', 'B', 'B-']) ? 'badge-info' : 'badge-warning') }}" style="font-size:0.875rem;padding:0.25rem 0.625rem">
            Skor: {{ $report->skor ?? '—' }}
        </span>
    </div>
    <div class="card-body">
        <div style="margin-bottom:1.25rem">
            <div class="form-label" style="font-weight:700">Catatan / Evaluasi Pembelajaran</div>
            <div style="background:#f8fafc;padding:0.875rem 1rem;border-radius:0.25rem;border:1px solid var(--topbar-border);font-size:.875rem;line-height:1.6;color:var(--text-dark);white-space:pre-wrap">
                {{ $report->evaluasi_bulanan ?? 'Belum ada catatan evaluasi.' }}
            </div>
        </div>

        @if($report->url_video)
        <div style="margin-bottom:1.25rem">
            <div class="form-label" style="font-weight:700">Video Dokumentasi KBM</div>
            <a href="{{ $report->url_video }}" target="_blank" class="btn btn-outline btn-sm" style="display:inline-flex;gap:0.35rem">
                <i class="fa-solid fa-play"></i> Buka Video KBM
            </a>
        </div>
        @endif

        <hr style="border:none;border-top:1px solid var(--topbar-border);margin:1.25rem 0">

        {{-- Form Edit Evaluasi --}}
        <details>
            <summary style="cursor:pointer;font-weight:600;font-size:0.875rem;color:var(--primary-navy);margin-bottom:0.75rem">
                <i class="fa-solid fa-pen-to-square"></i> Edit Evaluasi & Skor
            </summary>
            <form action="{{ route('admin.report.update', $report->id_report) }}" method="POST" style="margin-top:1rem">
                @csrf
                @method('PUT')
                <div class="form-grid" style="margin-bottom:1rem">
                    <div class="form-group">
                        <label class="form-label" for="skor">Skor Hasil Belajar <span style="color:#dc2626">*</span></label>
                        <select name="skor" id="skor" class="form-control" required>
                            @foreach(['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-'] as $optSkor)
                                <option value="{{ $optSkor }}" {{ old('skor', $report->skor) === $optSkor ? 'selected' : '' }}>
                                    {{ $optSkor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="url_video">URL Video KBM (Opsional)</label>
                        <input type="url" name="url_video" id="url_video" class="form-control" value="{{ old('url_video', $report->url_video) }}" placeholder="https://youtube.com/...">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:1rem">
                    <label class="form-label" for="evaluasi_bulanan">Evaluasi Bulanan <span style="color:#dc2626">*</span></label>
                    <textarea name="evaluasi_bulanan" id="evaluasi_bulanan" class="form-control" rows="4" style="height:auto;min-height:5rem;padding:0.5rem 0.75rem" required>{{ old('evaluasi_bulanan', $report->evaluasi_bulanan) }}</textarea>
                </div>
                <div style="display:flex;justify-content:flex-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </details>
    </div>
</div>
@else
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-body" style="text-align:center;padding:1.5rem">
        <p style="margin:0 0 1rem 0;color:var(--text-light);font-size:0.875rem">
            Monthly report belum dibuat untuk murid ini pada periode {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}.
        </p>
        <form method="POST" action="{{ route('admin.report.generate') }}" style="display:inline">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <button type="submit" class="btn btn-secondary btn-sm">
                Generate Monthly Report
            </button>
        </form>
    </div>
</div>
@endif

<!-- Detail per sesi -->
<div class="card">
    <div class="card-header">
        <h3>Detail Sesi KBM</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:8%;text-align:center">Sesi</th>
                    <th style="width:18%">Tanggal & Waktu</th>
                    <th style="width:20%">Guru</th>
                    <th style="width:14%;text-align:center">Kehadiran</th>
                    <th style="width:20%">Materi KBM</th>
                    <th style="width:20%">Catatan Perkembangan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jadwals as $j)
                <tr>
                    <td style="text-align:center;font-weight:700;font-variant-numeric:tabular-nums">Sesi {{ $j->sesi_ke }}</td>
                    <td>
                        <span style="font-variant-numeric:tabular-nums;font-weight:600">{{ $j->tanggal ? $j->tanggal->format('d/m/Y') : '-' }}</span><br>
                        <span style="font-size:0.75rem;color:var(--text-light);font-variant-numeric:tabular-nums">
                            {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }} WIB
                        </span>
                    </td>
                    <td>{{ $j->guru->nama_guru ?? '-' }}</td>
                    <td style="text-align:center">
                        @if($j->status_kehadiran_murid === 'Hadir')
                            <span class="badge badge-success">HADIR</span>
                        @elseif($j->status_kehadiran_murid === 'Tidak Hadir')
                            <span class="badge badge-danger">TIDAK HADIR</span>
                        @else
                            <span style="color:var(--text-light);font-size:0.75rem">Belum Diisi</span>
                        @endif
                    </td>
                    <td style="font-size:0.85rem">{{ $j->progresMurid?->materi_diajarkan ?? '—' }}</td>
                    <td style="font-size:0.8rem;color:var(--text-light)">{{ $j->progresMurid?->catatan_perkembangan ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:1.5rem;color:var(--text-light)">
                        Tidak ada sesi KBM pada periode ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection