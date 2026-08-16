@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Profil Saya</h2>
        <div class="breadcrumb">Guru / <span>Profil</span></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:18.75rem 1fr;gap:1.5rem;align-items:start">

    {{-- Kartu Info Kiri --}}
    <div class="card" style="text-align:center;padding:2rem 1.5rem">
        <div style="margin-bottom:1rem">
            @if($guru->user->foto_profil)
                <img src="{{ asset('storage/' . $guru->user->foto_profil) }}"
                     style="width:6rem;height:6rem;border-radius:50%;object-fit:cover;
                            border:2px solid var(--topbar-border);margin:0 auto;display:block">
            @else
                <div style="width:6rem;height:6rem;border-radius:50%;background:var(--primary-navy);
                            display:flex;align-items:center;justify-content:center;
                            font-size:2.2rem;font-weight:700;color:#fff;margin:0 auto">
                    {{ strtoupper(substr($guru->nama_guru, 0, 1)) }}
                </div>
            @endif
        </div>
        <div style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin-bottom:0.25rem">{{ $guru->nama_guru }}</div>
        <div style="font-size:.8rem;color:var(--text-light);margin-bottom:0.5rem">{{ $guru->user->email }}</div>

        @if($guru->spesialisasis->count())
            <div style="display:flex;flex-wrap:wrap;gap:0.25rem;justify-content:center;margin-bottom:1rem">
                @foreach($guru->spesialisasis as $s)
                    <span class="badge badge-info">{{ $s->nama_spesialisasi }}</span>
                @endforeach
            </div>
        @endif

        <span class="badge badge-warning" style="padding:0.25rem 1rem">
            GURU
        </span>

        <hr style="border:none;border-top:1px solid var(--topbar-border);margin:1.5rem 0">

        <div style="text-align:left">
            <div style="font-size:.72rem;color:var(--text-light);text-transform:uppercase;
                        letter-spacing:0.04em;font-weight:700;margin-bottom:0.75rem">Info Akun</div>
            <div style="display:flex;flex-direction:column;gap:0.5rem">
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:.82rem">
                    <span style="color:var(--text-light)">No. HP</span>
                    <span style="font-weight:600;color:var(--text-dark);font-variant-numeric:tabular-nums">{{ $guru->nomor_hp ?? '-' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:.82rem">
                    <span style="color:var(--text-light)">Bergabung</span>
                    <span style="font-weight:600;color:var(--text-dark)">{{ $guru->created_at->translatedFormat('M Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:.82rem">
                    <span style="color:var(--text-light)">Status</span>
                    <span style="color:{{ $guru->status_aktif ? '#16a34a' : '#dc2626' }};font-weight:700">
                        {{ $guru->status_aktif ? 'Aktif' : 'Non-aktif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Kanan --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem">

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
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">Pisahkan beberapa instrumen dengan koma.</div>
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
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
                    <button type="submit" class="btn btn-outline btn-sm" style="margin-top:0.25rem">
                        Update Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
