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
    <div><h2>Input Presensi & Materi</h2><div class="breadcrumb">Guru / <span>Presensi</span></div></div>
</div>

<div style="display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start">
    {{-- Pilih jadwal --}}
    <div class="card">
        <div class="card-header"><h3>Pilih Jadwal</h3></div>
        <div style="padding:0">
            @forelse($jadwals as $j)
            @php $sudah = $j->waktu_presensi_diisi !== null; @endphp
            <a href="{{ route('guru.presensi.index') }}?jadwal={{ $j->id_jadwal }}"
               style="display:block;padding:14px 18px;border-bottom:1px solid var(--topbar-border);transition:.15s;
                      {{ request('jadwal') == $j->id_jadwal ? 'background:#eff6ff;border-left:3px solid var(--primary-blue);' : '' }}
                      {{ $sudah ? 'opacity:.6;' : '' }}">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                    <div>
                        <div style="font-weight:600;font-size:.875rem">{{ $j->spp->murid->nama_murid ?? '-' }}</div>
                        <div style="font-size:.75rem;color:var(--text-light);margin-top:2px">
                            {{ \Carbon\Carbon::parse($j->tanggal)->translatedFormat('l, d M') }}
                            · {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}
                        </div>
                    </div>
                    @if($sudah)
                        <span style="font-size:.65rem;color:#16a34a;font-weight:600;white-space:nowrap">
                            <i class="fa-solid fa-circle-check"></i> Terisi
                        </span>
                    @endif
                </div>
            </a>
            @empty
                <div style="padding:24px;text-align:center;color:var(--text-light);font-size:.875rem">
                    Tidak ada jadwal aktif.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Form presensi --}}
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
                @php $selected = $jadwals->firstWhere('id_jadwal', request('jadwal')); @endphp
                @if($selected)
                @php $sudahDiisi = $selected->waktu_presensi_diisi !== null; @endphp

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
                        @endif
                    </div>
                </div>

                @if($sudahDiisi)
                    {{-- Tampilkan data yang sudah diisi --}}
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
                    <form method="POST" action="{{ route('guru.presensi.store') }}" id="presensi-form">
                        @csrf
                        <input type="hidden" name="id_jadwal" value="{{ $selected->id_jadwal }}"/>
                        <input type="hidden" name="id_murid"  value="{{ $selected->id_murid ?? '' }}"/>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Tanggal <span style="color:red">*</span></label>
                                <input type="date" name="tanggal" class="form-control"
                                       value="{{ now()->format('Y-m-d') }}" required/>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sesi Ke- <span style="color:red">*</span></label>
                                <input type="number" name="sesi_ke" class="form-control" min="1" value="1" required/>
                            </div>
                        </div>

                        <div class="form-grid" style="margin-bottom:4px">
                            <div class="form-group">
                                <label class="form-label">Kehadiran Murid <span style="color:red">*</span></label>
                                <select name="status_kehadiran_murid" id="status-select" class="form-control" required>
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

                        <div id="materi-section">
                            <hr style="border:none;border-top:1px solid var(--topbar-border);margin:16px 0"/>
                            <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">
                                <i class="fa-solid fa-book-open" style="margin-right:6px"></i>Catatan Materi
                            </div>
                            <div class="form-group">
                                <label class="form-label">Materi yang Diajarkan</label>
                                <input type="text" name="materi_diajarkan" class="form-control"
                                       placeholder="Contoh: Tangga nada C mayor, teknik fingering dasar"/>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Catatan Perkembangan</label>
                                <textarea name="catatan_perkembangan" class="form-control" rows="3"
                                          placeholder="Progres murid sesi ini..."></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tingkat Progres (0–100)</label>
                                <input type="range" name="tingkat_progres" min="0" max="100" value="70"
                                       oninput="this.nextElementSibling.textContent=this.value+'%'" style="width:100%"/>
                                <span style="font-size:.85rem;font-weight:600;color:var(--primary-blue)">70%</span>
                            </div>

                            <hr style="border:none;border-top:1px solid var(--topbar-border);margin:16px 0"/>
                            <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">
                                <i class="fa-brands fa-youtube" style="margin-right:6px"></i>Video Progres (Opsional)
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">URL Video</label>
                                    <input type="url" name="url_video" class="form-control"
                                           placeholder="https://drive.google.com/..."/>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Platform</label>
                                    <select name="platform" class="form-control">
                                        <option value="google_drive">Google Drive</option>
                                        <option value="youtube_private">YouTube Private</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Deskripsi Video</label>
                                <input type="text" name="deskripsi_video" class="form-control"
                                       placeholder="Singkat tentang isi video"/>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Presensi
                        </button>
                    </form>
                @endif
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

@push('scripts')
<script>
    document.getElementById('status-select')?.addEventListener('change', function () {
        const section = document.getElementById('materi-section');
        if (section) {
            section.style.display = this.value === 'Hadir' ? 'block' : 'none';
        }
    });
</script>
@endpush
@endsection
