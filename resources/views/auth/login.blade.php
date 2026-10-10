<x-guest-layout>
    <div class="auth-heading">
        <span class="auth-kicker">SELAMAT DATANG KEMBALI</span>
        <h1>Masuk ke akun Anda</h1>
        <p>Lanjutkan pelaporan dan pantau laporan Anda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <x-input-label for="email" value="Alamat email" />
            <x-text-input id="email" class="auth-input block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@perusahaan.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field mt-5">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="auth-input block mt-2 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="Masukkan kata sandi" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-red-700 shadow-sm focus:ring-red-600" name="remember">
                <span>Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-red-700 hover:text-red-900" href="{{ route('password.request') }}">Lupa kata sandi?</a>
            @endif
        </div>

        <x-auth-session-status class="mt-4" :status="session('status')" />
        <div class="mt-6">
            <x-primary-button class="auth-submit w-full justify-center">
                Masuk
            </x-primary-button>
        </div>
    </form>

    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
</x-guest-layout>
