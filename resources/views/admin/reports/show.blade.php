<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-sm font-medium text-red-700">Laporan #{{ $report->id }}</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Detail laporan</h2></div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Semua laporan</a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @include('admin.partials.flash')
        <section class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Form</p><p class="mt-2 text-sm font-semibold text-slate-800">{{ $report->form->name }}</p></div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pelapor</p><p class="mt-2 text-sm font-semibold text-slate-800">{{ $report->user?->name ?? 'Guest / belum dikaitkan' }}</p>@if ($report->user)<p class="mt-1 text-xs text-slate-500">{{ $report->user->email }}</p>@endif</div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diterima</p><p class="mt-2 text-sm font-semibold text-slate-800">{{ $report->created_at->format('d M Y, H:i') }}</p></div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Terakhir diperbarui</p><p class="mt-2 text-sm font-semibold text-slate-800">{{ $report->updated_at->format('d M Y, H:i') }}</p></div>
        </section>

        @php($currentSection = null)
        @foreach ($fields as $field)
            @if ($currentSection !== $field['section'])
                @if ($currentSection !== null)</dl></section>@endif
                @php($currentSection = $field['section'])
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <h3 class="border-b border-slate-200 bg-slate-50 px-5 py-4 font-semibold text-slate-900">{{ $currentSection }}</h3>
                    <dl class="divide-y divide-slate-100">
            @endif
            <div class="grid gap-1 px-5 py-4 sm:grid-cols-[minmax(180px,0.8fr)_2fr] sm:gap-6">
                <dt class="text-sm font-medium text-slate-500">{{ $field['label'] }}</dt>
                <dd class="whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">{{ filled($field['value']) ? $field['value'] : '—' }}</dd>
            </div>
        @endforeach
        @if ($currentSection !== null)</dl></section>@endif
    </div>
</x-app-layout>
