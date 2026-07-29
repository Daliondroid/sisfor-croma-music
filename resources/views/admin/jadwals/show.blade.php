@extends('layouts.app')
@section('title', 'Detail Jadwal KBM')
@section('page-title', 'Detail Jadwal')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2>Detail Jadwal KBM</h2>
        <div class="breadcrumb">Admin / Jadwal / <span>Detail</span></div>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('admin.jadwals.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('admin.jadwals.edit', $jadwal->id_jadwal) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen-to-square"></i> Edit Jadwal
        </a>
    </div>
</div>

<div class="card" style="padding: 2rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm);">
    
    {{-- Header Detail --}}
    <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.5rem; font-weight: 600; color: var(--text-dark);">
                Pertemuan Sesi Ke-{{ $jadwal->sesi_ke }}
            </h3>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <span class="badge {{ $jadwal->status_jadwal === 'Sesuai Jadwal' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                    Jadwal: {{ $jadwal->status_jadwal }}
                </span>
                <span class="badge {{ $jadwal->is_active ? 'badge-info' : 'badge-danger' }}" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                    {{ $jadwal->is_active ? 'Aktif' : 'Non-Aktif' }}
                </span>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0; font-size: 0.875rem; color: #64748b;">ID Jadwal</p>
            <p style="margin: 0; font-weight: 600; color: var(--text-dark);">#{{ $jadwal->id_jadwal }}</p>
        </div>
    </div>

    {{-- Grid Informasi --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr)); gap: 1.5rem;">
        
        {{-- Blok 1: Informasi Waktu & Pelaksanaan --}}
        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 0.375rem; border: 1px solid #e5e7eb;">
            <h4 style="font-size: 1rem; font-weight: 600; color: var(--text-dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-regular fa-calendar" style="color: #3b82f6;"></i> Waktu Pelaksanaan
            </h4>
            <table style="width: 100%; font-size: 0.95rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b; width: 40%; border-bottom: 1px solid #f1f5f9;">Hari</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #f1f5f9;">
                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Tanggal</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #f1f5f9;">
                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b;">Jam Belajar</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-dark);">
                        {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB
                    </td>
                </tr>
            </table>
        </div>

        {{-- Blok 2: Informasi Akademik & Kontrak Kerja --}}
        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 0.375rem; border: 1px solid #e5e7eb;">
            <h4 style="font-size: 1rem; font-weight: 600; color: var(--text-dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-graduation-cap" style="color: #10b981;"></i> Data Akademik
            </h4>
            <table style="width: 100%; font-size: 0.95rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b; width: 40%; border-bottom: 1px solid #f1f5f9;">Murid</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #f1f5f9;">
                        {{ $jadwal->spp->murid->nama_murid ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Guru / Pengajar</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #f1f5f9;">
                        {{ $jadwal->guru->nama_guru ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b;">Tipe Les</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-dark);">
                        <span class="badge badge-info">
                            {{ $jadwal->spp->tipe_les ?? $jadwal->spp->programKursus->tipe_les ?? '-' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Blok 3: Status Kehadiran Presensi --}}
        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 0.375rem; border: 1px solid #e5e7eb; grid-column: span 1;">
            <h4 style="font-size: 1rem; font-weight: 600; color: var(--text-dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-clipboard-user" style="color: #f59e0b;"></i> Status Kehadiran
            </h4>
            <table style="width: 100%; font-size: 0.95rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b; width: 40%; border-bottom: 1px solid #f1f5f9;">Kehadiran Murid</td>
                    <td style="padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                        @if($jadwal->status_kehadiran_murid)
                            <span class="badge {{ $jadwal->status_kehadiran_murid == 'Hadir' ? 'badge-success' : 'badge-danger' }}">
                                {{ $jadwal->status_kehadiran_murid }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Belum Diisi</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b;">Kehadiran Guru</td>
                    <td>
                        @if($jadwal->status_kehadiran_guru)
                            <span class="badge {{ $jadwal->status_kehadiran_guru == 'Hadir' ? 'badge-success' : 'badge-danger' }}">
                                {{ $jadwal->status_kehadiran_guru }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Belum Diisi</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Blok 4: Audit Log / Pengisian Data --}}
        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 0.375rem; border: 1px solid #e5e7eb;">
            <h4 style="font-size: 1rem; font-weight: 600; color: var(--text-dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #6b7280;"></i> Log & Pengisian
            </h4>
            <table style="width: 100%; font-size: 0.95rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b; width: 40%; border-bottom: 1px solid #f1f5f9;">Pengisi Presensi</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #f1f5f9;">
                        {{ $jadwal->presensi_diisi_oleh ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b; border-bottom: 1px solid #f1f5f9;">Waktu Presensi</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid #f1f5f9;">
                        {{ $jadwal->waktu_presensi_diisi ? \Carbon\Carbon::parse($jadwal->waktu_presensi_diisi)->format('d/m/Y H:i') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; color: #64748b;">Nomor Invoice SPP</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: #3b82f6;">
                        @if($jadwal->id_spp)
                            <span style="font-family: monospace;">#SPP-{{ $jadwal->id_spp }}</span>
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