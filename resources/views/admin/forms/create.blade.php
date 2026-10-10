<x-app-layout>
    <x-slot name="header">
        <p class="text-sm font-medium text-red-700">Administrasi</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Buat form</h2>
    </x-slot>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        @include('admin.partials.flash')
        @include('admin.forms._form', ['form' => null, 'action' => route('admin.forms.store'), 'method' => 'POST'])
    </div>
</x-app-layout>
