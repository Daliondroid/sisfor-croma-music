@extends('layouts.app')
@section('title', 'Tambah Murid')
@section('page-title', 'Tambah Murid')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <a href="{{ route('admin.murids.index') }}" class="crumb-root">Data Murid</a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Tambah Murid</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Tambah Murid</h2>
    </div>
</div>

<div class="card" style="max-width:48.75rem">
    <div class="card-header"><h3>Data Akun & Profil</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.murids.store') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <div style="font-weight:700;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:1rem;color:var(--text-light)">
                Data Login
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Username <span style="color:red">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span style="color:red">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Password <span style="color:red">*</span></label>
                    <input type="password" name="password" class="form-control" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password <span style="color:red">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required/>
                </div>
            </div>

            <hr style="border:none;border-top:1px solid var(--topbar-border);margin:1.25rem 0"/>
            <div style="font-weight:700;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:1rem;color:var(--text-light)">
                Data Pribadi
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                    <input type="text" name="nama_murid" class="form-control" value="{{ old('nama_murid') }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Orang Tua</label>
                    <input type="text" name="nama_orang_tua" class="form-control" value="{{ old('nama_orang_tua') }}"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="nomor_hp" class="form-control" value="{{ old('nomor_hp') }}"/>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" style="height:auto">{{ old('alamat') }}</textarea>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
                <button type="submit" class="btn btn-primary btn-sm">
                    Simpan
                </button>
                <a href="{{ route('admin.murids.index') }}" class="btn btn-outline btn-sm">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection