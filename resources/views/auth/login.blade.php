<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login — Croma Music</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    {{-- @vite(['../resources/css/app.css']) --}}
    <style>
        :root {
            --primary-blue:#0056b3; --primary-dark:#003d80;
            --primary-yellow:#ffcc00; --bg-light:#f3f4f6;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins',sans-serif;
            min-height:100vh; display:flex;
            background:var(--bg-light);
        }
        /* Left panel */
        .login-left {
            width:45%; background:var(--primary-dark);
            display:flex; flex-direction:column;
            justify-content:center; align-items:center;
            padding:40px; color:#fff; position:relative; overflow:hidden;
        }
        .login-left::before {
            content:''; position:absolute; inset:0;
            background:radial-gradient(circle at 30% 50%, rgba(0,86,179,.6) 0%, transparent 70%);
        }
        .login-left-content { position:relative; z-index:1; text-align:center; }
        .login-logo { display:flex; align-items:center; gap:14px; margin-bottom:48px; justify-content:center; }
        .login-logo img { width:52px; height:52px; border-radius:12px; object-fit:cover; }
        .login-logo span { font-size:1.5rem; font-weight:700; letter-spacing:1px; }
        .login-tagline { font-size:1.6rem; font-weight:700; line-height:1.3; margin-bottom:16px; }
        .login-tagline span { color:var(--primary-yellow); }
        .login-sub { font-size:.9rem; color:rgba(255,255,255,.65); line-height:1.6; }
        .login-features { margin-top:40px; text-align:left; display:flex; flex-direction:column; gap:12px; }
        .login-feature { display:flex; align-items:center; gap:12px; font-size:.85rem; color:rgba(255,255,255,.8); }
        .login-feature i { width:32px; height:32px; background:rgba(255,204,0,.15); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--primary-yellow); flex-shrink:0; }

        /* Right panel */
        .login-right {
            flex:1; display:flex; align-items:center;
            justify-content:center; padding:40px;
        }
        .login-box { width:100%; max-width:400px; }
        .login-box h2 { font-size:1.6rem; font-weight:700; margin-bottom:6px; }
        .login-box p { color:#6b7280; font-size:.875rem; margin-bottom:32px; }
        .form-group { margin-bottom:18px; }
        .form-label { display:block; font-size:.85rem; font-weight:500; margin-bottom:6px; }
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:.875rem; }
        .form-control {
            width:100%; padding:11px 14px 11px 40px;
            border:1.5px solid #e5e7eb; border-radius:8px;
            font-size:.875rem; font-family:inherit; transition:.2s;
        }
        .form-control:focus { outline:none; border-color:var(--primary-blue); box-shadow:0 0 0 3px rgba(0,86,179,.1); }
        .btn-login {
            width:100%; padding:12px; background:var(--primary-blue); color:#fff;
            border:none; border-radius:8px; font-size:.95rem; font-weight:600;
            cursor:pointer; font-family:inherit; transition:.2s; margin-top:4px;
        }
        .btn-login:hover { background:var(--primary-dark); }
        .alert { padding:12px 14px; border-radius:8px; font-size:.8rem; margin-bottom:20px; display:flex; align-items:center; gap:8px; background:#fee2e2; color:#b91c1c; border-left:3px solid #dc2626; }
        .back-link { display:inline-flex; align-items:center; gap:6px; font-size:.8rem; color:#6b7280; margin-top:20px; }
        .back-link:hover { color:var(--primary-blue); }

        @media(max-width:768px) {
            .login-left { display:none; }
            .login-right { padding:24px; }
        }
    </style>
</head>
<body>
    <div class="login-left">
        <div class="login-left-content">
            <div class="login-logo">
                <img src="{{ asset('images/croma_logo.jpg') }}" alt="Croma Music"/>
                <span>CROMA MUSIC</span>
            </div>
            <div class="login-tagline">Asah Bakat <span>Musikmu</span><br/>Bersama Kami</div>
            <p class="login-sub">Platform manajemen kursus musik modern<br/>di Jabodetabek.</p>
            <div class="login-features">
                <div class="login-feature"><i class="fa-solid fa-calendar-check"></i> Jadwal & absensi digital</div>
                <div class="login-feature"><i class="fa-solid fa-video"></i> Dokumentasi video progres</div>
                <div class="login-feature"><i class="fa-solid fa-file-invoice-dollar"></i> Monitoring SPP otomatis</div>
                <div class="login-feature"><i class="fa-solid fa-chart-bar"></i> Laporan bulanan real-time</div>
            </div>
        </div>
    </div>

    <div class="login-right">
        <div class="login-box">
            <h2>Selamat Datang</h2>
            <p>Masuk ke akun Croma Music Anda</p>

            @if($errors->any())
                <div class="alert"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
            @endif
            @if(session('status'))
                <div class="alert" style="background:#dcfce7;color:#15803d;border-color:#16a34a">
                    <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-wrap">
                        <i class="input-icon fa-regular fa-envelope"></i>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="email@cromamusic.com"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="input-icon fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••"/>
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;font-size:.8rem">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                        <input type="checkbox" name="remember"/> Ingat saya
                    </label>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket" style="margin-right:8px"></i>Masuk
                </button>
            </form>

            <a href="{{ url('/') }}" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
