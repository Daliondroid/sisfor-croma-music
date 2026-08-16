@extends('errors.layout')

@section('title', '403 Akses Ditolak')
@section('code', '403')
@section('heading', 'Akses Ditolak')
@section('message', 'Anda tidak memiliki hak akses atau otorisasi yang memadai untuk membuka halaman ini.')

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
