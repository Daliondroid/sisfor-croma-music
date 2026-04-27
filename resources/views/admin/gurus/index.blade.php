@extends('layouts.app')
@section('title', 'Data Guru')
@section('page-title', 'Data Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Data Guru</h2><div class="breadcrumb">Admin / <span>Guru</span></div></div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-outline" onclick="openSpesialisasiModal()">
            <i class="fa-solid fa-list"></i> Daftar Instrumen
        </button>
        <a href="{{ route('admin.gurus.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Guru</a>
    </div>
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
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 22%;">Nama Guru</th>
                    <th style="width: 20%;">Email</th>
                    <th style="width: 17%;">Instrumen</th>
                    <th style="width: 17%;">No. HP</th>
                    <th style="width: 5%;">Status</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            {{-- <thead><tr><th>#</th><th>Nama Guru</th><th>Email</th><th>Instrumen</th><th>No. HP</th><th>Status</th><th>Aksi</th></tr></thead> --}}
            <tbody>
            @forelse($gurus as $i => $g)
                <tr>
                    <td style="color:var(--text-light)">{{ $gurus->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                        <div class="avatar" style="width:34px;height:34px;overflow:hidden;border-radius:50%">
                            @if($g->user->foto_profil)
                                <img src="{{ asset('storage/' . $g->user->foto_profil) }}" style="width:100%;height:100%;object-fit:cover">
                            @else
                                {{ strtoupper(substr($g->nama_guru,0,1)) }}
                            @endif
                        </div>
                            <strong title="{{ $g->nama_guru }}">{{ \Illuminate\Support\Str::limit($g->nama_guru, 20) }}</strong>
                        </div>
                    </td>
                    <td title="{{ $g->user->email }}">
                        {{ \Illuminate\Support\Str::limit($g->user->email, 25) }}
                    </td>
                    <td>
                        @forelse($g->spesialisasis as $s)
                            <span class="badge badge-info" title="{{ $s->nama_spesialisasi }}" style="font-size: 10px; margin-bottom: 2px; display: inline-block;">
                                {{ \Illuminate\Support\Str::limit($s->nama_spesialisasi, 9) }}
                            </span>
                        @empty
                            <span style="color:var(--text-light); font-size: 0.8rem;">-</span>
                        @endforelse
                    </td>
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
                            {{-- Tombol hapus akun --}}
                            <button
                                class="btn btn-sm btn-danger"
                                title="Hapus Akun"
                                onclick="openDeleteModal(
                                    '{{ route('admin.gurus.destroy', $g) }}',
                                    '{{ addslashes($g->nama_guru) }}'
                                )"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
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

{{-- Delete Confirmation Modal --}}
<div class="delete-modal-backdrop" id="delete-modal-backdrop">
    <div class="delete-modal">
        <div class="delete-modal-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3>Hapus Akun Guru?</h3>
        <p>Aksi ini akan menghapus akun <strong id="delete-modal-name"></strong> secara permanen beserta semua data terkait. Tindakan ini tidak dapat dibatalkan.</p>
        <div class="delete-modal-actions">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Batal</button>
            <form id="delete-modal-form" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Modal Daftar Instrumen --}}
<div class="delete-modal-backdrop" id="spesialisasi-modal-backdrop">
    <div class="delete-modal" style="width: 500px; max-width: 90%; text-align: left; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px;">Daftar Instrumen</h3>
            <button type="button" onclick="closeSpesialisasiModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-light); line-height: 1;">&times;</button>
        </div>
        
        <form action="{{ route('admin.spesialisasi.store') }}" method="POST" style="margin-bottom: 20px;">
            @csrf
            <div style="display: flex; gap: 8px;">
                <input type="text" name="nama_spesialisasi" class="form-control" placeholder="Contoh: Piano" required style="flex: 1;">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>

        <hr style="border: none; border-top: 1px solid #eee; margin: 15px 0;">

        <ul style="list-style: none; padding: 0; margin: 0; max-height: 300px; overflow-y: auto;">
            @forelse($spesialisasis as $s)
                <li style="padding: 12px 0; border-bottom: 1px solid #f9f9f9;">
                    <div id="view-instrumen-{{ $s->id_spesialisasi }}" style="display: flex; justify-content: space-between; align-items: center;">
                        {{-- <span style="font-weight: 500;" title="{{ $s->nama_spesialisasi }}">{{ \Illuminate\Support\Str::limit($s->nama_spesialisasi, 35) }}</span> --}}
                        <span style="font-weight: 500; word-break: break-word; padding-right: 15px; line-height: 1.4;">
                            {{ $s->nama_spesialisasi }}
                        </span>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" onclick="editInstrumen({{ $s->id_spesialisasi }})" style="color: var(--primary-blue); border: none; background: none; cursor: pointer; padding: 4px;" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('admin.spesialisasi.destroy', $s) }}" method="POST" onsubmit="return confirm('Hapus instrumen {{ $s->nama_spesialisasi }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="color: #dc3545; border: none; background: none; cursor: pointer; padding: 4px;" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div id="edit-instrumen-{{ $s->id_spesialisasi }}" style="display: none;">
                        <form action="{{ route('admin.spesialisasi.update', $s) }}" method="POST">
                            @csrf @method('PUT')
                            <div style="display: flex; gap: 8px;">
                                <input type="text" name="nama_spesialisasi" class="form-control" value="{{ $s->nama_spesialisasi }}" required style="flex: 1; height: 32px; font-size: 13px;">
                                <button type="submit" class="btn btn-primary" style="padding: 0 12px; height: 32px; font-size: 12px;">Simpan</button>
                                <button type="button" onclick="cancelEdit({{ $s->id_spesialisasi }})" class="btn btn-outline" style="padding: 0 12px; height: 32px; font-size: 12px;">Batal</button>
                            </div>
                        </form>
                    </div>
                </li>
            @empty
                <li style="color: var(--text-light); font-size: 13px; text-align: center; padding: 20px 0;">Belum ada instrumen.</li>
            @endforelse
        </ul>
    </div>
</div>

@endsection

@push('scripts')
<script>

// --- Fungsi Buka/Tutup Modal Instrumen ---
function openSpesialisasiModal() {
    document.getElementById('spesialisasi-modal-backdrop').classList.add('open');
}

function editInstrumen(id) {
    document.getElementById('view-instrumen-' + id).style.display = 'none';
    document.getElementById('edit-instrumen-' + id).style.display = 'block';
}

function cancelEdit(id) {
    document.getElementById('view-instrumen-' + id).style.display = 'flex';
    document.getElementById('edit-instrumen-' + id).style.display = 'none';
}

function closeSpesialisasiModal() {
    document.getElementById('spesialisasi-modal-backdrop').classList.remove('open');
}

// Tutup modal jika area gelap di luar kotak diklik
document.getElementById('spesialisasi-modal-backdrop').addEventListener('click', function(e) {
    if (e.target === this) closeSpesialisasiModal();
});

function openDeleteModal(actionUrl, nama) {
    document.getElementById('delete-modal-form').action = actionUrl;
    document.getElementById('delete-modal-name').textContent = nama;
    document.getElementById('delete-modal-backdrop').classList.add('open');
}
function closeDeleteModal() {
    document.getElementById('delete-modal-backdrop').classList.remove('open');
}
document.getElementById('delete-modal-backdrop').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush