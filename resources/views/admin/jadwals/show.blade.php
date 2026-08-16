@extends('layouts.app')
@section('title', 'Detail Jadwal KBM')
@section('page-title', 'Detail Jadwal')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <a href="{{ route('admin.jadwals.index') }}" class="crumb-root">Jadwal KBM</a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Detail Jadwal #{{ $jadwal->id_jadwal }}</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Detail Jadwal KBM</h2>
    </div>
    <div style="display: flex; gap: 0.625rem;">
        <a href="{{ route('admin.jadwals.index') }}" class="btn btn-outline btn-sm">
            Kembali
        </a>
        <a href="{{ route('admin.jadwals.edit', $jadwal->id_jadwal) }}" class="btn btn-secondary btn-sm">
            Edit Jadwal
        </a>
    </div>
</div>

<div class="card" style="padding: 1.25rem 2.5rem;">
    
    {{-- Header Detail --}}
    <div style="border-bottom: 1px solid var(--topbar-border); padding-bottom: 1.25rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 700; color: var(--text-dark);">
                Pertemuan Sesi Ke-{{ $jadwal->sesi_ke }}
            </h3>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <span class="badge {{ $jadwal->status_jadwal === 'Sesuai Jadwal' ? 'badge-success' : 'badge-warning' }}">
                    {{ strtoupper($jadwal->status_jadwal) }}
                </span>
                <span class="badge {{ $jadwal->is_active ? 'badge-info' : 'badge-danger' }}">
                    {{ $jadwal->is_active ? 'AKTIF' : 'NONAKTIF' }}
                </span>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.06em;">ID Jadwal</p>
            <p style="margin: 0; font-weight: 700; font-family: monospace; color: var(--text-dark);">#{{ $jadwal->id_jadwal }}</p>
        </div>
    </div>

    {{-- Grid Informasi --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(18rem, 1fr)); gap: 1.25rem;">
        
        {{-- Blok 1: Informasi Waktu & Pelaksanaan --}}
        <div style="background-color: #f8fafc; padding: 1rem 2rem; border-radius: 0.25rem; border: 1px solid #e2e8f0;">
            <h4 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-dark); margin-bottom: 0.875rem;">
                Waktu Pelaksanaan
            </h4>
            <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light); width: 40%; border-bottom: 1px solid #e2e8f0;">Hari</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #e2e8f0;">
                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light); border-bottom: 1px solid #e2e8f0;">Tanggal</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #e2e8f0; font-variant-numeric: tabular-nums;">
                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light);">Jam Belajar</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-dark); font-variant-numeric: tabular-nums;">
                        {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                    </td>
                </tr>
            </table>
        </div>

        {{-- Blok 2: Informasi Akademik & Kontrak Kerja --}}
        <div style="background-color: #f8fafc; padding: 1rem 2rem; border-radius: 0.25rem; border: 1px solid #e2e8f0;">
            <h4 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-dark); margin-bottom: 0.875rem;">
                Data Akademik
            </h4>
            <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light); width: 40%; border-bottom: 1px solid #e2e8f0;">Murid</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #e2e8f0;">
                        {{ $jadwal->spp->murid->nama_murid ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light); border-bottom: 1px solid #e2e8f0;">Guru / Pengajar</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #e2e8f0;">
                        {{ $jadwal->guru->nama_guru ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light);">Tipe Les</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-dark);">
                        <span class="badge badge-info">
                            {{ strtoupper($jadwal->spp->tipe_les ?? $jadwal->spp->programKursus->tipe_les ?? '-') }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Blok 3: Status Kehadiran Presensi --}}
        <div style="background-color: #f8fafc; padding: 1rem 2rem; border-radius: 0.25rem; border: 1px solid #e2e8f0;">
            <h4 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-dark); margin-bottom: 0.875rem;">
                Status Kehadiran
            </h4>
            <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light); width: 40%; border-bottom: 1px solid #e2e8f0;">Kehadiran Murid</td>
                    <td style="padding: 0.4rem 0; border-bottom: 1px solid #e2e8f0;">
                        @if($jadwal->status_kehadiran_murid)
                            <span class="badge {{ $jadwal->status_kehadiran_murid == 'Hadir' ? 'badge-success' : 'badge-danger' }}">
                                {{ strtoupper($jadwal->status_kehadiran_murid) }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-size: 0.8rem;">Belum Diisi</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light);">Kehadiran Guru</td>
                    <td style="padding: 0.4rem 0;">
                        @if($jadwal->status_kehadiran_guru)
                            <span class="badge {{ $jadwal->status_kehadiran_guru == 'Hadir' ? 'badge-success' : 'badge-danger' }}">
                                {{ strtoupper($jadwal->status_kehadiran_guru) }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-size: 0.8rem;">Belum Diisi</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Blok 4: Audit Log / Pengisian Data --}}
        <div style="background-color: #f8fafc; padding: 1.25rem; border-radius: 0.25rem; border: 1px solid #e2e8f0;">
            <h4 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-dark); margin-bottom: 0.875rem;">
                Log & Pengisian
            </h4>
            <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light); width: 40%; border-bottom: 1px solid #e2e8f0;">Pengisi Presensi</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #e2e8f0;">
                        {{ $jadwal->presensi_diisi_oleh ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light); border-bottom: 1px solid #e2e8f0;">Waktu Presensi</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #e2e8f0; font-variant-numeric: tabular-nums;">
                        {{ $jadwal->waktu_presensi_diisi ? \Carbon\Carbon::parse($jadwal->waktu_presensi_diisi)->format('d/m/Y H:i') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.4rem 0; color: var(--text-light);">Nomor Invoice SPP</td>
                    <td style="padding: 0.4rem 0; font-weight: 600; color: var(--primary-navy);">
                        @if($jadwal->id_spp)
                            <span style="font-family: monospace; font-variant-numeric: tabular-nums;">#SPP-{{ $jadwal->id_spp }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>
@endsection