@extends('layouts.app')
@section('title', 'Histori Laporan KBM')
@section('page-title', 'Laporan KBM')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}"            class="nav-item {{ request()->routeIs('guru.dashboard')       ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('guru.jadwal.index') }}"         class="nav-item {{ request()->routeIs('guru.jadwal*')         ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('guru.absensi.index') }}"        class="nav-item {{ request()->routeIs('guru.absensi*')        ? 'active' : '' }}"><i class="fa-solid fa-chart-bar"></i> Data Absensi</a>
    <a href="{{ route('guru.presensi.index') }}"       class="nav-item {{ request()->routeIs('guru.presensi*')       ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Input Presensi</a>
    <a href="{{ route('guru.progres.index') }}"        class="nav-item {{ request()->routeIs('guru.progres*')        ? 'active' : '' }}"><i class="fa-solid fa-book-open"></i> Laporan KBM</a>
    <a href="{{ route('guru.monthly-report.index') }}" class="nav-item {{ request()->routeIs('guru.monthly-report*') ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i> Laporan Bulanan</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Histori Laporan KBM - {{ $murid->nama_murid ?? '-' }}</h2>
        <div class="breadcrumb">Guru / Laporan KBM / <span>Detail Histori</span></div>
    </div>
    <div>
        <a href="{{ route('guru.progres.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-book-open" style="color:var(--primary-blue);margin-right:0.5rem"></i>Catatan Perkembangan Sesi</h3>
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
                    <td style="text-align:center;font-weight:bold">{{ $j->sesi_ke }}</td>
                    <td>{{ $j->tanggal->translatedFormat('d M Y') }}</td>
                    <td style="text-align:center">
                        <span class="badge {{ $j->status_kehadiran_murid === 'Hadir' ? 'badge-success' : ($j->status_kehadiran_murid === 'Tidak Hadir' ? 'badge-danger' : 'badge-warning') }}">
                            {{ $j->status_kehadiran_murid ?? 'Belum' }}
                        </span>
                    </td>
                    <td>{{ $j->progresMurid->materi_diajarkan ?? '—' }}</td>
                    <td>{{ $j->progresMurid->catatan_perkembangan ?? '—' }}</td>
                    <td style="text-align:center">
                        @if($j->progresMurid)
                            <a href="{{ route('guru.progres.edit', $j->progresMurid->id_progres) }}" class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                        @else
                            <a href="{{ route('guru.progres.create', ['id_jadwal' => $j->id_jadwal]) }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-plus"></i> Isi
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
