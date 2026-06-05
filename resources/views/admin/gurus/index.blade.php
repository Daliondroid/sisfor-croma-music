@extends('layouts.app')
@section('title', 'Data Guru')
@section('page-title', 'Data Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Data Guru</h2><div class="breadcrumb">Admin / <span>Guru</span></div></div>
    <div style="display:flex;gap:10px">
        <button type="button" class="btn btn-outline" onclick="openSpesialisasiModal()">
            <i class="fa-solid fa-list"></i> Daftar Instrumen
        </button>
        <a href="{{ route('admin.gurus.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Guru
        </a>
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
                    <th style="width:5%">#</th>
                    <th style="width:22%">Nama Guru</th>
                    <th style="width:20%">Email</th>
                    <th style="width:20%">Instrumen</th>
                    <th style="width:15%">No. HP</th>
                    <th style="width:8%">Status</th>
                    <th style="width:10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($gurus as $i => $g)
                <tr>
                    <td style="color:var(--text-light)">{{ $gurus->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="avatar" style="width:34px;height:34px;overflow:hidden;border-radius:50%;flex-shrink:0">
                                @if($g->user->foto_profil)
                                    <img src="{{ asset('storage/' . $g->user->foto_profil) }}" style="width:100%;height:100%;object-fit:cover">
                                @else
                                    {{ strtoupper(substr($g->nama_guru,0,1)) }}
                                @endif
                            </div>
                            <strong title="{{ $g->nama_guru }}">{{ \Illuminate\Support\Str::limit($g->nama_guru, 20) }}</strong>
                        </div>
                    </td>
                    <td title="{{ $g->user->email }}">{{ \Illuminate\Support\Str::limit($g->user->email, 25) }}</td>
                    <td>
                        @forelse($g->spesialisasis as $s)
                            <span class="badge badge-info" style="font-size:10px;margin-bottom:2px;display:inline-block">
                                {{ \Illuminate\Support\Str::limit($s->nama_spesialisasi, 9) }}
                            </span>
                        @empty
                            <span style="color:var(--text-light);font-size:.8rem">-</span>
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
                            {{-- Tombol Detail --}}
                            <button class="btn btn-sm btn-outline" title="Detail"
                                onclick="openGuruDetail({{ json_encode([
                                    'nama'         => $g->nama_guru,
                                    'username'     => $g->user->username,
                                    'email'        => $g->user->email,
                                    'nomor_hp'     => $g->nomor_hp ?? '-',
                                    'status_aktif' => $g->status_aktif,
                                    'bergabung'    => $g->created_at->translatedFormat('d F Y'),
                                    'foto'         => $g->user->foto_profil ? asset('storage/'.$g->user->foto_profil) : null,
                                    'spesialisasis'=> $g->spesialisasis->pluck('nama_spesialisasi')->toArray(),
                                    'total_jadwal' => $g->jadwals->count(),
                                    'jadwal_aktif' => $g->jadwals->where('is_active', true)->count(),
                                ]) }})">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <a href="{{ route('admin.gurus.edit', $g) }}" class="btn btn-sm btn-outline" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.toggle', $g->user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $g->status_aktif ? 'btn-danger' : 'btn-primary' }}">
                                    <i class="fa-solid {{ $g->status_aktif ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>
                            <button class="btn btn-sm btn-danger" title="Hapus Akun"
                                onclick="openDeleteModal('{{ route('admin.gurus.destroy', $g) }}','{{ addslashes($g->nama_guru) }}')">
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
    @if($gurus->hasPages())
        <div style="padding:16px 24px">{{ $gurus->withQueryString()->links() }}</div>
    @endif
</div>

