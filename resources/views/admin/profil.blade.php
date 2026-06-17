@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Profil Saya</h2>
        <div class="breadcrumb">Admin / <span>Profil</span></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start">

    {{-- Kartu Info Kiri --}}
    <div class="card" style="text-align:center;padding:32px 24px">
        <div style="position:relative;display:inline-block;margin-bottom:16px">
            @if($user->foto_profil)
                <img src="{{ asset('storage/' . $user->foto_profil) }}"
                     style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-blue)">
            @else
                <div style="width:96px;height:96px;border-radius:50%;background:var(--primary-blue);
                            display:flex;align-items:center;justify-content:center;
                            font-size:2.2rem;font-weight:700;color:#fff;margin:0 auto;
                            border:3px solid var(--primary-blue)">
                    {{ strtoupper(substr($user->name ?? $user->username, 0, 1)) }}
                </div>
            @endif
        </div>
        <div style="font-size:1.1rem;font-weight:700;margin-bottom:4px">{{ $user->name ?? $user->username }}</div>
        <div style="font-size:.8rem;color:var(--text-light);margin-bottom:12px">{{ $user->email }}</div>
        <span class="badge badge-info" style="font-size:.75rem;padding:5px 14px">
            <i class="fa-solid fa-shield-halved" style="margin-right:5px"></i>Administrator
        </span>

        <hr style="border:none;border-top:1px solid var(--topbar-border);margin:20px 0">

        <div style="text-align:left">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;
                        letter-spacing:.8px;font-weight:600;margin-bottom:10px">Info Akun</div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div style="display:flex;align-items:center;gap:10px;font-size:.82rem">
                    <i class="fa-solid fa-user" style="width:16px;color:var(--text-light)"></i>
                    <span>{{ $user->username }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;font-size:.82rem">
                    <i class="fa-solid fa-calendar-plus" style="width:16px;color:var(--text-light)"></i>
                    <span>Bergabung {{ $user->created_at->translatedFormat('M Y') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;font-size:.82rem">
                    <i class="fa-solid fa-circle" style="width:16px;color:#16a34a;font-size:.5rem"></i>
                    <span style="color:#16a34a;font-weight:600">Akun Aktif</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Kanan --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
        @endif

        {{-- Data Profil --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-user-tie" style="color:var(--primary-blue);margin-right:8px"></i>Data Profil</h3>
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
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Username tidak dapat diubah</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="foto_profil" class="form-control" accept="image/*"/>
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">JPG/PNG, maks 2MB</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        {{-- Ganti Password --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-lock" style="color:var(--primary-blue);margin-right:8px"></i>Ganti Password</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    {{-- Kirim nama & email supaya validasi tidak gagal --}}
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
                    <button type="submit" class="btn btn-outline" style="margin-top:8px">
                        <i class="fa-solid fa-key"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection