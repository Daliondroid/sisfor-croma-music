@extends('layouts.app')
@section('title', 'Data Murid')
@section('page-title', 'Data Murid')

@section('breadcrumb')
    <span class="crumb-root">Akademik</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Data Murid</span>
@endsection

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <h2>Data Murid</h2>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.625rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama atau email..." value="{{ request('search') }}"/>
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status')=='1'?'selected':'' }}>Aktif</option>
                <option value="0" {{ request('status')=='0'?'selected':'' }}>Non-aktif</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">
                Cari
            </button>
        </form>
        <a href="{{ route('admin.murids.create') }}" class="btn btn-yellow btn-sm">
            + Tambah Murid
        </a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table style="table-layout:fixed;width:100%">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:26%">Nama Murid</th>
                    <th style="width:22%">Email / Username</th>
                    <th style="width:16%">Orang Tua</th>
                    <th style="width:14%">No. HP</th>
                    <th style="width:10%">Status</th>
                    <th style="width:8%;text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($murids as $i => $m)
                <tr>
                    <td style="color:var(--text-light)">{{ $murids->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.5rem">
                            <div class="avatar" style="width:1.85rem;height:1.85rem;overflow:hidden;border-radius:0.25rem;flex-shrink:0;font-size:0.75rem">
                                @if($m->user->foto_profil)
                                    <img src="{{ asset('storage/' . $m->user->foto_profil) }}" style="width:100%;height:100%;object-fit:cover">
                                @else
                                    {{ strtoupper(substr($m->nama_murid,0,1)) }}
                                @endif
                            </div>
                            <div style="overflow:hidden;text-overflow:ellipsis">
                                <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $m->nama_murid }}</div>
                                @if($m->tanggal_lahir)
                                    <div style="font-size:.72rem;color:var(--text-light)">{{ $m->tanggal_lahir->format('d/m/Y') }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="overflow:hidden;text-overflow:ellipsis">
                        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $m->user->email }}</div>
                        <div style="font-size:.72rem;color:var(--text-light)">{{ '@' . $m->user->username }}</div>
                    </td>
                    <td style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $m->nama_orang_tua ?? '-' }}</td>
                    <td style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $m->nomor_hp ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $m->status_aktif ? 'badge-success' : 'badge-gray' }}">
                            {{ $m->status_aktif ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </td>
                    <td style="text-align:right">
                        <div class="action-dropdown-wrap">
                            <button type="button" class="btn-action-dropdown" onclick="toggleActionDropdown(this, event)">
                                Kelola ▾
                            </button>
                            <div class="action-dropdown-menu">
                                <button type="button" class="action-dropdown-item"
                                    onclick="openMuridDetail({{ json_encode([
                                        'nama'         => $m->nama_murid,
                                        'username'     => $m->user->username,
                                        'email'        => $m->user->email,
                                        'nomor_hp'     => $m->nomor_hp ?? '-',
                                        'tanggal_lahir'=> $m->tanggal_lahir ? $m->tanggal_lahir->translatedFormat('d F Y') : '-',
                                        'alamat'       => $m->alamat ?? '-',
                                        'nama_orang_tua' => $m->nama_orang_tua ?? '-',
                                        'status_aktif' => $m->status_aktif,
                                        'bergabung'    => $m->created_at->translatedFormat('d F Y'),
                                        'foto'         => $m->user->foto_profil ? asset('storage/'.$m->user->foto_profil) : null,
                                        'total_spp'    => $m->spps_count,
                                        'spp_lunas'    => $m->spp_lunas_count,
                                    ]) }})">
                                    Lihat Detail
                                </button>
                                <a href="{{ route('admin.murids.edit', $m) }}" class="action-dropdown-item">
                                    Edit Data
                                </a>
                                <form method="POST" action="{{ route('admin.users.toggle', $m->user) }}" style="display:block;margin:0">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="action-dropdown-item">
                                        {{ $m->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <div class="action-dropdown-divider"></div>
                                <button type="button" class="action-dropdown-item danger"
                                    onclick="openDeleteModal('{{ route('admin.murids.destroy', $m) }}','{{ addslashes($m->nama_murid) }}')">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="empty-state-title">Belum ada data murid.</div>
                        <div class="empty-state-description">Mulai dengan menambahkan murid baru ke dalam sistem.</div>
                        <a href="{{ route('admin.murids.create') }}" class="btn btn-yellow btn-sm">+ Tambah Murid</a>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($murids->hasPages())
        <div style="padding:0.75rem 1.5rem;border-top:1px solid var(--topbar-border)">
            {{ $murids->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════
     MODAL DETAIL MURID
════════════════════════════════════════ --}}
<div id="murid-detail-backdrop"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:400;align-items:stretch;justify-content:flex-end"
     onclick="if(event.target===this) closeMuridDetail()">
    <div id="murid-detail-panel"
         style="width:26.5rem;max-width:95vw;background:var(--card-bg);height:100%;
                overflow-y:auto;box-shadow:var(--shadow-md);
                transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1)">

        {{-- Header --}}
        <div style="padding:0.75rem 1.5rem;display:flex;justify-content:space-between;align-items:center;
                    border-bottom:1px solid var(--topbar-border);">
            <div style="font-size:1rem;font-weight:700">Detail Murid</div>
            <button onclick="closeMuridDetail()"
                    style="border:none;background:none;cursor:pointer;font-size:1.25rem;
                            color:var(--text-light);width:2rem;height:2rem;border-radius:0.25rem;
                            display:flex;align-items:center;justify-content:center;transition:.15s"
                    onmouseover="this.style.background='var(--bg-light)'"
                    onmouseout="this.style.background='none'">
                ✕
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.25rem 2.5rem">

            {{-- Avatar & Nama --}}
            <div style="text-align:center;margin-bottom:1.25rem">
                <div id="md-foto-wrap" style="margin:0 auto 0.75rem;width:4.5rem;height:4.5rem;border-radius:0.25rem;
                            overflow:hidden;border:1px solid var(--topbar-border);
                            display:flex;align-items:center;justify-content:center;
                            background:var(--primary-navy);font-size:1.6rem;font-weight:700;color:#fff">
                </div>
                <div id="md-nama"   style="font-size:1.1rem;font-weight:700;margin-bottom:0.25rem"></div>
                <div id="md-username" style="font-size:.8rem;color:var(--text-light)"></div>
                <div id="md-status-badge" style="margin-top:0.5rem"></div>
            </div>

            {{-- Info Grid --}}
            <div style="display:flex;flex-direction:column;gap:0;border:1px solid var(--topbar-border);border-radius:0.25rem;overflow:hidden;margin-bottom:1.25rem">
                <div style="padding:0.75rem 1rem;font-size:.7rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:0.06em;
                            color:var(--text-light);background:var(--bg-light)">
                    Informasi Pribadi
                </div>
                <div class="md-row"><span class="md-label">Email</span><span id="md-email" class="md-value"></span></div>
                <div class="md-row"><span class="md-label">No. HP</span><span id="md-hp" class="md-value"></span></div>
                <div class="md-row"><span class="md-label">Tgl Lahir</span><span id="md-lahir" class="md-value"></span></div>
                <div class="md-row"><span class="md-label">Orang Tua</span><span id="md-ortu" class="md-value"></span></div>
                <div class="md-row" style="border-bottom:none"><span class="md-label">Alamat</span><span id="md-alamat" class="md-value"></span></div>
            </div>

            {{-- Statistik SPP --}}
            <div style="border:1px solid var(--topbar-border);border-radius:0.25rem;overflow:hidden;margin-bottom:1.25rem">
                <div style="padding:0.75rem 1rem;font-size:.7rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:0.06em;
                            color:var(--text-light);background:var(--bg-light)">
                    Statistik SPP
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0">
                    <div style="padding:0.875rem;text-align:center;border-right:1px solid var(--topbar-border)">
                        <div id="md-total-spp" style="font-size:1.5rem;font-weight:700;color:var(--primary-navy);font-variant-numeric:tabular-nums"></div>
                        <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-light);margin-top:0.125rem">Total Tagihan</div>
                    </div>
                    <div style="padding:0.875rem;text-align:center">
                        <div id="md-spp-lunas" style="font-size:1.5rem;font-weight:700;color:#15803d;font-variant-numeric:tabular-nums"></div>
                        <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-light);margin-top:0.125rem">Sudah Lunas</div>
                    </div>
                </div>
            </div>

            {{-- Info Akun --}}
            <div style="border:1px solid var(--topbar-border);border-radius:0.25rem;overflow:hidden">
                <div style="padding:0.75rem 1rem;font-size:.7rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:0.06em;
                            color:var(--text-light);background:var(--bg-light)">
                    Info Akun
                </div>
                <div class="md-row" style="border-bottom:none">
                    <span class="md-label">Bergabung</span>
                    <span id="md-bergabung" class="md-value"></span>
                </div>
            </div>

        </div>{{-- end body --}}
    </div>
