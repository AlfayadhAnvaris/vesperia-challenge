<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#b92724">
    <link rel="icon" type="image/png" href="{{ asset('images/vesperia.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/vesperia.png') }}">
    <title>{{ config('app.name', 'Vesperia') }} · Akun</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <main class="auth-shell">
        <a class="auth-brand" href="/">
            <img src="{{ asset('images/vesperia.png') }}" alt="Vesperia">
            <span>Pelaporan Risiko Operasional</span>
        </a>
        <section class="auth-card">
            {{ $slot }}
        </section>
        <a class="auth-back" href="/">← Kembali ke beranda</a>
    </main>
</body>
</html>
