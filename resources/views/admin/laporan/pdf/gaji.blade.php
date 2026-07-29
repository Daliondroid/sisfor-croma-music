<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <title>Rekap Gaji Guru {{ $bulan }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 0.625rem; color: #1f2937; }

        .header { background: #1e3a5f; color: #fff; padding: 1rem 1.5rem; margin-bottom: 1rem; }
        .header h1 { font-size: 1rem; letter-spacing: 0.03125rem; }
        .header .sub { font-size: 0.625rem; color: #93c5fd; margin-top: 0.25rem; }

        .section-title { font-size: 0.6875rem; font-weight: bold; color: #1e3a5f; margin: 1rem 0 0.5rem 0; padding-bottom: 0.25rem; border-bottom: 0.125rem solid #1e3a5f; }

        table { width: 100%; border-collapse: collapse; font-size: 0.5625rem; margin-bottom: 0.5rem; }
        thead tr { background: #1e3a5f; color: #fff; }
        thead th { padding: 0.5rem 0.5rem; text-align: left; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 0.25rem 0.5rem; border-bottom: 1px solid #f1f5f9; }
        tfoot td { padding: 0.5rem; font-weight: bold; background: #dbeafe; border-top: 0.125rem solid #2563eb; }

        .badge { display: inline-block; padding: 0.125rem 0.25rem; border-radius: 0.1875rem; font-size: 0.5rem; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-info    { background: #dbeafe; color: #2563eb; }
        .badge-warning { background: #fef3c7; color: #92400e; }

        .footer { margin-top: 1.5rem; font-size: 0.5rem; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>REKAP GAJI GURU — CROMA MUSIC</h1>
        <div class="sub">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            &nbsp;·&nbsp; Dibuat: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>

    {{-- Ringkasan per Guru --}}
    <div class="section-title">RINGKASAN PER GURU</div>
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:30%">Nama Guru</th>
                <th style="width:14%;text-align:center">Total Pertemuan</th>
                <th style="width:18%;text-align:right">Total Honor (Rp)</th>
                <th style="width:17%;text-align:right">Sudah Cair (Rp)</th>
                <th style="width:17%;text-align:right">Belum Cair (Rp)</th>
            </tr>
        </thead>
        <tbody>
        @forelse($ringkasanGuru as $i => $r)
            <tr>
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td><strong>{{ $r['guru']->nama_guru ?? '-' }}</strong></td>
                <td style="text-align:center">{{ $r['total_pertemuan'] }} sesi</td>
                <td style="text-align:right;font-weight:bold;color:#2563eb">{{ number_format($r['total_honor'], 0, ',', '.') }}</td>
                <td style="text-align:right;color:#16a34a;font-weight:bold">{{ number_format($r['total_lunas'], 0, ',', '.') }}</td>
                <td style="text-align:right;color:#dc2626;font-weight:bold">{{ number_format($r['total_pending'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:1.5rem;color:#9ca3af">Tidak ada data.</td>
            </tr>
        @endforelse
        </tbody>
        @if($ringkasanGuru->count())
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right">TOTAL</td>
                <td style="text-align:right">{{ number_format($honors->sum('jumlah_honor'), 0, ',', '.') }}</td>
                <td style="text-align:right">{{ number_format($honors->where('status_bayar','Lunas')->sum('jumlah_honor'), 0, ',', '.') }}</td>
                <td style="text-align:right">{{ number_format($honors->whereIn('status_bayar',['Belum Lunas','Siap Dibayar'])->sum('jumlah_honor'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Detail Record Honor --}}
    <div class="section-title">DETAIL RECORD HONOR</div>
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:10%">ID Honor</th>
                <th style="width:22%">Nama Guru</th>
                <th style="width:20%">Murid</th>
                <th style="width:8%;text-align:center">Sesi</th>
                <th style="width:16%;text-align:right">Nominal (Rp)</th>
                <th style="width:10%;text-align:center">Status</th>
                <th style="width:10%;text-align:center">Tgl Pencairan</th>
            </tr>
        </thead>
        <tbody>
        @forelse($honors as $i => $honor)
            @php $namaMurid = $honor->jadwals->first()?->spp?->murid?->nama_murid ?? 'N/A'; @endphp
            <tr>
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td style="font-family:monospace">HG-{{ str_pad($honor->id_honor, 4, '0', STR_PAD_LEFT) }}</td>
                <td><strong>{{ $honor->guru->nama_guru ?? '-' }}</strong></td>
                <td>{{ $namaMurid }}</td>
                <td style="text-align:center">{{ $honor->jumlah_pertemuan }}x</td>
                <td style="text-align:right;font-weight:bold">{{ number_format($honor->jumlah_honor, 0, ',', '.') }}</td>
                <td style="text-align:center">
                    @if($honor->status_bayar === 'Lunas')
                        <span class="badge badge-success">Lunas</span>
                    @elseif($honor->status_bayar === 'Siap Dibayar')
                        <span class="badge badge-info">Siap Dibayar</span>
                    @else
                        <span class="badge badge-warning">Belum Lunas</span>
                    @endif
                </td>
                <td style="text-align:center;color:#6b7280">
                    {{ $honor->tanggal_pencairan
                        ? \Carbon\Carbon::parse($honor->tanggal_pencairan)->format('d/m/Y')
                        : '—' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:1.5rem;color:#9ca3af">
                    Tidak ada data honor untuk periode ini.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh CROMIS — Sistem Informasi Manajemen Croma Music
    </div>

</body>
</html>