@extends('layouts.app')
@section('title', 'Program Kursus')
@section('page-title', 'Program Kursus')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Program Kursus</h2>
        <div class="breadcrumb">Admin / Akademik / <span>Program Kursus</span></div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-door-open"></i> Data Kelas
        </a>
        <a href="{{ route('admin.program-kursus.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Program
        </a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Nama Program</th><th>Tipe</th><th>Jumlah Kelas</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($programs as $i => $p)
                <tr>
                    <td style="color:var(--text-light)">{{ $programs->firstItem() + $i }}</td>
                    <td>
                        <div style="font-weight:600">{{ $p->nama_program }}</div>
                        @if($p->deskripsi)
                            <div style="font-size:.72rem;color:var(--text-light)">{{ Str::limit($p->deskripsi, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $p->tipe_les=='keduanya' ? 'badge-success' : ($p->tipe_les=='onsite'?'badge-info':'badge-warning') }}">
                            {{ $p->tipe_les=='keduanya' ? 'Onsite & Home' : ($p->tipe_les=='onsite'?'Onsite':'Home Private') }}
                        </span>
                    </td>
                    <td>{{ $p->kelas_count }} kelas</td>
                    <td>
                        <span class="badge {{ $p->is_active ? 'badge-success' : 'badge-gray' }}">
                            {{ $p->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.program-kursus.edit', $p) }}" class="btn btn-sm btn-outline">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.program-kursus.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus program ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-light)">Belum ada program kursus.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($programs->hasPages())<div style="padding:16px 24px">{{ $programs->links() }}</div>@endif
</div>
@endsection