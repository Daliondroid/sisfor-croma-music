@extends('layouts.app')
@section('title', 'Rekap Gaji Guru')
@section('page-title', 'Rekap Gaji Guru')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Rekap Gaji Guru</h2>
        <div class="breadcrumb">Admin / Laporan / <span>Gaji Guru</span></div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.laporan.export.pdf', ['jenis' => 'gaji']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
           class="btn btn-outline" target="_blank">
            <i class="fa-solid fa-file-pdf" style="color:#dc2626"></i> Ekspor PDF
        </a>
        <a href="{{ route('admin.laporan.export.xlsx', ['jenis' => 'gaji']) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
           class="btn btn-outline">
            <i class="fa-solid fa-file-excel" style="color:#16a34a"></i> Ekspor Excel
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="margin:0">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}"/>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}"/>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-search"></i> Tampilkan
            </button>
            <a href="{{ route('admin.laporan.gaji') }}" class="btn btn-outline">Reset</a>
        </form>
        @error('end_date')
            <div style="color:#dc2626;font-size:.8rem;margin-top:8px">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
            </div>
        @enderror
    </div>
</div>

{{-- Ringkasan per guru --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3>Ringkasan Per Guru</h3>
        <span style="font-size:.8rem;color:var(--text-light)">
            {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Guru</th>
                    <th>Total Pertemuan</th>
                    <th>Total Honor</th>
                    <th>Sudah Cair</th>
                    <th>Belum Cair</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ringkasanGuru as $i => $r)
                <tr>
                    <td style="color:var(--text-light)">{{ $i + 1 }}</td>
                    <td><strong>{{ $r['guru']->nama_guru ?? '-' }}</strong></td>
                    <td>
                        <span class="badge badge-info">{{ $r['total_pertemuan'] }} sesi</span>
                    </td>
                    <td style="font-weight:600;color:var(--primary-blue)">
                        Rp {{ number_format($r['total_honor'], 0, ',', '.') }}
                    </td>
                    <td style="color:#16a34a;font-weight:600">
                        Rp {{ number_format($r['total_lunas'], 0, ',', '.') }}
                    </td>
                    <td style="color:#dc2626;font-weight:600">
                        Rp {{ number_format($r['total_pending'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:var(--text-light)">
                        Tidak ada data gaji untuk periode ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
            @if($ringkasanGuru->count())
            <tfoot>
                <tr style="background:#f8f9fa;font-weight:600">
                    <td colspan="3" style="padding:12px 16px;text-align:right">Total</td>
                    <td style="padding:12px 16px;color:var(--primary-blue)">
                        Rp {{ number_format($honors->sum('jumlah_honor'), 0, ',', '.') }}
                    </td>
                    <td style="padding:12px 16px;color:#16a34a">
                        Rp {{ number_format($honors->where('status_bayar', 'Lunas')->sum('jumlah_honor'), 0, ',', '.') }}
                    </td>
                    <td style="padding:12px 16px;color:#dc2626">
                        Rp {{ number_format($honors->whereIn('status_bayar', ['Belum Lunas', 'Siap Dibayar'])->sum('jumlah_honor'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- Detail semua record --}}
<div class="card">
    <div class="card-header">
        <h3>Detail Record Honor</h3>
        <span style="font-size:.8rem;color:var(--text-light)">{{ $honors->count() }} record</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID Honor</th>
                    <th>Guru</th>
                    <th>Murid</th>
                    <th>Sesi</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Tgl Pencairan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($honors as $i => $honor)
                @php $namaMurid = $honor->jadwals->first()?->spp?->murid?->nama_murid ?? 'N/A'; @endphp
                <tr>
                    <td style="color:var(--text-light)">{{ $i + 1 }}</td>
                    <td style="font-family:monospace;font-size:.8rem">
                        HG-{{ str_pad($honor->id_honor, 4, '0', STR_PAD_LEFT) }}
                    </td>
                    <td><strong>{{ $honor->guru->nama_guru ?? '-' }}</strong></td>
                    <td>{{ $namaMurid }}</td>
                    <td><span class="badge badge-info">{{ $honor->jumlah_pertemuan }}x</span></td>
                    <td style="font-weight:600">Rp {{ number_format($honor->jumlah_honor, 0, ',', '.') }}</td>
                    <td>
                        @if($honor->status_bayar === 'Lunas')
                            <span class="badge badge-success">Lunas</span>
                        @elseif($honor->status_bayar === 'Siap Dibayar')
                            <span class="badge badge-info">Siap Dibayar</span>
                        @else
                            <span class="badge badge-warning">Belum Lunas</span>
                        @endif
                    </td>
                    <td style="font-size:.82rem;color:var(--text-light)">
                        {{ $honor->tanggal_pencairan
                            ? \Carbon\Carbon::parse($honor->tanggal_pencairan)->format('d/m/Y')
                            : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:32px;color:var(--text-light)">
                        Tidak ada record honor.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection