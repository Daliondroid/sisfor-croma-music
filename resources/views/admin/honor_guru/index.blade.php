@extends('layouts.app')
@section('title', 'Manajemen Gaji Guru')
@section('page-title', 'Gaji Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <h2>Manajemen Gaji Guru</h2>
    <div class="breadcrumb">Admin / <span>Gaji Guru</span></div>
    <div class="page-header-filters">
        <form action="{{ route('admin.honor-guru.index') }}" method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <select name="id_guru" class="form-control form-control-sm">
                <option value="">Semua Guru</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id_guru }}" {{ request('id_guru') == $g->id_guru ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                @endforeach
            </select>
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="Belum Lunas" {{ request('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas (Draft)</option>
                <option value="Siap Dibayar" {{ request('status') == 'Siap Dibayar' ? 'selected' : '' }}>Siap Dibayar</option>
                <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
            <a href="{{ route('admin.honor-guru.index') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>
</div>

<div class="card" style="border-radius: 0.5rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 0.125rem solid #e2e8f0;">
                    <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: #475569;">#ID Honor</th>
                    <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Guru Pengajar</th>
                    <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Konteks Mengajar</th>
                    <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Sesi</th>
                    <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Nominal Honor</th>
                    <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Status</th>
                    <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: #475569; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($honors as $honor)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;">
                        <td style="padding: 1rem; font-size: 0.875rem; color: #64748b; font-family: monospace;">
                            HG-{{ str_pad($honor->id_honor, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td style="padding: 1rem; font-weight: 600; color: var(--text-dark);">
                            {{ $honor->guru->nama_guru ?? 'Guru Dihapus' }}
                        </td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: #475569;">
                            @php
                                // Mengambil nama murid dari relasi jadwal -> spp -> murid
                                $namaMurid = $honor->jadwals->first()->spp->murid->nama_murid ?? 'N/A';
                            @endphp
                            <i class="fa-solid fa-user-graduate" style="color: #94a3b8; margin-right: 0.25rem;"></i> {{ $namaMurid }}
                        </td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: #475569;">
                            {{ $honor->jumlah_pertemuan }} Pertemuan
                        </td>
                        <td style="padding: 1rem; font-weight: 600; color: #10b981;">
                            Rp {{ number_format($honor->jumlah_honor, 0, ',', '.') }}
                        </td>
                        <td style="padding: 1rem;">
                            @if($honor->status_bayar === 'Lunas')
                                <span class="badge badge-success" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Lunas</span>
                            @elseif($honor->status_bayar === 'Siap Dibayar')
                                <span class="badge badge-info" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Siap Dibayar</span>
                            @else
                                <span class="badge badge-warning" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; color: #92400e;">Belum Lunas</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <a href="{{ route('admin.honor-guru.edit', $honor->id_honor) }}" class="btn btn-sm btn-outline-primary" style="padding: 0.25rem 1rem; font-size: 0.8rem; background-color: #fff;">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Kelola Gaji
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="12" width="48" height="56" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 28h24M28 38h16M28 48h20" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round" opacity=".5"/><circle cx="56" cy="56" r="14" stroke="var(--primary-blue)" stroke-width="2" fill="var(--card-bg)"/><text x="56" y="61" text-anchor="middle" font-size="14" font-weight="700" fill="var(--primary-blue)" font-family="Poppins">$</text></svg>
                            </div>
                            <div class="empty-state-title">Belum ada data gaji guru.</div>
                            <div class="empty-state-description">Tidak ada riwayat atau draft gaji guru yang sesuai dengan filter.</div>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($honors->hasPages())
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $honors->withQueryString()->links() }}
    </div>
@endif
@endsection