@extends('layouts.app')
@section('title', 'Tambah Guru')
@section('page-title', 'Tambah Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Tambah Guru</h2><div class="breadcrumb">Admin / Guru / <span>Tambah</span></div></div>
    <a href="{{ route('admin.gurus.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:42.5rem">
    <div class="card-header"><h3>Data Akun & Profil Guru</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.gurus.store') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
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
                {{-- <div class="form-group">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto_profil" class="form-control" accept="image/*"/>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">Format: JPG, PNG. Maks: 2MB</div>
                </div> --}}
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
            <div style="display:flex;gap:1rem;margin-top:0.5rem">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                <a href="{{ route('admin.gurus.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    function formatPhoneNumber(input) {
        // 1. Hapus semua karakter yang bukan angka
        let numbers = input.value.replace(/\D/g, '');
        
        // 2. Format grup per 4 angka (contoh: 0812-3456-7890)
        let formatted = '';
        for (let i = 0; i < numbers.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += '-';
            }
            formatted += numbers[i];
        }
        
        // 3. Tampilkan kembali ke input
        input.value = formatted;
    }
</script>
@endsection