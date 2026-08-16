@extends('layouts.app')
@section('title', 'Tagihan SPP')
@section('page-title', 'Tagihan SPP')

@section('breadcrumb')
    <span class="crumb-root">Keuangan</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Tagihan SPP</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')

{{-- ══ Page Header ══════════════════════════════════════════════════════════ --}}
<div class="page-header">
    <h2>Tagihan SPP</h2>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.625rem;align-items:center;flex-wrap:wrap">
            <input type="month" name="bulan" class="form-control form-control-sm"
                value="{{ request('bulan', now()->format('Y-m')) }}"/>
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="Belum Lunas" {{ request('status')=='Belum Lunas'?'selected':'' }}>Belum Lunas</option>
                <option value="Lunas"       {{ request('status')=='Lunas'?'selected':'' }}>Lunas</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">
                Filter
            </button>
            <a href="{{ route('admin.spp.index') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>
</div>

{{-- ══ Ringkasan Open KPI Strips ════════════════════════════════════════════ --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(14rem,1fr));margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-value">Rp {{ number_format($totalTagihan,0,',','.') }}</div>
        <div class="stat-label">Total Tagihan</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">Rp {{ number_format($totalMasuk,0,',','.') }}</div>
        <div class="stat-label">Sudah Masuk</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">Rp {{ number_format($totalTunggakan,0,',','.') }}</div>
        <div class="stat-label">Tunggakan</div>
    </div>
</div>

{{-- ══ Tabel Utama ══════════════════════════════════════════════════════════ --}}
<div class="card">
    <div class="table-wrap">
        <table style="table-layout:fixed;width:100%">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:25%">Nama Siswa</th>
                    <th style="width:14%">Bulan</th>
                    <th style="width:20%">Program Kursus</th>
                    <th style="width:14%">Jatuh Tempo</th>
                    <th style="width:11%">Status</th>
                    <th style="width:12%;text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($spps as $i => $spp)
                @php
                    $isOverdue      = $spp->status_bayar !== 'Lunas' && $spp->tanggal_jatuh_tempo->isPast();
                    $transaksi      = $spp->transaksi;
                    $buktiUrl       = $transaksi ? route('admin.transaksi.bukti', $transaksi) : '';
                    $buktiNominal   = $transaksi ? number_format($transaksi->nominal_bayar ?? 0, 0, ',', '.') : '0';
                    $buktiTanggal   = $transaksi && $transaksi->tanggal_bayar ? $transaksi->tanggal_bayar->format('d/m/Y') : '-';
                    $buktiTipe      = $transaksi && str_ends_with($transaksi->file_bukti_transfer ?? '', '.pdf') ? 'pdf' : 'img';
                    $periodeLabel   = \Carbon\Carbon::parse($spp->periode_tagihan)->translatedFormat('F Y');
                    $namaMurid      = $spp->murid->nama_murid ?? '-';
                @endphp
                <tr>
                    <td style="color:var(--text-light);font-size:.82rem">
                        {{ $spps->firstItem() + $i }}
                    </td>

                    <td>
                        <div style="font-weight:600;color:var(--text-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $namaMurid }}
                        </div>
                        <div style="font-size:.72rem;color:var(--text-light);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $spp->murid->user->email ?? '' }}
                        </div>
                    </td>

                    <td style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $periodeLabel }}</td>

                    <td>
                        <span style="font-size:.82rem;font-weight:600">{{ $spp->programKursus->nama_program ?? '—' }}</span>
                        <div style="font-size:.72rem;color:var(--text-light);font-variant-numeric:tabular-nums">
                            Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}
                        </div>
                    </td>

                    <td>
                        <span style="{{ $isOverdue ? 'color:#dc2626;font-weight:600;' : '' }}">
                            {{ $spp->tanggal_jatuh_tempo->format('d/m/Y') }}
                        </span>
                        @if($isOverdue)
                            <div style="font-size:.68rem;color:#dc2626;font-weight:700">
                                TERLAMBAT
                            </div>
                        @endif
                    </td>

                    <td>
                        @if($spp->status_bayar === 'Lunas')
                            <span class="badge badge-success">LUNAS</span>
                        @elseif($transaksi)
                            <span class="badge badge-warning">MENUNGGU</span>
                        @else
                            <span class="badge badge-danger">BELUM LUNAS</span>
                        @endif
                    </td>

                    <td style="text-align:right">
                        <div class="action-dropdown-wrap">
                            <button type="button" class="btn-action-dropdown" onclick="toggleActionDropdown(this, event)">
                                Kelola ▾
                            </button>
                            <div class="action-dropdown-menu">
                                @if($transaksi && $transaksi->file_bukti_transfer)
                                    <button type="button" class="action-dropdown-item"
                                        onclick="bukaModalBukti('{{ $buktiUrl }}', '{{ $namaMurid }}', '{{ $buktiNominal }}', '{{ $buktiTanggal }}', '{{ $buktiTipe }}')">
                                        Lihat Bukti Transfer
                                    </button>
                                @endif
                                @if($spp->status_bayar !== 'Lunas' && $transaksi)
                                    <button type="button" class="action-dropdown-item"
                                        onclick="bukaModalAksi({{ $spp->id_spp }}, '{{ $namaMurid }}', '{{ $periodeLabel }}')">
                                        Konfirmasi Pembayaran
                                    </button>
                                @endif
                                @if($spp->status_bayar !== 'Lunas')
                                    <button type="button" class="action-dropdown-item"
                                        onclick="bukaModalNotifikasi({{ $spp->id_spp }}, '{{ $namaMurid }}', '{{ $periodeLabel }}', '{{ number_format($spp->nominal_tagihan, 0, ',', '.') }}', '{{ $spp->tanggal_jatuh_tempo->format('d/m/Y') }}')">
                                        Kirim Notifikasi
                                    </button>
                                @else
                                    <div class="action-dropdown-item" style="color:var(--text-light);cursor:default">
                                        Transaksi Selesai
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="empty-state-title">Tidak ada data tagihan SPP.</div>
                        <div class="empty-state-description">Tidak ada tagihan yang sesuai dengan filter yang dipilih.</div>
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($spps->hasPages())
        <div style="padding:0.75rem 1.25rem;border-top:1px solid var(--topbar-border)">
            {{ $spps->links() }}
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: Bukti Transfer
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-bukti" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:300;align-items:center;justify-content:center;padding:1.5rem">
    <div class="card" style="width:32.5rem;max-width:95vw;max-height:90vh;display:flex;flex-direction:column">

        {{-- Header --}}
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
            <h3 style="margin:0;font-size:1rem">
                Detail Bukti Transfer
            </h3>
            <button onclick="tutupModal('modal-bukti')"
                style="background:none;border:none;font-size:1.25rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.25rem 1.25rem;overflow-y:auto;flex:1">

            {{-- Info Ringkas --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1.25rem">
                <div style="background:var(--bg-light,#f8fafc);border-radius:0.25rem;padding:0.75rem 1rem">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:0.25rem">Nama Wali Murid / Siswa</div>
                    <div id="bukti-nama" style="font-weight:600;font-size:.9rem">—</div>
                </div>
                <div style="background:var(--bg-light,#f8fafc);border-radius:0.25rem;padding:0.75rem 1rem">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:0.25rem">Tanggal Bayar</div>
                    <div id="bukti-tanggal" style="font-weight:600;font-size:.9rem;font-variant-numeric:tabular-nums">—</div>
                </div>
                <div style="background:var(--bg-light,#f8fafc);border-radius:0.25rem;padding:0.75rem 1rem;grid-column:1/-1">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:0.25rem">Nominal Dibayar</div>
                    <div id="bukti-nominal" style="font-weight:700;font-size:1.1rem;color:var(--primary-navy);font-variant-numeric:tabular-nums">—</div>
                </div>
            </div>

            {{-- Preview Bukti --}}
            <div style="border:1px solid var(--border);border-radius:0.25rem;overflow:hidden;background:#f1f5f9">
                <div style="padding:0.5rem 1rem;background:var(--bg-light,#f8fafc);border-bottom:1px solid var(--border);font-size:.78rem;color:var(--text-light);display:flex;justify-content:space-between;align-items:center">
                    <span>Lampiran Bukti</span>
                    <a id="bukti-link-download" href="#" target="_blank" class="btn btn-sm btn-outline" style="font-size:.72rem;padding:0.25rem 0.5rem">
                        Buka
                    </a>
                </div>
                <div style="padding:1rem;text-align:center">
                    <img id="bukti-img" src="" alt="Bukti Transfer"
                        style="max-width:100%;max-height:20rem;object-fit:contain;border-radius:0.25rem;display:none"/>
                    <div id="bukti-pdf-notice" style="display:none;padding:1.5rem;color:var(--text-light);font-size:0.85rem">
                        File PDF &mdash; klik tombol <strong>Buka</strong> untuk melihat.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: Aksi (Validasi / Tolak + Catatan opsional)
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-aksi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:300;align-items:center;justify-content:center;padding:1.5rem">
    <div class="card" style="width:29rem;max-width:95vw">

        {{-- Header --}}
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <h3 style="margin:0;font-size:1rem">
                Tindakan Pembayaran
            </h3>
            <button onclick="tutupModal('modal-aksi')"
                style="background:none;border:none;font-size:1.25rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        <div style="padding:1.25rem 1.25rem">

            {{-- Info SPP --}}
            <div style="background:var(--bg-light,#f8fafc);border-radius:0.25rem;padding:0.75rem 1rem;margin-bottom:1.25rem">
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:0.25rem">Memproses pembayaran untuk:</div>
                <div id="aksi-nama" style="font-weight:700;font-size:1rem">—</div>
                <div id="aksi-bulan" style="color:var(--text-light);font-size:.82rem;margin-top:0.125rem">—</div>
            </div>

            {{-- Form Validasi --}}
            <form id="form-validasi" method="POST" action="">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label class="form-label">
                        Catatan Admin
                        <span style="color:var(--text-light);font-weight:400;font-size:.8rem;margin-left:0.25rem">— opsional</span>
                    </label>
                    <textarea
                        name="catatan_admin"
                        class="form-control"
                        rows="3"
                        placeholder="Contoh: Sudah dikonfirmasi via rekening BCA atas nama …"
                        style="resize:vertical"
                    ></textarea>
                </div>

                <div style="display:flex;gap:0.5rem;margin-top:1rem">
                    <button type="submit" class="btn btn-primary btn-sm" style="flex:1">
                        Konfirmasi Lunas
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="tutupModal('modal-aksi')">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: Kirim Notifikasi
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-notifikasi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:300;align-items:center;justify-content:center;padding:1.5rem">
    <div class="card" style="width:29rem;max-width:95vw">

        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <h3 style="margin:0;font-size:1rem">
                Kirim Notifikasi Tagihan
            </h3>
            <button onclick="tutupModal('modal-notifikasi')"
                style="background:none;border:none;font-size:1.25rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        <div style="padding:1.25rem 1.25rem">

            {{-- Info penerima --}}
            <div style="background:var(--bg-light,#f8fafc);border-radius:0.25rem;padding:0.75rem 1rem;margin-bottom:1.25rem">
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:0.25rem">Penerima notifikasi:</div>
                <div id="notif-nama" style="font-weight:700;font-size:1rem">—</div>
                <div id="notif-bulan" style="color:var(--text-light);font-size:.82rem;margin-top:0.125rem">—</div>
            </div>

            <form id="form-notifikasi" method="POST" action="">
                @csrf

                <div class="form-group">
                    <label class="form-label">
                        Pesan Notifikasi
                        <span style="color:var(--text-light);font-weight:400;font-size:.8rem;margin-left:0.25rem">— kosongkan untuk pesan otomatis</span>
                    </label>
                    <textarea
                        id="notif-pesan"
                        name="pesan"
                        class="form-control"
                        rows="4"
                        style="resize:vertical"
                        placeholder="Pesan akan terisi otomatis jika dikosongkan…"
                    ></textarea>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">
                        Pesan otomatis: <em id="notif-pesan-preview">—</em>
                    </div>
                </div>

                <div style="display:flex;gap:0.5rem;margin-top:1rem">
                    <button type="submit" class="btn btn-primary btn-sm" style="flex:1">
                        Kirim Notifikasi
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="tutupModal('modal-notifikasi')">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ Script ══════════════════════════════════════════════════════════════ --}}
<script>
// ── Helpers ────────────────────────────────────────────────────────────────
function tutupModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Tutup modal saat klik backdrop
document.querySelectorAll('[id^="modal-"]').forEach(function(modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === modal) tutupModal(modal.id);
    });
});

