@extends('layouts.app')
@section('title', 'Tagihan SPP')
@section('page-title', 'Tagihan SPP')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')

{{-- ══ Page Header ══════════════════════════════════════════════════════════ --}}
<div class="page-header">
    <h2>Tagihan SPP</h2>
    <div class="breadcrumb">Admin / <span>SPP</span></div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            <input type="month" name="bulan" class="form-control form-control-sm"
                value="{{ request('bulan', now()->format('Y-m')) }}"/>
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="Belum Lunas" {{ request('status')=='Belum Lunas'?'selected':'' }}>Belum Lunas</option>
                <option value="Lunas"       {{ request('status')=='Lunas'?'selected':'' }}>Lunas</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-search"></i> Filter
            </button>
            <a href="{{ route('admin.spp.index') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>
</div>

{{-- ══ Ringkasan ════════════════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
    <div class="card" style="padding:1.5rem 1.5rem;border-left:0.25rem solid var(--primary)">
        <div style="font-size:.75rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.05em;margin-bottom:0.25rem">Total Tagihan</div>
        <div style="font-size:1.4rem;font-weight:700;color:var(--text-main)">Rp {{ number_format($totalTagihan,0,',','.') }}</div>
    </div>
    <div class="card" style="padding:1.5rem 1.5rem;">
        <div style="font-size:.75rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.05em;margin-bottom:0.25rem">Sudah Masuk</div>
        <div style="font-size:1.4rem;font-weight:700;color:var(--text-main)">Rp {{ number_format($totalMasuk,0,',','.') }}</div>
    </div>
    <div class="card" style="padding:1.5rem 1.5rem;">
        <div style="font-size:.75rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.05em;margin-bottom:0.25rem">Tunggakan</div>
        <div style="font-size:1.4rem;font-weight:700;color:var(--text-main)">Rp {{ number_format($totalTunggakan,0,',','.') }}</div>
    </div>
</div>

{{-- ══ Tabel Utama ══════════════════════════════════════════════════════════ --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:2.5rem">#</th>
                    <th>Nama Siswa</th>
                    <th>Bulan</th>
                    <th>Program Kursus</th>
                    <th>Jatuh Tempo</th>
                    <th style="width:7rem">Status</th>
                    <th style="width:5rem;text-align:center">Bukti</th>
                    <th style="width:5rem;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($spps as $i => $spp)
                {{-- Persiapkan variabel agar onclick bersih tanpa escape rumit --}}
                @php
                    $isOverdue      = $spp->status_bayar !== 'Lunas' && $spp->tanggal_jatuh_tempo->isPast();
                    $transaksi      = $spp->transaksi;
                    $buktiUrl       = $transaksi ? asset('storage/' . $transaksi->file_bukti_transfer) : '';
                    $buktiNominal   = $transaksi ? number_format($transaksi->nominal_bayar ?? 0, 0, ',', '.') : '0';
                    $buktiTanggal   = $transaksi && $transaksi->tanggal_bayar ? $transaksi->tanggal_bayar->format('d/m/Y') : '-';
                    $buktiTipe      = $transaksi && str_ends_with($transaksi->file_bukti_transfer ?? '', '.pdf') ? 'pdf' : 'img';
                    $periodeLabel   = \Carbon\Carbon::parse($spp->periode_tagihan)->translatedFormat('F Y');
                    $namaMurid      = $spp->murid->nama_murid ?? '-';
                @endphp
                <tr>
                    {{-- Nomor --}}
                    <td style="color:var(--text-light);font-size:.82rem">
                        {{ $spps->firstItem() + $i }}
                    </td>

                    {{-- Nama Siswa --}}
                    <td>
                        <div style="font-weight:600;color:var(--text-main)">
                            {{ $namaMurid }}
                        </div>
                        <div style="font-size:.75rem;color:var(--text-light)">
                            {{ $spp->murid->user->email ?? '' }}
                        </div>
                    </td>

                    {{-- Bulan --}}
                    <td>{{ $periodeLabel }}</td>

                    {{-- Program Kursus --}}
                    <td>
                        <span style="font-size:.82rem">{{ $spp->programKursus->nama_program ?? '—' }}</span>
                        <div style="font-size:.72rem;color:var(--text-light)">
                            Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}
                        </div>
                    </td>

                    {{-- Jatuh Tempo --}}
                    <td>
                        <span style="{{ $isOverdue ? 'color:#dc2626;font-weight:600' : '' }}">
                            {{ $spp->tanggal_jatuh_tempo->format('d/m/Y') }}
                        </span>
                        @if($isOverdue)
                            <div style="font-size:.7rem;color:#dc2626">
                                <i class="fa-solid fa-triangle-exclamation"></i> Terlambat
                            </div>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($spp->status_bayar === 'Lunas')
                            <span class="badge badge-success">
                                <i class="fa-solid fa-circle-check" style="margin-right:0.25rem"></i>Lunas
                            </span>
                        @elseif($transaksi)
                            <span class="badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a">
                                <i class="fa-solid fa-clock" style="margin-right:0.25rem"></i>Menunggu
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fa-solid fa-circle-xmark" style="margin-right:0.25rem"></i>Belum Lunas
                            </span>
                        @endif
                    </td>

                    {{-- Bukti Transfer --}}
                    <td style="text-align:center">
                        @if($transaksi && $transaksi->file_bukti_transfer)
                            <button
                                class="btn btn-sm btn-outline"
                                title="Lihat Bukti Transfer"
                                onclick="bukaModalBukti('{{ $buktiUrl }}', '{{ $namaMurid }}', '{{ $buktiNominal }}', '{{ $buktiTanggal }}', '{{ $buktiTipe }}')"
                            >
                                <i class="fa-solid fa-receipt"></i>
                            </button>
                        @else
                            <span style="color:var(--text-light);font-size:.8rem">—</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td style="text-align:center">
                        @if($spp->status_bayar !== 'Lunas' && $transaksi)
                            {{-- Ada bukti transfer: tombol validasi + notifikasi --}}
                            <div style="display:flex;gap:0.25rem;justify-content:center">
                                <button
                                    class="btn btn-sm btn-primary"
                                    title="Konfirmasi Pembayaran"
                                    onclick="bukaModalAksi({{ $spp->id_spp }}, '{{ $namaMurid }}', '{{ $periodeLabel }}')"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-outline"
                                    title="Kirim Notifikasi ke Murid"
                                    onclick="bukaModalNotifikasi({{ $spp->id_spp }}, '{{ $namaMurid }}', '{{ $periodeLabel }}', '{{ number_format($spp->nominal_tagihan, 0, ',', '.') }}', '{{ $spp->tanggal_jatuh_tempo->format('d/m/Y') }}')"
                                >
                                    <i class="fa-solid fa-bell"></i>
                                </button>
                            </div>
                        @elseif($spp->status_bayar !== 'Lunas')
                            {{-- Belum ada bukti: hanya tombol notifikasi --}}
                            <button
                                class="btn btn-sm btn-outline"
                                title="Kirim Notifikasi ke Murid"
                                onclick="bukaModalNotifikasi({{ $spp->id_spp }}, '{{ $namaMurid }}', '{{ $periodeLabel }}', '{{ number_format($spp->nominal_tagihan, 0, ',', '.') }}', '{{ $spp->tanggal_jatuh_tempo->format('d/m/Y') }}')"
                            >
                                <i class="fa-solid fa-bell"></i>
                            </button>
                        @else
                            <span style="color:#16a34a;font-size:.8rem">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="16" y="12" width="48" height="56" rx="4" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M28 28h24M28 38h16M28 48h20" stroke="var(--primary-blue)" stroke-width="2" stroke-linecap="round" opacity=".5"/><circle cx="56" cy="56" r="14" stroke="var(--primary-blue)" stroke-width="2" fill="var(--card-bg)"/><text x="56" y="61" text-anchor="middle" font-size="14" font-weight="700" fill="var(--primary-blue)" font-family="Poppins">$</text></svg>
                        </div>
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
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--border)">
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
                <i class="fa-solid fa-receipt" style="margin-right:0.5rem;color:var(--primary)"></i>
                Detail Bukti Transfer
            </h3>
            <button onclick="tutupModal('modal-bukti')"
                style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:1.5rem 1.5rem;overflow-y:auto;flex:1">

            {{-- Info Ringkas --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">
                <div style="background:var(--bg-light,#f8fafc);border-radius:0.5rem;padding:1rem 1rem">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:0.25rem">Nama Wali Murid / Siswa</div>
                    <div id="bukti-nama" style="font-weight:600;font-size:.9rem">—</div>
                </div>
                <div style="background:var(--bg-light,#f8fafc);border-radius:0.5rem;padding:1rem 1rem">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:0.25rem">Tanggal Bayar</div>
                    <div id="bukti-tanggal" style="font-weight:600;font-size:.9rem">—</div>
                </div>
                <div style="background:var(--bg-light,#f8fafc);border-radius:0.5rem;padding:1rem 1rem;grid-column:1/-1">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:0.25rem">Nominal Dibayar</div>
                    <div id="bukti-nominal" style="font-weight:700;font-size:1.1rem;color:var(--primary)">—</div>
                </div>
            </div>

            {{-- Preview Bukti --}}
            <div style="border:1px solid var(--border);border-radius:0.5rem;overflow:hidden;background:#f1f5f9">
                <div style="padding:0.5rem 1rem;background:var(--bg-light,#f8fafc);border-bottom:1px solid var(--border);font-size:.78rem;color:var(--text-light);display:flex;justify-content:space-between;align-items:center">
                    <span><i class="fa-solid fa-image" style="margin-right:0.25rem"></i>Lampiran Bukti</span>
                    <a id="bukti-link-download" href="#" target="_blank" class="btn btn-sm btn-outline" style="font-size:.72rem;padding:0.25rem 0.5rem">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka
                    </a>
                </div>
                <div style="padding:1rem;text-align:center">
                    <img id="bukti-img" src="" alt="Bukti Transfer"
                        style="max-width:100%;max-height:20rem;object-fit:contain;border-radius:0.25rem;display:none"/>
                    <div id="bukti-pdf-notice" style="display:none;padding:2rem;color:var(--text-light)">
                        <i class="fa-solid fa-file-pdf" style="font-size:2.5rem;display:block;margin-bottom:0.5rem;color:#ef4444"></i>
                        File PDF — klik tombol <strong>Buka</strong> untuk melihat.
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
                <i class="fa-solid fa-pen-to-square" style="margin-right:0.5rem;color:var(--primary)"></i>
                Tindakan Pembayaran
            </h3>
            <button onclick="tutupModal('modal-aksi')"
                style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        <div style="padding:1.5rem 1.5rem">

            {{-- Info SPP --}}
            <div style="background:var(--bg-light,#f8fafc);border-radius:0.5rem;padding:1rem 1rem;margin-bottom:1.5rem">
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
                    <button type="submit" class="btn btn-primary" style="flex:1">
                        <i class="fa-solid fa-circle-check"></i> Konfirmasi Lunas
                    </button>
                    <button type="button" class="btn btn-outline" onclick="tutupModal('modal-aksi')">
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
                <i class="fa-solid fa-bell" style="margin-right:0.5rem;color:var(--primary)"></i>
                Kirim Notifikasi Tagihan
            </h3>
            <button onclick="tutupModal('modal-notifikasi')"
                style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        <div style="padding:1.5rem 1.5rem">

            {{-- Info penerima --}}
            <div style="background:var(--bg-light,#f8fafc);border-radius:0.5rem;padding:1rem 1rem;margin-bottom:1.5rem">
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
                        <i class="fa-solid fa-circle-info" style="margin-right:0.25rem"></i>
                        Pesan otomatis: <em id="notif-pesan-preview">—</em>
                    </div>
                </div>

                <div style="display:flex;gap:0.5rem;margin-top:1rem">
                    <button type="submit" class="btn btn-primary" style="flex:1">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Notifikasi
                    </button>
                    <button type="button" class="btn btn-outline" onclick="tutupModal('modal-notifikasi')">
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