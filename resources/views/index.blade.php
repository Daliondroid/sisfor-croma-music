@php
$programs = [
    ['title' => 'Piano', 'desc' => 'Klasik, Pop, & Jazz', 'img' => 'images/piano.avif', 'delay' => ''],
    ['title' => 'Gitar', 'desc' => 'Akustik & Elektrik', 'img' => 'images/guitar.avif', 'delay' => 'delay-100'],
    ['title' => 'Drum', 'desc' => 'Rhythm & Percussion', 'img' => 'images/drums.avif', 'delay' => 'delay-200'],
    ['title' => 'Vokal', 'desc' => 'Teknik & Performance', 'img' => 'images/vocals.avif', 'delay' => 'delay-300'],
    ['title' => 'Keyboard', 'desc' => 'Synthesizer & Arranger', 'img' => 'images/keyboards.avif', 'delay' => ''],
    ['title' => 'Bass', 'desc' => 'Groove & Slap Technique', 'img' => 'images/bass.avif', 'delay' => 'delay-100'],
    ['title' => 'Flute', 'desc' => 'Klasik & Orkestra', 'img' => 'images/flute.avif', 'delay' => 'delay-200'],
    ['title' => 'Saxophone', 'desc' => 'Jazz & Pop Brass', 'img' => 'images/sax.avif', 'delay' => 'delay-300'],
];

