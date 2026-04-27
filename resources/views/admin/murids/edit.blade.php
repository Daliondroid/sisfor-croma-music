@extends('layouts.app')
@section('title', 'Edit Murid')
@section('page-title', 'Edit Murid')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Edit Data Murid</h2>
        <div class="breadcrumb">Admin / Murid / <span>Edit</span></div>
    </div>
    <a href="{{ route('admin.murids.index') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:780px">
    <div class="card-header"><h3>{{ $murid->nama_murid }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.murids.update', $murid) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif

            <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">
                <i class="fa-solid fa-lock" style="margin-right:6px"></i>Data Login
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Email <span style="color:red">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $murid->user->email) }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="{{ $murid->user->username }}" disabled style="background:#f8f9fa"/>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Username tidak dapat diubah</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru <span style="font-weight:400;color:var(--text-light)">(kosongkan jika tidak ingin ganti)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto_profil" class="form-control" accept="image/*"/>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Format: JPG, PNG. Maks: 2MB</div>
                </div>
            </div>

            <hr style="border:none;border-top:1px solid #f0f0f0;margin:20px 0"/>
            <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">
                <i class="fa-solid fa-user" style="margin-right:6px"></i>Data Pribadi
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                    <input type="text" name="nama_murid" class="form-control" value="{{ old('nama_murid', $murid->nama_murid) }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $murid->tanggal_lahir?->format('Y-m-d')) }}"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Orang Tua</label>
                    <input type="text" name="nama_orang_tua" class="form-control" value="{{ old('nama_orang_tua', $murid->nama_orang_tua) }}"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="nomor_hp" class="form-control" value="{{ old('nomor_hp', $murid->nomor_hp) }}"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Les <span style="color:red">*</span></label>
                    <select name="tipe_les" class="form-control" required>
                        <option value="onsite"       {{ old('tipe_les', $murid->tipe_les)=='onsite'?'selected':'' }}>Onsite</option>
                        <option value="home_private" {{ old('tipe_les', $murid->tipe_les)=='home_private'?'selected':'' }}>Home Private</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $murid->alamat) }}</textarea>
            </div>
            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.murids.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
