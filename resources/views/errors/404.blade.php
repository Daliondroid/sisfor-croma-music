@extends('errors.layout')

@section('title', '404 Halaman Tidak Ditemukan')
@section('code', '404')
@section('heading', 'Halaman Tidak Ditemukan')
@section('message', 'Halaman atau data yang Anda cari tidak tersedia, telah dihapus, atau tautan yang dimasukkan salah.')

@section('actions')
    <a href="{{ url('/') }}" class="btn btn-primary">
        <i class="fa-solid fa-house"></i> Halaman Utama
    </a>
    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    @endauth
@endsection
