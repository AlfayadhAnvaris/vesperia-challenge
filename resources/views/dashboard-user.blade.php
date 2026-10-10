<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-red-700">Ruang kerja pelapor</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Dashboard User</h2>
                <p class="mt-1 text-sm text-slate-500">Halo, {{ auth()->user()->name }}. Buat laporan dan pantau laporan Anda.</p>
            </div>
            <a href="{{ route('forms.show', 'operational-risk') }}" class="inline-flex items-center rounded-lg bg-red-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-800">
                + Buat laporan
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <section class="grid gap-4 sm:grid-cols-2" aria-label="Ringkasan laporan pribadi">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Total laporan saya</p>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-red-50 text-lg text-red-700" aria-hidden="true">▤</span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($submissionCount) }}</p>
                <p class="mt-2 text-sm text-slate-500">Seluruh laporan yang Anda kirim</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Laporan 7 hari terakhir</p>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-lg text-emerald-700" aria-hidden="true">↗</span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($recentSubmissionCount) }}</p>
                <p class="mt-2 text-sm text-slate-500">Aktivitas pelaporan Anda minggu ini</p>
            </article>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="user-report-chart-title">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 id="user-report-chart-title" class="font-semibold text-slate-900">Aktivitas laporan saya</h3>
                    <p class="mt-1 text-sm text-slate-500">Jumlah laporan yang Anda kirim dalam 7 hari terakhir</p>
                </div>
                <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">7 hari</span>
            </div>
            <div class="mt-6 grid h-44 grid-cols-7 items-end gap-3 sm:gap-5" role="img" aria-label="Grafik laporan pribadi selama tujuh hari terakhir">
                @foreach ($chartDays as $day)
                    <div class="flex h-full flex-col items-center justify-end gap-2">
                        <span class="text-xs font-semibold text-slate-600">{{ $day['total'] }}</span>
                        <div class="flex h-32 w-full items-end rounded-t-lg bg-slate-50">
                            <div class="w-full rounded-t-lg bg-gradient-to-t from-red-700 to-orange-400" style="height: {{ $day['height'] }}%"></div>
                        </div>
                        <span class="text-xs text-slate-500">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div>
                    <h3 class="font-semibold text-slate-900">Laporan terbaru</h3>
                    <p class="mt-1 text-sm text-slate-500">Hanya Anda yang dapat melihat laporan di daftar ini.</p>
                </div>
                <a href="{{ route('my-reports.index') }}" class="text-sm font-semibold text-red-700 hover:text-red-900">Semua laporan saya →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Form</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Dikirim</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestSubmissions as $report)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">#{{ $report->id }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $report->form?->name ?? 'Form dihapus' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $report->created_at->format('d M Y, H:i') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm"><a class="font-semibold text-red-700 hover:text-red-900" href="{{ route('my-reports.show', $report) }}">Lihat →</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <p class="text-sm font-semibold text-slate-700">Belum ada laporan</p>
                                    <p class="mt-1 text-sm text-slate-500">Mulai dengan membuat laporan pertama Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
