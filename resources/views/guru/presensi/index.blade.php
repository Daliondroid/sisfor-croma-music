@extends('layouts.app')
@section('title', 'Input Presensi')
@section('page-title', 'Input Presensi')
@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('guru.dashboard') }}" class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('guru.presensi.index') }}" class="nav-item {{ request()->routeIs('guru.presensi*') ? 'active' : '' }}">
        <i class="fa-solid fa-clipboard-check"></i> Input Presensi
    </a>
    <a href="{{ route('guru.profil.edit') }}" class="nav-item {{ request()->routeIs('guru.profil*') ? 'active' : '' }}">
        <i class="fa-solid fa-user-pen"></i> Profil Saya
    </a>
@endsection

@section('content')
<div class="page-header">
    <div><h2>Input Presensi & Materi</h2><div class="breadcrumb">Guru / <span>Presensi</span></div></div>
</div>

<div style="display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start">
    <!-- Pilih jadwal -->
    <div class="card">
        <div class="card-header"><h3>Pilih Jadwal</h3></div>
        <div style="padding:0">
            @forelse($jadwals as $j)
                <a href="{{ route('guru.presensi.index') }}?jadwal={{ $j->id_jadwal }}"
                   style="display:block;padding:16px 20px;border-bottom:1px solid #f0f0f0;transition:.15s;{{ request('jadwal')==$j->id_jadwal ? 'background:#eff6ff;border-left:3px solid var(--primary-blue)' : '' }}">
                    <div style="font-weight:600">{{ $j->murid->nama_murid }}</div>
                    <div style="font-size:.78rem;color:var(--text-light)">{{ $j->hari }} · {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</div>
                </a>
            @empty
                <div style="padding:24px;text-align:center;color:var(--text-light)">Tidak ada jadwal aktif.</div>
            @endforelse
        </div>
    </div>

    <!-- Form presensi -->
    <div class="card">
        <div class="card-header"><h3>Form Presensi Sesi</h3></div>
        <div class="card-body">
            @if(request('jadwal'))
                @php $selected = $jadwals->firstWhere('id_jadwal', request('jadwal')); @endphp
                @if($selected)
                <div style="background:#f8faff;border:1px solid #dbeafe;border-radius:8px;padding:14px;margin-bottom:20px">
                    <strong>{{ $selected->murid->nama_murid }}</strong> —
                    {{ $selected->kelas->nama_kelas ?? '' }} ·
                    {{ $selected->hari }}, {{ substr($selected->jam_mulai,0,5) }}–{{ substr($selected->jam_selesai,0,5) }}
                    @if($selected->lokasi) · {{ $selected->lokasi }} @endif
                </div>
                <form method="POST" action="{{ route('guru.presensi.store') }}">
                    @csrf
                    <input type="hidden" name="id_jadwal" value="{{ $selected->id_jadwal }}"/>
                    <input type="hidden" name="id_murid" value="{{ $selected->id_murid }}"/>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Tanggal <span style="color:red">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ now()->format('Y-m-d') }}" required/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sesi Ke- <span style="color:red">*</span></label>
                            <input type="number" name="sesi_ke" class="form-control" min="1" value="1" required/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status Kehadiran <span style="color:red">*</span></label>
                            <select name="status_murid" class="form-control" id="status-select" required>
                                <option value="hadir">✅ Hadir</option>
                                <option value="izin">🟡 Izin</option>
                                <option value="alpa">❌ Alpa</option>
                            </select>
                        </div>
                    </div>

                    <div id="materi-section">
                        <hr style="border:none;border-top:1px solid #f0f0f0;margin:16px 0"/>
                        <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">
                            <i class="fa-solid fa-book-open" style="margin-right:6px"></i>Catatan Materi
                        </div>
                        <div class="form-group">
                            <label class="form-label">Materi yang Diajarkan <span style="color:red">*</span></label>
                            <input type="text" name="materi_diajarkan" class="form-control" placeholder="Contoh: Tangga nada C mayor, teknik fingering dasar"/>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Catatan Perkembangan</label>
                            <textarea name="catatan_perkembangan" class="form-control" rows="3" placeholder="Progres murid sesi ini..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tingkat Progres (0–100)</label>
                            <input type="range" name="tingkat_progres" min="0" max="100" value="70" oninput="this.nextElementSibling.textContent=this.value+'%'" style="width:100%"/>
                            <span style="font-size:.85rem;font-weight:600;color:var(--primary-blue)">70%</span>
                        </div>

                        <hr style="border:none;border-top:1px solid #f0f0f0;margin:16px 0"/>
                        <div style="font-weight:600;margin-bottom:14px;color:var(--primary-blue)">
                            <i class="fa-brands fa-youtube" style="margin-right:6px"></i>Video Progres (Opsional)
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">URL Video</label>
                                <input type="url" name="url_video" class="form-control" placeholder="https://drive.google.com/..."/>
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
                            <input type="text" name="deskripsi_video" class="form-control" placeholder="Singkat tentang isi video"/>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Presensi
                    </button>
                </form>
                @endif
            @else
                <div style="text-align:center;padding:40px;color:var(--text-light)">
                    <i class="fa-solid fa-arrow-left" style="font-size:1.5rem;margin-bottom:12px;opacity:.3"></i>
                    <p>Pilih jadwal di sebelah kiri untuk mulai input.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('status-select')?.addEventListener('change', function() {
        document.getElementById('materi-section').style.display = this.value === 'hadir' ? 'block' : 'none';
    });
</script>
@endpush
@endsection