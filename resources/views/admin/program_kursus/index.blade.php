@extends('layouts.app')
@section('title', 'Program Kursus')
@section('page-title', 'Program Kursus')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <h2>Program Kursus</h2>
    <div class="breadcrumb">Admin / Akademik / <span>Program Kursus</span></div>
    <div class="page-header-filters">
        <a href="{{ route('admin.program-kursus.create') }}" class="btn btn-primary btn-sm">
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
                        <div style="display:flex;gap:0.25rem">
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
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="10" y="16" width="40" height="52" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><rect x="30" y="12" width="40" height="52" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--card-bg)"/><path d="M40 28h20M40 38h14M40 48h18" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round" opacity=".5"/></svg>
                        </div>
                        <div class="empty-state-title">Belum ada program kursus.</div>
                        <div class="empty-state-description">Mulai dengan menambahkan program kursus baru.</div>
                        <a href="{{ route('admin.program-kursus.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Program</a>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($programs->hasPages())<div style="padding:1rem 1.5rem">{{ $programs->links() }}</div>@endif
</div>
@endsection
