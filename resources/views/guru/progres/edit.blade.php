@extends('layouts.app')
@section('title', 'Edit Laporan KBM')
@section('page-title', 'Edit Laporan KBM')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}"            class="nav-item {{ request()->routeIs('guru.dashboard')       ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('guru.jadwal.index') }}"         class="nav-item {{ request()->routeIs('guru.jadwal*')         ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('guru.absensi.index') }}"        class="nav-item {{ request()->routeIs('guru.absensi*')        ? 'active' : '' }}"><i class="fa-solid fa-chart-bar"></i> Data Absensi</a>
    <a href="{{ route('guru.presensi.index') }}"       class="nav-item {{ request()->routeIs('guru.presensi*')       ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Input Presensi</a>
    <a href="{{ route('guru.progres.index') }}"        class="nav-item {{ request()->routeIs('guru.progres*')        ? 'active' : '' }}"><i class="fa-solid fa-book-open"></i> Laporan KBM</a>
    <a href="{{ route('guru.monthly-report.index') }}" class="nav-item {{ request()->routeIs('guru.monthly-report*') ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i> Laporan Bulanan</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Edit Laporan KBM</h2>
        <div class="breadcrumb">Guru / Laporan KBM / <span>Edit</span></div>
    </div>
    <a href="{{ route('guru.progres.index') }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-pen" style="color:var(--primary-blue);margin-right:8px"></i>
            Edit — {{ $jadwal->spp->murid->nama_murid ?? '-' }}
            · {{ $jadwal->tanggal->translatedFormat('d M Y') }}
        </h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
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
                <div style="margin-bottom:10px">
                    <img src="{{ asset('storage/' . $progresMurid->url_foto) }}"
                         style="max-width:200px;border-radius:8px;border:1px solid var(--topbar-border)">
                </div>
            </div>
            @endif

            <div class="form-group">
                <label class="form-label">Ganti Foto (Opsional)</label>
                <input type="file" name="url_foto" class="form-control" accept="image/jpg,image/jpeg,image/png"/>
                <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Kosongkan jika tidak ingin mengganti foto</div>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('guru.progres.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
