@extends('layouts.app')
@section('title', 'Edit Program Kursus')
@section('page-title', 'Edit Program Kursus')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Edit Program Kursus</h2><div class="breadcrumb">Admin / Program Kursus / <span>Edit</span></div></div>
    <a href="{{ route('admin.program-kursus.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:560px">
    <div class="card-header"><h3>{{ $program->nama_program }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.program-kursus.update', $program) }}">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
            <div class="form-group">
                <label class="form-label">Nama Program <span style="color:red">*</span></label>
                <input type="text" name="nama_program" class="form-control"
                       value="{{ old('nama_program', $program->nama_program) }}" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Les <span style="color:red">*</span></label>
                <select name="tipe_les" class="form-control" required>
                    <option value="keduanya"    {{ old('tipe_les',$program->tipe_les)=='keduanya'?'selected':'' }}>Onsite & Home Private</option>
                    <option value="onsite"      {{ old('tipe_les',$program->tipe_les)=='onsite'?'selected':'' }}>Onsite saja</option>
                    <option value="home_private"{{ old('tipe_les',$program->tipe_les)=='home_private'?'selected':'' }}>Home Private saja</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $program->deskripsi) }}</textarea>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.875rem">
                    <input type="hidden" name="is_active" value="0"/>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}/>
                    Program masih aktif
                </label>
            </div>
            <div style="display:flex;gap:12px">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.program-kursus.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection