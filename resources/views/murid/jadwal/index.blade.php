@extends('layouts.app')

@section('title', 'Jadwal Kelas Saya')
@section('page-title', 'Jadwal Kelas')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    
    <a href="{{ route('murid.dashboard') }}" class="nav-item {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    
    <a href="{{ route('murid.jadwal.index') }}" class="nav-item {{ request()->routeIs('murid.jadwal*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-days"></i> Jadwal Kelas
    </a>
    
    <a href="{{ route('murid.spp.index') }}" class="nav-item {{ request()->routeIs('murid.spp*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya
    </a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Jadwal Kelas Saya</h2>
        <div class="breadcrumb">Daftar seluruh sesi kelas yang Anda ikuti</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-calendar-week" style="color:var(--primary-blue);margin-right:8px"></i>Daftar Jadwal Les
        </h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Guru Pendamping</th>
                    <th>Program Kursus</th>
                    <th>Tipe / Lokasi</th>
                    <th>Status Kelas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $j)
                    <tr>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($j->tanggal)->translatedFormat('l, d F Y') }}</strong>
                        </td>
                        <td>
                            {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}
                        </td>
                        <td>{{ $j->guru->nama_guru }}</td>
                        <td>{{ $j->spp?->programKursus?->nama_program ?? 'Program Musik' }}</td>
                        <td>
                            <span class="badge {{ ($j->spp?->tipe_les ?? 'onsite') == 'onsite' ? 'badge-info' : 'badge-warning' }}">
                                {{ ucfirst($j->spp?->tipe_les ?? 'Onsite') }}
                            </span>
                            @if($j->lokasi)
                                <div style="font-size: 0.8rem; color: var(--text-light); margin-top: 6px;">
                                    <i class="fa-solid fa-location-dot"></i> {{ $j->lokasi }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $j->status_jadwal == 'Sesuai Jadwal' ? 'badge-success' : 'badge-danger' }}">
                                {{ $j->status_jadwal }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 24px; color: var(--text-light);">
                            Anda belum memiliki jadwal kelas yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection