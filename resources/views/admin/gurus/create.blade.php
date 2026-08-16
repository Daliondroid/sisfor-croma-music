@extends('layouts.app')
@section('title', 'Tambah Guru')
@section('page-title', 'Tambah Guru')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <a href="{{ route('admin.gurus.index') }}" class="crumb-root">Data Guru</a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Tambah Guru</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Tambah Guru</h2></div>
</div>
<div class="card" style="max-width:42.5rem">
    <div class="card-header"><h3>Data Akun & Profil Guru</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gurus.store') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
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
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                    <input type="text" name="nama_guru" class="form-control" value="{{ old('nama_guru') }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="nomor_hp" class="form-control" 
                        value="{{ old('nomor_hp') }}"
                        placeholder="08xx-xxxx-xxxx" 
                        maxlength="15" 
                        oninput="formatPhoneNumber(this)">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Spesialisasi Instrumen</label>
                <input type="text" name="spesialisasi" class="form-control"
                    value="{{ old('spesialisasi') }}"
                    placeholder="Piano, Gitar, Vokal (pisahkan dengan koma)"/>
                <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">
                    Pisahkan beberapa instrumen dengan koma. Contoh: Piano, Gitar Akustik, Vokal
                </div>
            </div>
            <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                <a href="{{ route('admin.gurus.index') }}" class="btn btn-outline btn-sm">Kembali</a>
            </div>
        </form>
    </div>
</div>

<script>
    function formatPhoneNumber(input) {
        let numbers = input.value.replace(/\D/g, '');
        let formatted = '';
        for (let i = 0; i < numbers.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += '-';
            }
            formatted += numbers[i];
        }
        input.value = formatted;
    }
</script>
@endsection