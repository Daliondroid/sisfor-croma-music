@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('sidebar-menu') @include('murid.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Profil Saya</h2>
        <div class="breadcrumb">Murid / <span>Profil</span></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:18.75rem 1fr;gap:1.5rem;align-items:start">

    {{-- Kartu Info Kiri --}}
    <div class="card" style="text-align:center;padding:2rem 1.5rem">
        <div style="margin-bottom:1rem">
            @if($murid->user->foto_profil ?? false)
                <img src="{{ asset('storage/' . $murid->user->foto_profil) }}"
                     style="width:6rem;height:6rem;border-radius:50%;object-fit:cover;
                            border:3px solid var(--primary-navy);margin:0 auto;display:block">
            @else
                <div style="width:6rem;height:6rem;border-radius:50%;background:var(--primary-navy);
                            display:flex;align-items:center;justify-content:center;
                            font-size:2.2rem;font-weight:700;color:#fff;margin:0 auto">
                    {{ strtoupper(substr($murid->nama_murid, 0, 1)) }}
                </div>
            @endif
        </div>
        <div style="font-size:1.1rem;font-weight:700;margin-bottom:0.25rem;color:var(--text-dark)">{{ $murid->nama_murid }}</div>
        <div style="font-size:.8rem;color:var(--text-light);margin-bottom:1rem">{{ $murid->user->email }}</div>
        <span class="badge badge-success" style="font-size:.75rem;padding:0.25rem 0.75rem">
            MURID
        </span>

        <hr style="border:none;border-top:1px solid var(--topbar-border);margin:1.5rem 0">

        <div style="text-align:left">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;
                        letter-spacing:0.04em;font-weight:700;margin-bottom:0.75rem">Info Pribadi</div>
            <div style="display:flex;flex-direction:column;gap:0.75rem">
                @if($murid->tanggal_lahir)
                <div style="display:flex;justify-content:space-between;font-size:.82rem">
                    <span style="color:var(--text-light)">Tanggal Lahir</span>
                    <span style="font-weight:600;color:var(--text-dark);font-variant-numeric:tabular-nums">{{ $murid->tanggal_lahir->translatedFormat('d M Y') }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:.82rem">
                    <span style="color:var(--text-light)">Nomor HP</span>
                    <span style="font-weight:600;color:var(--text-dark);font-variant-numeric:tabular-nums">{{ $murid->nomor_hp ?? '-' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.82rem">
                    <span style="color:var(--text-light)">Orang Tua</span>
                    <span style="font-weight:600;color:var(--text-dark)">{{ $murid->nama_orang_tua ?? '-' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.82rem">
                    <span style="color:var(--text-light)">Status Akun</span>
                    <span class="badge {{ $murid->status_aktif ? 'badge-success' : 'badge-danger' }}">
                        {{ $murid->status_aktif ? 'AKTIF' : 'NON-AKTIF' }}
                    </span>
                </div>
            </div>
        </div>

        @if($murid->alamat)
        <hr style="border:none;border-top:1px solid var(--topbar-border);margin:1rem 0">
        <div style="text-align:left">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;
                        letter-spacing:0.04em;font-weight:700;margin-bottom:0.25rem">Alamat</div>
            <div style="font-size:.82rem;line-height:1.6;color:var(--text-dark)">{{ $murid->alamat }}</div>
        </div>
        @endif
    </div>

    {{-- Form Kanan --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        {{-- Data Pribadi --}}
        <div class="card">
            <div class="card-header">
                <h3>Data Pribadi</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('murid.profil.update') }}">
                    @csrf @method('PUT')
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                            <input type="text" name="nama_murid" class="form-control"
                                   value="{{ old('nama_murid', $murid->nama_murid) }}" required/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Orang Tua</label>
                            <input type="text" name="nama_orang_tua" class="form-control"
                                   value="{{ old('nama_orang_tua', $murid->nama_orang_tua) }}"/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="nomor_hp" class="form-control"
                                   value="{{ old('nomor_hp', $murid->nomor_hp) }}" placeholder="08xx-xxxx-xxxx"/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="{{ $murid->user->username }}"
                                   disabled style="background:var(--bg-light);cursor:not-allowed"/>
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $murid->alamat) }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        {{-- Ganti Password --}}
        <div class="card">
            <div class="card-header">
                <h3>Ganti Password</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('murid.profil.update') }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="nama_murid"    value="{{ $murid->nama_murid }}"/>
                    <input type="hidden" name="nama_orang_tua" value="{{ $murid->nama_orang_tua }}"/>
                    <input type="hidden" name="nomor_hp"      value="{{ $murid->nomor_hp }}"/>
                    <input type="hidden" name="alamat"        value="{{ $murid->alamat }}"/>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Password Saat Ini <span style="color:red">*</span></label>
                            <input type="password" name="current_password" class="form-control"/>
                        </div>
                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter"/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"/>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm">
                        Update Password
                    </button>
                </form>
            </div>
    </div>
</div>
@endsection