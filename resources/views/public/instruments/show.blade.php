@extends('layouts.public')

@section('title', 'Kursus ' . $instrument['title'] . ' — Croma Music')

@section('content')
<section class="instrument-detail-hero section bg-navy">
    <div class="container">
        <div class="instrument-hero-content text-white hidden-element">
            <span class="eyebrow-light">{{ $instrument['family'] }}</span>
            <h1>Kursus {{ $instrument['title'] }} Modern</h1>
            <p class="hero-subtext">{{ $instrument['desc'] }} • Genre: {{ $instrument['genres'] }}</p>
            <div class="hero-cta-group">
                <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20tertarik%20dengan%20kursus%20{{ urlencode($instrument['title']) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fa-brands fa-whatsapp"></i> Daftar Trial Gratis {{ $instrument['title'] }}
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-2 detail-grid">
            <div class="detail-block hidden-element">
                <div class="section-header align-left">
                    <span class="eyebrow">Kurikulum Terstruktur</span>
                    <h2>Alur Belajar & Kurikulum</h2>
                    <p>Materi pembelajaran disusun secara bertahap dari tingkat dasar hingga mahir.</p>
                </div>
                <div class="curriculum-track">
                    <div class="curriculum-step">
                        <div class="step-badge">Level 1</div>
                        <h4>Beginner (Dasar)</h4>
                        <p>Pengenalan anatomi instrumen, posisi tubuh ideal, postur jari/tangan, serta membaca notasi & ritme dasar.</p>
                    </div>
                    <div class="curriculum-step">
                        <div class="step-badge">Level 2</div>
                        <h4>Intermediate (Menengah)</h4>
                        <p>Penguasaan tangga nada, eksplorasi variasi akor & artikulasi, serta memainkan lagu populer pilihanmu.</p>
                    </div>
                    <div class="curriculum-step">
                        <div class="step-badge">Level 3</div>
                        <h4>Advanced (Mahir & Improv)</h4>
                        <p>Improvisasi solo, latihan pendengaran (ear training) mendalam, serta persiapan ujian performa panggung.</p>
                    </div>
                </div>
            </div>

            <div class="detail-block hidden-element delay-100">
                <div class="card requirement-card">
                    <h3><i class="fa-solid fa-clipboard-list"></i> Persyaratan Peralatan</h3>
                    <ul class="check-list">
                        <li><i class="fa-solid fa-check text-gold"></i> Memiliki akses ke instrumen {{ $instrument['title'] }} standar untuk latihan mandiri di rumah.</li>
                        <li><i class="fa-solid fa-check text-gold"></i> Untuk program Onsite, instrumen utama telah disediakan lengkap di studio Croma Music.</li>
                        <li><i class="fa-solid fa-check text-gold"></i> Buku materi & lembar partitur digital disediakan langsung oleh mentor.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="section-header hidden-element">
            <span class="eyebrow">Tim Pengajar</span>
            <h2>Mentor {{ $instrument['title'] }}</h2>
            <p>Pengajar tersertifikasi yang siap membimbing latihanmu.</p>
        </div>

        <div class="grid-3 program-flex">
            @foreach($mentors as $mentor)
                <div class="card tutor-card hidden-element {{ $mentor['delay'] }}">
                    <div class="tutor-img-wrapper">
                        <img src="{{ asset('images/croma_logo.jpg') }}" alt="Foto {{ $mentor['name'] }}" loading="lazy" />
                    </div>
                    <div class="card-body">
                        <h4>{{ $mentor['name'] }}</h4>
                        <span class="badge-text">{{ $mentor['badge'] }}</span>
                        <div class="mentor-action">
                            <a href="{{ route('mentors.show', $mentor['slug']) }}" class="btn-text">Profil Lengkap →</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
