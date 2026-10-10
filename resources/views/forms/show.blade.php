<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#102a43">
    <link rel="icon" type="image/png" href="{{ asset('images/vesperia.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/vesperia.png') }}">
    <title>{{ $form->name }} · Vesperia</title>
    @vite(['resources/css/app.css', 'resources/js/form.js'])
</head>
<body>
    <div class="site-bar">
        <div class="site-bar__inner">
            <a class="brand" href="/" aria-label="Vesperia, beranda">
                <img class="brand__logo" src="{{ asset('images/vesperia.png') }}" alt="Vesperia">
            </a>
            <span class="site-bar__label"><span class="site-bar__label-mark" aria-hidden="true"></span>Pusat Pelaporan Risiko</span>
            @auth
                @if (auth()->user()->isAdmin())
                    <a class="site-bar__login" href="{{ route('dashboard') }}">Dashboard <span aria-hidden="true">→</span></a>
                @else
                    <a class="site-bar__login" href="{{ route('my-reports.index') }}">Laporan saya <span aria-hidden="true">→</span></a>
                @endif
            @else
                <a class="site-bar__login" href="{{ route('login') }}">Masuk <span aria-hidden="true">→</span></a>
            @endauth
        </div>
    </div>

    <main class="page-shell">
        <header class="page-header">
            <div class="page-header__copy">
                <span class="eyebrow"><span class="eyebrow__dot"></span> FORMULIR PELAPORAN</span>
                <h1>{{ $form->name }}</h1>
                <p>Sampaikan detail kejadian dan dampak risiko untuk membantu kami menindaklanjuti laporan Anda.</p>
                <div class="form-header-meta">
                    <span><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M6 2.75v2.5m8-2.5v2.5M3.75 7h12.5m-11.5-3h10.5c.69 0 1.25.56 1.25 1.25v10.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25V5.25C3.5 4.56 4.06 4 4.75 4Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>Formulir digital</span>
                    <span><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m10 2.5 6 2.25v4.5c0 3.75-2.55 6.45-6 8.25-3.45-1.8-6-4.5-6-8.25v-4.5L10 2.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="m7.5 9.75 1.65 1.65 3.4-3.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>Data terlindungi</span>
                </div>
            </div>
            <div class="header-card" aria-label="Informasi pengisian">
                <span class="header-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 8v4m0 4h.01M10.3 3.86 1.82 18.14A2 2 0 0 0 3.54 21h16.92a2 2 0 0 0 1.72-2.86L13.7 3.86a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span><strong>Isi dengan teliti</strong><small>Pastikan informasi kejadian sudah benar.</small></span>
            </div>
        </header>

        <div id="notice" class="notice notice--success" role="status" aria-live="polite" @if (!session('submitted')) hidden @endif>
            Laporan berhasil dikirim.
        </div>
        <div id="loading" class="loading" role="status">
            <span class="loading__spinner" aria-hidden="true"></span>
            <span>Menyiapkan formulir...</span>
        </div>
        <form id="risk-form" data-api-url="{{ url('/api/forms/'.$form->slug) }}" data-submit-url="{{ url('/api/forms/'.$form->slug.'/submissions') }}" hidden>
            <div id="form-content"></div>
            <footer class="form-footer">
                <p><span class="privacy-icon" aria-hidden="true">✓</span><span><strong>Privasi laporan Anda</strong><small>Informasi ini digunakan untuk proses tindak lanjut risiko.</small></span></p>
                <button class="submit-button" type="submit">
                    <span>Kirim laporan</span>
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="none">
                        <path d="M4.5 10h11m-4.5-4.5L15.5 10 11 14.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </footer>
        </form>
    </main>
    <footer class="site-footer">Vesperia <span>·</span> Pelaporan Risiko Operasional</footer>
</body>
</html>
