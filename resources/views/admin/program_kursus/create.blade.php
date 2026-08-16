@extends('layouts.app')
@section('title', 'Tambah Program Kursus')
@section('page-title', 'Tambah Program Kursus')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <a href="{{ route('admin.program-kursus.index') }}" class="crumb-root">Program Kursus</a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Tambah Program</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Tambah Program Kursus</h2></div>
</div>
<div class="card" style="max-width:35rem">
    <div class="card-header"><h3>Detail Program</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.program-kursus.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
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
                <label class="form-label">Biaya Kursus (Rp) <span style="color:red">*</span></label>
                <input type="number" name="biaya_kursus" class="form-control" min="0"
                       value="{{ old('biaya_kursus') }}" placeholder="Contoh: 350000" required/>
                <small class="text-muted" style="font-size:0.75rem;color:var(--text-light)">Masukkan angka saja tanpa titik atau koma.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" style="height:auto"
                          placeholder="Deskripsi singkat program...">{{ old('deskripsi') }}</textarea>
            </div>

            <div style="display:flex;gap:0.75rem">
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                <a href="{{ route('admin.program-kursus.index') }}" class="btn btn-outline btn-sm">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
