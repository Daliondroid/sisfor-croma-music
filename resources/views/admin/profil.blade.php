@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('breadcrumb')
    <span class="crumb-root">Utama</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Profil Saya</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Profil Saya</h2>
    </div>
</div>

<div style="display:grid;grid-template-columns:18.75rem 1fr;gap:1.25rem;align-items:start">

    {{-- Kartu Info Kiri --}}
    <div class="card" style="text-align:center;padding:1.5rem 1.25rem">
        <div style="position:relative;display:inline-block;margin-bottom:1rem">
            @if($user->foto_profil)
                <img src="{{ asset('storage/' . $user->foto_profil) }}"
                     style="width:5.5rem;height:5.5rem;border-radius:0.25rem;object-fit:cover;border:1px solid var(--topbar-border)">
            @else
                <div style="width:5.5rem;height:5.5rem;border-radius:0.25rem;background:var(--primary-navy);
                            display:flex;align-items:center;justify-content:center;
                            font-size:2rem;font-weight:700;color:#fff;margin:0 auto">
                    {{ strtoupper(substr($user->name ?? $user->username, 0, 1)) }}
                </div>
            @endif
        </div>
        <div style="font-size:1.05rem;font-weight:700;margin-bottom:0.25rem">{{ $user->name ?? $user->username }}</div>
        <div style="font-size:.8rem;color:var(--text-light);margin-bottom:0.75rem">{{ $user->email }}</div>
        <span class="badge badge-info">
            ADMINISTRATOR
        </span>

        <hr style="border:none;border-top:1px solid var(--topbar-border);margin:1.25rem 0">

        <div style="text-align:left">
            <div style="font-size:.7rem;color:var(--text-light);text-transform:uppercase;
                        letter-spacing:0.06em;font-weight:700;margin-bottom:0.5rem">Info Akun</div>
            <div style="display:flex;flex-direction:column;gap:0.5rem">
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:.82rem">
                    <span style="color:var(--text-light)">Username</span>
                    <strong>{{ $user->username }}</strong>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:.82rem">
                    <span style="color:var(--text-light)">Bergabung</span>
                    <strong style="font-variant-numeric:tabular-nums">{{ $user->created_at->translatedFormat('M Y') }}</strong>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:.82rem">
                    <span style="color:var(--text-light)">Status</span>
                    <span class="badge badge-success">AKTIF</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Kanan --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        {{-- Data Profil --}}
        <div class="card">
            <div class="card-header">
                <h3>Data Profil</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                            <input type="text" name="nama_admin" class="form-control"
                                   value="{{ old('nama_admin', $admin->nama_admin) }}" required/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email <span style="color:red">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $user->email) }}" required/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="{{ $user->username }}"
                                   disabled style="background:var(--bg-light);cursor:not-allowed"/>
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">Username tidak dapat diubah</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="foto_profil" class="form-control" accept="image/*"/>
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">JPG/PNG, maks 2MB</div>
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
                <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <input type="hidden" name="nama_admin" value="{{ $admin->nama_admin }}"/>
                    <input type="hidden" name="email"      value="{{ $user->email }}"/>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Password Saat Ini <span style="color:red">*</span></label>
                            <input type="password" name="current_password" class="form-control"/>
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
                            <div class="form-grid">
                                <div class="form-group" style="margin-bottom:0">
                                    <label class="form-label">Password Baru <span style="color:red">*</span></label>
                                    <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter"/>
                                </div>
                                <div class="form-group" style="margin-bottom:0">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline btn-sm" style="margin-top:0.5rem">
                        Update Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection