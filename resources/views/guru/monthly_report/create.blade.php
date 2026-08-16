@extends('layouts.app')
@section('title', 'Buat Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('sidebar-menu') @include('guru.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div>
        <h2>{{ $report ? 'Edit' : 'Buat' }} Laporan Bulanan</h2>
        <div class="breadcrumb">Guru / Laporan Bulanan / <span>{{ $report ? 'Edit' : 'Buat' }}</span></div>
    </div>
</div>

{{-- Open KPI Strips --}}
<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalSesi }}</div>
            <div class="stat-label">Total Sesi</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $totalHadir }}</div>
            <div class="stat-label">Hadir</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">
                {{ $persen }}%
            </div>
            <div class="stat-label">Kehadiran</div>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value" style="font-variant-numeric:tabular-nums">{{ $skorOtomatis }}</div>
            <div class="stat-label">Skor Otomatis</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 21.25rem;gap:1.5rem;align-items:start">

    {{-- Form Laporan --}}
    <div class="card">
        <div class="card-header">
            <h3>
                Form Laporan &mdash; {{ $spp->murid->nama_murid ?? '-' }}
                <span style="font-weight:400;color:var(--text-light);font-size:.85rem">
                    · {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                </span>
            </h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
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
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">
                        Skor otomatis berdasarkan kehadiran: <strong>{{ $skorOtomatis }}</strong>. Anda bisa mengubahnya.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Evaluasi Bulanan <span style="color:red">*</span></label>
                    <textarea name="evaluasi_bulanan" class="form-control" rows="8"
                              placeholder="Tuliskan evaluasi dan perkembangan murid selama bulan ini, pencapaian yang diraih, materi yang dikuasai, serta saran ke depan..."
                              required>{{ old('evaluasi_bulanan', $report?->evaluasi_bulanan) }}</textarea>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">Maks. 3000 karakter.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">URL Video KBM Bulanan (Opsional)</label>
                    <input type="url" name="url_video" class="form-control"
                           value="{{ old('url_video', $report?->url_video) }}"
                           placeholder="https://drive.google.com/... atau https://youtube.com/..."/>
                    <div style="font-size:.72rem;color:var(--text-light);margin-top:0.25rem">
                        Link video rekaman atau highlight pembelajaran bulan ini.
                    </div>
                </div>

                <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
                    <button type="submit" class="btn btn-primary btn-sm">
                        {{ $report ? 'Perbarui Laporan' : 'Simpan Laporan' }}
                    </button>
                    <a href="{{ route('guru.monthly-report.index', ['bulan' => $bulan]) }}" class="btn btn-outline btn-sm">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan Sesi --}}
    <div class="card">
        <div class="card-header">
            <h3>Rekap Sesi</h3>
        </div>
        <div style="padding:0;max-height:31.25rem;overflow-y:auto">
            @forelse($jadwals as $j)
            @php
                $sm = $j->status_kehadiran_murid;
                $color = match($sm) {
                    'Hadir'       => '#16a34a',
                    'Tidak Hadir' => '#dc2626',
                    default       => '#d97706',
                };
            @endphp
            <div style="padding:1rem 1rem;border-bottom:1px solid #f3f4f6;display:flex;gap:1rem;align-items:center">
                <div style="width:2rem;height:2rem;border-radius:0.25rem;background:var(--primary-navy);color:#fff;
                            display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;flex-shrink:0">
                    {{ $j->sesi_ke }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:.8rem;font-weight:600;color:var(--text-dark)">{{ $j->tanggal->translatedFormat('d M Y') }}</div>
                    <div style="font-size:.72rem;color:var(--text-light);font-variant-numeric:tabular-nums">{{ substr($j->jam_mulai, 0, 5) }}</div>
                    @if($j->progresMurid)
                        <div style="font-size:.72rem;color:var(--text-dark);margin-top:0.125rem">
                            {{ Str::limit($j->progresMurid->materi_diajarkan, 40) }}
                        </div>
                    @endif
                </div>
                <span style="font-size:.72rem;font-weight:700;color:{{ $color }}">{{ strtoupper($sm ?? 'BELUM') }}</span>
            </div>
            @empty
                <div style="padding:1.5rem;text-align:center;color:var(--text-light);font-size:.85rem">Tidak ada sesi.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
