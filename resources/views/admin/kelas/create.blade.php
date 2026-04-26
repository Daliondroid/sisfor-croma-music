@extends('layouts.app')
@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Tambah Kelas</h2><div class="breadcrumb">Admin / Kelas / <span>Tambah</span></div></div>
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:560px">
    <div class="card-header"><h3>Detail Kelas</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.kelas.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
            <div class="form-group">
                <label class="form-label">Program Kursus <span style="color:red">*</span></label>
                <select name="id_program" class="form-control" required>
                    <option value="">Pilih program</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id_program }}" {{ old('id_program')==$p->id_program?'selected':'' }}>
                            {{ $p->nama_program }}
                        </option>
                    @endforeach
                </select>
                <div style="margin-top:6px;font-size:.72rem;color:var(--text-light)">
                    Belum ada program? <a href="{{ route('admin.program-kursus.create') }}" style="color:var(--primary-blue)">Buat program baru</a>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Kelas <span style="color:red">*</span></label>
                <input type="text" name="nama_kelas" class="form-control" value="{{ old('nama_kelas') }}"
                       placeholder="Contoh: Gitar Beginner A, Piano Intermediate" required/>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Tipe Les <span style="color:red">*</span></label>
                    <select name="tipe_les" class="form-control" required>
                        <option value="">Pilih tipe</option>
                        <option value="onsite"       {{ old('tipe_les')=='onsite'?'selected':'' }}>Onsite</option>
                        <option value="home_private" {{ old('tipe_les')=='home_private'?'selected':'' }}>Home Private</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasitas (orang) <span style="color:red">*</span></label>
                    <input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', 1) }}" min="1" max="50" required/>
                </div>
            </div>
            <div style="display:flex;gap:12px">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection