@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}"            class="nav-item {{ request()->routeIs('guru.dashboard')       ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('guru.jadwal.index') }}"         class="nav-item {{ request()->routeIs('guru.jadwal*')         ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('guru.absensi.index') }}"        class="nav-item {{ request()->routeIs('guru.absensi*')        ? 'active' : '' }}"><i class="fa-solid fa-chart-bar"></i> Data Absensi</a>
    <a href="{{ route('guru.presensi.index') }}"       class="nav-item {{ request()->routeIs('guru.presensi*')       ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Input Presensi</a>
    <a href="{{ route('guru.progres.index') }}"        class="nav-item {{ request()->routeIs('guru.progres*')        ? 'active' : '' }}"><i class="fa-solid fa-book-open"></i> Laporan KBM</a>
    <a href="{{ route('guru.monthly-report.index') }}" class="nav-item {{ request()->routeIs('guru.monthly-report*') ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i> Laporan Bulanan</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Profil Saya</h2>
        <div class="breadcrumb">Guru / <span>Profil</span></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start">

    {{-- Kartu Info Kiri --}}
    <div class="card" style="text-align:center;padding:32px 24px">
        <div style="margin-bottom:16px">
            @if($guru->user->foto_profil)
                <img src="{{ asset('storage/' . $guru->user->foto_profil) }}"
                     style="width:96px;height:96px;border-radius:50%;object-fit:cover;
                            border:3px solid var(--primary-blue);margin:0 auto;display:block">
            @else
                <div style="width:96px;height:96px;border-radius:50%;background:var(--primary-blue);
                            display:flex;align-items:center;justify-content:center;
                            font-size:2.2rem;font-weight:700;color:#fff;margin:0 auto">
                    {{ strtoupper(substr($guru->nama_guru, 0, 1)) }}
                </div>
            @endif
        </div>
        <div style="font-size:1.1rem;font-weight:700;margin-bottom:4px">{{ $guru->nama_guru }}</div>
        <div style="font-size:.8rem;color:var(--text-light);margin-bottom:8px">{{ $guru->user->email }}</div>

        @if($guru->spesialisasis->count())
            <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:center;margin-bottom:12px">
                @foreach($guru->spesialisasis as $s)
                    <span class="badge badge-info" style="font-size:.7rem">{{ $s->nama_spesialisasi }}</span>
                @endforeach
            </div>
        @endif

        <span class="badge badge-warning" style="font-size:.75rem;padding:5px 14px">
            <i class="fa-solid fa-chalkboard-user" style="margin-right:5px"></i>Guru
        </span>

        <hr style="border:none;border-top:1px solid var(--topbar-border);margin:20px 0">

        <div style="text-align:left">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;
                        letter-spacing:.8px;font-weight:600;margin-bottom:10px">Info Akun</div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div style="display:flex;align-items:center;gap:10px;font-size:.82rem">
                    <i class="fa-solid fa-phone" style="width:16px;color:var(--text-light)"></i>
                    <span>{{ $guru->nomor_hp ?? '-' }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;font-size:.82rem">
                    <i class="fa-solid fa-calendar-plus" style="width:16px;color:var(--text-light)"></i>
                    <span>Bergabung {{ $guru->created_at->translatedFormat('M Y') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;font-size:.82rem">
                    <i class="fa-solid fa-circle" style="width:16px;color:{{ $guru->status_aktif ? '#16a34a' : '#dc2626' }};font-size:.5rem"></i>
                    <span style="color:{{ $guru->status_aktif ? '#16a34a' : '#dc2626' }};font-weight:600">
                        {{ $guru->status_aktif ? 'Aktif' : 'Non-aktif' }}
                    </span>
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
                <h3><i class="fa-solid fa-user" style="color:var(--primary-blue);margin-right:8px"></i>Data Profil</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('guru.profil.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                            <input type="text" name="nama_guru" class="form-control"
                                   value="{{ old('nama_guru', $guru->nama_guru) }}" required/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="nomor_hp" class="form-control"
                                   value="{{ old('nomor_hp', $guru->nomor_hp) }}" placeholder="08xx-xxxx-xxxx"/>
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
                            <label class="form-label">Spesialisasi Instrumen</label>
                            <input type="text" name="spesialisasi" class="form-control"
                                   value="{{ old('spesialisasi', $guru->spesialisasis->pluck('nama_spesialisasi')->implode(', ')) }}"
                                   placeholder="Piano, Gitar, Vokal (pisahkan dengan koma)"/>
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Pisahkan beberapa instrumen dengan koma.</div>
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
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
                <form method="POST" action="{{ route('guru.profil.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <input type="hidden" name="nama_guru"    value="{{ $guru->nama_guru }}"/>
                    <input type="hidden" name="nomor_hp"     value="{{ $guru->nomor_hp }}"/>
                    <input type="hidden" name="spesialisasi" value="{{ $guru->spesialisasis->pluck('nama_spesialisasi')->implode(', ') }}"/>
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
                    <button type="submit" class="btn btn-outline" style="margin-top:4px">
                        <i class="fa-solid fa-key"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
