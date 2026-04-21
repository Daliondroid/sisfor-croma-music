@extends('layouts.app')
@section('title', 'Data Guru')
@section('page-title', 'Data Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Data Guru</h2><div class="breadcrumb">Admin / <span>Guru</span></div></div>
    <a href="{{ route('admin.gurus.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Guru</a>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
            <div class="form-group" style="margin:0;flex:1;min-width:180px">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Nama atau email..." value="{{ request('search') }}"/>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua</option>
                    <option value="1" {{ request('status')=='1'?'selected':'' }}>Aktif</option>
                    <option value="0" {{ request('status')=='0'?'selected':'' }}>Non-aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Cari</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Nama Guru</th><th>Email</th><th>Spesialisasi</th><th>No. HP</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($gurus as $i => $g)
                <tr>
                    <td style="color:var(--text-light)">{{ $gurus->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar" style="width:34px;height:34px;font-size:.75rem;flex-shrink:0">{{ strtoupper(substr($g->nama_guru,0,1)) }}</div>
                            <strong>{{ $g->nama_guru }}</strong>
                        </div>
                    </td>
                    <td>{{ $g->user->email }}</td>
                    <td>{{ $g->spesialisasi ?? '-' }}</td>
                    <td>{{ $g->nomor_hp ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $g->status_aktif ? 'badge-success' : 'badge-gray' }}">
                            {{ $g->status_aktif ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.gurus.edit', $g) }}" class="btn btn-sm btn-outline"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.users.toggle', $g->user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $g->status_aktif ? 'btn-danger' : 'btn-primary' }}">
                                    <i class="fa-solid {{ $g->status_aktif ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-light)">Belum ada data guru.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($gurus->hasPages())<div style="padding:16px 24px">{{ $gurus->withQueryString()->links() }}</div>@endif
</div>
@endsection