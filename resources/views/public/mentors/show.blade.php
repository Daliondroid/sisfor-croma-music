@extends('layouts.public')

@section('title', 'Profil ' . $mentor['name'] . ' — Croma Music')

@section('content')
<section class="mentor-profile-header section bg-navy">
    <div class="container">
        <div class="profile-hero-flex hidden-element">
            <div class="profile-avatar-box">
                <img src="{{ asset('images/croma_logo.jpg') }}" alt="{{ $mentor['name'] }}" />
            </div>
            <div class="profile-hero-info">
                <span class="eyebrow-light">{{ $mentor['badge'] }}</span>
                <h1>{{ $mentor['name'] }}</h1>
                <p class="mentor-tagline">Mentor Pengajar Spesialis {{ ucfirst($mentor['category']) }} di Croma Music</p>
                <div class="profile-badges">
                    <span class="p-badge"><i class="fa-solid fa-certificate"></i> Verified Mentor</span>
                    <span class="p-badge"><i class="fa-solid fa-clock"></i> {{ $mentor['experience'] }} Pengalaman</span>
                    <span class="p-badge"><i class="fa-solid fa-location-dot"></i> Onsite & Home Visit</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-2 profile-content-grid">
            <div class="profile-main hidden-element">
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

            <div class="profile-sidebar hidden-element delay-100">
                <div class="card booking-widget-card">
                    <h3>Jadwalkan Sesi Bersama {{ $mentor['name'] }}</h3>
                    <p>Dapatkan sesi trial gratis 1-on-1 untuk konseling kebutuhan bermusikmu.</p>
                    <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20ingin%20jadwalkan%20trial%20bersama%20{{ urlencode($mentor['name']) }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary full-width">
                        <i class="fa-brands fa-whatsapp"></i> Konsultasi & Trial Gratis
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
