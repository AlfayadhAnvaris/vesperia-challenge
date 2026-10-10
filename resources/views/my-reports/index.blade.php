<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-sm font-medium text-red-700">Ruang pelaporan</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Laporan saya</h2></div>
            <a href="{{ route('forms.show', 'operational-risk') }}" class="rounded-lg bg-red-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-800">+ Buat laporan</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @include('admin.partials.flash')
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor laporan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Form</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Dikirim</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reports as $report)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">#{{ $report->id }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $report->form?->name ?? 'Form dihapus' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $report->created_at->format('d M Y, H:i') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm"><a class="font-semibold text-red-700 hover:text-red-900" href="{{ route('my-reports.show', $report) }}">Lihat laporan →</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-14 text-center">
                                <p class="text-sm font-semibold text-slate-700">Belum ada laporan</p>
                                <p class="mt-1 text-sm text-slate-500">Laporan yang Anda kirim akan muncul di sini.</p>
                                <a class="mt-4 inline-flex text-sm font-semibold text-red-700 hover:text-red-900" href="{{ route('forms.show', 'operational-risk') }}">Buat laporan pertama →</a>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($reports->hasPages())<div class="border-t border-slate-200 px-6 py-4">{{ $reports->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
