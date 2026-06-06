@extends('layouts.app')
@section('title', 'Kelola Gaji Guru')
@section('page-title', 'Kelola Gaji Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')

<div class="page-header">
    <div>
        <h2>Kelola Gaji Guru</h2>
        <div class="breadcrumb">Admin / <a href="{{ route('admin.honor-guru.index') }}" style="color:var(--primary-blue)">Gaji Guru</a> / <span>Kelola Data</span></div>
    </div>
    <a href="{{ route('admin.honor-guru.index') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
@endif

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">

    {{-- ── Kartu Info Guru ── --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-chalkboard-user" style="color:var(--primary-blue);margin-right:8px"></i>Info Guru</h3>
        </div>
        <div class="card-body" style="padding:20px">

            {{-- Avatar --}}
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;padding-bottom:16px;border-bottom:1px solid var(--topbar-border)">
                <div style="width:48px;height:48px;border-radius:50%;background:var(--primary-blue);
                            display:flex;align-items:center;justify-content:center;
                            font-size:1.2rem;font-weight:700;color:#fff;flex-shrink:0">
                    {{ strtoupper(substr($honorGuru->guru->nama_guru, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:700;font-size:.95rem">{{ $honorGuru->guru->nama_guru }}</div>
                    <div style="font-size:.75rem;color:var(--text-light);margin-top:2px">Guru Pengajar</div>
                </div>
            </div>

            {{-- Detail rows --}}
            @php
                $statusColor = match($honorGuru->status_bayar) {
                    'Lunas'       => ['bg'=>'#dcfce7','c'=>'#15803d'],
                    'Siap Dibayar'=> ['bg'=>'#dbeafe','c'=>'#1d4ed8'],
                    default       => ['bg'=>'#fee2e2','c'=>'#b91c1c'],
                };
            @endphp

            <div style="display:flex;flex-direction:column;gap:12px;font-size:.83rem">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--text-light)"><i class="fa-solid fa-calendar-check" style="width:16px"></i> Jumlah Sesi</span>
                    <strong>{{ $honorGuru->jumlah_pertemuan }} Pertemuan</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--text-light)"><i class="fa-solid fa-coins" style="width:16px"></i> Nominal Saat Ini</span>
                    <strong>Rp {{ number_format($honorGuru->jumlah_honor, 0, ',', '.') }}</strong>
                </div>
                @if($honorGuru->tanggal_pencairan)
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--text-light)"><i class="fa-regular fa-calendar" style="width:16px"></i> Tgl Pencairan</span>
                    <strong>{{ \Carbon\Carbon::parse($honorGuru->tanggal_pencairan)->format('d M Y') }}</strong>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--text-light)"><i class="fa-solid fa-circle-dot" style="width:16px"></i> Status</span>
                    <span style="font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:20px;
                                 background:{{ $statusColor['bg'] }};color:{{ $statusColor['c'] }}">
                        {{ $honorGuru->status_bayar }}
                    </span>
                </div>
            </div>

            {{-- Bukti lama --}}
            @if($honorGuru->file_bukti_transfer)
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--topbar-border)">
                <div style="font-size:.7rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.6px;font-weight:600;margin-bottom:8px">
                    Bukti Transfer Tersimpan
                </div>
                <a href="{{ asset('storage/'.$honorGuru->file_bukti_transfer) }}" target="_blank"
                   class="btn btn-outline btn-sm" style="width:100%;justify-content:center">
                    <i class="fa-solid fa-eye"></i> Lihat Bukti
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Form Edit ── --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-pen-to-square" style="color:var(--primary-blue);margin-right:8px"></i>Edit Data Pembayaran</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.honor-guru.update', $honorGuru->id_honor) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-grid" style="margin-bottom:18px">
                    {{-- Nominal --}}
                    <div class="form-group">
                        <label class="form-label">Nominal Gaji (Rp) <span style="color:red">*</span></label>
                        <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);
                                         color:var(--text-light);font-size:.85rem;font-weight:600">Rp</span>
                            <input type="number" name="jumlah_honor"
                                   value="{{ old('jumlah_honor', $honorGuru->jumlah_honor) }}"
                                   class="form-control" required
                                   style="padding-left:36px"/>
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
                        <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">
                            Format: JPG, PNG, PDF. Maks 5MB.
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="form-group" style="margin-bottom:24px">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3"
                              placeholder="Catatan tambahan mengenai pembayaran...">{{ old('catatan', $honorGuru->catatan) }}</textarea>
                </div>

                <div style="display:flex;gap:10px">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.honor-guru.index') }}" class="btn btn-outline">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection