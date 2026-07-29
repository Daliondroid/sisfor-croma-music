<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 0.625rem;
        color: #1f2937;
        background: #fff;
    }

    /* ── Header ── */
    .header {
        background: #003d80;
        color: #fff;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .header-title {
        font-size: 1rem;
        font-weight: bold;
        margin-bottom: 0.125rem;
    }
    .header-sub {
        font-size: 0.5625rem;
        opacity: .75;
    }

    /* ── Info grid ── */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
        padding: 0 1.5rem;
    }
    .info-table td {
        padding: 0.25rem 0;
        font-size: 0.625rem;
    }
    .info-table .lbl {
        width: 7.5rem;
        color: #6b7280;
        font-weight: bold;
    }
    .info-table .sep { width: 1rem; color: #6b7280; }

    /* ── Section title ── */
    .section-title {
        font-size: 0.6875rem;
        font-weight: bold;
        color: #1f2937;
        border-bottom: 1px solid #e5e7eb;
        padding: 0 1.5rem 0.25rem;
        margin: 0 0 0.5rem 0;
    }
    .section-wrap { padding: 0 1.5rem; margin-bottom: 1.5rem; }

    /* ── Skor box ── */
    .skor-wrap {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
        font-size: 1.375rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }
    .skor-A { background: #dcfce7; color: #15803d; }
    .skor-B { background: #dbeafe; color: #1d4ed8; }
    .skor-C { background: #fef9c3; color: #a16207; }
    .skor-none { background: #f3f4f6; color: #6b7280; }

    /* ── Stats row ── */
    .stats-row { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
    .stats-row td {
        width: 33.33%;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 0.5rem 0.5rem;
        text-align: center;
    }
    .stats-val { font-size: 0.875rem; font-weight: bold; color: #003d80; }
    .stats-lbl { font-size: 0.5rem; color: #6b7280; margin-top: 0.125rem; }

    /* ── Jadwal table ── */
    .jadwal-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.59375rem;
    }
    .jadwal-table th {
        background: #003d80;
        color: #fff;
        padding: 0.5rem 0.5rem;
        text-align: left;
        font-size: 0.53125rem;
        text-transform: uppercase;
        letter-spacing: 0.025rem;
    }
    .jadwal-table td {
        padding: 0.5rem 0.5rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
    }
    .jadwal-table tr:nth-child(even) td { background: #f9fafb; }
    .hadir-badge {
        display: inline-block;
        padding: 0.125rem 0.5rem;
        border-radius: 0.625rem;
        font-size: 0.5rem;
        font-weight: bold;
    }
    .hadir-ok { background: #dcfce7; color: #15803d; }
    .hadir-no { background: #fee2e2; color: #b91c1c; }
    .hadir-na { background: #f3f4f6; color: #6b7280; }

    /* ── Evaluasi ── */
    .eval-box {
        background: #f0f7ff;
        border-left: 0.25rem solid #003d80;
        border-radius: 0 0.375rem 0.375rem 0;
        padding: 0.5rem 1rem;
        font-size: 0.625rem;
        line-height: 1.7;
        color: #1f2937;
    }

    /* ── TTD ── */
    .ttd-wrap { text-align: right; margin-top: 1.5rem; }
    .ttd-inner {
        display: inline-block;
        text-align: center;
        min-width: 10rem;
    }
    .ttd-line {
        border-top: 1px solid #9ca3af;
        margin-top: 2.5rem;
        padding-top: 0.25rem;
        font-weight: bold;
        font-size: 0.625rem;
    }
    .ttd-sub { font-size: 0.5625rem; color: #6b7280; }

    /* ── Footer ── */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0; right: 0;
        text-align: center;
        font-size: 0.46875rem;
        color: #9ca3af;
        border-top: 1px solid #e5e7eb;
        padding: 0.25rem 0;
        background: #fff;
    }
</style>
</head>
<body>

{{-- ── HEADER ── --}}
<div class="header">
    <div class="header-title">Capaian Belajar Murid</div>
    <div class="header-sub">CROMA MUSIC — Sistem Manajemen Sekolah Musik</div>
</div>

{{-- ── INFO MURID ── --}}
<table class="info-table" style="margin:0 1.5rem 1.5rem;width:calc(100% - 3rem)">
    <tr>
        <td class="lbl">Nama</td>
        <td class="sep">:</td>
        <td>{{ $murid->nama_murid }}</td>
    </tr>
    <tr>
        <td class="lbl">Program Kursus</td>
        <td class="sep">:</td>
        <td>{{ $namaProgram }}</td>
    </tr>
    <tr>
        <td class="lbl">Guru</td>
        <td class="sep">:</td>
        <td>{{ $guruNama }}</td>
    </tr>
    <tr>
        <td class="lbl">Periode</td>
        <td class="sep">:</td>
        <td>{{ $bulanLabel }}</td>
    </tr>
</table>

{{-- ── PENILAIAN ── --}}
<div class="section-title">Penilaian</div>
<div class="section-wrap">
    @php
        $skor = $report->skor ?? '—';
        $skorLetter = strtoupper(substr($skor, 0, 1));
        $skorClass = match($skorLetter) {
            'A' => 'skor-A',
            'B' => 'skor-B',
            'C' => 'skor-C',
            default => 'skor-none',
        };
    @endphp
    <div class="skor-wrap {{ $skorClass }}">{{ $skor }}</div>

    <table class="stats-row">
        <tr>
            <td>
                <div class="stats-val">{{ $hadirSesi }}x</div>
                <div class="stats-lbl">Total Hadir</div>
            </td>
            <td>
                <div class="stats-val">{{ $totalSesi }}x</div>
                <div class="stats-lbl">Total Sesi</div>
            </td>
            <td>
                <div class="stats-val">{{ $pct }}%</div>
                <div class="stats-lbl">Kehadiran</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── DETAIL PERTEMUAN ── --}}
<div class="section-title">Detail Pertemuan</div>
<div class="section-wrap">
    <table class="jadwal-table">
        <thead>
            <tr>
                <th style="width:2rem">No</th>
                <th style="width:4.5rem">Tanggal</th>
                <th>Materi Pembelajaran</th>
                <th style="width:7.5rem">Catatan Perkembangan</th>
                <th style="width:4rem;text-align:center">Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwalsData as $j)
            <tr>
                <td style="text-align:center;color:#6b7280;font-weight:bold">{{ $j['no'] }}</td>
                <td>{{ $j['tanggal'] }}</td>
                <td>{{ $j['materi'] }}</td>
                <td style="color:#4b5563">{{ $j['catatan'] ?: '-' }}</td>
                <td style="text-align:center">
                    @php $h = $j['hadir']; @endphp
                    <span class="hadir-badge {{ $h === 'Hadir' ? 'hadir-ok' : ($h === 'Tidak Hadir' ? 'hadir-no' : 'hadir-na') }}">
                        {{ $h }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#9ca3af;padding:1rem">Belum ada data pertemuan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── KESIMPULAN ── --}}
@if($report->evaluasi_bulanan)
<div class="section-title">Kesimpulan Pembelajaran Bulan {{ $bulanLabel }}</div>
<div class="section-wrap">
    <div class="eval-box">{{ $report->evaluasi_bulanan }}</div>
</div>
@endif

{{-- ── TTD ── --}}
<div class="section-wrap">
    <div class="ttd-wrap">
        <div class="ttd-inner">
            <div style="font-size:0.5625rem;color:#6b7280;margin-bottom:0.125rem">{{ $kota }}, {{ $bulanLabel }}</div>
            <div class="ttd-line">{{ $guruNama }}</div>
            <div class="ttd-sub">Guru {{ $namaProgram }}</div>
        </div>
    </div>
</div>

{{-- ── FOOTER ── --}}
<div class="footer">
    Croma Music &bull; Dokumen ini digenerate otomatis oleh sistem CROMIS
</div>

</body>
</html>