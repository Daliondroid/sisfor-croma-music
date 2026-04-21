@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('guru.presensi.index') }}" class="nav-item"><i class="fa-solid fa-clipboard-check"></i> Input Presensi</a>
    <a href="{{ route('guru.profil.edit') }}" class="nav-item active"><i class="fa-solid fa-user-pen"></i> Profil Saya</a>
@endsection

@section('content')
<div class="page-header">
    <div><h2>Profil Saya</h2><div class="breadcrumb">Guru / <span>Profil</span></div></div>
</div>
<div class="card" style="max-width:600px">
    <div class="card-body">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #f0f0f0">
            <div class="avatar" style="width:60px;height:60px;font-size:1.4rem">{{ strtoupper(substr($guru->nama_guru,0,1)) }}</div>
            <div>
                <div style="font-size:1.2rem;font-weight:700">{{ $guru->nama_guru }}</div>
                <div style="color:var(--text-light);font-size:.85rem">{{ $guru->spesialisasi ?? 'Guru' }}</div>
                <div style="font-size:.78rem;color:var(--text-light)">{{ $guru->user->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('guru.profil.update') }}">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif

            <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">Data Profil</div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_guru" class="form-control" value="{{ old('nama_guru', $guru->nama_guru) }}" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Spesialisasi</label>
                <input type="text" name="spesialisasi" class="form-control" value="{{ old('spesialisasi', $guru->spesialisasi) }}" placeholder="Piano, Gitar, Drum..."/>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor HP</label>
                <input type="text" name="nomor_hp" class="form-control" value="{{ old('nomor_hp', $guru->nomor_hp) }}"/>
            </div>

            <hr style="border:none;border-top:1px solid #f0f0f0;margin:20px 0"/>
            <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">Ganti Password <span style="font-weight:400;color:var(--text-light);font-size:.85rem">(opsional)</span></div>
            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control"/>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control"/>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection