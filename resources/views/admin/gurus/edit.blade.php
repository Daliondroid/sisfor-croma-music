@extends('layouts.app')
@section('title', 'Edit Guru')
@section('page-title', 'Edit Guru')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <a href="{{ route('admin.gurus.index') }}" class="crumb-root">Data Guru</a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Edit Guru</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Edit Data Guru</h2></div>
</div>
<div class="card" style="max-width:42.5rem">
    <div class="card-header"><h3>{{ $guru->nama_guru }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gurus.update', $guru) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
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
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">
                        Pisahkan beberapa instrumen dengan koma.
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
                <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                <a href="{{ route('admin.gurus.index') }}" class="btn btn-outline btn-sm">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection