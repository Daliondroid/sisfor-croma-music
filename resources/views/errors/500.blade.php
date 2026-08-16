@extends('errors.layout')

@section('title', '500 Kesalahan Server')
@section('code', '500')
@section('heading', 'Terjadi Gangguan Server')
@section('message', 'Sistem mengalami kendala tak terduga saat memproses permintaan Anda. Tim teknis kami telah mencatat peristiwa ini.')

@section('actions')
    <a href="{{ url('/') }}" class="btn btn-primary">
        <i class="fa-solid fa-house"></i> Halaman Utama
    </a>
    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-outline">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
    @endauth
@endsection
