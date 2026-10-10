<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-sm font-medium text-red-700">Laporan #{{ $report->id }}</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Edit laporan</h2></div>
            <a href="{{ route('admin.reports.show', $report) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Kembali ke detail</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @include('admin.partials.flash')
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Perubahan jawaban akan memperbarui laporan tersimpan. Waktu diterima tetap menunjukkan waktu laporan pertama kali dikirim.
        </div>

        <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-semibold">Periksa kembali jawaban:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @foreach ($report->form->schema as $section)
                <section class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <h3 class="border-b border-slate-100 pb-4 text-lg font-semibold text-slate-900">{{ $section['name'] }}</h3>
                    @foreach ($section['payloads'] ?? [] as $field)
                        @php
                            $fieldId = $field['id'];
                            $value = old("answers.{$fieldId}", $report->answers[$fieldId] ?? null);
                            $options = collect($field['options'] ?? []);
                        @endphp
                        <fieldset>
                            <legend class="text-sm font-semibold text-slate-700">{{ $field['label'] }}</legend>
                            @if (!empty($field['description']))<p class="mt-1 text-xs text-slate-500">{{ $field['description'] }}</p>@endif
                            @if ($field['type'] === 'radio_button')
                                <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($options as $option)
                                        <label class="flex min-h-11 items-center gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            <input type="radio" name="answers[{{ $fieldId }}]" value="{{ $option['id'] }}" @checked($value === $option['id']) class="border-slate-300 text-red-700 focus:ring-red-600">
                                            {{ $option['label'] }}
                                        </label>
                                    @endforeach
                                </div>
                            @elseif ($field['type'] === 'checkbox')
                                @php($selected = is_array($value) ? $value : [])
                                <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($options as $option)
                                        <label class="flex min-h-11 items-center gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            <input type="checkbox" name="answers[{{ $fieldId }}][]" value="{{ $option['id'] }}" @checked(in_array($option['id'], $selected, true)) class="rounded border-slate-300 text-red-700 focus:ring-red-600">
                                            {{ $option['label'] }}
                                        </label>
                                    @endforeach
                                </div>
                            @elseif ($field['type'] === 'long_text')
                                <textarea name="answers[{{ $fieldId }}]" rows="4" class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-red-600 focus:ring-red-600">{{ $value }}</textarea>
                            @else
                                <input
                                    name="answers[{{ $fieldId }}]"
                                    value="{{ $value }}"
                                    @if (($field['sub_type'] ?? null) === 'date') type="date"
                                    @elseif (($field['sub_type'] ?? null) === 'amount') type="text" inputmode="decimal"
                                    @else type="text" @endif
                                    class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-red-600 focus:ring-red-600"
                                >
                            @endif
                        </fieldset>
                    @endforeach
                </section>
            @endforeach

            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <a href="{{ route('admin.reports.show', $report) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Batal</a>
                <button type="submit" class="rounded-lg bg-red-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-800">Simpan laporan</button>
            </div>
        </form>
    </div>
</x-app-layout>
