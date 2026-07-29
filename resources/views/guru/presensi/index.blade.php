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
    <h2>Input Presensi</h2>
    <div class="breadcrumb">Guru / <span>Presensi</span></div>
</div>

<div style="display:grid;grid-template-columns:20rem 1fr;gap:1.5rem;align-items:start">

    {{-- ── Panel Kiri: Pilih Jadwal ── --}}
    <div class="card">
        <div class="card-header"><h3>Pilih Jadwal</h3></div>

        {{-- Month picker --}}
        <form method="GET" style="padding:1rem 1rem;border-bottom:1px solid var(--topbar-border);background:var(--bg-light)">
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
                <label style="font-size:.72rem;font-weight:600;color:var(--text-light);white-space:nowrap">
                    <i class="fa-regular fa-calendar" style="color:var(--primary-blue);margin-right:0.25rem"></i>
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
                            text-transform:uppercase;letter-spacing:0.03125rem;display:flex;align-items:center;gap:0.5rem">
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
                   style="display:block;padding:1rem 1rem;border-bottom:1px solid var(--topbar-border);transition:.15s;
                          {{ $isSelected  ? 'background:#eff6ff;border-left:0.1875rem solid var(--primary-blue);' : '' }}
                          {{ $sudah       ? 'opacity:.55;'  : '' }}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem">
                        <div>
                            <div style="font-weight:600;font-size:.85rem">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                            <div style="font-size:.72rem;color:var(--text-light);margin-top:0.125rem">
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
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="12" y="20" width="56" height="48" rx="6" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M12 32h56" stroke="var(--primary-blue)" stroke-width="2"/><rect x="24" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><rect x="52" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><circle cx="30" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="40" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="50" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="30" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="40" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="50" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/></svg>
                    </div>
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
                <div style="background:#f8faff;border:1px solid #dbeafe;border-radius:0.5rem;padding:1rem;margin-bottom:1.5rem">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem">
                        <div>
                            <div style="font-weight:700">{{ $selected->spp->murid->nama_murid ?? '-' }}</div>
                            <div style="font-size:.82rem;color:var(--text-light);margin-top:0.125rem">
                                {{ $selected->spp->programKursus->nama_program ?? '-' }}
                                · {{ $selected->spp->tipe_les ?? '' }}
                            </div>
                            <div style="font-size:.82rem;margin-top:0.25rem">
                                <i class="fa-regular fa-calendar" style="margin-right:0.25rem;color:var(--primary-blue)"></i>
                                {{ \Carbon\Carbon::parse($selected->tanggal)->translatedFormat('l, d M Y') }},
                                {{ substr($selected->jam_mulai,0,5) }}–{{ substr($selected->jam_selesai,0,5) }}
                            </div>
                        </div>
                        @if($sudahDiisi)
                            <span class="badge badge-success">
                                <i class="fa-solid fa-lock" style="margin-right:0.25rem"></i>Sudah Diisi
                            </span>
                        @elseif($belumMulai)
                            <span class="badge badge-warning">
                                <i class="fa-solid fa-clock" style="margin-right:0.25rem"></i>Belum Dimulai
                            </span>
                        @endif
                    </div>
                </div>

                @if($belumMulai)
                    {{-- Belum waktunya --}}
                    <div style="background:#fffbeb;border:1px solid #fbbf24;border-radius:0.5rem;padding:1.5rem;text-align:center">
                        <i class="fa-solid fa-clock" style="font-size:1.8rem;color:#d97706;margin-bottom:0.5rem;display:block"></i>
                        <div style="font-weight:700;color:#92400e;margin-bottom:0.25rem">Jadwal Belum Dimulai</div>
                        <div style="font-size:.82rem;color:#b45309">
                            Presensi bisa diisi mulai pukul <strong>{{ substr($selected->jam_mulai, 0, 5) }}</strong>.
                        </div>
                    </div>

                @elseif($sudahDiisi)
                    {{-- Sudah diisi — tampilkan ringkasan --}}
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:0.5rem;padding:1rem;margin-bottom:1rem">
                        <div style="font-size:.85rem;font-weight:600;color:#16a34a;margin-bottom:0.5rem">
                            <i class="fa-solid fa-circle-check" style="margin-right:0.25rem"></i>Presensi sudah dicatat
                        </div>
                        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;font-size:.85rem">
                            <div>
                                <span style="color:var(--text-light)">Murid:</span>
                                <strong style="margin-left:0.25rem">{{ $selected->status_kehadiran_murid }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-light)">Guru:</span>
                                <strong style="margin-left:0.25rem">{{ $selected->status_kehadiran_guru }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-light)">Waktu:</span>
                                <strong style="margin-left:0.25rem">
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

                        <div class="form-grid" style="margin-bottom:1.5rem">
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
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="14" y="14" width="52" height="52" rx="8" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M32 40h16M40 32v16" stroke="var(--primary-blue)" stroke-width="2.5" stroke-linecap="round" opacity=".5"/></svg>
                        </div>
                        <div class="empty-state-title">Jadwal tidak ditemukan.</div>
                    </div>
                @endif

            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="12" y="20" width="56" height="48" rx="6" stroke="var(--primary-blue)" stroke-width="2" fill="var(--sidebar-active-bg)"/><path d="M12 32h56" stroke="var(--primary-blue)" stroke-width="2"/><rect x="24" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><rect x="52" y="8" width="4" height="20" rx="2" fill="var(--primary-blue)"/><circle cx="30" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="40" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="50" cy="44" r="3" fill="var(--primary-blue)" opacity=".5"/><circle cx="30" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="40" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/><circle cx="50" cy="56" r="3" fill="var(--primary-blue)" opacity=".3"/></svg>
                    </div>
                    <div class="empty-state-title">Pilih jadwal di sebelah kiri untuk mulai input.</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection