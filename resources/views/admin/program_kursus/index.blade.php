@extends('layouts.app')
@section('title', 'Program Kursus')
@section('page-title', 'Program Kursus')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Program Kursus</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <h2>Program Kursus</h2>
    <div class="page-header-filters">
        <a href="{{ route('admin.program-kursus.create') }}" class="btn btn-yellow btn-sm">
            + Tambah Program
        </a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table style="table-layout:fixed;width:100%">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="width:35%">Nama Program</th>
                    <th style="width:20%">Tipe</th>
                    <th style="width:15%">Jumlah Kelas</th>
                    <th style="width:12%">Status</th>
                    <th style="width:13%;text-align:right">Aksi</th>
                </tr>
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
                            {{ $p->tipe_les=='keduanya' ? 'ONSITE & HOME' : ($p->tipe_les=='onsite'?'ONSITE':'HOME PRIVATE') }}
                        </span>
                    </td>
                    <td>{{ $p->kelas_count }} kelas</td>
                    <td>
                        <span class="badge {{ $p->is_active ? 'badge-success' : 'badge-gray' }}">
                            {{ $p->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </td>
                    <td style="text-align:right">
                        <div class="action-dropdown-wrap">
                            <button type="button" class="btn-action-dropdown" onclick="toggleActionDropdown(this, event)">
                                Kelola ▾
                            </button>
                            <div class="action-dropdown-menu">
                                <a href="{{ route('admin.program-kursus.edit', $p) }}" class="action-dropdown-item">
                                    Edit Data
                                </a>
                                <div class="action-dropdown-divider"></div>
                                <button type="button" class="action-dropdown-item danger"
                                    onclick="openDeleteModal('{{ route('admin.program-kursus.destroy', $p) }}','{{ addslashes($p->nama_program) }}')">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="empty-state-title">Belum ada program kursus.</div>
                        <div class="empty-state-description">Mulai dengan menambahkan program kursus baru.</div>
                        <a href="{{ route('admin.program-kursus.create') }}" class="btn btn-yellow btn-sm">+ Tambah Program</a>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($programs->hasPages())<div style="padding:0.75rem 1.25rem;border-top:1px solid var(--topbar-border)">{{ $programs->links() }}</div>@endif
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="delete-modal-backdrop" id="delete-modal-backdrop">
    <div class="delete-modal">
        <h3>Hapus Program Kursus?</h3>
        <p>Apakah Anda yakin ingin menghapus program kursus <strong id="delete-item-name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
        <form id="delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="delete-modal-actions">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openDeleteModal(actionUrl, itemName) {
        document.getElementById('delete-form').action = actionUrl;
        document.getElementById('delete-item-name').textContent = itemName;
        document.getElementById('delete-modal-backdrop').classList.add('open');
    }
    function closeDeleteModal() {
        document.getElementById('delete-modal-backdrop').classList.remove('open');
    }
</script>
@endpush
@endsection
