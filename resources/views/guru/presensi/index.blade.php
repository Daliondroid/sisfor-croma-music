@extends('layouts.app')
@section('title', 'Input Presensi')
@section('page-title', 'Input Presensi')

@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}"            class="nav-item {{ request()->routeIs('guru.dashboard')       ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('guru.jadwal.index') }}"         class="nav-item {{ request()->routeIs('guru.jadwal*')         ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Jadwal Kelas</a>
    <a href="{{ route('guru.absensi.index') }}"        class="nav-item {{ request()->routeIs('guru.absensi*')        ? 'active' : '' }}"><i class="fa-solid fa-chart-bar"></i> Data Absensi</a>
    <a href="{{ route('guru.presensi.index') }}"       class="nav-item {{ request()->routeIs('guru.presensi*')       ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Input Presensi</a>
    <a href="{{ route('guru.progres.index') }}"        class="nav-item {{ request()->routeIs('guru.progres*')        ? 'active' : '' }}"><i class="fa-solid fa-book-open"></i> Laporan KBM</a>
    <a href="{{ route('guru.monthly-report.index') }}" class="nav-item {{ request()->routeIs('guru.monthly-report*') ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i> Laporan Bulanan</a>
@endsection

@section('content')
<div class="page-header">
    <div><h2>Input Presensi</h2><div class="breadcrumb">Guru / <span>Presensi</span></div></div>
</div>

<div style="display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start">

    {{-- ── Panel Kiri: Pilih Jadwal ── --}}
    <div class="card">
        <div class="card-header"><h3>Pilih Jadwal</h3></div>

        {{-- Month picker --}}
        <form method="GET" style="padding:12px 16px;border-bottom:1px solid var(--topbar-border);background:var(--bg-light)">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <label style="font-size:.72rem;font-weight:600;color:var(--text-light);white-space:nowrap">
                    <i class="fa-regular fa-calendar" style="color:var(--primary-blue);margin-right:4px"></i>
                    Bulan:
                </label>
                <input type="month" name="bulan" class="form-control"
                       style="flex:1;min-width:130px;font-size:.8rem;padding:6px 10px"
                       value="{{ $bulan }}"
                       max="{{ now()->format('Y-m') }}"
                       onchange="this.form.submit()"/>
            </div>
        </form>

        {{-- Daftar jadwal dikelompokkan per tanggal --}}
        <div style="padding:0;max-height:600px;overflow-y:auto">
            @forelse($jadwalGrouped as $tgl => $items)
                @php
                    $dt      = \Carbon\Carbon::parse($tgl);
                    $isToday = $dt->isToday();
                @endphp

                {{-- Header tanggal --}}
                <div style="padding:8px 16px;background:var(--bg-light);border-bottom:1px solid var(--topbar-border);
                            font-size:.72rem;font-weight:700;color:var(--text-light);
                            text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:8px">
                    {{ $dt->translatedFormat('l, d M') }}
                    @if($isToday)
                        <span class="badge badge-success" style="font-size:.6rem">Hari Ini</span>
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
                   style="display:block;padding:12px 18px;border-bottom:1px solid var(--topbar-border);transition:.15s;
                          {{ $isSelected  ? 'background:#eff6ff;border-left:3px solid var(--primary-blue);' : '' }}
                          {{ $sudah       ? 'opacity:.55;'  : '' }}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                        <div>
                            <div style="font-weight:600;font-size:.85rem">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:2px">
                                {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}
                                · {{ $j->spp->programKursus->nama_program ?? '-' }}
                            </div>
                        </div>
                        @if($sudah)
                            <span style="font-size:.62rem;color:#16a34a;font-weight:700;white-space:nowrap;flex-shrink:0">
                                <i class="fa-solid fa-circle-check"></i> Terisi
                            </span>
                        @elseif($belumMulai)
                            <span style="font-size:.62rem;color:#d97706;font-weight:700;white-space:nowrap;flex-shrink:0">
                                <i class="fa-solid fa-clock"></i> Belum Mulai
                            </span>
                        @else
                            <span style="font-size:.62rem;color:var(--primary-blue);font-weight:700;white-space:nowrap;flex-shrink:0">
                                <i class="fa-solid fa-pen"></i> Isi
                            </span>
                        @endif
                    </div>
                </a>
                @endforeach
            @empty
                <div style="padding:32px;text-align:center;color:var(--text-light);font-size:.875rem">
                    Tidak ada jadwal pada bulan ini.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── Panel Kanan: Form Presensi ── --}}
    <div class="card">
        <div class="card-header"><h3>Form Presensi Sesi</h3></div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
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
                <div style="background:#f8faff;border:1px solid #dbeafe;border-radius:8px;padding:14px;margin-bottom:20px">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                        <div>
                            <div style="font-weight:700">{{ $selected->spp->murid->nama_murid ?? '-' }}</div>
                            <div style="font-size:.82rem;color:var(--text-light);margin-top:2px">
                                {{ $selected->spp->programKursus->nama_program ?? '-' }}
                                · {{ $selected->spp->tipe_les ?? '' }}
                            </div>
                            <div style="font-size:.82rem;margin-top:4px">
                                <i class="fa-regular fa-calendar" style="margin-right:4px;color:var(--primary-blue)"></i>
                                {{ \Carbon\Carbon::parse($selected->tanggal)->translatedFormat('l, d M Y') }},
                                {{ substr($selected->jam_mulai,0,5) }}–{{ substr($selected->jam_selesai,0,5) }}
                            </div>
                        </div>
                        @if($sudahDiisi)
                            <span class="badge badge-success">
                                <i class="fa-solid fa-lock" style="margin-right:4px"></i>Sudah Diisi
                            </span>
                        @elseif($belumMulai)
                            <span class="badge badge-warning">
                                <i class="fa-solid fa-clock" style="margin-right:4px"></i>Belum Dimulai
                            </span>
                        @endif
                    </div>
                </div>

                @if($belumMulai)
                    {{-- Belum waktunya --}}
                    <div style="background:#fffbeb;border:1px solid #fbbf24;border-radius:8px;padding:20px;text-align:center">
                        <i class="fa-solid fa-clock" style="font-size:1.8rem;color:#d97706;margin-bottom:10px;display:block"></i>
                        <div style="font-weight:700;color:#92400e;margin-bottom:4px">Jadwal Belum Dimulai</div>
                        <div style="font-size:.82rem;color:#b45309">
                            Presensi bisa diisi mulai pukul <strong>{{ substr($selected->jam_mulai, 0, 5) }}</strong>.
                        </div>
                    </div>

                @elseif($sudahDiisi)
                    {{-- Sudah diisi — tampilkan ringkasan --}}
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px;margin-bottom:16px">
                        <div style="font-size:.85rem;font-weight:600;color:#16a34a;margin-bottom:10px">
                            <i class="fa-solid fa-circle-check" style="margin-right:6px"></i>Presensi sudah dicatat
                        </div>
                        <div style="display:flex;gap:24px;flex-wrap:wrap;font-size:.85rem">
                            <div>
                                <span style="color:var(--text-light)">Murid:</span>
                                <strong style="margin-left:6px">{{ $selected->status_kehadiran_murid }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-light)">Guru:</span>
                                <strong style="margin-left:6px">{{ $selected->status_kehadiran_guru }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-light)">Waktu:</span>
                                <strong style="margin-left:6px">
                                    {{ $selected->waktu_presensi_diisi->translatedFormat('d M Y, H:i') }}
                                </strong>
                            </div>
                        </div>
                    </div>
                    @if(!$selected->progresMurid)
                        <a href="{{ route('guru.progres.create', ['id_jadwal' => $selected->id_jadwal]) }}"
                           class="btn btn-primary">
                            <i class="fa-solid fa-book-open"></i> Input Laporan KBM
                        </a>
                    @else
                        <a href="{{ route('guru.progres.edit', $selected->progresMurid->id_progres) }}"
                           class="btn btn-outline">
                            <i class="fa-solid fa-pen"></i> Edit Laporan KBM
                        </a>
                    @endif

                @else
                    {{-- Form presensi --}}
                    <form method="POST" action="{{ route('guru.presensi.store') }}">
                        @csrf
                        <input type="hidden" name="id_jadwal" value="{{ $selected->id_jadwal }}"/>

                        <div class="form-grid" style="margin-bottom:20px">
                            <div class="form-group">
                                <label class="form-label">Kehadiran Murid <span style="color:red">*</span></label>
                                <select name="status_kehadiran_murid" class="form-control" required>
                                    <option value="Hadir">✅ Hadir</option>
                                    <option value="Tidak Hadir">❌ Tidak Hadir</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kehadiran Guru <span style="color:red">*</span></label>
                                <select name="status_kehadiran_guru" class="form-control" required>
                                    <option value="Hadir">✅ Hadir</option>
                                    <option value="Tidak Hadir">❌ Tidak Hadir</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Presensi
                        </button>
                    </form>
                @endif

                @else
                    <div style="text-align:center;padding:48px;color:var(--text-light)">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size:1.5rem;margin-bottom:12px;opacity:.3;display:block"></i>
                        <p>Jadwal tidak ditemukan.</p>
                    </div>
                @endif

            @else
                <div style="text-align:center;padding:48px;color:var(--text-light)">
                    <i class="fa-solid fa-arrow-left" style="font-size:1.5rem;margin-bottom:12px;opacity:.3;display:block"></i>
                    <p>Pilih jadwal di sebelah kiri untuk mulai input.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection