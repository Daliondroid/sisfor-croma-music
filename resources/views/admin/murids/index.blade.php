@extends('layouts.app')
@section('title', 'Data Murid')
@section('page-title', 'Data Murid')

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Data Murid</h2>
        <div class="breadcrumb">Admin / <span>Murid</span></div>
    </div>
    <a href="{{ route('admin.murids.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Murid
    </a>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
            <div class="form-group" style="margin:0;flex:1;min-width:180px">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Nama atau email..." value="{{ request('search') }}"/>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Tipe Les</label>
                <select name="tipe" class="form-control">
                    <option value="">Semua</option>
                    <option value="onsite" {{ request('tipe')=='onsite'?'selected':'' }}>Onsite</option>
                    <option value="home_private" {{ request('tipe')=='home_private'?'selected':'' }}>Home Private</option>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua</option>
                    <option value="1" {{ request('status')=='1'?'selected':'' }}>Aktif</option>
                    <option value="0" {{ request('status')=='0'?'selected':'' }}>Non-aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-bottom:0">
                <i class="fa-solid fa-search"></i> Cari
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Murid</th>
                    <th>Email / Username</th>
                    <th>Tipe Les</th>
                    <th>Orang Tua</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($murids as $i => $m)
                <tr>
                    <td style="color:var(--text-light)">{{ $murids->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar" style="width:34px;height:34px;font-size:.75rem;flex-shrink:0">
                                {{ strtoupper(substr($m->nama_murid,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600">{{ $m->nama_murid }}</div>
                                @if($m->tanggal_lahir)
                                    <div style="font-size:.72rem;color:var(--text-light)">{{ $m->tanggal_lahir->format('d/m/Y') }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>{{ $m->user->email }}</div>
                        <div style="font-size:.72rem;color:var(--text-light)">@{{ $m->user->username }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $m->tipe_les=='onsite' ? 'badge-info' : 'badge-warning' }}">
                            {{ $m->tipe_les=='onsite' ? 'Onsite' : 'Home Private' }}
                        </span>
                    </td>
                    <td>{{ $m->nama_orang_tua ?? '-' }}</td>
                    <td>{{ $m->nomor_hp ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $m->status_aktif ? 'badge-success' : 'badge-gray' }}">
                            {{ $m->status_aktif ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.murids.edit', $m) }}" class="btn btn-sm btn-outline" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.toggle', $m->user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $m->status_aktif ? 'btn-danger' : 'btn-primary' }}" title="{{ $m->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fa-solid {{ $m->status_aktif ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-light)">Belum ada data murid.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($murids->hasPages())
        <div style="padding:16px 24px;border-top:1px solid #f0f0f0">
            {{ $murids->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection