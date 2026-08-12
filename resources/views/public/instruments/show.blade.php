@extends('layouts.public')

@section('title', 'Kursus ' . $instrument['title'] . ' — Croma Music')

@section('content')
<section class="instrument-detail-hero section bg-navy">
    <div class="breadcrumb-container">
        <nav class="breadcrumb-trail" aria-label="Breadcrumb Navigasi">
            <a href="{{ route('home') }}">Beranda</a>
            <span class="sep"><i class="fa-solid fa-chevron-right"></i></span>
            <a href="{{ route('instruments.index') }}">Katalog Instrumen</a>
            <span class="sep"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="current">{{ $instrument['title'] }}</span>
        </nav>
    </div>
    <div class="container">
        <div class="instrument-hero-content text-white text-center">
            <div class="margin-top-xs">
                <span class="eyebrow-light">{{ $instrument['family'] }}</span>
            </div>
            <h1>Kursus {{ $instrument['title'] }} Modern</h1>
            <p class="hero-subtext">Program privat komprehensif • Genre: {{ $instrument['genres'] }} • Tingkat: {{ $instrument['levels'] }}</p>
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
            <div class="detail-block">
                <div class="section-header align-left">
                    <h2>Alur Belajar & Kurikulum</h2>
                    <p class="subtitle-text">Materi pembelajaran disusun secara bertahap dari tingkat dasar hingga mahir.</p>
                </div>
                
                <div class="curriculum-track">
                    <details class="curriculum-step card" open>
                        <summary>
                            <div>
                                <div class="step-badge">Level 1</div>
                                <h4>Beginner (Dasar)</h4>
                            </div>
                            <i class="fa-solid fa-chevron-down summary-icon"></i>
                        </summary>
                        <div class="curriculum-content">
                            <p>Pengenalan anatomi instrumen, posisi tubuh ideal, postur jari/tangan, serta membaca notasi & ritme dasar.</p>
                            <ul class="curriculum-topics margin-top-xs">
                                <li><i class="fa-solid fa-check text-gold"></i> Pengenalan teknik dasar & postur jari</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Membaca notasi balok & ritme sederhana</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Memainkan 2-3 lagu beginner populer</li>
                            </ul>
                        </div>
                    </details>

                    <details class="curriculum-step card">
                        <summary>
                            <div>
                                <div class="step-badge">Level 2</div>
                                <h4>Intermediate (Menengah)</h4>
                            </div>
                            <i class="fa-solid fa-chevron-down summary-icon"></i>
                        </summary>
                        <div class="curriculum-content">
                            <p>Penguasaan tangga nada, eksplorasi variasi akor & artikulasi, serta memainkan lagu populer pilihanmu.</p>
                            <ul class="curriculum-topics margin-top-xs">
                                <li><i class="fa-solid fa-check text-gold"></i> Tangga nada mayor & minor lengkap</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Artikulasi, dinamika, & phrasing lagu</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Eksplorasi genre pop, jazz, & rock</li>
                            </ul>
                        </div>
                    </details>

                    <details class="curriculum-step card">
                        <summary>
                            <div>
                                <div class="step-badge">Level 3</div>
                                <h4>Advanced (Mahir & Improv)</h4>
                            </div>
                            <i class="fa-solid fa-chevron-down summary-icon"></i>
                        </summary>
                        <div class="curriculum-content">
                            <p>Improvisasi solo, latihan pendengaran (ear training) mendalam, serta persiapan ujian performa panggung.</p>
                            <ul class="curriculum-topics margin-top-xs">
                                <li><i class="fa-solid fa-check text-gold"></i> Teknik improvisasi solo & ear training</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Aransemen lagu kompleks & teknik panggung</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Persiapan ujian sertifikasi / performa recital</li>
                            </ul>
                        </div>
                    </details>
                </div>
            </div>

            <div class="detail-block">
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
        <div class="section-header">
            <h2>Mentor {{ $instrument['title'] }}</h2>
            <p class="subtitle-text">Pengajar tersertifikasi yang siap membimbing latihanmu.</p>
        </div>

        <div class="grid-3 program-flex">
            @foreach($mentors as $mentor)
                <div class="card tutor-card">
                    <div class="tutor-img-wrapper">
                        <img src="{{ asset('images/croma_logo.jpg') }}" alt="Foto {{ $mentor['name'] }}" loading="lazy" />
                    </div>
                    <div class="card-body">
                        <h4>{{ $mentor['name'] }}</h4>
                        <span class="badge-text">{{ $mentor['badge'] }}</span>
                        <div class="mentor-action margin-top-sm">
                            <a href="{{ route('mentors.show', $mentor['slug']) }}" class="btn btn-outline-dark btn-sm full-width">
                                lihat Profil <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Bottom Conversion Banner -->
<section class="section bottom-cta-banner text-white text-center">
    <div class="container">
        <div class="bottom-cta-content">
            <h2>Siap Memulai Kursus {{ $instrument['title'] }}?</h2>
            <p class="hero-subtext">Konsultasikan kebutuhanmu dan dapatkan 1 sesi trial gratis 1-on-1 bersama mentor pengajar pilihanmu.</p>
            <div class="margin-top-sm">
                <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20tertarik%20dengan%20kursus%20{{ urlencode($instrument['title']) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fa-brands fa-whatsapp"></i> Daftar Trial Gratis {{ $instrument['title'] }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
