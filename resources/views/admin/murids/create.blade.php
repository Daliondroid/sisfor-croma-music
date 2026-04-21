@extends('layouts.app')
@section('title', 'Tambah Murid')
@section('page-title', 'Tambah Murid')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Tambah Murid</h2>
        <div class="breadcrumb">Admin / Murid / <span>Tambah</span></div>
    </div>
    <a href="{{ route('admin.murids.index') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:780px">
    <div class="card-header"><h3>Data Akun & Profil</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.murids.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">
                <i class="fa-solid fa-lock" style="margin-right:6px"></i>Data Login
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

            <hr style="border:none;border-top:1px solid #f0f0f0;margin:20px 0"/>
            <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">
                <i class="fa-solid fa-user" style="margin-right:6px"></i>Data Pribadi
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
                <div class="form-group">
                    <label class="form-label">Tipe Les <span style="color:red">*</span></label>
                    <select name="tipe_les" class="form-control" required>
                        <option value="">Pilih tipe les</option>
                        <option value="onsite" {{ old('tipe_les')=='onsite'?'selected':'' }}>Onsite</option>
                        <option value="home_private" {{ old('tipe_les')=='home_private'?'selected':'' }}>Home Private</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
                <a href="{{ route('admin.murids.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection