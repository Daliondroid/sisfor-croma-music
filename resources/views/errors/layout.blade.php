<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <script>
        (function() {
            const saved = localStorage.getItem('croma-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-theme', saved ? saved : (prefersDark ? 'dark' : 'light'));
        })();
    </script>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title') - Croma Music</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/croma_logo.jpg') }}"/>
    <link rel="shortcut icon" href="{{ asset('images/croma_logo.jpg') }}"/>
    <link rel="apple-touch-icon" href="{{ asset('images/croma_logo.jpg') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        :root, [data-theme="light"] {
            --primary-navy: #0f172a;
            --accent-gold: #f59e0b;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --font-heading: "Outfit", sans-serif;
            --font-body: "Plus Jakarta Sans", sans-serif;
        }
        [data-theme="dark"] {
            --primary-navy: #38bdf8;
            --accent-gold: #f59e0b;
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --bg-body: #0b1120;
            --card-bg: #1e293b;
            --border-color: #334155;
            --font-heading: "Outfit", sans-serif;
            --font-body: "Plus Jakarta Sans", sans-serif;
        }
        * { margin:0; padding:0; box-sizing:border-box; box-shadow: none !important; }
        body {
            font-family: var(--font-body);
            background: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .error-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.25rem;
            max-width: 32rem;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .error-code {
            font-family: var(--font-heading);
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
            color: var(--accent-gold);
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-dark);
        }
        .error-message {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }
        .error-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: var(--font-heading);
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            border-radius: 0.25rem;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.9; }
        .btn-primary {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }
        [data-theme="dark"] .btn-primary {
            background: var(--accent-gold);
            color: #0f172a;
            border-color: var(--accent-gold);
        }
        .btn-outline {
            background: transparent;
            color: var(--text-dark);
            border-color: var(--border-color);
        }
        .trace-box {
            margin-top: 1.5rem;
            padding: 0.6rem 0.8rem;
            background: var(--bg-body);
            border: 1px dashed var(--border-color);
            border-radius: 0.25rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-family: monospace;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="error-code">@yield('code')</div>
        <h1 class="error-title">@yield('heading')</h1>
        <p class="error-message">@yield('message')</p>
        <div class="error-actions">
            @yield('actions')
        </div>
        @php
            $traceId = request()->header('X-Request-ID') ?? ($requestId ?? null);
        @endphp
        @if($traceId)
            <div class="trace-box">
                Trace ID: {{ $traceId }}
            </div>
        @endif
    </main>
</body>
</html>
