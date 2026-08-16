@extends('errors.layout')

@section('title', '429 Terlalu Banyak Permintaan')
@section('code', '429')
@section('heading', 'Batas Permintaan Terlampaui')
@section('message', 'Sistem mendeteksi terlalu banyak permintaan dari perangkat Anda dalam waktu singkat demi keamanan server. Silakan tunggu sekitar 1 menit sebelum mencoba kembali.')

@section('actions')
    <button onclick="window.location.reload()" class="btn btn-primary">
        <i class="fa-solid fa-rotate-right"></i> Coba Lagi
    </button>
    <a href="{{ url('/') }}" class="btn btn-outline">
        <i class="fa-solid fa-house"></i> Halaman Utama
    </a>
@endsection
