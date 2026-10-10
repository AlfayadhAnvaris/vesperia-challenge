<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><p class="text-sm font-medium text-red-700">Administrasi</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Form</h2></div>
            <a href="{{ route('admin.forms.create') }}" class="rounded-lg bg-red-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-800">+ Buat form</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @include('admin.partials.flash')
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nama form</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Laporan</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($forms as $form)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800">{{ $form->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $form->slug }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $form->submissions_count }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <a class="font-semibold text-red-700 hover:text-red-900" href="{{ route('admin.forms.edit', $form) }}">Edit</a>
                                    <form class="ml-3 inline" method="POST" action="{{ route('admin.forms.destroy', $form) }}" onsubmit="return confirm('Hapus form ini? Form dengan laporan terkait tidak dapat dihapus.');">
                                        @csrf @method('DELETE')
                                        <button class="font-semibold text-red-600 hover:text-red-800" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada form di database.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($forms->hasPages())<div class="border-t border-slate-200 px-6 py-4">{{ $forms->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
