@extends('layouts.app')
@section('title', 'Input Laporan KBM')
@section('page-title', 'Input Laporan KBM')

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
        <h2>Input Laporan KBM Harian</h2>
        <div class="breadcrumb">Guru / Laporan KBM / <span>Input</span></div>
    </div>
    <a href="{{ route('guru.progres.index') }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Info Jadwal --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px;background:#f8faff">
        <div style="display:flex;gap:24px;flex-wrap:wrap">
            <div>
                <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;font-weight:600">Murid</div>
                <div style="font-weight:700;margin-top:2px">{{ $jadwal->spp->murid->nama_murid ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;font-weight:600">Program</div>
                <div style="font-weight:700;margin-top:2px">{{ $jadwal->spp->programKursus->nama_program ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;font-weight:600">Tanggal</div>
                <div style="font-weight:700;margin-top:2px">{{ $jadwal->tanggal->translatedFormat('l, d M Y') }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;font-weight:600">Jam</div>
                <div style="font-weight:700;margin-top:2px">{{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }}</div>
            </div>
            <div>
                <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;font-weight:600">Kehadiran</div>
                <div style="margin-top:2px">
                    <span class="badge {{ $jadwal->status_kehadiran_murid === 'Hadir' ? 'badge-success' : 'badge-danger' }}">
                        Murid: {{ $jadwal->status_kehadiran_murid }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-book-open" style="color:var(--primary-blue);margin-right:8px"></i>Form Laporan KBM</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('guru.progres.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_jadwal" value="{{ $jadwal->id_jadwal }}">

            <div class="form-group">
                <label class="form-label">Materi yang Diajarkan <span style="color:red">*</span></label>
                <input type="text" name="materi_diajarkan" class="form-control"
                       value="{{ old('materi_diajarkan') }}"
                       placeholder="Contoh: Tangga nada C mayor, teknik fingering dasar" required/>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Perkembangan <span style="color:red">*</span></label>
                <textarea name="catatan_perkembangan" class="form-control" rows="5"
                          placeholder="Deskripsikan progres dan perkembangan murid pada sesi ini..."
                          required>{{ old('catatan_perkembangan') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Dokumentasi (Opsional)</label>
                <input type="file" name="url_foto" class="form-control" accept="image/jpg,image/jpeg,image/png"/>
                <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">JPG/PNG, maks 5MB</div>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Laporan KBM
                </button>
                <a href="{{ route('guru.progres.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
