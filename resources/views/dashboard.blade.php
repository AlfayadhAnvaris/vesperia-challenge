<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-red-700">Pusat monitoring</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Dashboard Admin</h2>
                <p class="mt-1 text-sm text-slate-500">Pantau aktivitas laporan; akses admin bersifat hanya-baca.</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center rounded-lg bg-red-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-800">
                Monitoring laporan <span class="ml-2" aria-hidden="true">→</span>
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @include('admin.partials.flash')

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-label="Statistik">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Total laporan</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($submissionCount) }}</p>
                <p class="mt-2 text-sm text-slate-500">Seluruh laporan yang tersimpan</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Laporan 7 hari terakhir</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($recentSubmissionCount) }}</p>
                <p class="mt-2 text-sm text-slate-500">Aktivitas pelaporan terbaru</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Form aktif</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($formCount) }}</p>
                <p class="mt-2 text-sm text-slate-500">Definisi form tersedia</p>
            </article>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="admin-report-chart-title">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 id="admin-report-chart-title" class="font-semibold text-slate-900">Tren laporan</h3>
                    <p class="mt-1 text-sm text-slate-500">Laporan masuk selama 7 hari terakhir</p>
                </div>
                <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">7 hari</span>
            </div>
            <div class="mt-6 grid h-44 grid-cols-7 items-end gap-3 sm:gap-5" role="img" aria-label="Grafik jumlah laporan masuk selama tujuh hari terakhir">
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
                    <p class="mt-1 text-sm text-slate-500">Submission yang baru masuk ke sistem.</p>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="text-sm font-semibold text-red-700 hover:text-red-900">Buka laporan →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Form</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Diterima</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestSubmissions as $report)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-700">#{{ $report->id }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $report->form?->name ?? 'Form dihapus' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $report->created_at->format('d M Y, H:i') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm"><a class="font-semibold text-red-700 hover:text-red-900" href="{{ route('admin.reports.show', $report) }}">Lihat</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada laporan masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-slate-900 p-6 text-white">
            <div>
                <h3 class="font-semibold">Mode monitoring</h3>
                <p class="mt-1 text-sm text-slate-300">Admin dapat melihat laporan dan tren tanpa mengubah data yang dikirim user.</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-slate-100">Buka monitoring</a>
        </section>
    </div>
</x-app-layout>
