@extends('layouts.public')

@section('title', 'Croma Music | Sekolah Musik Modern')

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
    ['name' => 'Kak Budi', 'slug' => 'kak-budi', 'badge' => 'Piano Expert', 'category' => 'piano', 'delay' => ''],
    ['name' => 'Kak Sarah', 'slug' => 'kak-sarah', 'badge' => 'Vocal Coach', 'category' => 'vocal', 'delay' => 'delay-100'],
    ['name' => 'Kak Reza', 'slug' => 'kak-reza', 'badge' => 'Guitarist', 'category' => 'guitar', 'delay' => 'delay-200'],
    ['name' => 'Kak Doni', 'slug' => 'kak-doni', 'badge' => 'Drummer', 'category' => 'drum', 'delay' => 'delay-300'],
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
	<div class="container hero-content hidden-element">
		<h1 class="hero-title">
			Asah Bakat <span class="highlight">Musikmu</span> Bersama Kami
		</h1>
		<p class="hero-desc">
			Platform belajar musik modern di Jabodetabek. Pilih metode
			Onsite atau Home Visit dengan jadwal yang fleksibel.
		</p>
		<div class="hero-actions">
			<span class="trial-text">Mau coba gratis?</span>
			<a
				href="https://wa.me/628123456789"
				class="btn btn-primary pulse-animation"
				target="_blank"
				rel="noopener noreferrer"
			>
				<i class="fa-brands fa-whatsapp"></i> Daftar Free Trial
			</a>
		</div>
	</div>
</section>

<!-- 2. Program Teaser Carousel Section -->
<section id="program" class="section">
	<div class="container">
		<div class="section-header hidden-element">
			<span class="eyebrow">Program Kursus</span>
			<h2>Program Unggulan</h2>
			<p>Pilih instrumen favoritmu dan mulai perjalanan musikmu bersama pengajar profesional.</p>
		</div>

		<div class="carousel-wrapper hidden-element">
			<div class="carousel-track" id="carousel-track">
				@foreach(array_merge($programs, $programs) as $program)
					<div class="card program-card carousel-item">
						<img
							src="{{ asset($program['img']) }}"
							alt="{{ $program['title'] }}"
							class="card-bg-img"
							loading="lazy"
						/>
						<div class="card-overlay">
							<h3>{{ $program['title'] }}</h3>
							<p>{{ $program['desc'] }}</p>
						</div>
					</div>
				@endforeach
			</div>
		</div>

		<div class="text-center margin-top-lg hidden-element">
			<a href="{{ route('instruments.index') }}" class="btn btn-outline-dark">
				Jelajahi Katalog Instrumen <i class="fa-solid fa-arrow-right"></i>
			</a>
		</div>
	</div>
</section>

<!-- 3. Keunggulan & Cara Belajar Section -->
<section id="about" class="section bg-light">
	<div class="container">
		<div class="section-header text-center hidden-element">
			<span class="eyebrow">Standar Kualitas</span>
			<h2>Keunggulan & Cara Belajar</h2>
			<p>Pengalaman belajar musik fleksibel, profesional, dan terstruktur.</p>
		</div>

		<div class="grid-3 feature-flex">
			<div class="card feature-card hidden-element delay-100">
				<div class="icon-box">
					<i class="fa-solid fa-house-laptop"></i>
				</div>
				<h3>Onsite & Home Visit</h3>
				<p>Belajar di studio kami yang nyaman atau guru profesional kami datang langsung ke rumah Anda.</p>
			</div>
			<div class="card feature-card hidden-element delay-200">
				<div class="icon-box">
					<i class="fa-regular fa-clock"></i>
				</div>
				<h3>Jadwal Fleksibel</h3>
				<p>Kesibukan bukan halangan. Atur dan reschedule jadwal latihan musikmu dengan mudah melalui sistem kami.</p>
			</div>
			<div class="card feature-card hidden-element delay-300">
				<div class="icon-box">
					<i class="fa-solid fa-user-tie"></i>
				</div>
				<h3>Tutor Berpengalaman</h3>
				<p>Tutor kami memiliki latar belakang pendidikan formal di bidang musik atau tersertifikasi setara.</p>
			</div>
		</div>
	</div>
</section>

<!-- 4. Mentor Spotlight Teaser Section -->
<section id="tutor" class="section">
	<div class="container">
		<div class="section-header hidden-element">
			<span class="eyebrow">Tim Pengajar</span>
			<h2>Mentor Unggulan Kami</h2>
			<p>Belajar langsung dari pengajar musik profesional dan tersertifikasi.</p>
		</div>

		<div class="grid-4 program-flex">
			@foreach($featuredMentors as $mentor)
				<div class="card tutor-card hidden-element {{ $mentor['delay'] }}">
					<div class="tutor-img-wrapper">
						<img
							src="{{ asset('images/croma_logo.jpg') }}"
							alt="Foto {{ $mentor['name'] }} - {{ $mentor['badge'] }}"
							loading="lazy"
						/>
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

		<div class="text-center margin-top-lg hidden-element">
			<a href="{{ route('mentors.index') }}" class="btn btn-outline-dark">
				Lihat Seluruh Direktori Mentor <i class="fa-solid fa-arrow-right"></i>
			</a>
		</div>
	</div>
</section>

<!-- 5. Demo Video Section (Placed directly below Mentor Unggulan Kami) -->
<section id="demo-video-section" class="section bg-light">
	<div class="container">
		<div class="section-header hidden-element">
			<span class="eyebrow">Suasana Belajar</span>
			<h2>Demo Kursus Musik</h2>
			<p>
				Lihat suasana dan metode pembelajaran instrumen musik di Croma Music.
			</p>
		</div>

		<div class="demo-video-card card hidden-element">
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
@endsection
