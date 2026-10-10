<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#b92724">
    <meta name="description" content="Vesperia — platform pelaporan risiko operasional yang aman, terstruktur, dan mudah digunakan.">
    <link rel="icon" type="image/png" href="{{ asset('images/vesperia.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/vesperia.png') }}">
    <title>Vesperia · Pelaporan Risiko</title>
    @vite(['resources/css/app.css'])
</head>
<body class="landing-body">
    <header class="landing-nav">
        <a href="/" class="landing-brand" aria-label="Vesperia beranda">
            <img src="{{ asset('images/vesperia.png') }}" alt="Vesperia">
        </a>
        <nav class="landing-nav__links" aria-label="Navigasi utama">
            <a href="#tentang">Tentang</a>
            @auth
                <a class="landing-login" href="{{ route('dashboard') }}">Dashboard <span aria-hidden="true">↗</span></a>
            @else
                <a class="landing-login" href="{{ route('login') }}">Masuk <span aria-hidden="true">↗</span></a>
            @endauth
        </nav>
    </header>

    <main>
        <section class="landing-hero">
            <div class="landing-hero__content">
                <span class="landing-kicker"><span></span> PLATFORM PELAPORAN RISIKO OPERASIONAL</span>
                <h1>Risiko lebih terkelola,<br><em>langkah lebih terarah.</em></h1>
                <p class="landing-hero__lead">Satu tempat untuk melaporkan, meninjau, dan menindaklanjuti risiko operasional secara terstruktur.</p>
                <div class="landing-actions">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <a class="landing-button landing-button--primary" href="{{ route('admin.reports.index') }}">Monitoring laporan <span aria-hidden="true">→</span></a>
                            <a class="landing-button landing-button--secondary" href="{{ route('dashboard') }}">Dashboard admin</a>
                        @else
                            <a class="landing-button landing-button--primary" href="{{ route('forms.show', 'operational-risk') }}">Mulai buat laporan <span aria-hidden="true">→</span></a>
                            <a class="landing-button landing-button--secondary" href="{{ route('my-reports.index') }}">Laporan saya</a>
                        @endif
                    @else
                        <a class="landing-button landing-button--primary" href="{{ route('forms.show', 'operational-risk') }}">Mulai buat laporan <span aria-hidden="true">→</span></a>
                        <a class="landing-button landing-button--secondary" href="{{ route('register') }}">Buat akun</a>
                    @endauth
                </div>
                <div class="landing-trust"><span class="landing-trust__check">✓</span> Pelaporan tersimpan dengan aman dan mudah ditelusuri.</div>
            </div>
            <div class="landing-hero__visual" aria-label="Identitas Vesperia">
                <div class="landing-visual__halo"></div>
                <div class="landing-logo-card">
                    <img src="{{ asset('images/vesperia.png') }}" alt="Logo Vesperia">
                </div>
            </div>
            <div class="landing-hero__watermark" aria-hidden="true">V</div>
        </section>

        <section id="tentang" class="landing-intro">
            <div class="landing-intro__heading">
                <span class="landing-kicker landing-kicker--muted">TENTANG VESPERIA</span>
                <h2>Melihat risiko dengan lebih jelas.</h2>
            </div>
            <p>Vesperia membantu tim menyampaikan kejadian, memahami dampak, dan menyimpan informasi laporan dengan rapi untuk mendukung proses tindak lanjut.</p>
        </section>

        <section id="alur" class="landing-steps">
            <article class="landing-step">
                <span class="landing-step__number">01</span>
                <div><h3>Laporkan kejadian</h3><p>Isi detail kejadian, penyebab, dan dampak melalui formulir yang terarah.</p></div>
                <span class="landing-step__arrow" aria-hidden="true">↗</span>
            </article>
            <article class="landing-step">
                <span class="landing-step__number">02</span>
                <div><h3>Tinjau laporan</h3><p>Laporan tersimpan agar dapat ditinjau dan ditelusuri kembali.</p></div>
                <span class="landing-step__arrow" aria-hidden="true">↗</span>
            </article>
            <article class="landing-step">
                <span class="landing-step__number">03</span>
                <div><h3>Tindak lanjuti</h3><p>Gunakan informasi yang tercatat untuk mendukung tindakan berikutnya.</p></div>
                <span class="landing-step__arrow" aria-hidden="true">↗</span>
            </article>
        </section>
    </main>

    <footer class="landing-footer">
        <a href="/" class="landing-footer__brand">VESPERIA<span>®</span></a>
        <span>Pelaporan Risiko Operasional</span>
        <span>© {{ now()->year }} Vesperia</span>
    </footer>
</body>
</html>
