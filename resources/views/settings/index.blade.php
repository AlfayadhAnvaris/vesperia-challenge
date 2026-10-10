<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-red-700">Preferensi akun</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Pengaturan</h2>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">Tampilan</h3>
                <p class="mt-1 text-sm text-slate-500">Pilih tema yang nyaman. Preferensi tersimpan di browser ini.</p>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-5 px-5 py-5 sm:px-6">
                <div class="flex items-center gap-4">
                    <span class="settings-theme-icon" aria-hidden="true">
                        <svg class="settings-theme-icon__sun" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        <svg class="settings-theme-icon__moon" viewBox="0 0 24 24" fill="none">
                            <path d="M20.2 15.2A8.5 8.5 0 0 1 8.8 3.8 8.5 8.5 0 1 0 20.2 15.2Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Tema aplikasi</p>
                        <p id="theme-label" class="mt-1 text-sm text-slate-500">Mode terang aktif</p>
                    </div>
                </div>
                <label class="theme-switch">
                    <span class="sr-only">Aktifkan mode gelap</span>
                    <input id="theme-toggle" type="checkbox" role="switch" aria-label="Aktifkan mode gelap">
                    <span class="theme-switch__track" aria-hidden="true"><span></span></span>
                    <span class="theme-switch__labels" aria-hidden="true"><span>Terang</span><span>Gelap</span></span>
                </label>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h3 class="font-semibold text-slate-900">Akun Anda</h3>
                <p class="mt-1 text-sm text-slate-500">Kelola informasi profil dan keamanan akun.</p>
            </div>
            <div class="divide-y divide-slate-100">
                <a class="settings-link" href="{{ route('profile.edit') }}">
                    <span class="settings-link__icon" aria-hidden="true">◎</span>
                    <span class="min-w-0 flex-1"><strong>Profil dan keamanan</strong><small>Ubah nama, email, kata sandi, atau hapus akun.</small></span>
                    <span class="settings-link__arrow" aria-hidden="true">→</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="settings-link settings-link--button" type="submit">
                        <span class="settings-link__icon settings-link__icon--logout" aria-hidden="true">↗</span>
                        <span class="min-w-0 flex-1 text-left"><strong>Keluar dari akun</strong><small>Akhiri sesi Vesperia pada perangkat ini.</small></span>
                        <span class="settings-link__arrow" aria-hidden="true">→</span>
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-app-layout>