// ── Modal Bukti Transfer ───────────────────────────────────────────────────
function bukaModalBukti(url, nama, nominal, tanggal, tipe) {
    document.getElementById('bukti-nama').textContent    = nama    || '—';
    document.getElementById('bukti-nominal').textContent = 'Rp ' + nominal;
    document.getElementById('bukti-tanggal').textContent = tanggal || '—';
    document.getElementById('bukti-link-download').href  = url;

    var img   = document.getElementById('bukti-img');
    var pdf   = document.getElementById('bukti-pdf-notice');

    if (tipe === 'pdf') {
        img.style.display = 'none';
        pdf.style.display = 'block';
    } else {
        img.src           = url;
        img.style.display = 'block';
        pdf.style.display = 'none';
    }

    document.getElementById('modal-bukti').style.display = 'flex';
}

// ── Modal Notifikasi ───────────────────────────────────────────────────────
function bukaModalNotifikasi(idSpp, nama, bulan, nominal, jatuhTempo) {
    document.getElementById('notif-nama').textContent  = nama  || '—';
    document.getElementById('notif-bulan').textContent = bulan || '—';

    // Preview pesan otomatis
    var pesanOtomatis = 'Halo ' + nama + ', mohon segera melunasi tagihan SPP bulan ' + bulan
        + ' sebesar Rp ' + nominal + ' sebelum ' + jatuhTempo + '.';
    document.getElementById('notif-pesan-preview').textContent = pesanOtomatis;

    // Set action URL
    var baseUrl = '{{ url("admin/spp") }}';
    document.getElementById('form-notifikasi').action = baseUrl + '/' + idSpp + '/notifikasi';

    // Reset textarea
    document.getElementById('notif-pesan').value = '';

    document.getElementById('modal-notifikasi').style.display = 'flex';
}


function bukaModalAksi(idSpp, nama, bulan) {
    document.getElementById('aksi-nama').textContent  = nama  || '—';
    document.getElementById('aksi-bulan').textContent = bulan || '—';

    // Set action URL untuk validasi (PATCH ke admin/spp/{id}/validasi)
    var baseUrl = '{{ url("admin/spp") }}';
    document.getElementById('form-validasi').action = baseUrl + '/' + idSpp + '/validasi';

    // Reset textarea catatan setiap buka modal
    document.querySelector('#form-validasi textarea[name="catatan_admin"]').value = '';

    document.getElementById('modal-aksi').style.display = 'flex';
}
</script>

@endsection