@extends('layouts.app')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('sidebar-menu')
    {{-- Sidebar dinamis berdasarkan role --}}
    @if(auth()->user()->role === 'admin')
        @include('admin.partials.sidebar')
    @elseif(auth()->user()->role === 'guru')
        <div class="nav-section-label">Menu</div>
        <a href="{{ route('guru.dashboard') }}" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="{{ route('guru.presensi.index') }}" class="nav-item"><i class="fa-solid fa-clipboard-check"></i> Input Presensi</a>
    @else
        <div class="nav-section-label">Menu</div>
        <a href="{{ route('murid.dashboard') }}" class="nav-item {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="{{ route('murid.jadwal.index') }}" class="nav-item {{ request()->routeIs('murid.jadwal*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i> Jadwal Kelas
        </a>
        <a href="{{ route('murid.laporan.index') }}" class="nav-item {{ request()->routeIs('murid.laporan*') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open"></i> Laporan Bulanan
        </a>
        <a href="{{ route('murid.spp.index') }}" class="nav-item {{ request()->routeIs('murid.spp*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya
        </a>
    @endif
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Notifikasi</h2>
        <div class="breadcrumb">Semua notifikasi untukmu</div>
    </div>
</div>

<div class="card">
    @forelse($notifikasis as $n)
    <div style="padding:1rem 1.5rem;border-bottom:1px solid #f3f4f6;display:flex;gap:1rem;align-items:flex-start;
        {{ $n->status_baca=='belum_dibaca' ? 'background:#f8faff;' : '' }}">
        <div style="width:2.5rem;height:2.5rem;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:.9rem;
            {{ match(true) {
                str_contains($n->jenis_notifikasi,'spp')     => 'background:#fee2e2;color:#dc2626;',
                str_contains($n->jenis_notifikasi,'absensi') => 'background:#fffbeb;color:#d97706;',
                default                                       => 'background:#eff6ff;color:var(--primary-blue);'
            } }}">
            <i class="fa-solid {{ match(true) {
                str_contains($n->jenis_notifikasi,'spp')     => 'fa-file-invoice-dollar',
                str_contains($n->jenis_notifikasi,'absensi') => 'fa-clipboard-list',
                default                                       => 'fa-bell'
            } }}"></i>
        </div>
        <div style="flex:1">
            <div style="font-size:.875rem;{{ $n->status_baca=='belum_dibaca' ? 'font-weight:600;' : '' }}">
                {{ $n->pesan }}
            </div>
            <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">
                {{ $n->created_at->diffForHumans() }}
                @if($n->status_baca=='belum_dibaca')
                    · <span style="color:var(--primary-blue);font-weight:600">Baru</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:4rem;color:var(--text-light)">
        <i class="fa-regular fa-bell" style="font-size:2.5rem;opacity:.25;margin-bottom:1rem;display:block"></i>
        <div>Tidak ada notifikasi.</div>
    </div>
    @endforelse
    @if($notifikasis->hasPages())<div style="padding:1rem 1.5rem">{{ $notifikasis->links() }}</div>@endif
</div>
@endsection