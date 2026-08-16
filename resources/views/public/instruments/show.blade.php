@extends('layouts.public')

@section('title', 'Kursus ' . $instrument['title'])

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
            <p class="hero-subtext">Semua Tingkat Kemahiran</p>
            <div class="hero-cta-group margin-top-sm">
                <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20tertarik%20dengan%20kursus%20{{ urlencode($instrument['title']) }}%20(Rp{{ number_format($instrument['price'], 0, ',', '.') }}/bulan)" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fa-brands fa-whatsapp"></i> Daftar Trial Gratis {{ $instrument['title'] }}
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="detail-grid">
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
                            <ul class="curriculum-topics">
                                <li><i class="fa-solid fa-check text-gold"></i> Pengenalan teknik dasar, posisi tubuh, & postur jari</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Membaca notasi balok & ritme sederhana</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Praktik memainkan 2–3 lagu dasar populer</li>
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
                            <ul class="curriculum-topics">
                                <li><i class="fa-solid fa-check text-gold"></i> Penguasaan tangga nada mayor & minor lengkap</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Artikulasi, dinamika, & phrasing ekspresif</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Eksplorasi repertoar & variasi aransemen lagu</li>
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
                            <ul class="curriculum-topics">
                                <li><i class="fa-solid fa-check text-gold"></i> Teknik improvisasi solo & ear training mendalam</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Aransemen lagu kompleks & teknik performa panggung</li>
                                <li><i class="fa-solid fa-check text-gold"></i> Persiapan ujian sertifikasi & resital musik</li>
                            </ul>
                        </div>
                    </details>
                </div>
            </div>

            <div class="detail-block">
                <div class="card requirement-card">
                    <h3><i class="fa-solid fa-file-lines"></i> Detail Program & Biaya</h3>
                    <div style="margin: 0.75rem 0">
                        <span style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark); font-variant-numeric: tabular-nums;">
                            Rp {{ number_format($instrument['price'], 0, ',', '.') }}
                        </span>
                        <span style="color: var(--text-light); font-size: 0.85rem;"> / bulan (4 pertemuan)</span>
                    </div>
                    <ul class="check-list">
                        <li><i class="fa-solid fa-check text-gold"></i> <strong>4 Sesi Privat per Bulan</strong> (1 sesi per minggu, durasi 45–60 menit).</li>
                        <li><i class="fa-solid fa-check text-gold"></i> <strong>Metode Belajar (Onsite &amp; Home Visit)</strong>: Pilihan belajar di studio Croma Music atau tutor datang ke rumah Anda.</li>
                        <li><i class="fa-solid fa-check text-gold"></i> <strong>Persyaratan Latihan</strong>: Memiliki akses ke instrumen {{ $instrument['title'] }} standar untuk latihan mandiri di rumah.</li>
                        <li><i class="fa-solid fa-check text-gold"></i> <strong>Evaluasi & Sertifikasi</strong>: Evaluasi berkala perkembangan bulanan dan sertifikasi level bertahap.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light" style="padding-top: 4.5rem; padding-bottom: 4.5rem;">
    <div class="container">
        <div class="section-header">
            <h2>Mentor {{ $instrument['title'] }}</h2>
            <p class="subtitle-text">Pengajar tersertifikasi yang siap membimbing latihanmu.</p>
        </div>

        <div class="grid-4 program-flex" id="mentor-instrument-grid">
            @foreach($mentors as $mentor)
                <div class="card tutor-card" data-category="{{ $mentor['category'] }}">
                    <div class="tutor-img-wrapper">
                        <div class="tutor-avatar-placeholder" aria-label="Foto {{ $mentor['name'] }}" role="img">
                            {{ strtoupper(substr($mentor['name'], 0, 1)) }}
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>{{ $mentor['name'] }}</h4>
                        <span class="badge-text">{{ $mentor['badge'] }}</span>
                        <div class="card-action-wrap margin-top-sm">
                            <a href="{{ route('mentors.show', $mentor['slug']) }}" class="btn btn-outline-dark btn-sm full-width">
                                Lihat Profil <i class="fa-solid fa-arrow-right"></i>
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
