@extends('layouts.app')
@section('title', 'Laporan KBM Harian')
@section('page-title', 'Laporan KBM Harian')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Laporan KBM Harian</h2>
        <div class="breadcrumb">Guru / <span>Laporan KBM</span></div>
    </div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <select name="id_spp" class="form-control form-control-sm" style="width:auto" onchange="this.form.submit()">
                <option value="">Semua Murid</option>
                @foreach($muridDiajar as $spp)
                    <option value="{{ $spp->id_spp }}" {{ request('id_spp') == $spp->id_spp ? 'selected' : '' }}>
                        {{ $spp->murid->nama_murid ?? '-' }}
                    </option>
                @endforeach
            </select>
            <input type="month" name="bulan" class="form-control form-control-sm" style="width:auto"
                   value="{{ request('bulan', now()->format('Y-m')) }}" onchange="this.form.submit()"/>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Riwayat Laporan KBM</h3>
        <span style="font-size:.8rem;color:var(--text-light);font-variant-numeric:tabular-nums">{{ $progres->total() }} record</span>
    </div>

    @if($progres->isEmpty())
        <div class="empty-state">
            <div class="empty-state-title">Belum ada laporan KBM.</div>
            <div class="empty-state-description">Belum ada laporan KBM yang diinput.</div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Murid</th>
                        <th>Program</th>
                        <th>Materi Diajarkan</th>
                        <th>Catatan Perkembangan</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($progres as $p)
                    <tr>
                        <td style="white-space:nowrap">
                            <div style="font-weight:600;color:var(--text-dark)">{{ $p->jadwal->tanggal->translatedFormat('d M Y') }}</div>
                            <div style="font-size:.75rem;color:var(--text-light)">
                                Sesi {{ $p->jadwal->sesi_ke }}
                            </div>
                        </td>
                        <td style="font-weight:600;color:var(--text-dark)">{{ $p->jadwal->spp->murid->nama_murid ?? '-' }}</td>
                        <td>{{ $p->jadwal->spp->programKursus->nama_program ?? '-' }}</td>
                        <td style="max-width:12.5rem;color:var(--text-dark)">{{ Str::limit($p->materi_diajarkan, 60) }}</td>
                        <td style="max-width:12.5rem;color:var(--text-light);font-size:.82rem">
                            {{ Str::limit($p->catatan_perkembangan, 60) }}
                        </td>
                        <td style="text-align:center">
                            <a href="{{ route('guru.progres.edit', $p->id_progres) }}" class="btn btn-outline btn-sm">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.5rem">
            {{ $progres->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
