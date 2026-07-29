<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <title>Laporan Bulanan - {{ $murid->nama_murid ?? 'Murid' }} ({{ $bulan }})</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 0.7rem; color: #1f2937; padding: 1.5rem; }
        .header { background: #1e3a5f; color: #fff; padding: 1.25rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .header h1 { font-size: 1.2rem; }
        .header .sub { font-size: 0.75rem; color: #93c5fd; margin-top: 0.25rem; }
        .section-title { font-size: 0.85rem; font-weight: bold; color: #1e3a5f; margin-bottom: 0.5rem; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.25rem; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 1rem; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.65rem; margin-bottom: 1.5rem; }
        thead tr { background: #1e3a5f; color: #fff; }
        thead th { padding: 0.5rem; text-align: left; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 0.4rem 0.5rem; border-bottom: 1px solid #e2e8f0; }
        .footer { margin-top: 2rem; font-size: 0.6rem; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN EVALUASI BULANAN MURID</h1>
        <div class="sub">
            Croma Music Studio &nbsp;·&nbsp; Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
        </div>
    </div>

    <div class="box">
        <table style="margin:0;border:none">
            <tr>
                <td style="width:50%;border:none"><strong>Nama Murid:</strong> {{ $murid->nama_murid ?? '-' }}</td>
                <td style="width:50%;border:none"><strong>Program:</strong> {{ $program->nama_program ?? '-' }} ({{ $spp->tipe_les ?? '-' }})</td>
            </tr>
            <tr>
                <td style="border:none"><strong>Guru Pengajar:</strong> {{ $guru->nama_guru ?? '-' }}</td>
                <td style="border:none"><strong>Skor Evaluasi:</strong> <span style="font-size:0.9rem;font-weight:bold;color:#1e3a5f">{{ $monthlyReport->skor }}</span></td>
            </tr>
        </table>
    </div>

    <div class="section-title">Evaluasi & Catatan Guru</div>
    <div class="box">
        <p style="line-height:1.6;white-space:pre-wrap">{{ $monthlyReport->evaluasi_bulanan }}</p>
    </div>

    <div class="section-title">Rincian Kehadiran & Sesi KBM</div>
    <table>
        <thead>
            <tr>
                <th style="width:8%;text-align:center">Sesi</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:15%;text-align:center">Status</th>
                <th style="width:27%">Materi KBM</th>
                <th style="width:30%">Catatan Perkembangan</th>
            </tr>
        </thead>
        <tbody>
        @foreach($jadwals as $j)
            <tr>
                <td style="text-align:center"><strong>{{ $j->sesi_ke }}</strong></td>
                <td>{{ $j->tanggal->translatedFormat('d M Y') }}</td>
                <td style="text-align:center">{{ $j->status_kehadiran_murid ?? 'Belum' }}</td>
                <td>{{ $j->progresMurid->materi_diajarkan ?? '—' }}</td>
                <td>{{ $j->progresMurid->catatan_perkembangan ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh CROMIS - Sistem Informasi Manajemen Croma Music.
    </div>
</body>
</html>
