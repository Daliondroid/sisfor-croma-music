@extends('errors.layout')

@section('title', '419 Sesi Kedaluwarsa')
@section('code', '419')
@section('heading', 'Sesi Telah Kedaluwarsa')
@section('message', 'Sesi keamanan Anda telah berakhir karena tidak ada aktivitas. Silakan muat ulang halaman ini untuk memperbarui token keamanan.')

@section('actions')
    <button onclick="window.location.reload()" class="btn btn-primary">
        <i class="fa-solid fa-rotate-right"></i> Muat Ulang Halaman
    </button>
    <a href="{{ route('login') }}" class="btn btn-outline">
        <i class="fa-solid fa-right-to-bracket"></i> Masuk Kembali
    </a>
@endsection
