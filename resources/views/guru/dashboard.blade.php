@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Guru')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}" class="nav-item active">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('guru.presensi.index') }}" class="nav-item">
        <i class="fa-solid fa-clipboard-check"></i> Input Presensi
    </a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Selamat Datang, {{ $guru->nama_guru }}</h2>
        <div class="breadcrumb">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
</div>

<!-- Jadwal Hari Ini -->
<div class="card" style="margin-bottom:24px">
    <div class="card-header">
        <h3><i class="fa-solid fa-calendar-day" style="color:var(--primary-blue);margin-right:8px"></i>Jadwal Mengajar Hari Ini</h3>
        <a href="{{ route('guru.presensi.index') }}" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-clipboard-check"></i> Input Presensi
        </a>
    </div>
    @if($jadwalHariIni->isEmpty())
        <div style="text-align:center;padding:40px;color:var(--text-light)">
            <i class="fa-solid fa-mug-hot" style="font-size:2rem;margin-bottom:12px;opacity:.4"></i>
            <p>Tidak ada jadwal mengajar hari ini.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:0;border-top:1px solid #f0f0f0">
            @foreach($jadwalHariIni as $j)
            <div style="padding:20px 24px;border-right:1px solid #f0f0f0;border-bottom:1px solid #f0f0f0">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px">
                    <div>
                        <div style="font-weight:700;font-size:1rem">{{ $j->murid->nama_murid }}</div>
                        <div style="font-size:.78rem;color:var(--text-light)">{{ $j->kelas->nama_kelas ?? '-' }}</div>
                    </div>
                    <span class="badge {{ $j->kelas->tipe_les=='onsite' ? 'badge-info' : 'badge-warning' }}">
                        {{ $j->kelas->tipe_les=='onsite' ? 'Onsite' : 'Home' }}
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:.85rem;color:var(--text-light)">
                    <i class="fa-regular fa-clock"></i>
                    {{ substr($j->jam_mulai,0,5) }} – {{ substr($j->jam_selesai,0,5) }}
                </div>
                @if($j->lokasi)
                <div style="display:flex;align-items:center;gap:6px;font-size:.85rem;color:var(--text-light);margin-top:4px">
                    <i class="fa-solid fa-location-dot"></i> {{ $j->lokasi }}
                </div>
                @endif
                <a href="{{ route('guru.presensi.index') }}?jadwal={{ $j->id_jadwal }}" class="btn btn-sm btn-primary" style="margin-top:14px;width:100%;justify-content:center">
                    <i class="fa-solid fa-pen-to-square"></i> Input Presensi
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection