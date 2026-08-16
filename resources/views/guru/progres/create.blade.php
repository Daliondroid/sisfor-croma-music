@extends('layouts.app')
@section('title', 'Input Laporan KBM')
@section('page-title', 'Input Laporan KBM')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Input Laporan KBM Harian</h2>
        <div class="breadcrumb">Guru / Laporan KBM / <span>Input</span></div>
    </div>
</div>

{{-- Info Jadwal --}}
<div class="card" style="margin-bottom:1.5rem">
    <div class="card-body" style="padding:1rem 1.5rem;background:var(--bg-light)">
        <div style="display:flex;gap:2rem;flex-wrap:wrap">
            <div>
                <div style="font-size:.68rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700">Murid</div>
                <div style="font-weight:700;margin-top:0.25rem;color:var(--text-dark)">{{ $jadwal->spp->murid->nama_murid ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:.68rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700">Program</div>
                <div style="font-weight:700;margin-top:0.25rem;color:var(--text-dark)">{{ $jadwal->spp->programKursus->nama_program ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:.68rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700">Tanggal</div>
                <div style="font-weight:700;margin-top:0.25rem;color:var(--text-dark)">{{ $jadwal->tanggal->translatedFormat('l, d M Y') }}</div>
            </div>
            <div>
                <div style="font-size:.68rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700">Jam</div>
                <div style="font-weight:700;margin-top:0.25rem;color:var(--text-dark);font-variant-numeric:tabular-nums">{{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }}</div>
            </div>
            <div>
                <div style="font-size:.68rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:700">Kehadiran</div>
                <div style="margin-top:0.25rem">
                    <span class="badge {{ $jadwal->status_kehadiran_murid === 'Hadir' ? 'badge-success' : 'badge-danger' }}">
                        MURID: {{ strtoupper($jadwal->status_kehadiran_murid) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Form Laporan KBM</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
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
                <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">JPG/PNG, maks 5MB</div>
            </div>

            <div style="display:flex;gap:0.5rem;margin-top:0.75rem">
                <button type="submit" class="btn btn-primary btn-sm">
                    Simpan Laporan KBM
                </button>
                <a href="{{ route('guru.progres.index') }}" class="btn btn-outline btn-sm">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
