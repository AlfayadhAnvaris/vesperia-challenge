<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-sm font-medium text-red-700">Administrasi</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Edit form</h2></div>
            <a class="text-sm font-semibold text-slate-600 hover:text-slate-900" href="{{ route('admin.forms.index') }}">← Kembali ke daftar</a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-4xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @include('admin.partials.flash')
        @include('admin.forms._form', ['action' => route('admin.forms.update', $form), 'method' => 'PUT'])
    </div>
</x-app-layout>
