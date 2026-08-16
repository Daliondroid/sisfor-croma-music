@extends('layouts.app')
@section('title', 'Data Absensi')
@section('page-title', 'Data Absensi')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Data Absensi Murid</h2>
        <div class="breadcrumb">Guru / <span>Data Absensi</span></div>
    </div>
    <div class="page-header-filters">
        <form method="GET" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
            @if(request('id_spp'))
                <input type="hidden" name="id_spp" value="{{ request('id_spp') }}">
            @endif
            <input type="month" name="bulan" class="form-control form-control-sm" style="width:auto"
                   value="{{ $bulan }}" onchange="this.form.submit()"/>
            <button type="submit" class="btn btn-primary btn-sm">
                Tampilkan
            </button>
            @if(request('id_spp'))
                <a href="{{ route('guru.absensi.index', ['bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Open KPI Strips --}}
<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalSesiAll }}</div>
            <div class="stat-label">Total Sesi</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalHadirAll }}</div>
            <div class="stat-label">Hadir</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalAbsenAll }}</div>
            <div class="stat-label">Tidak Hadir</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalBelumAll }}</div>
            <div class="stat-label">Belum Diisi</div>
        </div>
    </div>
    @if($totalMenunggu > 0)
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalMenunggu }}</div>
            <div class="stat-label">Perlu Verifikasi</div>
        </div>
    </div>
    @endif
</div>

