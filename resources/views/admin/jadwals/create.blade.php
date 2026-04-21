@extends('layouts.app')
@section('title', 'Buat Jadwal')
@section('page-title', 'Buat Jadwal')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Buat Jadwal KBM</h2><div class="breadcrumb">Admin / Jadwal / <span>Buat</span></div></div>
    <a href="{{ route('admin.jadwals.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:680px">
    <div class="card-header"><h3>Detail Jadwal</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.jadwals.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Murid <span style="color:red">*</span></label>
                    <select name="id_murid" class="form-control" required>
                        <option value="">Pilih murid</option>
                        @foreach($murids as $m)
                            <option value="{{ $m->id_murid }}" {{ old('id_murid')==$m->id_murid?'selected':'' }}>{{ $m->nama_murid }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Guru <span style="color:red">*</span></label>
                    <select name="id_guru" class="form-control" required>
                        <option value="">Pilih guru</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id_guru }}" {{ old('id_guru')==$g->id_guru?'selected':'' }}>{{ $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas <span style="color:red">*</span></label>
                    <select name="id_kelas" class="form-control" required>
                        <option value="">Pilih kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}" {{ old('id_kelas')==$k->id_kelas?'selected':'' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Hari <span style="color:red">*</span></label>
                    <select name="hari" class="form-control" required>
                        <option value="">Pilih hari</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                            <option value="{{ $h }}" {{ old('hari')==$h?'selected':'' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Mulai <span style="color:red">*</span></label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai <span style="color:red">*</span></label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi') }}" placeholder="Studio / Alamat rumah"/>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Berlaku <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_berlaku" class="form-control" value="{{ old('tanggal_berlaku', now()->format('Y-m-d')) }}" required/>
                </div>
            </div>
            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                <a href="{{ route('admin.jadwals.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection