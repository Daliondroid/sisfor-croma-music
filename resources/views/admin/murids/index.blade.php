@extends('layouts.app')
@section('title', 'Data Murid')
@section('page-title', 'Data Murid')

@section('sidebar-menu')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <h2>Data Murid</h2>
    <div class="breadcrumb">Admin / <span>Murid</span></div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama atau email..." value="{{ request('search') }}"/>
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status')=='1'?'selected':'' }}>Aktif</option>
                <option value="0" {{ request('status')=='0'?'selected':'' }}>Non-aktif</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-search"></i> Cari
            </button>
        </form>
        <a href="{{ route('admin.murids.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Tambah Murid
        </a>
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
                        <div style="display:flex;align-items:center;gap:0.5rem">
                            <div class="avatar" style="width:2rem;height:2rem;overflow:hidden;border-radius:50%;flex-shrink:0">
                                @if($m->user->foto_profil)
                                    <img src="{{ asset('storage/' . $m->user->foto_profil) }}" style="width:100%;height:100%;object-fit:cover">
                                @else
                                    {{ strtoupper(substr($m->nama_murid,0,1)) }}
                                @endif
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
                        <div style="font-size:.72rem;color:var(--text-light)">{{ '@' . $m->user->username }}</div>
                    </td>
                    <td>{{ $m->nama_orang_tua ?? '-' }}</td>
                    <td>{{ $m->nomor_hp ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $m->status_aktif ? 'badge-success' : 'badge-gray' }}">
                            {{ $m->status_aktif ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:0.25rem">
                            {{-- Tombol Detail --}}
                            <button class="btn btn-sm btn-outline" title="Detail"
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
                                    'total_spp'    => $m->spps->count(),
                                    'spp_lunas'    => $m->spps->where('status_bayar','Lunas')->count(),
                                ]) }})">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <a href="{{ route('admin.murids.edit', $m) }}" class="btn btn-sm btn-outline" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.toggle', $m->user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $m->status_aktif ? 'btn-danger' : 'btn-primary' }}" title="{{ $m->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fa-solid {{ $m->status_aktif ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>
                            <button class="btn btn-sm btn-danger" title="Hapus Akun"
                                onclick="openDeleteModal('{{ route('admin.murids.destroy', $m) }}','{{ addslashes($m->nama_murid) }}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="40" cy="28" r="12" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M18 68c0-12.15 9.85-22 22-22s22 9.85 22 22" stroke="var(--primary-blue)" stroke-width="2" fill="none"/><circle cx="58" cy="24" r="8" stroke="var(--primary-blue)" stroke-width="1.5" fill="var(--sidebar-active-bg)" opacity=".5"/><path d="M50 58c0-8 4.5-14 12-16" stroke="var(--primary-blue)" stroke-width="1.5" opacity=".5"/></svg>
                        </div>
                        <div class="empty-state-title">Belum ada data murid.</div>
                        <div class="empty-state-description">Mulai dengan menambahkan murid baru ke dalam sistem.</div>
                        <a href="{{ route('admin.murids.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Murid</a>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($murids->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--topbar-border)">
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
        <div style="padding:1.5rem 1.5rem 0;display:flex;justify-content:space-between;align-items:center;
                    border-bottom:1px solid var(--topbar-border);padding-bottom:1rem;
                    position:sticky;top:0;background:var(--card-bg);z-index:1">
            <div style="font-size:1rem;font-weight:700">Detail Murid</div>
            <button onclick="closeMuridDetail()"
                    style="background:none;border:none;cursor:pointer;font-size:1.3rem;
                           color:var(--text-light);width:2rem;height:2rem;border-radius:50%;
                           display:flex;align-items:center;justify-content:center;transition:.15s"
                    onmouseover="this.style.background='var(--bg-light)'"
                    onmouseout="this.style.background='none'">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.5rem">

            {{-- Avatar & Nama --}}
            <div style="text-align:center;margin-bottom:1.5rem">
                <div id="md-foto-wrap" style="margin:0 auto 1rem;width:5rem;height:5rem;border-radius:50%;
                            overflow:hidden;border:0.1875rem solid var(--primary-blue);
                            display:flex;align-items:center;justify-content:center;
                            background:var(--primary-blue);font-size:1.8rem;font-weight:700;color:#fff">
                </div>
                <div id="md-nama"   style="font-size:1.15rem;font-weight:700;margin-bottom:0.25rem"></div>
                <div id="md-username" style="font-size:.8rem;color:var(--text-light)"></div>
                <div id="md-status-badge" style="margin-top:0.5rem"></div>
            </div>

            {{-- Info Grid --}}
            <div style="display:flex;flex-direction:column;gap:0;border:1px solid var(--topbar-border);border-radius:0.625rem;overflow:hidden;margin-bottom:1.5rem">
                <div style="padding:1rem 1rem;font-size:.72rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:0.05rem;
                            color:var(--text-light);background:var(--bg-light)">
                    Informasi Pribadi
                </div>
                <div class="md-row"><span class="md-label"><i class="fa-solid fa-envelope"></i> Email</span><span id="md-email" class="md-value"></span></div>
                <div class="md-row"><span class="md-label"><i class="fa-solid fa-phone"></i> No. HP</span><span id="md-hp" class="md-value"></span></div>
                <div class="md-row"><span class="md-label"><i class="fa-solid fa-cake-candles"></i> Tgl Lahir</span><span id="md-lahir" class="md-value"></span></div>
                <div class="md-row"><span class="md-label"><i class="fa-solid fa-user-group"></i> Orang Tua</span><span id="md-ortu" class="md-value"></span></div>
                <div class="md-row" style="border-bottom:none"><span class="md-label"><i class="fa-solid fa-location-dot"></i> Alamat</span><span id="md-alamat" class="md-value"></span></div>
            </div>

            {{-- Statistik SPP --}}
            <div style="border:1px solid var(--topbar-border);border-radius:0.625rem;overflow:hidden;margin-bottom:1.5rem">
                <div style="padding:1rem 1rem;font-size:.72rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:0.05rem;
                            color:var(--text-light);background:var(--bg-light)">
                    Statistik SPP
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0">
                    <div style="padding:1rem;text-align:center;border-right:1px solid var(--topbar-border)">
                        <div id="md-total-spp" style="font-size:1.6rem;font-weight:700;color:var(--primary-blue)"></div>
                        <div style="font-size:.72rem;color:var(--text-light);margin-top:0.125rem">Total Tagihan</div>
                    </div>
                    <div style="padding:1rem;text-align:center">
                        <div id="md-spp-lunas" style="font-size:1.6rem;font-weight:700;color:#16a34a"></div>
                        <div style="font-size:.72rem;color:var(--text-light);margin-top:0.125rem">Sudah Lunas</div>
                    </div>
                </div>
            </div>

            {{-- Info Akun --}}
            <div style="border:1px solid var(--topbar-border);border-radius:0.625rem;overflow:hidden">
                <div style="padding:1rem 1rem;font-size:.72rem;font-weight:700;
                            text-transform:uppercase;letter-spacing:0.05rem;
                            color:var(--text-light);background:var(--bg-light)">
                    Info Akun
                </div>
                <div class="md-row" style="border-bottom:none">
                    <span class="md-label"><i class="fa-solid fa-calendar-plus"></i> Bergabung</span>
                    <span id="md-bergabung" class="md-value"></span>
                </div>
            </div>

        </div>{{-- end body --}}
    </div>
</div>

{{-- Delete Modal --}}
<div class="delete-modal-backdrop" id="delete-modal-backdrop">
    <div class="delete-modal">
        <div class="delete-modal-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3>Hapus Akun Murid?</h3>
        <p>Aksi ini akan menghapus akun <strong id="delete-modal-name"></strong> secara permanen beserta semua data terkait. Tindakan ini tidak dapat dibatalkan.</p>
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