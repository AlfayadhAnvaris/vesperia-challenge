<x-guest-layout>
    <div class="auth-heading">
        <span class="auth-kicker">MULAI BERSAMA VESPERIA</span>
        <h1>Buat akun baru</h1>
        <p>Daftar sebagai user untuk mengirim dan memantau laporan risiko Anda.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800" role="note">
            Pendaftaran publik membuat akun <strong>User</strong>. Role Admin hanya dapat diberikan oleh administrator sistem.
        </div>

        <div class="auth-field">
            <x-input-label for="name" value="Nama lengkap" />
            <x-text-input id="name" class="auth-input block mt-2 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="auth-field mt-4">
            <x-input-label for="email" value="Alamat email" />
            <x-text-input id="email" class="auth-input block mt-2 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@perusahaan.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field mt-4">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="auth-input block mt-2 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="Buat kata sandi yang kuat" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-field mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi" />
            <x-text-input id="password_confirmation" class="auth-input block mt-2 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="auth-submit w-full justify-center">
                Buat akun
            </x-primary-button>
        </div>
    </form>

    <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
</x-guest-layout>
