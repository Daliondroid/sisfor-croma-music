@extends('layouts.app')
@section('title', 'Edit Guru')
@section('page-title', 'Edit Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Edit Data Guru</h2><div class="breadcrumb">Admin / Guru / <span>Edit</span></div></div>
    <a href="{{ route('admin.gurus.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:680px">
    <div class="card-header"><h3>{{ $guru->nama_guru }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gurus.update', $guru) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                    <input type="text" name="nama_guru" class="form-control" value="{{ old('nama_guru', $guru->nama_guru) }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span style="color:red">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $guru->user->email) }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="nomor_hp" class="form-control" value="{{ old('nomor_hp', $guru->nomor_hp) }}"/>
                </div>
                {{-- <div class="form-group">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto_profil" class="form-control" accept="image/*"/>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Format: JPG, PNG. Maks: 2MB</div>
                </div> --}}
                <div class="form-group">
                    <label class="form-label">Password Baru <span style="font-weight:400;color:var(--text-light)">(opsional)</span></label>
                    <input type="password" name="password" class="form-control"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Spesialisasi Instrumen</label>
                    <input type="text" name="spesialisasi" class="form-control"
                        value="{{ old('spesialisasi', $guru->spesialisasis->pluck('nama_spesialisasi')->implode(', ')) }}"
                        placeholder="Piano, Gitar, Vokal (pisahkan dengan koma)"/>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">
                        Pisahkan beberapa instrumen dengan koma.
                    </div>
                </div>
                    @error('spesialisasi_ids') <small style="color:red; margin-top:4px; display:block;">{{ $message }}</small> @enderror
                </div>
            </div>
            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.gurus.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection