<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <title>Rekap Absensi — Croma Music</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1f2937; }

        .header { background: #1e3a5f; color: #fff; padding: 18px 24px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; letter-spacing: .5px; }
        .header .sub { font-size: 10px; color: #93c5fd; margin-top: 4px; }

        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead tr { background: #1e3a5f; color: #fff; }
        thead th { padding: 7px 8px; text-align: left; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; }

        .bar-wrap { width: 80px; height: 7px; background: #e5e7eb; border-radius: 99px; display: inline-block; vertical-align: middle; overflow: hidden; }
        .bar-fill  { height: 100%; border-radius: 99px; }
        .green  { background: #16a34a; }
        .yellow { background: #f59e0b; }
        .red    { background: #dc2626; }

        .footer { margin-top: 24px; font-size: 8px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP ABSENSI MURID — CROMA MUSIC</h1>
        <div class="sub">
            Periode: {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
            &nbsp;·&nbsp; Dibuat: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:28%">Nama Murid</th>
                <th style="width:12%;text-align:center">Total Sesi</th>
                <th style="width:10%;text-align:center">Hadir</th>
                <th style="width:13%;text-align:center">Tidak Hadir</th>
                <th style="width:13%;text-align:center">Belum Diisi</th>
                <th style="width:20%;text-align:center">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
        @foreach($murids as $i => $m)
            <tr>
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td><strong>{{ $m->nama_murid }}</strong></td>
                <td style="text-align:center">{{ $m->total_sesi }}</td>
                <td style="text-align:center;color:#16a34a;font-weight:bold">{{ $m->total_hadir }}</td>
                <td style="text-align:center;color:#dc2626">{{ $m->total_absen }}</td>
                <td style="text-align:center;color:#9ca3af">{{ $m->belum_diisi }}</td>
                <td style="text-align:center">
                    @php
                        $cls = $m->persen_hadir >= 75 ? 'green' : ($m->persen_hadir >= 50 ? 'yellow' : 'red');
                    @endphp
                    <span class="bar-wrap">
                        <span class="bar-fill {{ $cls }}" style="width:{{ $m->persen_hadir }}%"></span>
                    </span>
                    <span style="font-weight:bold;color:{{ $m->persen_hadir >= 75 ? '#16a34a' : ($m->persen_hadir >= 50 ? '#d97706' : '#dc2626') }}">
                        {{ $m->persen_hadir }}%
                    </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh CROMIS — Sistem Informasi Manajemen Croma Music
    </div>
</body>
</html>