@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Laporan Bulanan</h2>
        <div class="breadcrumb">Guru / <span>Laporan Bulanan</span></div>
    </div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="month" name="bulan" class="form-control form-control-sm" style="width:auto"
                   value="{{ $bulan }}" onchange="this.form.submit()"/>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>
            Daftar Murid &mdash; {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
        </h3>
        <span style="font-size:.8rem;color:var(--text-light);font-variant-numeric:tabular-nums">{{ $spps->count() }} murid</span>
    </div>

    @if($spps->isEmpty())
        <div class="empty-state">
            <div class="empty-state-title">Tidak ada murid ditemukan.</div>
            <div class="empty-state-description">Tidak ada murid yang diajar pada bulan ini.</div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Murid</th>
                        <th>Program</th>
                        <th style="text-align:center">Total Sesi</th>
                        <th style="text-align:center">Kehadiran</th>
                        <th style="text-align:center">% Hadir</th>
                        <th style="text-align:center">Skor Otomatis</th>
                        <th style="text-align:center">Status Laporan</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($spps as $spp)
                    @php
                        $persen     = $spp->persen;
                        $barColor   = $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626');
                        $sudahAda   = $spp->report !== null;

                        $skorOtomatis = match(true) {
                            $persen >= 95 => 'A+',
                            $persen >= 90 => 'A',
                            $persen >= 85 => 'A-',
                            $persen >= 80 => 'B+',
                            $persen >= 75 => 'B',
                            $persen >= 70 => 'B-',
                            $persen >= 65 => 'C+',
                            $persen >= 60 => 'C',
                            default       => 'C-',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;color:var(--text-dark)">{{ $spp->murid->nama_murid ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-size:.85rem;color:var(--text-dark)">{{ $spp->programKursus->nama_program ?? '-' }}</div>
                            <span class="badge {{ $spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}">{{ strtoupper($spp->tipe_les) }}</span>
                        </td>
                        <td style="text-align:center;font-weight:600;font-variant-numeric:tabular-nums">{{ $spp->total_sesi }}</td>
                        <td style="text-align:center;font-variant-numeric:tabular-nums">
                            <span style="color:#16a34a;font-weight:700">{{ $spp->hadir_murid }}</span>
                            <span style="color:var(--text-light)"> / {{ $spp->total_sesi }}</span>
                        </td>
                        <td style="text-align:center;min-width:6.25rem">
                            <div style="display:flex;align-items:center;gap:0.5rem">
                                <div style="flex:1;height:0.25rem;background:#f3f4f6;border-radius:0.125rem;overflow:hidden">
                                    <div style="width:{{ $persen }}%;height:100%;background:{{ $barColor }};border-radius:0.125rem"></div>
                                </div>
                                <span style="font-size:.78rem;font-weight:700;color:{{ $barColor }};min-width:2.125rem;font-variant-numeric:tabular-nums">{{ $persen }}%</span>
                            </div>
                        </td>
                        <td style="text-align:center">
                            <span style="font-size:1rem;font-weight:700;color:var(--text-dark);font-variant-numeric:tabular-nums">{{ $skorOtomatis }}</span>
                        </td>
                        <td style="text-align:center">
                            @if($sudahAda)
                                <span class="badge badge-success">
                                    SELESAI
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    BELUM DIBUAT
                                </span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            @if($sudahAda)
                                <div class="action-dropdown-wrapper" style="position:relative;display:inline-block">
                                    <button type="button" class="btn btn-outline btn-sm action-dropdown-btn" onclick="toggleActionDropdown(this, event)">
                                        Kelola ▾
                                    </button>
                                    <div class="action-dropdown-menu">
                                        <a href="{{ route('guru.monthly-report.show', $spp->report->id_report) }}" class="dropdown-item">Lihat Laporan</a>
                                        <a href="{{ route('guru.monthly-report.create', ['id_spp' => $spp->id_spp, 'bulan' => $bulan]) }}" class="dropdown-item">Edit Laporan</a>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('guru.monthly-report.create', ['id_spp' => $spp->id_spp, 'bulan' => $bulan]) }}"
                                   class="btn btn-primary btn-sm">
                                    Buat Laporan
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
