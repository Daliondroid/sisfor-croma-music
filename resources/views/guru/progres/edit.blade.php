@extends('layouts.app')
@section('title', 'Edit Laporan KBM')
@section('page-title', 'Edit Laporan KBM')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Edit Laporan KBM</h2>
        <div class="breadcrumb">Guru / Laporan KBM / <span>Edit</span></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>
            Edit &mdash; {{ $jadwal->spp->murid->nama_murid ?? '-' }}
            · {{ $jadwal->tanggal->translatedFormat('d M Y') }}
        </h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('guru.progres.update', $progresMurid->id_progres) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Materi yang Diajarkan <span style="color:red">*</span></label>
                <input type="text" name="materi_diajarkan" class="form-control"
                       value="{{ old('materi_diajarkan', $progresMurid->materi_diajarkan) }}" required/>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Perkembangan <span style="color:red">*</span></label>
                <textarea name="catatan_perkembangan" class="form-control" rows="5"
                          required>{{ old('catatan_perkembangan', $progresMurid->catatan_perkembangan) }}</textarea>
            </div>

            @if($progresMurid->url_foto)
            <div class="form-group">
                <label class="form-label">Foto Saat Ini</label>
                <div style="margin-bottom:0.5rem">
                    <img src="{{ asset('storage/' . $progresMurid->url_foto) }}"
                         style="max-width:12.5rem;border-radius:0.25rem;border:1px solid var(--topbar-border)">
                </div>
            </div>
            @endif

            <div class="form-group">
                <label class="form-label">Ganti Foto (Opsional)</label>
                <input type="file" name="url_foto" class="form-control" accept="image/jpg,image/jpeg,image/png"/>
                <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">Kosongkan jika tidak ingin mengganti foto</div>
            </div>

            <div style="display:flex;gap:0.5rem;margin-top:0.75rem">
                <button type="submit" class="btn btn-primary btn-sm">
                    Simpan Perubahan
                </button>
                <a href="{{ route('guru.progres.index') }}" class="btn btn-outline btn-sm">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
