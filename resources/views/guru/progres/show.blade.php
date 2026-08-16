@extends('layouts.app')
@section('title', 'Histori Laporan KBM')
@section('page-title', 'Laporan KBM')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Histori Laporan KBM &mdash; {{ $murid->nama_murid ?? '-' }}</h2>
        <div class="breadcrumb">Guru / Laporan KBM / <span>Detail Histori</span></div>
    </div>
    <div>
        <a href="{{ route('guru.progres.index') }}" class="btn btn-outline btn-sm">
            Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Catatan Perkembangan Sesi</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="text-align:center">Sesi</th>
                    <th>Tanggal</th>
                    <th style="text-align:center">Kehadiran</th>
                    <th>Materi Diajarkan</th>
                    <th>Catatan Perkembangan</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jadwals as $j)
                <tr>
                    <td style="text-align:center;font-weight:700;font-variant-numeric:tabular-nums">{{ $j->sesi_ke }}</td>
                    <td style="font-weight:600;color:var(--text-dark)">{{ $j->tanggal->translatedFormat('d M Y') }}</td>
                    <td style="text-align:center">
                        <span class="badge {{ $j->status_kehadiran_murid === 'Hadir' ? 'badge-success' : ($j->status_kehadiran_murid === 'Tidak Hadir' ? 'badge-danger' : 'badge-warning') }}">
                            {{ strtoupper($j->status_kehadiran_murid ?? 'BELUM') }}
                        </span>
                    </td>
                    <td style="color:var(--text-dark)">{{ $j->progresMurid->materi_diajarkan ?? '—' }}</td>
                    <td style="color:var(--text-light);font-size:.82rem">{{ $j->progresMurid->catatan_perkembangan ?? '—' }}</td>
                    <td style="text-align:center">
                        @if($j->progresMurid)
                            <a href="{{ route('guru.progres.edit', $j->progresMurid->id_progres) }}" class="btn btn-outline btn-sm">
                                Edit
                            </a>
                        @else
                            <a href="{{ route('guru.progres.create', ['id_jadwal' => $j->id_jadwal]) }}" class="btn btn-primary btn-sm">
                                Isi
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:2rem;color:var(--text-light)">
                        Belum ada catatan progres untuk murid ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
