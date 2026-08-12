@extends('layouts.public')

@section('title', 'Profil ' . $mentor['name'] . ' — Croma Music')

@section('content')
<section class="mentor-profile-header section bg-navy">
    <div class="breadcrumb-container">
        <nav class="breadcrumb-trail" aria-label="Breadcrumb Navigasi">
            <a href="{{ route('home') }}">Beranda</a>
            <span class="sep"><i class="fa-solid fa-chevron-right"></i></span>
            <a href="{{ route('mentors.index') }}">Direktori Mentor</a>
            <span class="sep"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="current">{{ $mentor['name'] }}</span>
        </nav>
    </div>
    <div class="container">
        <div class="profile-hero-flex margin-top-xs">
            <div class="profile-avatar-box">
                <img src="{{ asset('images/croma_logo.jpg') }}" alt="{{ $mentor['name'] }}" />
            </div>
            <div class="profile-hero-info">
                <span class="eyebrow-light">{{ $mentor['badge'] }}</span>
                <h1>{{ $mentor['name'] }}</h1>
                <p class="mentor-tagline">Mentor Pengajar Spesialis {{ ucfirst($mentor['category']) }} di Croma Music</p>
                <div class="profile-meta-line">
                    <span class="meta-item"><i class="fa-solid fa-certificate text-gold"></i> Verified Mentor</span>
                    <span class="meta-dot">•</span>
                    <span class="meta-item"><i class="fa-solid fa-clock text-gold"></i> {{ $mentor['experience'] }} Pengalaman</span>
                    <span class="meta-dot">•</span>
                    <span class="meta-item"><i class="fa-solid fa-location-dot text-gold"></i> Onsite & Home Visit</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-2 profile-content-grid">
            <div class="profile-main">
                <div class="card content-card">
                    <h3><i class="fa-solid fa-user"></i> Biografi & Pendekatan Mengajar</h3>
                    <p>
                        {{ $mentor['name'] }} merupakan praktisi musik profesional sekaligus pengajar aktif di Croma Music. 
                        Dengan pendekatan mengajar yang interaktif dan adaptif, {{ $mentor['name'] }} berfokus pada penguasaan teknik dasar yang kokoh serta pengembangan karakter bermusik setiap siswa.
                    </p>
                </div>

                <!-- Media Showcase Module -->
                <div class="card content-card media-sample-card">
                    <h3><i class="fa-solid fa-circle-play"></i> Sampel Performa & Video Mengajar</h3>
                    <p class="subtitle-text">Dengarkan karakter vokal/instrumen dan simulasi suasana belajar bersama {{ $mentor['name'] }}.</p>
                    <div class="media-player-box margin-top-sm">
                        <div class="audio-sample-item">
                            <div class="audio-info">
                                <strong>Demo Performa Instrumental / Vokal</strong>
                                <span class="text-sm">Sampel audio rekaman langsung studio Croma Music</span>
                            </div>
                            <button class="btn btn-outline-dark btn-sm audio-play-btn" type="button">
                                <i class="fa-solid fa-play"></i> Putar Sampel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card content-card">
                    <h3><i class="fa-solid fa-graduation-cap"></i> Kualifikasi & Sertifikasi</h3>
                    <ul class="check-list">
                        <li><i class="fa-solid fa-check text-gold"></i> Pendidikan formal / sertifikasi akademis pengajaran musik terverifikasi.</li>
                        <li><i class="fa-solid fa-check text-gold"></i> Berpengalaman mendampingi murid dari usia anak-anak hingga dewasa.</li>
                        <li><i class="fa-solid fa-check text-gold"></i> Aktif sebagai performer profesional dan composer/arranger musik.</li>
                    </ul>
                </div>
            </div>

            <div class="profile-sidebar">
                <div class="sticky-sidebar-wrap">
                    <div class="card booking-widget-card">
                        <h3>Jadwalkan Sesi Bersama {{ $mentor['name'] }}</h3>
                        <p class="subtitle-text">Dapatkan 1 sesi trial gratis 1-on-1 untuk konseling & konsultasi kebutuhan bermusikmu.</p>
                        <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20ingin%20jadwalkan%20trial%20bersama%20{{ urlencode($mentor['name']) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary full-width margin-top-sm">
                            <i class="fa-brands fa-whatsapp"></i> Konsultasi & Trial Gratis
                        </a>
                    </div>

                    <div class="card proof-widget-card margin-top-sm">
                        <div class="proof-header">
                            <div class="rating-stars">
                                <i class="fa-solid fa-star text-gold"></i>
                                <i class="fa-solid fa-star text-gold"></i>
                                <i class="fa-solid fa-star text-gold"></i>
                                <i class="fa-solid fa-star text-gold"></i>
                                <i class="fa-solid fa-star text-gold"></i>
                            </div>
                            <strong>4.9 / 5.0 (28 Ulasan Murid)</strong>
                        </div>
                        <p class="quote-text">"Penjelasan Kak {{ explode(' ', $mentor['name'])[1] ?? $mentor['name'] }} sangat sabar dan ramah. Dalam 3 bulan latihan sudah bisa main lagu favorit!"</p>
                        <span class="student-name">— Murid Program Privat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Persistent Sticky Bottom CTA Bar -->
<div class="mobile-sticky-bottom-cta">
    <div class="container">
        <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20ingin%20jadwalkan%20trial%20bersama%20{{ urlencode($mentor['name']) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary full-width">
            <i class="fa-brands fa-whatsapp"></i> Konsultasi & Trial Gratis {{ $mentor['name'] }}
        </a>
    </div>
</div>
@endsection
