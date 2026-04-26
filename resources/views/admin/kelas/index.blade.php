@extends('layouts.app')
@section('title', 'Data Kelas')
@section('page-title', 'Data Kelas')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Data Kelas</h2>
        <div class="breadcrumb">Admin / Akademik / <span>Kelas</span></div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.program-kursus.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-layer-group"></i> Program Kursus
        </a>
        <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Kelas
        </a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kelas</th>
                    <th>Program Kursus</th>
                    <th>Tipe Les</th>
                    <th>Kapasitas</th>
                    <th>Jadwal Aktif</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($kelas as $i => $k)
                <tr>
                    <td style="color:var(--text-light)">{{ $kelas->firstItem() + $i }}</td>
                    <td><strong>{{ $k->nama_kelas }}</strong></td>
                    <td>
                        <span style="font-size:.8rem">{{ $k->program->nama_program ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $k->tipe_les=='onsite' ? 'badge-info' : 'badge-warning' }}">
                            {{ $k->tipe_les=='onsite' ? 'Onsite' : 'Home Private' }}
                        </span>
                    </td>
                    <td>{{ $k->kapasitas }} orang</td>
                    <td>
                        <span class="badge badge-gray">{{ $k->jadwals()->where('is_active',true)->count() }} jadwal</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.kelas.edit', $k) }}" class="btn btn-sm btn-outline">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.kelas.destroy', $k) }}"
                                  onsubmit="return confirm('Hapus kelas ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-light)">Belum ada data kelas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($kelas->hasPages())<div style="padding:16px 24px">{{ $kelas->withQueryString()->links() }}</div>@endif
</div>
@endsection