</div>

{{-- Delete Modal --}}
<div class="delete-modal-backdrop" id="delete-modal-backdrop">
    <div class="delete-modal">
        <h3>Hapus Akun Murid?</h3>
        <p>Aksi ini akan menghapus akun <strong id="delete-modal-name"></strong> secara permanen beserta semua data terkait. Tindakan ini tidak dapat dibatalkan.</p>
        <div class="delete-modal-actions">
            <button class="btn btn-outline btn-sm" onclick="closeDeleteModal()">Batal</button>
            <form id="delete-modal-form" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<style>
.md-row {
    display:flex;align-items:flex-start;gap:1rem;
    padding:0.5rem 1rem;border-bottom:1px solid var(--topbar-border);
    font-size:.85rem;
}
.md-label {
    width:7rem;flex-shrink:0;color:var(--text-light);font-size:.8rem;
    display:flex;align-items:center;gap:0.25rem;padding-top:0.0625rem;
}
.md-label i { width:1rem;text-align:center; }
.md-value { flex:1;font-weight:500;color:var(--text-dark);word-break:break-word; }
</style>

@endsection

@push('scripts')
<script>
function openMuridDetail(data) {
    // Foto
    const fotoWrap = document.getElementById('md-foto-wrap');
    if (data.foto) {
        fotoWrap.innerHTML = `<img src="${data.foto}" style="width:100%;height:100%;object-fit:cover">`;
    } else {
        fotoWrap.textContent = data.nama.charAt(0).toUpperCase();
    }

    // Nama & username
    document.getElementById('md-nama').textContent     = data.nama;
    document.getElementById('md-username').textContent = '@' + data.username;

    // Badge status
    const badge = document.getElementById('md-status-badge');
    badge.innerHTML = data.status_aktif
        ? '<span class="badge badge-success" style="font-size:.75rem;padding:0.25rem 1rem">Aktif</span>'
        : '<span class="badge badge-gray"    style="font-size:.75rem;padding:0.25rem 1rem">Non-aktif</span>';

    // Info pribadi
    document.getElementById('md-email').textContent   = data.email;
    document.getElementById('md-hp').textContent      = data.nomor_hp;
    document.getElementById('md-lahir').textContent   = data.tanggal_lahir;
    document.getElementById('md-ortu').textContent    = data.nama_orang_tua;
    document.getElementById('md-alamat').textContent  = data.alamat;

    // Statistik
    document.getElementById('md-total-spp').textContent  = data.total_spp;
    document.getElementById('md-spp-lunas').textContent   = data.spp_lunas;

    // Akun
    document.getElementById('md-bergabung').textContent = data.bergabung;

    // Tampilkan
    const backdrop = document.getElementById('murid-detail-backdrop');
    const panel    = document.getElementById('murid-detail-panel');
    backdrop.style.display = 'flex';
    requestAnimationFrame(() => { panel.style.transform = 'translateX(0)'; });
}

function closeMuridDetail() {
    const panel = document.getElementById('murid-detail-panel');
    panel.style.transform = 'translateX(100%)';
    setTimeout(() => { document.getElementById('murid-detail-backdrop').style.display = 'none'; }, 300);
}

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