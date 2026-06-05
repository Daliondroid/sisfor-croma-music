@extends('layouts.app')
@section('title', 'Kelola Gaji Guru')
@section('page-title', 'Kelola Gaji Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <h2>Kelola Gaji Guru</h2>
        <div class="breadcrumb">Admin / Gaji Guru / <span>Kelola Data</span></div>
    </div>
</div>

<div class="card" style="padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <form action="{{ route('admin.honor-guru.update', $honorGuru->id_honor) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
            
            {{-- Informasi Dasar --}}
            <div>
                <h4 style="margin-bottom: 1rem;">Informasi Honor</h4>
                <div style="background: #f8fafc; padding: 1rem; border-radius: 6px;">
                    <p><strong>Guru:</strong> {{ $honorGuru->guru->nama_guru }}</p>
                    <p><strong>Jumlah Sesi:</strong> {{ $honorGuru->jumlah_pertemuan }} Pertemuan</p>
                    <p><strong>Status:</strong> {{ $honorGuru->status_bayar }}</p>
                </div>
            </div>

            {{-- Form Input --}}
            <div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label>Nominal Gaji (Rp)</label>
                    <input type="number" name="jumlah_honor" value="{{ old('jumlah_honor', $honorGuru->jumlah_honor) }}" class="form-control" required style="width: 100%; padding: 0.5rem;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label>Status Pembayaran</label>
                    <select name="status_bayar" class="form-control" required style="width: 100%; padding: 0.5rem;">
                        <option value="Belum Lunas" {{ $honorGuru->status_bayar == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="Siap Dibayar" {{ $honorGuru->status_bayar == 'Siap Dibayar' ? 'selected' : '' }}>Siap Dibayar</option>
                        <option value="Lunas" {{ $honorGuru->status_bayar == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label>Bukti Transfer (Opsional)</label>
            <input type="file" name="file_bukti_transfer" class="form-control" style="width: 100%; padding: 0.5rem;">
            @if($honorGuru->file_bukti_transfer)
                <small><a href="{{ asset('storage/'.$honorGuru->file_bukti_transfer) }}" target="_blank">Lihat bukti saat ini</a></small>
            @endif
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control" style="width: 100%; padding: 0.5rem;">{{ old('catatan', $honorGuru->catatan) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('admin.honor-guru.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection