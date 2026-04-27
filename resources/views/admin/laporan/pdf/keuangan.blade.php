<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan {{ $bulan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <h2>Laporan Keuangan Bulanan</h2>
    <p>Bulan: {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Murid</th>
                <th>Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $spp)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $spp->murid->nama_murid }}</td>
                <td>Rp {{ number_format($spp->nominal_tagihan, 0, ',', '.') }}</td>
                <td>{{ $spp->sudahBayar() ? 'Lunas' : 'Belum Bayar' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>