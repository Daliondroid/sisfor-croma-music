@extends('layouts.app')
@section('title', 'Tambah Program Kursus')
@section('page-title', 'Tambah Program Kursus')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Tambah Program Kursus</h2><div class="breadcrumb">Admin / Program Kursus / <span>Tambah</span></div></div>
    <a href="{{ route('admin.program-kursus.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:560px">
    <div class="card-header"><h3>Detail Program</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.program-kursus.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
            <div class="form-group">
                <label class="form-label">Nama Program <span style="color:red">*</span></label>
                <input type="text" name="nama_program" class="form-control"
                       value="{{ old('nama_program') }}" placeholder="Contoh: Piano, Gitar Akustik, Vokal Pop" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Les <span style="color:red">*</span></label>
                <select name="tipe_les" class="form-control" required>
                    <option value="keduanya"    {{ old('tipe_les','keduanya')=='keduanya'?'selected':'' }}>Onsite & Home Private</option>
                    <option value="onsite"      {{ old('tipe_les')=='onsite'?'selected':'' }}>Onsite saja</option>
                    <option value="home_private"{{ old('tipe_les')=='home_private'?'selected':'' }}>Home Private saja</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"
                          placeholder="Deskripsi singkat program...">{{ old('deskripsi') }}</textarea>
            </div>
            <div style="display:flex;gap:12px">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                <a href="{{ route('admin.program-kursus.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
