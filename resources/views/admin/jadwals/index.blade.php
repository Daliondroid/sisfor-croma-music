@extends('layouts.app')
@section('title', 'Jadwal KBM')
@section('page-title', 'Jadwal KBM')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Jadwal KBM</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <h2>Jadwal KBM</h2>
    <div class="page-header-filters">
        <form action="{{ route('admin.jadwals.index') }}" method="GET" style="display:flex;gap:0.625rem;align-items:center;flex-wrap:wrap">
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
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
            <a href="{{ route('admin.jadwals.index') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
        <a href="{{ route('admin.jadwals.create') }}" class="btn btn-yellow btn-sm">
            + Buat Jadwal
        </a>
    </div>
</div>

@php
    $groupedJadwals = collect($jadwals->items())->groupBy(function ($jadwal) {
        return $jadwal->spp->murid->id_murid ?? $jadwal->id_spp;
    });
@endphp

<div class="jadwal-container" style="display: flex; flex-direction: column; gap: 1.25rem;">
    @forelse($groupedJadwals as $muridId => $listJadwal)
        @php
            $murid = $listJadwal->first()->spp->murid ?? $listJadwal->first()->murid;
        @endphp
        
        <div class="card" style="padding: 1.25rem;">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid var(--topbar-border); padding-bottom: 0.75rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--text-dark);">
                        {{ $murid->nama_murid ?? 'Data Murid Tidak Ditemukan' }}
                    </h3>
                    <p style="margin: 0.25rem 0 0 0; color: var(--text-light); font-size: 0.8rem;">
                        Kelas: <span style="font-weight: 600; color: var(--text-dark);">{{ $listJadwal->first()->kelas->nama_kelas ?? '-' }}</span>
                    </p>
                </div>
            </div>

            <h4 style="font-size: 0.75rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.06em;">
                JADWAL TERBARU (AKTIF)
            </h4>
            
            <div style="display: flex; gap: 0.875rem; overflow-x: auto; padding-bottom: 0.5rem;">
                @foreach($listJadwal as $j)
                    <div style="min-width: 15rem; border: 1px solid var(--topbar-border); border-radius: 0.25rem; padding: 0.875rem; background-color: var(--bg-light); flex-shrink: 0; display: flex; flex-direction: column;">
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.625rem;">
                            <span class="badge badge-info">{{ strtoupper($j->hari ?? \Carbon\Carbon::parse($j->tanggal)->translatedFormat('l')) }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-light); font-weight: 600; font-variant-numeric: tabular-nums;">
                                {{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}
                            </span>
                        </div>
                        
                        <div style="margin-bottom: 0.35rem; font-weight: 700; color: var(--text-dark); font-size: 0.95rem; font-variant-numeric: tabular-nums;">
                            {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}
                        </div>
                        
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                            Guru: {{ $j->guru->nama_guru }}
                        </div>
                        
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.875rem;">
                            <span class="badge {{ stripos($j->lokasi, 'off') !== false ? 'badge-warning' : 'badge-success' }}">
                                {{ strtoupper($j->lokasi ?? 'On Site') }}
                            </span>
                        </div>

                        <div style="margin-top: auto; padding-top: 0.75rem; border-top: 1px dashed var(--input-border); text-align: center;">
                            <a href="{{ route('admin.jadwals.show', $j->id_jadwal) }}" class="btn btn-sm btn-outline" style="width: 100%; font-size: 0.78rem; padding: 0.25rem 0.5rem; height: 1.85rem; min-height: 1.85rem;">
                                Detail Jadwal
                            </a>
                        </div>
                        
                    </div>
                @endforeach
            </div>
            
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-title">Belum ada jadwal ditemukan.</div>
            <div class="empty-state-description">Tidak ada jadwal yang sesuai dengan filter/pencarian Anda.</div>
            <a href="{{ route('admin.jadwals.create') }}" class="btn btn-yellow btn-sm">+ Buat Jadwal</a>
        </div>
    @endforelse
</div>

@if($jadwals->hasPages())
    <div class="card" style="margin-top: 1.25rem; padding: 0.75rem 1.25rem; display: flex; justify-content: center;">
        {{ $jadwals->withQueryString()->links() }}
    </div>
@endif
@endsection