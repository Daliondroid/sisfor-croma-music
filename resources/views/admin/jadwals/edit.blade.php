@extends('layouts.app')
@section('title', 'Edit Jadwal KBM')
@section('page-title', 'Edit Jadwal')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <a href="{{ route('admin.jadwals.index') }}" class="crumb-root">Jadwal KBM</a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Edit Jadwal</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Edit Jadwal KBM</h2>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="padding: 1.25rem 2.5rem;">
    <form action="{{ route('admin.jadwals.update', $jadwal->id_jadwal) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(18.75rem, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
            
            <div class="form-group">
                <label class="form-label">
                    Murid & Program Kursus
                </label>
                <select class="form-control" style="background-color: var(--bg-light); cursor: not-allowed;" disabled>
                    @foreach($spps as $spp)
                        <option value="{{ $spp->id_spp }}" {{ $jadwal->id_spp == $spp->id_spp ? 'selected' : '' }}>
                            {{ $spp->murid->nama_murid ?? '-' }} — {{ $spp->programKursus->nama_program ?? '-' }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="id_spp" value="{{ $jadwal->id_spp }}">
            </div>

            <div class="form-group">
                <label class="form-label">
                    Guru / Instruktur Pengajar
                </label>
                <select name="id_guru" class="form-control" required>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id_guru }}" {{ old('id_guru', $jadwal->id_guru) == $guru->id_guru ? 'selected' : '' }}>
                            {{ $guru->nama_guru }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(12.5rem, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
            
            <div class="form-group">
                <label class="form-label">
                    Tanggal Pelaksanaan
                </label>
                <input type="date" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::parse($jadwal->tanggal)->format('Y-m-d')) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Jam Mulai Belajar
                </label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal->jam_mulai, 0, 5)) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Jam Selesai Belajar
                </label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal->jam_selesai, 0, 5)) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Sesi Pertemuan
                </label>
                <input type="text" class="form-control" value="Sesi Ke-{{ $jadwal->sesi_ke }}" style="background-color: var(--bg-light); cursor: not-allowed; font-weight: 600; color: #475569;" disabled>
                <input type="hidden" name="sesi_ke" value="{{ $jadwal->sesi_ke }}">
            </div>

        </div>

        <div style="display: flex; gap: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
            <button type="submit" class="btn btn-primary btn-sm">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.jadwals.index') }}" class="btn btn-outline btn-sm">
                Kembali
            </a>
        </div>

    </form>
</div>
@endsection