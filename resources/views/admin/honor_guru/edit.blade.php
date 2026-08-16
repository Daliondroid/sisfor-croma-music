@extends('layouts.app')
@section('title', 'Kelola Gaji Guru')
@section('page-title', 'Kelola Gaji Guru')

@section('breadcrumb')
    <span class="crumb-root">Gaji & Laporan</span>
    <span class="crumb-sep">/</span>
    <a href="{{ route('admin.honor-guru.index') }}" class="crumb-root">Manajemen Gaji Guru</a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Kelola Gaji HG-{{ str_pad($honorGuru->id_honor, 4, '0', STR_PAD_LEFT) }}</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')

<div class="page-header">
    <div>
        <h2>Kelola Gaji Guru</h2>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div style="display:grid;grid-template-columns:18.75rem 1fr;gap:1.25rem;align-items:start">

    {{-- ── Kartu Info Guru ── --}}
    <div class="card">
        <div class="card-header">
            <h3>Info Guru</h3>
        </div>
        <div class="card-body" style="padding:1.25rem">

            {{-- Avatar --}}
            <div style="display:flex;align-items:center;gap:0.875rem;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--topbar-border)">
                <div style="width:2.75rem;height:2.75rem;border-radius:0.25rem;background:var(--primary-navy);
                            display:flex;align-items:center;justify-content:center;
                            font-size:1.1rem;font-weight:700;color:#fff;flex-shrink:0">
                    {{ strtoupper(substr($honorGuru->guru->nama_guru, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:700;font-size:.9rem;color:var(--text-dark)">{{ $honorGuru->guru->nama_guru }}</div>
                    <div style="font-size:.75rem;color:var(--text-light);margin-top:0.125rem">Guru Pengajar</div>
                </div>
            </div>

            {{-- Detail rows --}}
            <div style="display:flex;flex-direction:column;gap:0.75rem;font-size:.83rem">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--text-light)">Jumlah Sesi</span>
                    <strong style="font-variant-numeric:tabular-nums">{{ $honorGuru->jumlah_pertemuan }} Pertemuan</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--text-light)">Nominal Saat Ini</span>
                    <strong style="font-variant-numeric:tabular-nums">Rp {{ number_format($honorGuru->jumlah_honor, 0, ',', '.') }}</strong>
                </div>
                @if($honorGuru->tanggal_pencairan)
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--text-light)">Tgl Pencairan</span>
                    <strong style="font-variant-numeric:tabular-nums">{{ \Carbon\Carbon::parse($honorGuru->tanggal_pencairan)->format('d M Y') }}</strong>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--text-light)">Status</span>
                    @if($honorGuru->status_bayar === 'Lunas')
                        <span class="badge badge-success">LUNAS</span>
                    @elseif($honorGuru->status_bayar === 'Siap Dibayar')
                        <span class="badge badge-info">SIAP DIBAYAR</span>
                    @else
                        <span class="badge badge-warning">BELUM LUNAS</span>
                    @endif
                </div>
            </div>

            {{-- Bukti lama --}}
            @if($honorGuru->file_bukti_transfer)
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--topbar-border)">
                <div style="font-size:.7rem;color:var(--text-light);text-transform:uppercase;letter-spacing:0.04em;font-weight:600;margin-bottom:0.5rem">
                    Bukti Transfer Tersimpan
                </div>
                <a href="{{ route('admin.honor-guru.bukti', $honorGuru) }}" target="_blank"
                   class="btn btn-outline btn-sm" style="width:100%;justify-content:center">
                    Lihat Bukti
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Form Edit ── --}}
    <div class="card">
        <div class="card-header">
            <h3>Edit Data Pembayaran</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.honor-guru.update', $honorGuru->id_honor) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-grid" style="margin-bottom:1rem">
                    {{-- Nominal --}}
                    <div class="form-group">
                        <label class="form-label">Nominal Gaji (Rp) <span style="color:red">*</span></label>
                        <div style="position:relative">
                            <span style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);
                                         color:var(--text-light);font-size:.85rem;font-weight:600">Rp</span>
                            <input type="number" name="jumlah_honor"
                                   value="{{ old('jumlah_honor', $honorGuru->jumlah_honor) }}"
                                   class="form-control" required
                                   style="padding-left:2.25rem"/>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label">Status Pembayaran <span style="color:red">*</span></label>
                        <select name="status_bayar" class="form-control" required>
                            @foreach(['Belum Lunas','Siap Dibayar','Lunas'] as $st)
                                <option value="{{ $st }}" {{ old('status_bayar', $honorGuru->status_bayar) == $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal pencairan --}}
                    <div class="form-group">
                        <label class="form-label">Tanggal Pencairan</label>
                        <input type="date" name="tanggal_pencairan" class="form-control"
                               value="{{ old('tanggal_pencairan', $honorGuru->tanggal_pencairan?->format('Y-m-d')) }}"/>
                    </div>

                    {{-- Upload bukti --}}
                    <div class="form-group">
                        <label class="form-label">Upload Bukti Transfer <span style="color:var(--text-light);font-weight:400">(opsional)</span></label>
                        <input type="file" name="file_bukti_transfer" class="form-control"
                               accept=".jpg,.jpeg,.png,.pdf"/>
                        <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">
                            Format: JPG, PNG, PDF. Maks 5MB.
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="form-group" style="margin-bottom:1.25rem">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3" style="height:auto"
                              placeholder="Catatan tambahan mengenai pembayaran...">{{ old('catatan', $honorGuru->catatan) }}</textarea>
                </div>

                <div style="display:flex;gap:0.625rem">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.honor-guru.index') }}" class="btn btn-outline btn-sm">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection