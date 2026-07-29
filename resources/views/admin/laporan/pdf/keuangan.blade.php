<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Laporan Keuangan {{ $bulan }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 0.625rem; color: #1f2937; }

        .header { background: #1e3a5f; color: #fff; padding: 1rem 1.5rem; margin-bottom: 1rem; }
        .header h1 { font-size: 1rem; letter-spacing: 0.03125rem; }
        .header .sub { font-size: 0.625rem; color: #93c5fd; margin-top: 0.25rem; }

        .summary { display: table; width: 100%; margin-bottom: 1rem; border-collapse: collapse; }
        .summary-box { display: table-cell; width: 25%; padding: 0.5rem 1rem; border: 1px solid #e5e7eb; }
        .summary-box .label { font-size: 0.5rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03125rem; margin-bottom: 0.25rem; }
        .summary-box .value { font-size: 0.75rem; font-weight: bold; }
        .value-green  { color: #16a34a; }
        .value-blue   { color: #2563eb; }
        .value-red    { color: #dc2626; }
        .value-yellow { color: #d97706; }

        table { width: 100%; border-collapse: collapse; font-size: 0.5625rem; }
        thead tr { background: #1e3a5f; color: #fff; }
        thead th { padding: 0.5rem 0.5rem; text-align: left; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr.lunas { background: #f0fdf4; }
        tbody tr.belum { background: #fffbeb; }
        tbody td { padding: 0.25rem 0.5rem; border-bottom: 1px solid #f1f5f9; }

        tfoot td { padding: 0.5rem; font-weight: bold; background: #dbeafe; border-top: 0.125rem solid #2563eb; }

        .badge { display: inline-block; padding: 0.125rem 0.25rem; border-radius: 0.1875rem; font-size: 0.5rem; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-danger  { background: #fee2e2; color: #dc2626; }
        .badge-info    { background: #dbeafe; color: #2563eb; }
        .badge-warning { background: #fef3c7; color: #92400e; }

        .footer { margin-top: 1.5rem; font-size: 0.5rem; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN KEUANGAN BULANAN — CROMA MUSIC</h1>
        <div class="sub">
            @if(\Carbon\Carbon::parse($startDate)->isSameMonth(\Carbon\Carbon::parse($endDate)))
                Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}
            @else
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @endif
            &nbsp;·&nbsp; Dibuat: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>

    {{-- Ringkasan --}}
    <table style="margin-bottom:1rem; border-collapse:collapse;">
        <tr>
            <td style="width:25%; padding:0.5rem 1rem; border:1px solid #e5e7eb; background:#f0fdf4;">
                <div style="font-size:0.5rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.03125rem;margin-bottom:0.25rem;">Total Masuk (Lunas)</div>
                <div style="font-size:0.6875rem;font-weight:bold;color:#16a34a;">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
            </td>
            <td style="width:25%; padding:0.5rem 1rem; border:1px solid #e5e7eb; background:#eff6ff;">
                <div style="font-size:0.5rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.03125rem;margin-bottom:0.25rem;">Total Tagihan</div>
                <div style="font-size:0.6875rem;font-weight:bold;color:#2563eb;">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
            </td>
            <td style="width:25%; padding:0.5rem 1rem; border:1px solid #e5e7eb; background:#fef2f2;">
                <div style="font-size:0.5rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.03125rem;margin-bottom:0.25rem;">Total Tunggakan</div>
                <div style="font-size:0.6875rem;font-weight:bold;color:#dc2626;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
            </td>
            <td style="width:25%; padding:0.5rem 1rem; border:1px solid #e5e7eb; background:#fffbeb;">
                <div style="font-size:0.5rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.03125rem;margin-bottom:0.25rem;">Tingkat Pembayaran</div>
                <div style="font-size:0.6875rem;font-weight:bold;color:#d97706;">{{ $totalTagihan > 0 ? round(($totalMasuk / $totalTagihan) * 100) : 0 }}%</div>
            </td>
        </tr>
    </table>

    {{-- Tabel Detail --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:22%">Nama Murid</th>
                <th style="width:18%">Program Kursus</th>
                <th style="width:12%;text-align:center">Periode</th>
                <th style="width:14%;text-align:right">Nominal (Rp)</th>
                <th style="width:10%;text-align:center">Tipe</th>
                <th style="width:10%;text-align:center">Status</th>
                <th style="width:10%;text-align:center">Tgl Konfirmasi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($spps as $i => $spp)
            <tr class="{{ $spp->status_bayar === 'Lunas' ? 'lunas' : 'belum' }}">
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td><strong>{{ $spp->murid->nama_murid ?? '-' }}</strong></td>
                <td>{{ $spp->programKursus->nama_program ?? '-' }}</td>
                <td style="text-align:center">{{ \Carbon\Carbon::parse($spp->periode_tagihan)->translatedFormat('F Y') }}</td>
                <td style="text-align:right">{{ number_format($spp->nominal_tagihan, 0, ',', '.') }}</td>
                <td style="text-align:center">
                    <span class="badge {{ $spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}">
                        {{ $spp->tipe_les ?? '-' }}
                    </span>
                </td>
                <td style="text-align:center">
                    <span class="badge {{ $spp->status_bayar === 'Lunas' ? 'badge-success' : 'badge-danger' }}">
                        {{ $spp->status_bayar }}
                    </span>
                </td>
                <td style="text-align:center;color:#6b7280">
                    {{ $spp->transaksi?->tanggal_konfirmasi
                        ? \Carbon\Carbon::parse($spp->transaksi->tanggal_konfirmasi)->format('d/m/Y')
                        : '—' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:1.5rem;color:#9ca3af">
                    Tidak ada data tagihan untuk periode ini.
                </td>
            </tr>
        @endforelse
        </tbody>
        @if($spps->count())
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;padding:0.5rem;font-weight:bold;background:#dbeafe;border-top:0.125rem solid #2563eb;">TOTAL</td>
                <td style="text-align:right;padding:0.5rem;font-weight:bold;background:#dbeafe;border-top:0.125rem solid #2563eb;">
                    {{ number_format($totalTagihan, 0, ',', '.') }}
                </td>
                <td colspan="3" style="background:#dbeafe;border-top:0.125rem solid #2563eb;"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh CROMIS — Sistem Informasi Manajemen Croma Music
    </div>

</body>
</html>