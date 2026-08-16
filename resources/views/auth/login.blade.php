<!DOCTYPE html>
<html lang="id">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Login - CROMIS</title>
		<meta name="description" content="Masuk ke portal CROMIS - Sistem Informasi Akademik & Manajemen Kursus Croma Music" />
		<meta name="robots" content="noindex, nofollow" />

		<!-- Favicon -->
		<link rel="icon" type="image/jpeg" href="{{ asset('images/croma_logo.jpg') }}" />
		<link rel="shortcut icon" href="{{ asset('images/croma_logo.jpg') }}" />
		<link rel="apple-touch-icon" href="{{ asset('images/croma_logo.jpg') }}" />
		<link rel="preconnect" href="https://fonts.googleapis.com" />
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
		<link
			href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
			rel="stylesheet"
		/>

		<!-- Font Awesome & Master Design System Stylesheet -->
		<link
			rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
		/>
		<link rel="stylesheet" href="{{ asset('css/style.css') }}" />
	</head>
	<body class="login-body">
		<!-- Centered Authentication Card -->
		<div class="login-box">
			<!-- Croma Music Brand Header -->
			<div class="login-brand-header">
				<img src="{{ asset('images/croma_logo.jpg') }}" alt="Logo Croma Music" />
				<h2>CROMA MUSIC</h2>
			</div>
			<p class="subtitle">Masuk ke sistem manajemen Croma Music</p>

			@if($errors->any())
				<div class="auth-alert error" role="alert">
					<i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
					<span>{{ $errors->first() }}</span>
				</div>
			@endif

			@if(session('status'))
				<div class="auth-alert success" role="status">
					<i class="fa-solid fa-circle-check" aria-hidden="true"></i>
					<span>{{ session('status') }}</span>
				</div>
			@endif

			<form method="POST" action="{{ route('login') }}" novalidate>
				@csrf
				
				<div class="form-group">
					<label for="email" class="form-label">Alamat Email</label>
					<div class="input-wrap">
						<i class="input-icon fa-regular fa-envelope" aria-hidden="true"></i>
						<input
							type="email"
							id="email"
							name="email"
							class="form-control"
							value="{{ old('email') }}"
							required
							autofocus
							autocomplete="username"
							placeholder="nama@cromamusic.com"
						/>
					</div>
				</div>

				<div class="form-group">
					<label for="password" class="form-label">Kata Sandi</label>
					<div class="input-wrap">
						<i class="input-icon fa-solid fa-lock" aria-hidden="true"></i>
						<input
							type="password"
							id="password"
							name="password"
							class="form-control"
							required
							autocomplete="current-password"
							placeholder="••••••••"
						/>
						<button
							type="button"
							class="toggle-password-btn"
							id="toggle-password"
							aria-label="Tampilkan kata sandi"
						>
							<i class="fa-regular fa-eye" id="toggle-password-icon"></i>
						</button>
					</div>
				</div>

				<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.875rem;">
					<label for="remember" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-dark);">
						<input
							type="checkbox"
							id="remember"
							name="remember"
							value="1"
							{{ old('remember') ? 'checked' : '' }}
							style="accent-color: var(--primary-navy); width: 1.1rem; height: 1.1rem;"
						/>
						<span>Ingat saya di perangkat ini</span>
					</label>
					@if (Route::has('password.request'))
						<a
							href="{{ route('password.request') }}"
							style="color: var(--primary-navy); font-weight: 600; transition: color 0.2s ease;"
							onmouseover="this.style.color='var(--accent-gold-hover)'"
							onmouseout="this.style.color='var(--primary-navy)'"
						>Lupa kata sandi?</a>
					@endif
				</div>

				<button type="submit" class="btn-login">
					<i class="fa-solid fa-right-to-bracket" style="margin-right: 0.5rem;"></i>
					Masuk Ke Sistem
				</button>
			</form>

			<a href="{{ url('/') }}" class="back-to-home-link">
				<i class="fa-solid fa-arrow-left"></i>
				<span>Kembali ke Beranda</span>
			</a>
		</div>

		<!-- Password Visibility Toggle Script -->
		<script>
			document.addEventListener("DOMContentLoaded", () => {
				const toggleBtn = document.getElementById("toggle-password");
				const passwordInput = document.getElementById("password");
				const toggleIcon = document.getElementById("toggle-password-icon");

				if (toggleBtn && passwordInput && toggleIcon) {
					toggleBtn.addEventListener("click", () => {
						const isPassword = passwordInput.getAttribute("type") === "password";
						passwordInput.setAttribute("type", isPassword ? "text" : "password");

						toggleIcon.classList.toggle("fa-eye", !isPassword);
						toggleIcon.classList.toggle("fa-eye-slash", isPassword);
						toggleBtn.setAttribute(
							"aria-label",
							isPassword ? "Sembunyikan kata sandi" : "Tampilkan kata sandi"
						);
					});
				}
			});
		</script>
	</body>
</html>
