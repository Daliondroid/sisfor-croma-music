@extends('layouts.app')
@section('title', 'Edit Kelas')
@section('page-title', 'Edit Kelas')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Edit Kelas</h2><div class="breadcrumb">Admin / Kelas / <span>Edit</span></div></div>
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:560px">
    <div class="card-header"><h3>{{ $kelas->nama_kelas }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.kelas.update', $kelas) }}">
            @csrf @method('PUT')
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
            <div class="form-group">
                <label class="form-label">Program Kursus <span style="color:red">*</span></label>
                <select name="id_program" class="form-control" required>
                    @foreach($programs as $p)
                        <option value="{{ $p->id_program }}"
                            {{ old('id_program', $kelas->id_program)==$p->id_program?'selected':'' }}>
                            {{ $p->nama_program }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Kelas <span style="color:red">*</span></label>
                <input type="text" name="nama_kelas" class="form-control"
                       value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required/>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Tipe Les <span style="color:red">*</span></label>
                    <select name="tipe_les" class="form-control" required>
                        <option value="onsite"       {{ old('tipe_les', $kelas->tipe_les)=='onsite'?'selected':'' }}>Onsite</option>
                        <option value="home_private" {{ old('tipe_les', $kelas->tipe_les)=='home_private'?'selected':'' }}>Home Private</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasitas (orang) <span style="color:red">*</span></label>
                    <input type="number" name="kapasitas" class="form-control"
                           value="{{ old('kapasitas', $kelas->kapasitas) }}" min="1" max="50" required/>
                </div>
            </div>
            <div style="display:flex;gap:12px">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection