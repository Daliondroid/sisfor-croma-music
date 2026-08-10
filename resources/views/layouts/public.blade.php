<!DOCTYPE html>
<html lang="id">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta
			name="description"
			content="Croma Music - Sekolah musik modern dengan jadwal fleksibel di Jabodetabek."
		/>
		<title>@yield('title', 'Croma Music | Sekolah Musik Modern')</title>

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

		<link rel="stylesheet" href="{{ asset('css/style.css') }}" />
		@stack('styles')
	</head>
	<body id="top">
		<header class="header @yield('header-class')">
			<div class="container nav-container">
				<a href="{{ route('home') }}" class="logo" aria-label="Croma Music Beranda">
					<img
						src="{{ asset('images/croma_logo.jpg') }}"
						alt="Logo Croma Music"
						class="logo-img"
					/>
					<span>CROMA MUSIC</span>
				</a>

				<nav class="public-nav" aria-label="Navigasi Utama">
					<ul class="nav-links">
						<li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
						<li><a href="{{ route('instruments.index') }}" class="{{ request()->routeIs('instruments.*') ? 'active' : '' }}">Instrumen</a></li>
						<li><a href="{{ route('mentors.index') }}" class="{{ request()->routeIs('mentors.*') ? 'active' : '' }}">Mentor</a></li>
					</ul>
				</nav>

				<div class="nav-actions">
					<a href="{{ route('login') }}" class="btn btn-outline">Login</a>
				</div>
			</div>
		</header>

		<main>
			@yield('content')
		</main>

		<footer class="footer">
			<div class="container">
				<div class="footer-content">
					<div class="footer-brand">
						<h3>CROMA MUSIC</h3>
						<p>Asah bakat musikmu bersama kami. Platform musik terintegrasi di Jabodetabek.</p>
					</div>
					<div class="footer-links">
						<h4>Navigasi Public</h4>
						<ul>
							<li><a href="{{ route('home') }}">Beranda</a></li>
							<li><a href="{{ route('instruments.index') }}">Katalog Instrumen</a></li>
							<li><a href="{{ route('mentors.index') }}">Direktori Mentor</a></li>
							<li><a href="{{ route('login') }}">Portal Login</a></li>
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

		<script src="{{ asset('js/script.js') }}"></script>
		@stack('scripts')
	</body>
</html>