{{-- ═══════════════════════════════════════
     MODAL DETAIL GURU
════════════════════════════════════════ --}}
<div id="guru-detail-backdrop"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:400;align-items:stretch;justify-content:flex-end"
     onclick="if(event.target===this) closeGuruDetail()">
    <div id="guru-detail-panel"
         style="width:420px;max-width:95vw;background:var(--card-bg);height:100%;
                overflow-y:auto;box-shadow:-8px 0 32px rgba(0,0,0,.18);
                transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1)">

        {{-- Header --}}
        <div style="padding:24px 24px 16px;display:flex;justify-content:space-between;align-items:center;
                    border-bottom:1px solid var(--topbar-border);
                    position:sticky;top:0;background:var(--card-bg);z-index:1">
            <div style="font-size:1rem;font-weight:700">Detail Guru</div>
            <button onclick="closeGuruDetail()"
                    style="background:none;border:none;cursor:pointer;font-size:1.3rem;
                           color:var(--text-light);width:32px;height:32px;border-radius:50%;
                           display:flex;align-items:center;justify-content:center;transition:.15s"
                    onmouseover="this.style.background='var(--bg-light)'"
                    onmouseout="this.style.background='none'">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:24px">

            {{-- Avatar & Nama --}}
            <div style="text-align:center;margin-bottom:24px">
                <div id="gd-foto-wrap" style="margin:0 auto 12px;width:80px;height:80px;border-radius:50%;
                            overflow:hidden;border:3px solid var(--primary-blue);
                            display:flex;align-items:center;justify-content:center;
                            background:var(--primary-blue);font-size:1.8rem;font-weight:700;color:#fff">
                </div>
                <div id="gd-nama"     style="font-size:1.15rem;font-weight:700;margin-bottom:4px"></div>
                <div id="gd-username" style="font-size:.8rem;color:var(--text-light);margin-bottom:8px"></div>
                <div id="gd-spesialisasi-badges" style="display:flex;flex-wrap:wrap;gap:4px;justify-content:center;margin-bottom:8px"></div>
                <div id="gd-status-badge"></div>
            </div>

            {{-- Info Kontak --}}
            <div style="border:1px solid var(--topbar-border);border-radius:10px;overflow:hidden;margin-bottom:20px">
                <div style="padding:12px 16px;font-size:.72rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:.8px;
                            color:var(--text-light);background:var(--bg-light)">
                    Informasi Kontak
                </div>
                <div class="gd-row"><span class="gd-label"><i class="fa-solid fa-envelope"></i> Email</span><span id="gd-email" class="gd-value"></span></div>
                <div class="gd-row" style="border-bottom:none"><span class="gd-label"><i class="fa-solid fa-phone"></i> No. HP</span><span id="gd-hp" class="gd-value"></span></div>
            </div>

            {{-- Statistik Mengajar --}}
            <div style="border:1px solid var(--topbar-border);border-radius:10px;overflow:hidden;margin-bottom:20px">
                <div style="padding:12px 16px;font-size:.72rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:.8px;
                            color:var(--text-light);background:var(--bg-light)">
                    Statistik Mengajar
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0">
                    <div style="padding:16px;text-align:center;border-right:1px solid var(--topbar-border)">
                        <div id="gd-total-jadwal" style="font-size:1.6rem;font-weight:700;color:var(--primary-blue)"></div>
                        <div style="font-size:.72rem;color:var(--text-light);margin-top:2px">Total Jadwal</div>
                    </div>
                    <div style="padding:16px;text-align:center">
                        <div id="gd-jadwal-aktif" style="font-size:1.6rem;font-weight:700;color:#16a34a"></div>
                        <div style="font-size:.72rem;color:var(--text-light);margin-top:2px">Jadwal Aktif</div>
                    </div>
                </div>
            </div>

            {{-- Info Akun --}}
            <div style="border:1px solid var(--topbar-border);border-radius:10px;overflow:hidden">
                <div style="padding:12px 16px;font-size:.72rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:.8px;
                            color:var(--text-light);background:var(--bg-light)">
                    Info Akun
                </div>
                <div class="gd-row" style="border-bottom:none">
                    <span class="gd-label"><i class="fa-solid fa-calendar-plus"></i> Bergabung</span>
                    <span id="gd-bergabung" class="gd-value"></span>
                </div>
            </div>

        </div>{{-- end body --}}
    </div>
</div>

{{-- Modal Daftar Instrumen --}}
<div class="delete-modal-backdrop" id="spesialisasi-modal-backdrop">
    <div class="delete-modal" style="width:500px;max-width:90%;text-align:left;padding:24px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h3 style="margin:0;font-size:18px">Daftar Instrumen Terdaftar</h3>
            <button onclick="closeSpesialisasiModal()" style="background:none;border:none;font-size:24px;cursor:pointer;color:var(--text-light)">&times;</button>
        </div>
        <p style="font-size:.8rem;color:var(--text-light);margin-bottom:16px">
            Instrumen dikelola per-guru melalui tombol <strong>Edit</strong>.
        </p>
        <ul style="list-style:none;padding:0;margin:0;max-height:350px;overflow-y:auto">
            @forelse($spesialisasis as $s)
                <li style="padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:.875rem">
                    {{ $s->nama_spesialisasi }}
                </li>
            @empty
                <li style="color:var(--text-light);font-size:13px;text-align:center;padding:20px 0">Belum ada instrumen terdaftar.</li>
            @endforelse
        </ul>
    </div>
