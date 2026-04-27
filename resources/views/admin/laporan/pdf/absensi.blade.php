<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi {{ $bulan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <h2>Rekap Absensi Bulanan</h2>
    <p>Bulan: {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Murid</th>
                <th>Hadir</th>
                <th>Alpa</th>
                <th>Izin</th>
                <th>Total Sesi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $m)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $m->nama_murid }}</td>
                <td>{{ $m->total_hadir }}</td>
                <td>{{ $m->total_alpa }}</td>
                <td>{{ $m->total_izin }}</td>
                <td>{{ $m->total_hadir + $m->total_alpa + $m->total_izin }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>