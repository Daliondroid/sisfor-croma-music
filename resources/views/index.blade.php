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

		<link rel="stylesheet" href="css/style.css" />

		<link rel="preconnect" href="https://fonts.googleapis.com" />
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
		<link
			href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
			rel="stylesheet"
		/>
		<link
			rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
		/>
	</head>
	<body id="top">
		<header class="header">
			<div class="container nav-container">
				<a href="#top" class="logo">
					<img
						src="images/croma_logo.jpg"
						alt="Logo Croma"
						class="logo-img"
					/>
					CROMA MUSIC
				</a>

				<div class="mobile-toggle" id="mobile-toggle">
					<i class="fa-solid fa-bars"></i>
				</div>

				<nav class="nav-menu" id="nav-menu">
					<span class="close-menu"
						><i class="fa-solid fa-xmark"></i
					></span>

					<a href="#home" class="nav-link">Beranda</a>
					<a href="#about" class="nav-link">Tentang</a>
					<a href="#program" class="nav-link">Program</a>
					<a href="#tutor" class="nav-link">Mentor</a>

					<a href="/login" class="btn btn-outline mobile-only-btn"
						>Login</a
					>
				</nav>

				<div class="nav-actions desktop-only">
					<a href="{{ route('login') }}" class="btn btn-outline">Login</a>
				</div>
			</div>
		</header>

		<section id="home" class="hero">
			<div class="container hero-content hidden-element">
				<h1 class="hero-title">
					Asah Bakat <span class="highlight">Musikmu</span> Bersama
					Kami
				</h1>
				<p class="hero-desc">
					Platform belajar musik modern di Jabodetabek. Pilih metode
					Onsite atau Home Visit dengan jadwal fleksibel.
				</p>
				<div class="hero-buttons">
					<p
						style="
							margin-bottom: 10px;
							font-weight: 600;
							font-size: 1.1rem;
						"
					>
						Mau coba gratis?
					</p>
					<a
						href="https://wa.me/628123456789"
						class="btn btn-primary pulse-animation"
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
							nyaman atau guru profesional kami datang ke rumah
							Anda.
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
						<h3>Tutor yang Berpengalaman</h3>
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

				<div class="grid-4 program-flex">
					<div class="card program-card hidden-element">
						<div
							class="card-image"
							style="
								background-image: url(&quot;images/piano.avif&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Piano</h3>
							<p class="text-sm">Klasik, Pop, & Jazz</p>
						</div>
					</div>

					<div class="card program-card hidden-element delay-100">
						<div
							class="card-image"
							style="
								background-image: url(&quot;images/guitar.avif&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Gitar</h3>
							<p class="text-sm">Akustik & Elektrik</p>
						</div>
					</div>

					<div class="card program-card hidden-element delay-200">
						<div
							class="card-image"
							style="
								background-image: url(&quot;images/drums.avif&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Drum</h3>
							<p class="text-sm">Rhythm & Percussion</p>
						</div>
					</div>

					<div class="card program-card hidden-element delay-300">
						<div
							class="card-image"
							style="
								background-image: url(&quot;images/vocals.avif&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Vokal</h3>
							<p class="text-sm">Teknik & Performance</p>
						</div>
					</div>

					<div class="card program-card hidden-element">
						<div
							class="card-image"
							style="
								background-image: url(&quot;images/keyboards.avif&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Keyboard</h3>
							<p class="text-sm">Synthesizer & Arranger</p>
						</div>
					</div>

					<div class="card program-card hidden-element delay-100">
						<div
							class="card-image"
							style="
								background-image: url(&quot;images/bass.avif&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Bass</h3>
							<p class="text-sm">Groove & Slap Technique</p>
						</div>
					</div>

					<div class="card program-card hidden-element delay-200">
						<div
							class="card-image"
							style="
								background-image: url(&quot;images/flute.avif&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Flute</h3>
							<p class="text-sm">Klasik & Orkestra</p>
						</div>
					</div>

					<div class="card program-card hidden-element delay-300">
						<div
							class="card-image"
							style="
								background-image: url(&quot;images/sax.avif&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Saxophone</h3>
							<p class="text-sm">Jazz & Pop Brass</p>
						</div>
					</div>
					<div
						class="card program-card hidden-element"
						id="btn-other-instruments"
						style="cursor: pointer"
					>
						<div
							class="card-image"
							style="
								background-image: url(&quot;https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=800&auto=format&fit=crop&quot;);
							"
						>
							<div class="play-overlay">
								<i class="fa-solid fa-play"></i>
							</div>
						</div>
						<div class="card-body">
							<h3>Instrumen Lainnya</h3>
							<p class="text-sm">Lihat Demo Video</p>
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
					<select id="mentor-filter" class="filter-dropdown">
						<option value="all">Semua Instrumen</option>
						<option value="piano">Piano</option>
						<option value="vocal">Vokal</option>
						<option value="guitar">Gitar</option>
						<option value="drum">Drum</option>
						<option value="keyboard">Keyboard</option>
						<option value="bass">Bass</option>
						<option value="flute">Flute</option>
						<option value="saxophone">Saxophone</option>
					</select>
				</div>
				<div class="grid-4 program-flex" id="mentor-grid">
					<div
						class="card tutor-card hidden-element"
						data-category="piano"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Piano"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Budi</h4>
							<span class="badge">Piano Expert</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-100"
						data-category="piano"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Piano"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Andi</h4>
							<span class="badge">Classical Piano</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-200"
						data-category="piano"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Piano"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Siska</h4>
							<span class="badge">Jazz Piano</span>
						</div>
					</div>

					<div
						class="card tutor-card hidden-element delay-300"
						data-category="vocal"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Vokal"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Sarah</h4>
							<span class="badge">Vocal Coach</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element"
						data-category="vocal"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Vokal"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Maya</h4>
							<span class="badge">Pop Vocal</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-100"
						data-category="vocal"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Vokal"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Rio</h4>
							<span class="badge">RnB Vocal</span>
						</div>
					</div>

					<div
						class="card tutor-card hidden-element delay-200"
						data-category="guitar"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Gitar"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Reza</h4>
							<span class="badge">Guitarist</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-300"
						data-category="guitar"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Gitar"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Dinda</h4>
							<span class="badge">Acoustic Guitar</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element"
						data-category="guitar"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Gitar"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Bima</h4>
							<span class="badge">Electric Guitar</span>
						</div>
					</div>

					<div
						class="card tutor-card hidden-element delay-100"
						data-category="drum"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Drum"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Doni</h4>
							<span class="badge">Drummer</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-200"
						data-category="drum"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Drum"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Eka</h4>
							<span class="badge">Percussionist</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-300"
						data-category="drum"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Drum"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Gilang</h4>
							<span class="badge">Rock Drummer</span>
						</div>
					</div>

					<div
						class="card tutor-card hidden-element"
						data-category="keyboard"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Keyboard"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Rian</h4>
							<span class="badge">Keyboardist</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-100"
						data-category="keyboard"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Keyboard"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Tika</h4>
							<span class="badge">Synth Expert</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-200"
						data-category="keyboard"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Keyboard"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Kevin</h4>
							<span class="badge">Pop Keyboard</span>
						</div>
					</div>

					<div
						class="card tutor-card hidden-element delay-300"
						data-category="bass"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Bass"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Dika</h4>
							<span class="badge">Bassist</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element"
						data-category="bass"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Bass"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Laras</h4>
							<span class="badge">Slap Bass</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-100"
						data-category="bass"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Bass"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Toni</h4>
							<span class="badge">Jazz Bass</span>
						</div>
					</div>

					<div
						class="card tutor-card hidden-element delay-200"
						data-category="flute"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Flute"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Dina</h4>
							<span class="badge">Flutist</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-300"
						data-category="flute"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Flute"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Bayu</h4>
							<span class="badge">Classical Flute</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element"
						data-category="flute"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Flute"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Nisa</h4>
							<span class="badge">Wind Instrument</span>
						</div>
					</div>

					<div
						class="card tutor-card hidden-element delay-100"
						data-category="saxophone"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Saxophone"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Aldo</h4>
							<span class="badge">Saxophonist</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-200"
						data-category="saxophone"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Saxophone"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Fira</h4>
							<span class="badge">Alto Sax</span>
						</div>
					</div>
					<div
						class="card tutor-card hidden-element delay-300"
						data-category="saxophone"
					>
						<img
							src="images/croma_logo.jpg"
							alt="Mentor Saxophone"
							loading="lazy"
						/>
						<div class="card-body">
							<h4>Kak Denny</h4>
							<span class="badge">Tenor Sax</span>
						</div>
					</div>
				</div>
			</div>
		</section>

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
							<li><a href="#program">Program</a></li>
							<li><a href="/login">Login Murid</a></li>
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
							<i class="fa-solid fa-location-dot"></i> Bekasi,
							Jawa Barat
						</p>
					</div>
				</div>
				<div class="footer-bottom">
					&copy; 2026 Croma Music. UI Design Project.
				</div>
			</div>
		</footer>

		<a
			href="https://wa.me/628123456789"
			class="floating-wa"
			target="_blank"
			aria-label="Chat WhatsApp"
		>
			<i class="fa-brands fa-whatsapp"></i>
			<span>Tanya Admin</span>
		</a>
		<div id="video-modal" class="modal">
			<div class="modal-content">
				<span class="close-modal"
					><i class="fa-solid fa-xmark"></i
				></span>
				<iframe
					id="demo-video"
					width="100%"
					height="450"
					src="https://www.youtube.com/embed/dQw4w9WgXcQ"
					title="Demo Video"
					frameborder="0"
					allow="
						accelerometer;
						autoplay;
						clipboard-write;
						encrypted-media;
						gyroscope;
						picture-in-picture;
					"
					referrerpolicy="strict-origin-when-cross-origin"
					allowfullscreen
				></iframe>
			</div>
		</div>
		<script src="js/script.js"></script>
	</body>
</html>