</div>

{{-- Delete Modal --}}
<div class="delete-modal-backdrop" id="delete-modal-backdrop">
    <div class="delete-modal">
        <div class="delete-modal-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3>Hapus Akun Guru?</h3>
        <p>Aksi ini akan menghapus akun <strong id="delete-modal-name"></strong> secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
        <div class="delete-modal-actions">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Batal</button>
            <form id="delete-modal-form" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<style>
.gd-row {
    display:flex;align-items:flex-start;gap:12px;
    padding:11px 16px;border-bottom:1px solid var(--topbar-border);
    font-size:.85rem;
}
.gd-label {
    width:100px;flex-shrink:0;color:var(--text-light);font-size:.8rem;
    display:flex;align-items:center;gap:6px;padding-top:1px;
}
.gd-label i { width:14px;text-align:center; }
.gd-value { flex:1;font-weight:500;color:var(--text-dark);word-break:break-word; }
</style>

@endsection

@push('scripts')
<script>
// ── Detail Guru ─────────────────────────────────────────────
function openGuruDetail(data) {
    // Foto
    const fotoWrap = document.getElementById('gd-foto-wrap');
    fotoWrap.innerHTML = data.foto
        ? `<img src="${data.foto}" style="width:100%;height:100%;object-fit:cover">`
        : data.nama.charAt(0).toUpperCase();

    document.getElementById('gd-nama').textContent     = data.nama;
    document.getElementById('gd-username').textContent = '@' + data.username;

    // Spesialisasi badges
    const badgesEl = document.getElementById('gd-spesialisasi-badges');
    badgesEl.innerHTML = data.spesialisasis.length
        ? data.spesialisasis.map(s =>
            `<span class="badge badge-info" style="font-size:.72rem">${s}</span>`
          ).join('')
        : '<span style="font-size:.8rem;color:var(--text-light)">Belum ada spesialisasi</span>';

    // Status badge
    document.getElementById('gd-status-badge').innerHTML = data.status_aktif
        ? '<span class="badge badge-success" style="font-size:.75rem;padding:4px 14px">Aktif</span>'
        : '<span class="badge badge-gray"    style="font-size:.75rem;padding:4px 14px">Non-aktif</span>';

    document.getElementById('gd-email').textContent        = data.email;
    document.getElementById('gd-hp').textContent           = data.nomor_hp;
    document.getElementById('gd-total-jadwal').textContent = data.total_jadwal;
    document.getElementById('gd-jadwal-aktif').textContent = data.jadwal_aktif;
    document.getElementById('gd-bergabung').textContent    = data.bergabung;

    const backdrop = document.getElementById('guru-detail-backdrop');
    const panel    = document.getElementById('guru-detail-panel');
    backdrop.style.display = 'flex';
    requestAnimationFrame(() => { panel.style.transform = 'translateX(0)'; });
}

function closeGuruDetail() {
    const panel = document.getElementById('guru-detail-panel');
    panel.style.transform = 'translateX(100%)';
    setTimeout(() => { document.getElementById('guru-detail-backdrop').style.display = 'none'; }, 300);
}

// ── Modal Instrumen ──────────────────────────────────────────
function openSpesialisasiModal()  { document.getElementById('spesialisasi-modal-backdrop').classList.add('open'); }
function closeSpesialisasiModal() { document.getElementById('spesialisasi-modal-backdrop').classList.remove('open'); }
document.getElementById('spesialisasi-modal-backdrop').addEventListener('click', function(e) {
    if (e.target === this) closeSpesialisasiModal();
});

// ── Delete Modal ─────────────────────────────────────────────
function openDeleteModal(actionUrl, nama) {
    document.getElementById('delete-modal-form').action = actionUrl;
    document.getElementById('delete-modal-name').textContent = nama;
    document.getElementById('delete-modal-backdrop').classList.add('open');
}
function closeDeleteModal() { document.getElementById('delete-modal-backdrop').classList.remove('open'); }
document.getElementById('delete-modal-backdrop').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush