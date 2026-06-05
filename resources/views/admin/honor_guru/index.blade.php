@extends('layouts.app')
@section('title', 'Manajemen Gaji Guru')
@section('page-title', 'Gaji Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <h2>Manajemen Gaji Guru</h2>
        <div class="breadcrumb">Admin / <span>Gaji Guru</span></div>
    </div>
</div>

<div class="card" style="margin-bottom: 1.5rem; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <form action="{{ route('admin.honor-guru.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; color: var(--text-dark);">Filter Guru</label>
            <select name="id_guru" class="form-control" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 0.5rem;">
                <option value="">Semua Guru</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id_guru }}" {{ request('id_guru') == $g->id_guru ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; color: var(--text-dark);">Status Pembayaran</label>
            <select name="status" class="form-control" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 0.5rem;">
                <option value="">Semua Status</option>
                <option value="Belum Lunas" {{ request('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas (Draft)</option>
                <option value="Siap Dibayar" {{ request('status') == 'Siap Dibayar' ? 'selected' : '' }}>Siap Dibayar</option>
                <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" title="Terapkan Filter" style="padding: 0.5rem 1.25rem;">
                <i class="fa-solid fa-magnifying-glass"></i> Filter
            </button>
            <a href="{{ route('admin.honor-guru.index') }}" class="btn btn-secondary" title="Reset Filter" style="padding: 0.5rem 1.25rem;">
                Reset
            </a>
        </div>
    </form>
</div>

<div class="card" style="border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
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
                            <i class="fa-solid fa-user-graduate" style="color: #94a3b8; margin-right: 4px;"></i> {{ $namaMurid }}
                        </td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: #475569;">
                            {{ $honor->jumlah_pertemuan }} Pertemuan
                        </td>
                        <td style="padding: 1rem; font-weight: 600; color: #10b981;">
                            Rp {{ number_format($honor->jumlah_honor, 0, ',', '.') }}
                        </td>
                        <td style="padding: 1rem;">
                            @if($honor->status_bayar === 'Lunas')
                                <span class="badge badge-success" style="font-size: 0.75rem; padding: 0.35rem 0.6rem;">Lunas</span>
                            @elseif($honor->status_bayar === 'Siap Dibayar')
                                <span class="badge badge-info" style="font-size: 0.75rem; padding: 0.35rem 0.6rem;">Siap Dibayar</span>
                            @else
                                <span class="badge badge-warning" style="font-size: 0.75rem; padding: 0.35rem 0.6rem; color: #92400e;">Belum Lunas</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <a href="{{ route('admin.honor-guru.edit', $honor->id_honor) }}" class="btn btn-sm btn-outline-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background-color: #fff;">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Kelola Gaji
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 3rem 1rem; text-align: center; color: #94a3b8;">
                            <i class="fa-solid fa-box-open" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                            <p style="margin: 0;">Belum ada data riwayat atau draft gaji guru yang sesuai.</p>
                        </td>
                    </tr>
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