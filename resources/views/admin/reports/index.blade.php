<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-red-700">Operasional</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Laporan risiko</h2>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @include('admin.partials.flash')

        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="min-w-56 flex-1">
                <label for="form_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Filter form</label>
                <select id="form_id" name="form_id" class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-red-600 focus:ring-red-600">
                    <option value="">Semua form</option>
                    @foreach ($forms as $form)
                        <option value="{{ $form->id }}" @selected((string) $selectedForm === (string) $form->id)>{{ $form->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700" type="submit">Terapkan filter</button>
            @if ($selectedForm)<a class="px-2 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900" href="{{ route('admin.reports.index') }}">Reset</a>@endif
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Form</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Pelapor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Waktu diterima</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Monitoring</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">#{{ $report->id }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $report->form?->name ?? 'Form dihapus' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $report->user?->name ?? 'Guest' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $report->created_at->format('d M Y, H:i') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm"><a class="font-semibold text-red-700 hover:text-red-900" href="{{ route('admin.reports.show', $report) }}">Lihat detail →</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada laporan untuk filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($reports->hasPages())<div class="border-t border-slate-200 px-6 py-4">{{ $reports->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
