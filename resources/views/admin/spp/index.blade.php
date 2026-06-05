@extends('layouts.app')
@section('title', 'Tagihan SPP')
@section('page-title', 'Tagihan SPP')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')

{{-- ══ Page Header ══════════════════════════════════════════════════════════ --}}
<div class="page-header">
    <div>
        <h2>Tagihan SPP</h2>
        <div class="breadcrumb">Admin / <span>SPP</span></div>
    </div>
</div>

{{-- ══ Ringkasan ════════════════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
    <div class="card" style="padding:20px 24px;border-left:4px solid var(--primary)">
        <div style="font-size:.75rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Total Tagihan</div>
        <div style="font-size:1.4rem;font-weight:700;color:var(--text-main)">Rp {{ number_format($totalTagihan,0,',','.') }}</div>
    </div>
    <div class="card" style="padding:20px 24px;border-left:4px solid #22c55e">
        <div style="font-size:.75rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Sudah Masuk</div>
        <div style="font-size:1.4rem;font-weight:700;color:#16a34a">Rp {{ number_format($totalMasuk,0,',','.') }}</div>
    </div>
    <div class="card" style="padding:20px 24px;border-left:4px solid #ef4444">
        <div style="font-size:.75rem;color:var(--text-light);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Tunggakan</div>
        <div style="font-size:1.4rem;font-weight:700;color:#dc2626">Rp {{ number_format($totalTunggakan,0,',','.') }}</div>
    </div>
</div>

{{-- ══ Filter ═══════════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="margin:0">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control"
                    value="{{ request('bulan', now()->format('Y-m')) }}"/>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="Belum Lunas" {{ request('status')=='Belum Lunas'?'selected':'' }}>Belum Lunas</option>
                    <option value="Lunas"       {{ request('status')=='Lunas'?'selected':'' }}>Lunas</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-search"></i> Filter
            </button>
            <a href="{{ route('admin.spp.index') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

{{-- ══ Tabel Utama ══════════════════════════════════════════════════════════ --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Nama Siswa</th>
                    <th>Bulan</th>
                    <th>Program Kursus</th>
                    <th>Jatuh Tempo</th>
                    <th style="width:110px">Status</th>
                    <th style="width:80px;text-align:center">Bukti</th>
                    <th style="width:80px;text-align:center">Aksi</th>
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
                                <i class="fa-solid fa-circle-check" style="margin-right:4px"></i>Lunas
                            </span>
                        @elseif($transaksi)
                            <span class="badge" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a">
                                <i class="fa-solid fa-clock" style="margin-right:4px"></i>Menunggu
                            </span>
                        @else
                            <span class="badge badge-danger">
                                <i class="fa-solid fa-circle-xmark" style="margin-right:4px"></i>Belum Lunas
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
                            <div style="display:flex;gap:6px;justify-content:center">
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
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:var(--text-light)">
                        <i class="fa-solid fa-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4"></i>
                        Tidak ada data tagihan SPP untuk filter ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($spps->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--border)">
            {{ $spps->links() }}
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: Bukti Transfer
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-bukti" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:300;align-items:center;justify-content:center;padding:20px">
    <div class="card" style="width:520px;max-width:95vw;max-height:90vh;display:flex;flex-direction:column">

        {{-- Header --}}
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
            <h3 style="margin:0;font-size:1rem">
                <i class="fa-solid fa-receipt" style="margin-right:8px;color:var(--primary)"></i>
                Detail Bukti Transfer
            </h3>
            <button onclick="tutupModal('modal-bukti')"
                style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:20px 24px;overflow-y:auto;flex:1">

            {{-- Info Ringkas --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
                <div style="background:var(--bg-light,#f8fafc);border-radius:8px;padding:12px 14px">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:4px">Nama Wali Murid / Siswa</div>
                    <div id="bukti-nama" style="font-weight:600;font-size:.9rem">—</div>
                </div>
                <div style="background:var(--bg-light,#f8fafc);border-radius:8px;padding:12px 14px">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:4px">Tanggal Bayar</div>
                    <div id="bukti-tanggal" style="font-weight:600;font-size:.9rem">—</div>
                </div>
                <div style="background:var(--bg-light,#f8fafc);border-radius:8px;padding:12px 14px;grid-column:1/-1">
                    <div style="font-size:.7rem;text-transform:uppercase;color:var(--text-light);letter-spacing:.05em;margin-bottom:4px">Nominal Dibayar</div>
                    <div id="bukti-nominal" style="font-weight:700;font-size:1.1rem;color:var(--primary)">—</div>
                </div>
            </div>

            {{-- Preview Bukti --}}
            <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;background:#f1f5f9">
                <div style="padding:8px 14px;background:var(--bg-light,#f8fafc);border-bottom:1px solid var(--border);font-size:.78rem;color:var(--text-light);display:flex;justify-content:space-between;align-items:center">
                    <span><i class="fa-solid fa-image" style="margin-right:6px"></i>Lampiran Bukti</span>
                    <a id="bukti-link-download" href="#" target="_blank" class="btn btn-sm btn-outline" style="font-size:.72rem;padding:4px 10px">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka
                    </a>
                </div>
                <div style="padding:12px;text-align:center">
                    <img id="bukti-img" src="" alt="Bukti Transfer"
                        style="max-width:100%;max-height:320px;object-fit:contain;border-radius:4px;display:none"/>
                    <div id="bukti-pdf-notice" style="display:none;padding:32px;color:var(--text-light)">
                        <i class="fa-solid fa-file-pdf" style="font-size:2.5rem;display:block;margin-bottom:10px;color:#ef4444"></i>
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
<div id="modal-aksi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:300;align-items:center;justify-content:center;padding:20px">
    <div class="card" style="width:460px;max-width:95vw">

        {{-- Header --}}
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <h3 style="margin:0;font-size:1rem">
                <i class="fa-solid fa-pen-to-square" style="margin-right:8px;color:var(--primary)"></i>
                Tindakan Pembayaran
            </h3>
            <button onclick="tutupModal('modal-aksi')"
                style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        <div style="padding:20px 24px">

            {{-- Info SPP --}}
            <div style="background:var(--bg-light,#f8fafc);border-radius:8px;padding:14px 16px;margin-bottom:20px">
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:4px">Memproses pembayaran untuk:</div>
                <div id="aksi-nama" style="font-weight:700;font-size:1rem">—</div>
                <div id="aksi-bulan" style="color:var(--text-light);font-size:.82rem;margin-top:2px">—</div>
            </div>

            {{-- Form Validasi --}}
            <form id="form-validasi" method="POST" action="">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label class="form-label">
                        Catatan Admin
                        <span style="color:var(--text-light);font-weight:400;font-size:.8rem;margin-left:4px">— opsional</span>
                    </label>
                    <textarea
                        name="catatan_admin"
                        class="form-control"
                        rows="3"
                        placeholder="Contoh: Sudah dikonfirmasi via rekening BCA atas nama …"
                        style="resize:vertical"
                    ></textarea>
                </div>

                <div style="display:flex;gap:10px;margin-top:16px">
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
<div id="modal-notifikasi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:300;align-items:center;justify-content:center;padding:20px">
    <div class="card" style="width:460px;max-width:95vw">

        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <h3 style="margin:0;font-size:1rem">
                <i class="fa-solid fa-bell" style="margin-right:8px;color:var(--primary)"></i>
                Kirim Notifikasi Tagihan
            </h3>
            <button onclick="tutupModal('modal-notifikasi')"
                style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text-light);line-height:1">
                &times;
            </button>
        </div>

        <div style="padding:20px 24px">

            {{-- Info penerima --}}
            <div style="background:var(--bg-light,#f8fafc);border-radius:8px;padding:14px 16px;margin-bottom:20px">
                <div style="font-size:.75rem;color:var(--text-light);margin-bottom:4px">Penerima notifikasi:</div>
                <div id="notif-nama" style="font-weight:700;font-size:1rem">—</div>
                <div id="notif-bulan" style="color:var(--text-light);font-size:.82rem;margin-top:2px">—</div>
            </div>

            <form id="form-notifikasi" method="POST" action="">
                @csrf

                <div class="form-group">
                    <label class="form-label">
                        Pesan Notifikasi
                        <span style="color:var(--text-light);font-weight:400;font-size:.8rem;margin-left:4px">— kosongkan untuk pesan otomatis</span>
                    </label>
                    <textarea
                        id="notif-pesan"
                        name="pesan"
                        class="form-control"
                        rows="4"
                        style="resize:vertical"
                        placeholder="Pesan akan terisi otomatis jika dikosongkan…"
                    ></textarea>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:6px">
                        <i class="fa-solid fa-circle-info" style="margin-right:4px"></i>
                        Pesan otomatis: <em id="notif-pesan-preview">—</em>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:16px">
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