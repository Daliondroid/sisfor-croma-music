@extends('errors.layout')

@section('title', '503 Pemeliharaan Sistem')
@section('code', '503')
@section('heading', 'Pemeliharaan Sistem Sedang Berlangsung')
@section('message', 'Sistem Informasi Croma Music saat ini sedang menjalani pemeliharaan berkala untuk peningkatan kinerja dan keamanan. Kami akan segera kembali.')

@section('actions')
    <button onclick="window.location.reload()" class="btn btn-primary">
        <i class="fa-solid fa-rotate-right"></i> Periksa Status
    </button>
@endsection
