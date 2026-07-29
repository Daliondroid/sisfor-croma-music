@extends('layouts.app')
@section('title', 'Jadwal KBM')
@section('page-title', 'Jadwal KBM')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <h2>Jadwal KBM</h2>
    <div class="breadcrumb">Admin / <span>Jadwal</span></div>
    <div class="page-header-filters">
        <form action="{{ route('admin.jadwals.index') }}" method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari murid...">
            <select name="id_guru" class="form-control form-control-sm">
                <option value="">Semua Guru</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id_guru }}" {{ request('id_guru') == $g->id_guru ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                @endforeach
            </select>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm">
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="Sesuai Jadwal" {{ request('status') == 'Sesuai Jadwal' ? 'selected' : '' }}>Sesuai Jadwal</option>
                <option value="Reschedule" {{ request('status') == 'Reschedule' ? 'selected' : '' }}>Reschedule</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        </form>
        <a href="{{ route('admin.jadwals.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Buat Jadwal
        </a>
    </div>
</div>

@php
    $groupedJadwals = collect($jadwals->items())->groupBy(function ($jadwal) {
        return $jadwal->spp->murid->id_murid ?? $jadwal->id_spp;
    });
@endphp

<div class="jadwal-container" style="display: flex; flex-direction: column; gap: 1.5rem;">
    @forelse($groupedJadwals as $muridId => $listJadwal)
        @php
            $murid = $listJadwal->first()->spp->murid ?? $listJadwal->first()->murid;
        @endphp
        
        <div class="card" style="padding: 1.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm);">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: var(--text-dark);">
                        {{ $murid->nama_murid ?? 'Data Murid Tidak Ditemukan' }}
                    </h3>
                    <p style="margin: 0.25rem 0 0 0; color: var(--text-light); font-size: 0.875rem;">
                        Kelas: {{ $listJadwal->first()->kelas->nama_kelas ?? '-' }}
                    </p>
                </div>
                {{-- <div>
                    <a href="#" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-calendar-days"></i> Lihat Full Jadwal
                    </a>
                </div> --}}
            </div>

            <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-dark);">
                Jadwal Terbaru (Aktif)
            </h4>
            
            <div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem;">
                @foreach($listJadwal as $j)
                    <div style="min-width: 15rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 1rem; background-color: #f8fafc; flex-shrink: 0; display: flex; flex-direction: column;">
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span class="badge badge-info">{{ $j->hari ?? \Carbon\Carbon::parse($j->tanggal)->translatedFormat('l') }}</span>
                            <span style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">
                                {{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}
                            </span>
                        </div>
                        
                        <div style="margin-bottom: 0.5rem; font-weight: 600; color: var(--text-dark); font-size: 1rem;">
                            <i class="fa-regular fa-clock" style="margin-right: 0.25rem; color: #64748b;"></i>
                            {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}
                        </div>
                        
                        <div style="font-size: 0.875rem; color: #475569; margin-bottom: 0.25rem;">
                            <i class="fa-solid fa-user-tie" style="margin-right: 0.25rem; width: 1rem; color: #64748b; text-align: center;"></i> 
                            {{ $j->guru->nama_guru }}
                        </div>
                        
                        <div style="font-size: 0.875rem; color: #475569; margin-bottom: 1rem;">
                            <i class="fa-solid fa-map-pin" style="margin-right: 0.25rem; width: 1rem; color: #64748b; text-align: center;"></i> 
                            <span class="badge {{ stripos($j->lokasi, 'off') !== false ? 'badge-warning' : 'badge-success' }}">
                                {{ $j->lokasi ?? 'On Site' }}
                            </span>
                        </div>

                        <div style="margin-top: auto; padding-top: 1rem; border-top: 1px dashed #cbd5e1; text-align: center;">
                            <a href="{{ route('admin.jadwals.show', $j->id_jadwal) }}" class="btn btn-sm btn-outline-primary" style="width: 100%; font-size: 0.75rem; padding: 0.25rem 0.5rem; background-color: #fff;">
                                <i class="fa-solid fa-circle-info"></i> Detail Jadwal
                            </a>
                        </div>
                        
                    </div>
                @endforeach
            </div>
            
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="12" y="20" width="56" height="48" rx="6" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M12 32h56" stroke="var(--primary-blue)" stroke-width="2"/><rect x="24" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><rect x="52" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><circle cx="30" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="40" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="50" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="30" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="40" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="50" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/></svg>
            </div>
            <div class="empty-state-title">Belum ada jadwal ditemukan.</div>
            <div class="empty-state-description">Tidak ada jadwal yang sesuai dengan filter/pencarian Anda.</div>
            <a href="{{ route('admin.jadwals.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Jadwal</a>
        </div>
    @endforelse
</div>

@if($jadwals->hasPages())
    <div class="card" style="margin-top: 1.5rem; padding: 1rem 1.5rem; display: flex; justify-content: center;">
        {{ $jadwals->withQueryString()->links() }}
    </div>
@endif
@endsection