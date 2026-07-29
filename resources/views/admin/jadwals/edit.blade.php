@extends('layouts.app')
@section('title', 'Edit Jadwal KBM')
@section('page-title', 'Edit Jadwal')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2>Edit Jadwal KBM</h2>
        <div class="breadcrumb">Admin / Jadwal / <span>Edit</span></div>
    </div>
    <a href="{{ route('admin.jadwals.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
    <div class="card" style="background-color: #fef2f2; border: 1px solid #fca5a5; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.375rem;">
        <ul style="margin: 0; padding-left: 1.5rem; color: #991b1b; font-size: 0.9rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="padding: 2rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); background-color: #fff;">
    <form action="{{ route('admin.jadwals.update', $jadwal->id_jadwal) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(18.75rem, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--text-dark);">
                    Murid & Program Kursus
                </label>
                <select class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; background-color: #f1f5f9; cursor: not-allowed;" disabled>
                    @foreach($spps as $spp)
                        <option value="{{ $spp->id_spp }}" {{ $jadwal->id_spp == $spp->id_spp ? 'selected' : '' }}>
                            {{ $spp->murid->nama_murid ?? '-' }} — {{ $spp->programKursus->nama_program ?? '-' }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="id_spp" value="{{ $jadwal->id_spp }}">
            </div>

            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--text-dark);">
                    Guru / Instruktur Pengajar
                </label>
                <select name="id_guru" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #cbd5e1;" required>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id_guru }}" {{ old('id_guru', $jadwal->id_guru) == $guru->id_guru ? 'selected' : '' }}>
                            {{ $guru->nama_guru }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(12.5rem, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--text-dark);">
                    Tanggal Pelaksanaan
                </label>
                <input type="date" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::parse($jadwal->tanggal)->format('Y-m-d')) }}" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #cbd5e1;" required>
            </div>

            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--text-dark);">
                    Jam Mulai Belajar
                </label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal->jam_mulai, 0, 5)) }}" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #cbd5e1;" required>
            </div>

            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--text-dark);">
                    Jam Selesai Belajar
                </label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal->jam_selesai, 0, 5)) }}" class="form-control" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #cbd5e1;" required>
            </div>

            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; color: var(--text-dark);">
                    Sesi Pertemuan
                </label>
                <input type="text" class="form-control" value="Sesi Ke-{{ $jadwal->sesi_ke }}" style="width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; background-color: #f1f5f9; cursor: not-allowed; font-weight: 600; color: #475569;" disabled>
                <input type="hidden" name="sesi_ke" value="{{ $jadwal->sesi_ke }}">
            </div>

        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
            <a href="{{ route('admin.jadwals.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1.5rem;">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection