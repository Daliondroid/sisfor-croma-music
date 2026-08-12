@extends('layouts.public')

@section('title', 'Croma Music | Sekolah Musik Modern')

@push('preload')
<link rel="preload" as="image" href="{{ asset('images/TIM05080-1140-x-570.png') }}" fetchpriority="high">
@endpush

@php
$programs = [
    ['title' => 'Piano', 'desc' => 'Klasik, Pop, & Jazz', 'img' => 'images/piano.avif'],
    ['title' => 'Gitar', 'desc' => 'Akustik & Elektrik', 'img' => 'images/guitar.avif'],
    ['title' => 'Drum', 'desc' => 'Rhythm & Percussion', 'img' => 'images/drums.avif'],
    ['title' => 'Vokal', 'desc' => 'Teknik & Performance', 'img' => 'images/vocals.avif'],
    ['title' => 'Keyboard', 'desc' => 'Synthesizer & Arranger', 'img' => 'images/keyboards.avif'],
    ['title' => 'Bass', 'desc' => 'Groove & Slap Technique', 'img' => 'images/bass.avif'],
    ['title' => 'Flute', 'desc' => 'Klasik & Orkestra', 'img' => 'images/flute.avif'],
    ['title' => 'Saxophone', 'desc' => 'Jazz & Pop Brass', 'img' => 'images/sax.avif'],
];

$featuredMentors = [
    ['name' => 'Kak Budi', 'slug' => 'kak-budi', 'badge' => 'Piano Expert', 'category' => 'piano'],
    ['name' => 'Kak Sarah', 'slug' => 'kak-sarah', 'badge' => 'Vocal Coach', 'category' => 'vocal'],
    ['name' => 'Kak Reza', 'slug' => 'kak-reza', 'badge' => 'Guitarist', 'category' => 'guitar'],
    ['name' => 'Kak Doni', 'slug' => 'kak-doni', 'badge' => 'Drummer', 'category' => 'drum'],
];
@endphp

@section('content')
<!-- 1. Hero Gateway Section -->
<section id="home" class="hero">
	<div
		class="hero-bg-lower"
		style="background-image: url('{{ asset('images/TIM05080-1140-x-570.png') }}');"
	></div>
	<div class="hero-bg-upper"></div>
	<div class="hero-navy-column">
		<div class="hero-content">
			<h1 class="hero-title">
				Asah Bakat <span class="highlight">Musikmu</span> Bersama Kami
			</h1>
			<p class="hero-desc">
				Platform belajar musik modern di Jabodetabek. Pilih metode
				Onsite <br> atau Home Visit dengan jadwal yang fleksibel.
			</p>
			<div class="hero-actions">
				<a
					href="https://wa.me/628123456789"
					class="btn btn-primary"
					target="_blank"
					rel="noopener noreferrer"
				>
					<i class="fa-brands fa-whatsapp"></i> Daftar Free Trial
				</a>
				<a href="#program" class="btn btn-outline" aria-label="Lihat program kursus">
					Lihat Program <i class="fa-solid fa-chevron-down"></i>
				</a>
			</div>
		</div>
	</div>
</section>

<!-- 2. Keunggulan & Cara Belajar Section -->
<section id="about" class="section bg-light">
	<div class="container">
		<div class="section-header text-center">
			<h2>Keunggulan & Cara Belajar</h2>
			<p>Pengalaman belajar musik fleksibel, profesional, dan terstruktur.</p>
		</div>

		<div class="grid-3 feature-flex">
			<div class="card feature-card">
				<div class="icon-box">
					<i class="fa-solid fa-house-laptop"></i>
				</div>
				<h3>Onsite & Home Visit</h3>
				<p>Belajar di studio kami yang nyaman atau guru profesional kami datang langsung ke rumah Anda.</p>
			</div>
			<div class="card feature-card">
				<div class="icon-box">
					<i class="fa-regular fa-clock"></i>
				</div>
				<h3>Jadwal Fleksibel</h3>
				<p>Kesibukan bukan halangan. Atur dan reschedule jadwal latihan musikmu dengan mudah melalui sistem kami.</p>
			</div>
			<div class="card feature-card">
				<div class="icon-box">
					<i class="fa-solid fa-user-tie"></i>
				</div>
				<h3>Tutor Berpengalaman</h3>
				<p>Tutor kami memiliki latar belakang pendidikan formal di bidang musik atau tersertifikasi setara.</p>
			</div>
		</div>
	</div>
</section>

<!-- 3. Program Teaser Section (Static 4-Column Grid) -->
<section id="program" class="section">
	<div class="container">
		<div class="section-header text-center">
			<h2>Program Unggulan</h2>
			<p>Pilih instrumen favoritmu dan mulai perjalanan musikmu bersama pengajar profesional.</p>
		</div>

		<div class="program-grid-4">
			@foreach($programs as $program)
				<div class="card program-card">
					<div class="program-img-wrap">
						<img
							src="{{ asset($program['img']) }}"
							alt="{{ $program['title'] }}"
							class="card-bg-img"
							loading="lazy"
						/>
					</div>
					<div class="card-overlay">
						<h3>{{ $program['title'] }}</h3>
						<p>{{ $program['desc'] }}</p>
					</div>
				</div>
			@endforeach
		</div>

		<div class="text-center margin-top-lg">
			<a href="{{ route('instruments.index') }}" class="btn btn-outline-dark">
				Jelajahi Katalog Instrumen <i class="fa-solid fa-arrow-right"></i>
			</a>
		</div>
	</div>
</section>

<!-- 4. Demo Video Section (Suasana Belajar) -->
<section id="demo-video-section" class="section bg-light">
	<div class="container">
		<div class="section-header text-center">
			<h2>Suasana Belajar</h2>
			<p>
				Lihat suasana dan metode pembelajaran instrumen musik di Croma Music.
			</p>
		</div>

		<div class="demo-video-card card">
			<div
				class="demo-poster"
				style="background-image: url('{{ asset('images/others.avif') }}');"
			>
				<div class="demo-placeholder-content">
					<div class="demo-icon">
						<i class="fa-solid fa-film"></i>
					</div>
					<h3>Video Demo Segera Hadir</h3>
					<p>Saksikan langsung bagaimana keseruan dan kualitas latihan musik di Croma Music.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 5. Mentor Spotlight Teaser Section (Tim Pengajar) -->
<section id="tutor" class="section">
	<div class="container">
		<div class="section-header text-center">
			<h2>Tim Pengajar</h2>
			<p>Belajar langsung dari pengajar musik profesional dan tersertifikasi.</p>
		</div>

		<div class="grid-4 program-flex">
			@foreach($featuredMentors as $mentor)
				<div class="card tutor-card">
					<div class="tutor-img-wrapper">
						{{-- TODO: Replace placeholder below with real mentor photo <img> when client provides assets --}}
						<div class="tutor-avatar-placeholder" aria-label="Foto {{ $mentor['name'] }}" role="img">
							{{ strtoupper(substr($mentor['name'], 0, 1)) }}
						</div>
					</div>
					<div class="card-body">
						<h4>{{ $mentor['name'] }}</h4>
						<span class="badge-text">{{ $mentor['badge'] }}</span>
						<div class="card-action-wrap margin-top-sm">
							<a href="{{ route('mentors.show', $mentor['slug']) }}" class="btn btn-outline-dark btn-sm">
								Lihat Profil
							</a>
						</div>
					</div>
				</div>
			@endforeach
		</div>

		<div class="text-center margin-top-lg">
			<a href="{{ route('mentors.index') }}" class="btn btn-outline-dark">
				Lihat Seluruh Direktori Mentor <i class="fa-solid fa-arrow-right"></i>
			</a>
		</div>
	</div>
</section>
@endsection