$mentors = [
    ['name' => 'Kak Budi', 'badge' => 'Piano Expert', 'category' => 'piano', 'featured' => true, 'delay' => ''],
    ['name' => 'Kak Andi', 'badge' => 'Classical Piano', 'category' => 'piano', 'featured' => false, 'delay' => 'delay-100'],
    ['name' => 'Kak Siska', 'badge' => 'Jazz Piano', 'category' => 'piano', 'featured' => false, 'delay' => 'delay-200'],
    ['name' => 'Kak Sarah', 'badge' => 'Vocal Coach', 'category' => 'vocal', 'featured' => true, 'delay' => 'delay-300'],
    ['name' => 'Kak Maya', 'badge' => 'Pop Vocal', 'category' => 'vocal', 'featured' => false, 'delay' => ''],
    ['name' => 'Kak Rio', 'badge' => 'RnB Vocal', 'category' => 'vocal', 'featured' => false, 'delay' => 'delay-100'],
    ['name' => 'Kak Reza', 'badge' => 'Guitarist', 'category' => 'guitar', 'featured' => true, 'delay' => 'delay-200'],
    ['name' => 'Kak Dinda', 'badge' => 'Acoustic Guitar', 'category' => 'guitar', 'featured' => false, 'delay' => 'delay-300'],
    ['name' => 'Kak Bima', 'badge' => 'Electric Guitar', 'category' => 'guitar', 'featured' => false, 'delay' => ''],
    ['name' => 'Kak Doni', 'badge' => 'Drummer', 'category' => 'drum', 'featured' => true, 'delay' => 'delay-100'],
    ['name' => 'Kak Eka', 'badge' => 'Percussionist', 'category' => 'drum', 'featured' => false, 'delay' => 'delay-200'],
    ['name' => 'Kak Gilang', 'badge' => 'Rock Drummer', 'category' => 'drum', 'featured' => false, 'delay' => 'delay-300'],
    ['name' => 'Kak Rian', 'badge' => 'Keyboardist', 'category' => 'keyboard', 'featured' => true, 'delay' => ''],
    ['name' => 'Kak Tika', 'badge' => 'Synth Expert', 'category' => 'keyboard', 'featured' => false, 'delay' => 'delay-100'],
    ['name' => 'Kak Kevin', 'badge' => 'Pop Keyboard', 'category' => 'keyboard', 'featured' => false, 'delay' => 'delay-200'],
    ['name' => 'Kak Dika', 'badge' => 'Bassist', 'category' => 'bass', 'featured' => true, 'delay' => 'delay-300'],
    ['name' => 'Kak Laras', 'badge' => 'Slap Bass', 'category' => 'bass', 'featured' => false, 'delay' => ''],
    ['name' => 'Kak Toni', 'badge' => 'Jazz Bass', 'category' => 'bass', 'featured' => false, 'delay' => 'delay-100'],
    ['name' => 'Kak Dina', 'badge' => 'Flutist', 'category' => 'flute', 'featured' => true, 'delay' => 'delay-200'],
    ['name' => 'Kak Bayu', 'badge' => 'Classical Flute', 'category' => 'flute', 'featured' => false, 'delay' => 'delay-300'],
    ['name' => 'Kak Nisa', 'badge' => 'Wind Instrument', 'category' => 'flute', 'featured' => false, 'delay' => ''],
    ['name' => 'Kak Aldo', 'badge' => 'Saxophonist', 'category' => 'saxophone', 'featured' => true, 'delay' => 'delay-100'],
    ['name' => 'Kak Fira', 'badge' => 'Alto Sax', 'category' => 'saxophone', 'featured' => false, 'delay' => 'delay-200'],
    ['name' => 'Kak Denny', 'badge' => 'Tenor Sax', 'category' => 'saxophone', 'featured' => false, 'delay' => 'delay-300'],
];
@endphp
<!DOCTYPE html>
<html lang="id">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta
			name="description"
			content="Croma Music - Sekolah musik modern dengan jadwal fleksibel di Jabodetabek."
		/>
		<title>Croma Music | Sekolah Musik Modern</title>

		<link rel="preconnect" href="https://fonts.googleapis.com" />
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
		<link
			href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
			rel="stylesheet"
		/>
		<link
			rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
		/>

		<link rel="stylesheet" href="css/style.css" />
	</head>
	<body id="top">
		<header class="header">
			<div class="container nav-container">
				<a href="#top" class="logo" aria-label="Croma Music Beranda">
					<img
						src="images/croma_logo.jpg"
						alt="Logo Croma Music"
						class="logo-img"
					/>
					<span>CROMA MUSIC</span>
				</a>

				<div class="nav-actions">
					<a href="{{ route('login') }}" class="btn btn-outline">Login</a>
				</div>
			</div>
		</header>

		<main>
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

			<section id="about" class="section bg-light">
				<div class="container">
					<div class="grid-3 feature-flex">
						<div class="card feature-card hidden-element delay-100">
							<div class="icon-box">
								<i class="fa-solid fa-house-laptop"></i>
							</div>
							<h3>Onsite & Home Visit</h3>
							<p>
								Fleksibilitas penuh. Belajar di studio kami yang
								nyaman atau guru profesional kami datang ke rumah Anda.
							</p>
						</div>
						<div class="card feature-card hidden-element delay-200">
							<div class="icon-box">
								<i class="fa-regular fa-clock"></i>
							</div>
							<h3>Jadwal Fleksibel</h3>
							<p>
								Kesibukan bukan halangan. Atur dan reschedule jadwal
								latihan musikmu dengan mudah melalui sistem kami.
							</p>
						</div>
						<div class="card feature-card hidden-element delay-300">
							<div class="icon-box">
								<i class="fa-solid fa-user-tie"></i>
							</div>
							<h3>Tutor Berpengalaman</h3>
							<p>
								Tutor kami memiliki latar belakang pendidikan formal
								di bidang musik atau tersertifikasi setara.
							</p>
						</div>
					</div>
				</div>
			</section>

			<section id="program" class="section">
				<div class="container">
					<div class="section-header hidden-element">
						<h2>Program Unggulan</h2>
						<p>
							Pilih instrumen favoritmu dan mulai perjalanan musikmu.
						</p>
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
				</div>
			</section>

			<section id="demo-video-section" class="section bg-light">
				<div class="container">
					<div class="section-header hidden-element">
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

			<section id="tutor" class="section bg-light">
				<div class="container">
					<div class="section-header hidden-element">
						<h2>Mentor Kami</h2>
						<p>Belajar langsung dari praktisi dan akademisi musik.</p>
						<div class="mentor-filter-pills" role="tablist" aria-label="Filter Category Mentor">
							<button type="button" class="filter-pill active" data-filter="all" role="tab" aria-selected="true">Semua</button>
							<button type="button" class="filter-pill" data-filter="piano" role="tab" aria-selected="false">Piano</button>
							<button type="button" class="filter-pill" data-filter="vocal" role="tab" aria-selected="false">Vokal</button>
							<button type="button" class="filter-pill" data-filter="guitar" role="tab" aria-selected="false">Gitar</button>
							<button type="button" class="filter-pill" data-filter="drum" role="tab" aria-selected="false">Drum</button>
							<button type="button" class="filter-pill" data-filter="keyboard" role="tab" aria-selected="false">Keyboard</button>
							<button type="button" class="filter-pill" data-filter="bass" role="tab" aria-selected="false">Bass</button>
							<button type="button" class="filter-pill" data-filter="flute" role="tab" aria-selected="false">Flute</button>
							<button type="button" class="filter-pill" data-filter="saxophone" role="tab" aria-selected="false">Saxophone</button>
						</div>
					</div>

					<div class="grid-4 program-flex" id="mentor-grid">
						@foreach($mentors as $mentor)
							<div
								class="card tutor-card hidden-element {{ $mentor['delay'] }}"
								data-category="{{ $mentor['category'] }}"
								data-featured="{{ $mentor['featured'] ? 'true' : 'false' }}"
							>
								<div class="tutor-img-wrapper">
									<img
										src="{{ asset('images/croma_logo.jpg') }}"
										alt="Foto {{ $mentor['name'] }} - {{ $mentor['badge'] }}"
										loading="lazy"
									/>
								</div>
								<div class="card-body">
									<h4>{{ $mentor['name'] }}</h4>
									<span class="badge">{{ $mentor['badge'] }}</span>
								</div>
							</div>
						@endforeach
					</div>

					<div class="mentor-toggle-wrapper hidden-element" id="mentor-toggle-wrapper">
						<button type="button" class="btn btn-outline-dark" id="toggle-all-mentors" aria-expanded="false">
							<span id="toggle-text">Lihat Semua Mentor</span>
							<i class="fa-solid fa-chevron-down" id="toggle-icon"></i>
						</button>
					</div>
				</div>
			</section>
		</main>

		<footer class="footer">
			<div class="container">
				<div class="footer-content">
					<div class="footer-brand">
						<h3>CROMA MUSIC</h3>
						<p>Asah bakat musikmu bersama kami.</p>
					</div>
					<div class="footer-links">
						<h4>Navigasi</h4>
						<ul>
							<li><a href="#home">Beranda</a></li>
							<li><a href="#about">Tentang</a></li>
							<li><a href="#program">Program</a></li>
							<li><a href="#tutor">Mentor</a></li>
							<li><a href="{{ route('login') }}">Login Murid</a></li>
						</ul>
					</div>
					<div class="footer-contact">
						<h4>Hubungi Kami</h4>
						<p>
							<i class="fa-brands fa-whatsapp"></i> 0812-3456-7890
						</p>
						<p>
							<i class="fa-brands fa-instagram"></i>
							@cromamusic.id
						</p>
						<p>
							<i class="fa-solid fa-envelope"></i>
							admin@cromamusic.id
						</p>
						<p>
							<i class="fa-solid fa-location-dot"></i> Bekasi, Jawa Barat
						</p>
					</div>
				</div>
				<div class="footer-bottom">
					&copy; 2026 Croma Music. All rights reserved.
				</div>
			</div>
		</footer>

		<a
			href="#top"
			class="back-to-top"
			id="back-to-top"
			aria-label="Kembali ke atas beranda"
		>
			<i class="fa-solid fa-arrow-up"></i>
		</a>

		<div id="video-modal" class="modal" role="dialog" aria-modal="true" aria-label="Demo Video Instrumen">
			<div class="modal-content">
				<button type="button" class="close-modal" id="close-modal" aria-label="Tutup Video Modal">
					<i class="fa-solid fa-xmark"></i>
				</button>
				<div class="iframe-container">
					<iframe
						id="demo-video"
						width="100%"
						height="450"
						data-src="https://www.youtube.com/embed/dQw4w9WgXcQ"
						title="Demo Video Croma Music"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen
					></iframe>
				</div>
			</div>
		</div>

		<script src="js/script.js"></script>
	</body>
</html>
