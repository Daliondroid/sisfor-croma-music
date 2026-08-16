@extends('layouts.app')
@section('title', 'Input Presensi')
@section('page-title', 'Input Presensi')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Input Presensi</h2>
        <div class="breadcrumb">Guru / <span>Presensi</span></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:20rem 1fr;gap:1.5rem;align-items:start">

    {{-- ── Panel Kiri: Pilih Jadwal ── --}}
    <div class="card">
        <div class="card-header"><h3>Pilih Jadwal</h3></div>

        {{-- Month picker --}}
        <form method="GET" style="padding:1rem 1rem;border-bottom:1px solid var(--topbar-border);background:var(--bg-light)">
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
                <label style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-light);white-space:nowrap">
                    Bulan:
                </label>
                <input type="month" name="bulan" class="form-control"
                       style="flex:1;min-width:8.125rem;font-size:.8rem;padding:0.25rem 0.5rem"
                       value="{{ $bulan }}"
                       max="{{ now()->format('Y-m') }}"
                       onchange="this.form.submit()"/>
            </div>
        </form>

        {{-- Daftar jadwal dikelompokkan per tanggal --}}
        <div style="padding:0;max-height:37.5rem;overflow-y:auto">
            @forelse($jadwalGrouped as $tgl => $items)
                @php
                    $dt      = \Carbon\Carbon::parse($tgl);
                    $isToday = $dt->isToday();
                @endphp

                {{-- Header tanggal --}}
                <div style="padding:0.5rem 1rem;background:var(--bg-light);border-bottom:1px solid var(--topbar-border);
                            font-size:.72rem;font-weight:700;color:var(--text-light);
                            text-transform:uppercase;letter-spacing:0.04em;display:flex;align-items:center;gap:0.5rem">
                    {{ $dt->translatedFormat('l, d M') }}
                    @if($isToday)
                        <span class="badge badge-success">HARI INI</span>
                    @endif
                </div>

                @foreach($items as $j)
                @php
                    $sudah       = $j->waktu_presensi_diisi !== null;
                    $jadwalMulai = \Carbon\Carbon::parse($dt->format('Y-m-d') . ' ' . $j->jam_mulai);
                    $belumMulai  = now()->lt($jadwalMulai);
                    $isSelected  = request('jadwal') == $j->id_jadwal;
                @endphp
                <a href="{{ route('guru.presensi.index') }}?bulan={{ $bulan }}&jadwal={{ $j->id_jadwal }}"
                   style="display:block;padding:1rem 1rem;border-bottom:1px solid var(--topbar-border);transition:.15s;text-decoration:none;
                          {{ $isSelected  ? 'background:var(--bg-light);' : '' }}
                          {{ $sudah       ? 'opacity:.6;'  : '' }}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem">
                        <div>
                            <div style="font-weight:600;font-size:.85rem;color:var(--text-dark)">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:0.125rem;font-variant-numeric:tabular-nums">
                                {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}
                                · {{ $j->spp->programKursus->nama_program ?? '-' }}
                            </div>
                        </div>
                        @if($sudah)
                            <span class="badge badge-success">TERISI</span>
                        @elseif($belumMulai)
                            <span class="badge badge-warning">BELUM MULAI</span>
                        @else
                            <span class="badge badge-info">ISI</span>
                        @endif
                    </div>
                </a>
                @endforeach
            @empty
                <div class="empty-state">
                    <div class="empty-state-title">Tidak ada jadwal pada bulan ini.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── Panel Kanan: Form Presensi ── --}}
    <div class="card">
        <div class="card-header"><h3>Form Presensi Sesi</h3></div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(request('jadwal'))
                @php
                    $selected    = $jadwals->firstWhere('id_jadwal', request('jadwal'));
                @endphp

                @if($selected)
                @php
                    $sudahDiisi  = $selected->waktu_presensi_diisi !== null;
                    $jadwalMulai = \Carbon\Carbon::parse(
                        \Carbon\Carbon::parse($selected->tanggal)->format('Y-m-d') . ' ' . $selected->jam_mulai
                    );
                    $belumMulai  = now()->lt($jadwalMulai);
                @endphp

                {{-- Info jadwal terpilih --}}
                <div style="background:var(--bg-light);border:1px solid var(--topbar-border);border-radius:0.25rem;padding:1rem;margin-bottom:1.5rem">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem">
                        <div>
                            <div style="font-weight:700;font-size:1rem;color:var(--text-dark)">{{ $selected->spp->murid->nama_murid ?? '-' }}</div>
                            <div style="font-size:.82rem;color:var(--text-light);margin-top:0.125rem">
                                {{ $selected->spp->programKursus->nama_program ?? '-' }}
                                · {{ $selected->spp->tipe_les ?? '' }}
                            </div>
                            <div style="font-size:.82rem;margin-top:0.25rem;font-variant-numeric:tabular-nums;color:var(--text-dark)">
                                {{ \Carbon\Carbon::parse($selected->tanggal)->translatedFormat('l, d M Y') }},
                                {{ substr($selected->jam_mulai,0,5) }}–{{ substr($selected->jam_selesai,0,5) }}
                            </div>
                        </div>
                        @if($sudahDiisi)
                            <span class="badge badge-success">
                                SUDAH DIISI
                            </span>
                        @elseif($belumMulai)
                            <span class="badge badge-warning">
                                BELUM DIMULAI
                            </span>
                        @endif
                    </div>
                </div>

                @if($belumMulai)
                    {{-- Belum waktunya --}}
                    <div style="background:#fffbeb;border:1px solid #fbbf24;border-radius:0.25rem;padding:1.5rem;text-align:center">
                        <div style="font-weight:700;color:#92400e;margin-bottom:0.25rem">Jadwal Belum Dimulai</div>
                        <div style="font-size:.82rem;color:#b45309">
                            Presensi bisa diisi mulai pukul <strong style="font-variant-numeric:tabular-nums">{{ substr($selected->jam_mulai, 0, 5) }}</strong>.
                        </div>
                    </div>

                @elseif($sudahDiisi)
                    {{-- Sudah diisi — tampilkan ringkasan --}}
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:0.25rem;padding:1rem;margin-bottom:1rem">
                        <div style="font-size:.85rem;font-weight:700;color:#16a34a;margin-bottom:0.5rem">
                            Presensi sudah dicatat
                        </div>
                        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;font-size:.85rem">
                            <div>
                                <span style="color:var(--text-light)">Murid:</span>
                                <strong style="margin-left:0.25rem;color:var(--text-dark)">{{ $selected->status_kehadiran_murid }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-light)">Guru:</span>
                                <strong style="margin-left:0.25rem;color:var(--text-dark)">{{ $selected->status_kehadiran_guru }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-light)">Waktu:</span>
                                <strong style="margin-left:0.25rem;color:var(--text-dark);font-variant-numeric:tabular-nums">
                                    {{ $selected->waktu_presensi_diisi->translatedFormat('d M Y, H:i') }}
                                </strong>
                            </div>
                        </div>
                    </div>
                    @if(!$selected->progresMurid)
                        <a href="{{ route('guru.progres.create', ['id_jadwal' => $selected->id_jadwal]) }}"
                           class="btn btn-primary btn-sm">
                            Input Laporan KBM
                        </a>
                    @else
                        <a href="{{ route('guru.progres.edit', $selected->progresMurid->id_progres) }}"
                           class="btn btn-outline btn-sm">
                            Edit Laporan KBM
                        </a>
                    @endif

                @else
                    {{-- Form presensi --}}
                    <form method="POST" action="{{ route('guru.presensi.store') }}">
                        @csrf
                        <input type="hidden" name="id_jadwal" value="{{ $selected->id_jadwal }}"/>

                        <div class="form-grid" style="margin-bottom:1.5rem">
                            <div class="form-group">
                                <label class="form-label">Kehadiran Murid <span style="color:red">*</span></label>
                                <select name="status_kehadiran_murid" class="form-control" required>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Tidak Hadir">Tidak Hadir</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kehadiran Guru <span style="color:red">*</span></label>
                                <select name="status_kehadiran_guru" class="form-control" required>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Tidak Hadir">Tidak Hadir</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">
                            Simpan Presensi
                        </button>
                    </form>
                @endif

                @else
                    <div class="empty-state">
                        <div class="empty-state-title">Jadwal tidak ditemukan.</div>
                    </div>
                @endif

            @else
                <div class="empty-state">
                    <div class="empty-state-title">Pilih jadwal di sebelah kiri untuk mulai input.</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection