@extends('layouts.app')
@section('title', 'Manajemen Gaji Guru')
@section('page-title', 'Gaji Guru')

@section('breadcrumb')
    <span class="crumb-root">Gaji & Laporan</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Manajemen Gaji Guru</span>
@endsection

@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <h2>Manajemen Gaji Guru</h2>
    <div class="page-header-filters">
        <form action="{{ route('admin.honor-guru.index') }}" method="GET" style="display:flex;gap:0.625rem;align-items:center;flex-wrap:wrap">
            <select name="id_guru" class="form-control form-control-sm">
                <option value="">Semua Guru</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id_guru }}" {{ request('id_guru') == $g->id_guru ? 'selected' : '' }}>{{ $g->nama_guru }}</option>
                @endforeach
            </select>
            <select name="status" class="form-control form-control-sm">
                <option value="">Semua Status</option>
                <option value="Belum Lunas" {{ request('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas (Draft)</option>
                <option value="Siap Dibayar" {{ request('status') == 'Siap Dibayar' ? 'selected' : '' }}>Siap Dibayar</option>
                <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="{{ route('admin.honor-guru.index') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table style="table-layout:fixed;width:100%">
            <thead>
                <tr>
                    <th style="width:12%">#ID Honor</th>
                    <th style="width:22%">Guru Pengajar</th>
                    <th style="width:22%">Konteks Mengajar</th>
                    <th style="width:12%">Sesi</th>
                    <th style="width:16%">Nominal Honor</th>
                    <th style="width:10%">Status</th>
                    <th style="width:6%;text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($honors as $honor)
                    <tr>
                        <td style="color:var(--text-light);font-family:monospace;font-weight:600;font-variant-numeric:tabular-nums">
                            HG-{{ str_pad($honor->id_honor, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <div style="font-weight:600;color:var(--text-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $honor->guru->nama_guru ?? 'Guru Dihapus' }}
                            </div>
                        </td>
                        <td>
                            @php
                                $namaMurid = $honor->jadwals->first()->spp->murid->nama_murid ?? 'N/A';
                            @endphp
                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:0.85rem">
                                Murid: {{ $namaMurid }}
                            </div>
                        </td>
                        <td style="white-space:nowrap;font-variant-numeric:tabular-nums">
                            {{ $honor->jumlah_pertemuan }} Pertemuan
                        </td>
                        <td>
                            <span style="font-weight:700;color:#15803d;font-variant-numeric:tabular-nums">
                                Rp {{ number_format($honor->jumlah_honor, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @if($honor->status_bayar === 'Lunas')
                                <span class="badge badge-success">LUNAS</span>
                            @elseif($honor->status_bayar === 'Siap Dibayar')
                                <span class="badge badge-info">SIAP DIBAYAR</span>
                            @else
                                <span class="badge badge-warning">BELUM LUNAS</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <div class="action-dropdown-wrap">
                                <button type="button" class="btn-action-dropdown" onclick="toggleActionDropdown(this, event)">
                                    Kelola ▾
                                </button>
                                <div class="action-dropdown-menu">
                                    <a href="{{ route('admin.honor-guru.edit', $honor->id_honor) }}" class="action-dropdown-item">
                                        Kelola Gaji
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-title">Belum ada data gaji guru.</div>
                            <div class="empty-state-description">Tidak ada riwayat atau draft gaji guru yang sesuai dengan filter.</div>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($honors->hasPages())
        <div style="padding:0.75rem 1.5rem;border-top:1px solid var(--topbar-border)">
            {{ $honors->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection