@extends('layouts.public')

@section('title', 'Profil ' . $mentor['name'])

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
        <div class="profile-hero-grid">
            <div class="profile-portrait-column">
                <div class="profile-overlapping-card">
                    <div class="tutor-avatar-placeholder" aria-label="Foto {{ $mentor['name'] }}" role="img">
                        {{ strtoupper(substr($mentor['name'], 0, 1)) }}
                    </div>
                </div>
            </div>
            <div class="profile-hero-info">
                <div class="margin-top-xs">
                    <span class="eyebrow-light">{{ $mentor['badge'] }}</span>
                </div>
                <h1>{{ $mentor['name'] }}</h1>
                <p class="mentor-tagline">Mentor Pengajar Spesialis {{ ucfirst($mentor['category']) }} di Croma Music</p>
            </div>
        </div>
    </div>
</section>

<section class="section mentor-profile-content-section">
    <div class="container">
        <div class="profile-content-grid">
            <div class="profile-main">
                <div class="card content-card">
                    <h3><i class="fa-solid fa-user"></i> Biografi & Pendekatan Mengajar</h3>
                    <p>
                        {{ $mentor['name'] }} merupakan praktisi musik profesional sekaligus pengajar aktif di Croma Music. 
                        Dengan pendekatan mengajar yang interaktif dan adaptif, {{ $mentor['name'] }} berfokus pada penguasaan teknik dasar yang kokoh serta pengembangan karakter bermusik setiap siswa.
                    </p>
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
