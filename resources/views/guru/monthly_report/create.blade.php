@extends('layouts.app')
@section('title', 'Buat Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

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
    <div>
        <h2>{{ $report ? 'Edit' : 'Buat' }} Laporan Bulanan</h2>
        <div class="breadcrumb">Guru / Laporan Bulanan / <span>{{ $report ? 'Edit' : 'Buat' }}</span></div>
    </div>
    <a href="{{ route('guru.monthly-report.index', ['bulan' => $bulan]) }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Ringkasan Sesi --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:16px;margin-bottom:24px">
    <div class="card" style="padding:16px 20px;text-align:center">
        <div style="font-size:1.8rem;font-weight:700;color:var(--primary-blue)">{{ $totalSesi }}</div>
        <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Total Sesi</div>
    </div>
    <div class="card" style="padding:16px 20px;text-align:center">
        <div style="font-size:1.8rem;font-weight:700;color:#16a34a">{{ $totalHadir }}</div>
        <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Hadir</div>
    </div>
    <div class="card" style="padding:16px 20px;text-align:center">
        <div style="font-size:1.8rem;font-weight:700;color:{{ $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#d97706' : '#dc2626') }}">
            {{ $persen }}%
        </div>
        <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Kehadiran</div>
    </div>
    <div class="card" style="padding:16px 20px;text-align:center">
        <div style="font-size:1.8rem;font-weight:700;color:var(--primary-blue)">{{ $skorOtomatis }}</div>
        <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Skor Otomatis</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start">

    {{-- Form Laporan --}}
    <div class="card">
        <div class="card-header">
            <h3>
                <i class="fa-solid fa-file-pen" style="color:var(--primary-blue);margin-right:8px"></i>
                Form Laporan — {{ $spp->murid->nama_murid ?? '-' }}
                <span style="font-weight:400;color:var(--text-light);font-size:.85rem">
                    · {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                </span>
            </h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('guru.monthly-report.store') }}">
                @csrf
                <input type="hidden" name="id_spp" value="{{ $spp->id_spp }}">
                <input type="hidden" name="bulan"   value="{{ $bulan }}">

                <div class="form-group">
                    <label class="form-label">Skor Evaluasi <span style="color:red">*</span></label>
                    <select name="skor" class="form-control" required>
                        @foreach(['A+','A','A-','B+','B','B-','C+','C','C-'] as $s)
                            <option value="{{ $s }}"
                                {{ old('skor', $report?->skor ?? $skorOtomatis) === $s ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">
                        Skor otomatis berdasarkan kehadiran: <strong>{{ $skorOtomatis }}</strong>. Anda bisa mengubahnya.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Evaluasi Bulanan <span style="color:red">*</span></label>
                    <textarea name="evaluasi_bulanan" class="form-control" rows="8"
                              placeholder="Tuliskan evaluasi dan perkembangan murid selama bulan ini, pencapaian yang diraih, materi yang dikuasai, serta saran ke depan..."
                              required>{{ old('evaluasi_bulanan', $report?->evaluasi_bulanan) }}</textarea>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">Maks. 3000 karakter.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">URL Video KBM Bulanan (Opsional)</label>
                    <input type="url" name="url_video" class="form-control"
                           value="{{ old('url_video', $report?->url_video) }}"
                           placeholder="https://drive.google.com/... atau https://youtube.com/..."/>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:4px">
                        Link video rekaman atau highlight pembelajaran bulan ini.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ $report ? 'Perbarui Laporan' : 'Simpan Laporan' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Ringkasan Sesi --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-list-check" style="color:var(--primary-blue);margin-right:8px"></i>Rekap Sesi</h3>
        </div>
        <div style="padding:0;max-height:500px;overflow-y:auto">
            @forelse($jadwals as $j)
            @php
                $sm = $j->status_kehadiran_murid;
                $color = match($sm) {
                    'Hadir'       => '#16a34a',
                    'Tidak Hadir' => '#dc2626',
                    default       => '#d97706',
                };
            @endphp
            <div style="padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;gap:12px;align-items:center">
                <div style="width:28px;height:28px;border-radius:50%;background:var(--primary-blue);color:#fff;
                            display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;flex-shrink:0">
                    {{ $j->sesi_ke }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:.8rem;font-weight:600">{{ $j->tanggal->translatedFormat('d M Y') }}</div>
                    <div style="font-size:.72rem;color:var(--text-light)">{{ substr($j->jam_mulai, 0, 5) }}</div>
                    @if($j->progresMurid)
                        <div style="font-size:.72rem;color:var(--primary-blue);margin-top:2px">
                            {{ Str::limit($j->progresMurid->materi_diajarkan, 40) }}
                        </div>
                    @endif
                </div>
                <span style="font-size:.72rem;font-weight:700;color:{{ $color }}">{{ $sm ?? '?' }}</span>
            </div>
            @empty
                <div style="padding:24px;text-align:center;color:var(--text-light);font-size:.85rem">Tidak ada sesi.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
