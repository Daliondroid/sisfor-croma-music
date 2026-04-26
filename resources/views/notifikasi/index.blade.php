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
        <a href="{{ route('murid.dashboard') }}" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="{{ route('murid.spp.index') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya</a>
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
    <div style="padding:16px 24px;border-bottom:1px solid #f3f4f6;display:flex;gap:14px;align-items:flex-start;
        {{ $n->status_baca=='belum_dibaca' ? 'background:#f8faff;' : '' }}">
        <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:.9rem;
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
            <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">
                {{ $n->created_at->diffForHumans() }}
                @if($n->status_baca=='belum_dibaca')
                    · <span style="color:var(--primary-blue);font-weight:600">Baru</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:60px;color:var(--text-light)">
        <i class="fa-regular fa-bell" style="font-size:2.5rem;opacity:.25;margin-bottom:12px;display:block"></i>
        <div>Tidak ada notifikasi.</div>
    </div>
    @endforelse
    @if($notifikasis->hasPages())<div style="padding:16px 24px">{{ $notifikasis->links() }}</div>@endif
</div>
@endsection