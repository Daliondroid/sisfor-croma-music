@extends('layouts.app')
@section('title', 'Jadwal KBM')
@section('page-title', 'Jadwal KBM')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Jadwal KBM</h2>
        <div class="breadcrumb">Admin / <span>Jadwal</span></div>
    </div>
    <a href="{{ route('admin.jadwals.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Buat Jadwal
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Murid</th><th>Guru</th><th>Kelas</th>
                    <th>Hari</th><th>Jam</th><th>Lokasi</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jadwals as $i => $j)
                <tr>
                    <td style="color:var(--text-light)">{{ $jadwals->firstItem() + $i }}</td>
                    <td><strong>{{ $j->murid->nama_murid }}</strong></td>
                    <td>{{ $j->guru->nama_guru }}</td>
                    <td>{{ $j->kelas->nama_kelas ?? '-' }}</td>
                    <td><span class="badge badge-info">{{ $j->hari }}</span></td>
                    <td>{{ substr($j->jam_mulai,0,5) }} – {{ substr($j->jam_selesai,0,5) }}</td>
                    <td>{{ $j->lokasi ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $j->is_active ? 'badge-success' : 'badge-gray' }}">
                            {{ $j->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.jadwals.destroy', $j) }}" onsubmit="return confirm('Nonaktifkan jadwal ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" title="Nonaktifkan">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text-light)">Belum ada jadwal.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($jadwals->hasPages())
        <div style="padding:16px 24px">{{ $jadwals->links() }}</div>
    @endif
</div>
@endsection