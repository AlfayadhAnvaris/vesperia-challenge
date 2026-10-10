<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-sm font-medium text-red-700">Laporan #{{ $report->id }}</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Detail laporan</h2></div>
            <a href="{{ route('my-reports.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Kembali ke laporan saya</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        <section class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Form</p><p class="mt-2 text-sm font-semibold text-slate-800">{{ $report->form->name }}</p></div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Dikirim</p><p class="mt-2 text-sm font-semibold text-slate-800">{{ $report->created_at->format('d M Y, H:i') }}</p></div>
        </section>

        @foreach ($sections as $section)
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <h3 class="border-b border-slate-200 bg-slate-50 px-5 py-4 font-semibold text-slate-900">{{ $section['name'] }}</h3>
                <dl class="divide-y divide-slate-100">
                    @foreach ($section['fields'] as $field)
                        <div class="grid gap-1 px-5 py-4 sm:grid-cols-[minmax(180px,0.8fr)_2fr] sm:gap-6">
                            <dt class="text-sm font-medium text-slate-500">{{ $field['label'] }}</dt>
                            <dd class="whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">{{ filled($field['value']) ? $field['value'] : '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endforeach
    </div>
</x-app-layout>