{{-- Layout dua kolom --}}
<div style="display:grid;grid-template-columns:{{ $selectedSpp ? '1fr 1fr' : '1fr' }};gap:1.5rem;align-items:start">

    {{-- Tabel Rekap Per Murid --}}
    <div class="card">
        <div class="card-header">
            <h3>
                Rekap per Murid
                <span style="font-size:.78rem;color:var(--text-light);font-weight:400;margin-left:0.25rem">
                    — {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                </span>
            </h3>
        </div>

        @if($rekapAbsensi->isEmpty())
            <div class="empty-state">
                <div class="empty-state-title">Tidak ada data absensi.</div>
                <div class="empty-state-description">Tidak ada data jadwal pada bulan ini.</div>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Murid</th>
                            <th style="text-align:center">Sesi</th>
                            <th style="text-align:center;color:#16a34a">Hadir</th>
                            <th style="text-align:center;color:#dc2626">Absen</th>
                            <th style="text-align:center;color:#d97706">Belum</th>
                            <th>Kehadiran</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapAbsensi as $rekap)
                        @php
                            $isSelected = $selectedSpp && $selectedSpp->id_spp == $rekap->spp->id_spp;
                            $persen     = $rekap->persen_hadir;
                            $barColor   = $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626');
                        @endphp
                        <tr style="{{ $isSelected ? 'background:#eff6ff;' : '' }}">
                            <td>
                                <div style="font-weight:600;color:var(--text-dark)">{{ $rekap->murid->nama_murid ?? '-' }}</div>
                                <div style="font-size:.75rem;color:var(--text-light);margin-top:0.125rem">
                                    {{ $rekap->program->nama_program ?? '-' }}
                                    · <span class="badge {{ $rekap->spp->tipe_les === 'Onsite' ? 'badge-info' : 'badge-warning' }}">{{ strtoupper($rekap->spp->tipe_les) }}</span>
                                    @if($rekap->menunggu > 0)
                                        <span class="badge badge-warning" style="margin-left:0.25rem">
                                            {{ $rekap->menunggu }} PERLU VERIFIKASI
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align:center;font-weight:600;font-variant-numeric:tabular-nums">{{ $rekap->total_sesi }}</td>
                            <td style="text-align:center">
                                <span style="color:#16a34a;font-weight:700;font-variant-numeric:tabular-nums">{{ $rekap->hadir }}</span>
                            </td>
                            <td style="text-align:center">
                                <span style="color:{{ $rekap->tidak_hadir > 0 ? '#dc2626' : 'var(--text-light)' }};font-weight:{{ $rekap->tidak_hadir > 0 ? '700' : '400' }};font-variant-numeric:tabular-nums">
                                    {{ $rekap->tidak_hadir }}
                                </span>
                            </td>
                            <td style="text-align:center">
                                <span style="color:{{ $rekap->belum_diisi > 0 ? '#d97706' : 'var(--text-light)' }};font-variant-numeric:tabular-nums">
                                    {{ $rekap->belum_diisi }}
                                </span>
                            </td>
                            <td style="min-width:7.5rem">
                                <div style="display:flex;align-items:center;gap:0.5rem">
                                    <div style="flex:1;height:0.25rem;background:#f3f4f6;border-radius:0.125rem;overflow:hidden">
                                        <div style="width:{{ $persen }}%;height:100%;background:{{ $barColor }};border-radius:0.125rem;transition:.4s"></div>
                                    </div>
                                    <span style="font-size:.78rem;font-weight:700;color:{{ $barColor }};min-width:2.25rem;font-variant-numeric:tabular-nums">{{ $persen }}%</span>
                                </div>
                            </td>
                            <td style="text-align:center">
                                <a href="{{ route('guru.absensi.index', ['bulan' => $bulan, 'id_spp' => $rekap->spp->id_spp]) }}"
                                   class="btn btn-outline btn-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Detail Sesi Per Murid + Tombol Verifikasi --}}
    @if($selectedSpp)
    <div class="card">
        <div class="card-header">
            <h3>
                Detail — {{ $selectedSpp->murid->nama_murid ?? '-' }}
            </h3>
            <span style="font-size:.78rem;color:var(--text-light)">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
            </span>
        </div>

        @if($detailJadwals->isEmpty())
            <div class="empty-state">
                <div class="empty-state-title">Tidak ada jadwal ditemukan.</div>
            </div>
        @else
            <div style="padding:0">
                @foreach($detailJadwals as $j)
                @php
                    $statusMurid = $j->status_kehadiran_murid;
                    $statusGuru  = $j->status_kehadiran_guru;
                    $perluVerif  = $j->presensi_diisi_oleh === 'Murid' && is_null($j->verified_at ?? null);
                @endphp
                <div style="padding:1rem 1.5rem;border-bottom:1px solid #f3f4f6;{{ $perluVerif ? 'background:#fffbeb;' : '' }}">
                    <div style="display:flex;gap:1rem;align-items:flex-start">
                        {{-- Nomor sesi --}}
                        <div style="width:2rem;height:2rem;border-radius:0.25rem;
                                    background:{{ $perluVerif ? '#d97706' : 'var(--primary-navy)' }};
                                    color:#fff;display:flex;align-items:center;justify-content:center;
                                    font-size:.8rem;font-weight:700;flex-shrink:0">
                            {{ $j->sesi_ke }}
                        </div>

                        <div style="flex:1;min-width:0">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem;flex-wrap:wrap">
                                <div>
                                    <div style="font-weight:600;font-size:.875rem;color:var(--text-dark)">
                                        {{ $j->tanggal->translatedFormat('l, d M Y') }}
                                    </div>
                                    <div style="font-size:.78rem;color:var(--text-light);margin-top:0.125rem;font-variant-numeric:tabular-nums">
                                        {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}
                                        @if($perluVerif)
                                            <span class="badge badge-warning" style="margin-left:0.25rem">
                                                DIISI MURID
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    @if($statusMurid === 'Hadir')
                                        <span class="badge badge-success">HADIR</span>
                                    @elseif($statusMurid === 'Tidak Hadir')
                                        <span class="badge badge-danger">TIDAK HADIR</span>
                                    @else
                                        <span class="badge badge-warning">BELUM DIISI</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Status Guru --}}
                            <div style="margin-top:0.5rem;display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;font-size:.75rem">
                                <span style="color:var(--text-light)">Guru:</span>
                                @if($statusGuru === 'Hadir')
                                    <span style="color:#16a34a;font-weight:700">Hadir</span>
                                @elseif($statusGuru === 'Tidak Hadir')
                                    <span style="color:#dc2626;font-weight:700">Tidak Hadir</span>
                                @else
                                    <span style="color:#d97706;font-weight:700">Belum Diisi</span>
                                @endif

                                @if($j->waktu_presensi_diisi)
                                    <span style="color:var(--text-light);font-variant-numeric:tabular-nums">
                                        · Dicatat {{ $j->waktu_presensi_diisi->translatedFormat('d M, H:i') }}
                                        oleh {{ $j->presensi_diisi_oleh }}
                                    </span>
                                @endif
                            </div>

                            {{-- Tombol verifikasi --}}
                            @if($perluVerif)
                                <form method="POST"
                                      action="{{ route('guru.absensi.verifikasi', $j->id_jadwal) }}"
                                      style="margin-top:0.5rem">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Verifikasi Kehadiran
                                    </button>
                                </form>
                            @elseif(!is_null($j->verified_at ?? null))
                                <div style="margin-top:0.5rem;font-size:.72rem;color:#16a34a;font-variant-numeric:tabular-nums;font-weight:600">
                                    Terverifikasi {{ \Carbon\Carbon::parse($j->verified_at)->translatedFormat('d M Y, H:i') }}
                                </div>
                            @endif

                            {{-- Progres KBM --}}
                            @if($j->progresMurid)
                                <div style="margin-top:0.5rem;background:var(--bg-light);border:1px solid var(--topbar-border);border-radius:0.25rem;padding:0.5rem 1rem">
                                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:var(--text-light);margin-bottom:0.25rem">
                                        Materi KBM
                                    </div>
                                    <div style="font-size:.8rem;color:var(--text-dark);font-weight:600">{{ $j->progresMurid->materi_diajarkan }}</div>
                                    @if($j->progresMurid->catatan_perkembangan)
                                        <div style="font-size:.78rem;color:var(--text-light);margin-top:0.25rem">
                                            {{ Str::limit($j->progresMurid->catatan_perkembangan, 80) }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif
</div>
@endsection
