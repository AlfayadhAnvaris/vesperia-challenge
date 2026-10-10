<form method="POST" action="{{ $action }}" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Periksa kembali isian:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div>
        <label for="name" class="block text-sm font-semibold text-slate-700">Nama form</label>
        <input id="name" name="name" value="{{ old('name', $form?->name) }}" required maxlength="255" class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-red-600 focus:ring-red-600">
    </div>

    <div>
        <label for="slug" class="block text-sm font-semibold text-slate-700">Slug URL</label>
        <input id="slug" name="slug" value="{{ old('slug', $form?->slug) }}" required maxlength="120" pattern="[A-Za-z0-9_-]+" class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-red-600 focus:ring-red-600">
        <p class="mt-1 text-xs text-slate-500">Gunakan huruf, angka, tanda hubung, atau garis bawah. Slug yang sudah digunakan form publik sebaiknya tidak diubah.</p>
    </div>

    <div>
        <label for="schema_json" class="block text-sm font-semibold text-slate-700">Schema form (JSON)</label>
        <p class="mt-1 text-xs text-slate-500">Schema memuat array section; setiap section memiliki <code>name</code> dan <code>payloads</code>. Pertahankan ID field dan pilihan agar laporan lama tetap terbaca.</p>
        <textarea id="schema_json" name="schema_json" required rows="22" spellcheck="false" placeholder="[{&quot;name&quot;:&quot;Detail&quot;,&quot;payloads&quot;:[{&quot;id&quot;:&quot;field-id&quot;,&quot;label&quot;:&quot;Deskripsi&quot;,&quot;type&quot;:&quot;long_text&quot;,&quot;options&quot;:[]}]}]" class="mt-2 block w-full rounded-lg border-slate-300 font-mono text-xs shadow-sm focus:border-red-600 focus:ring-red-600">{{ old('schema_json', $form ? json_encode($form->schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.forms.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Batal</a>
        <button type="submit" class="rounded-lg bg-red-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-800">{{ $form ? 'Simpan perubahan' : 'Buat form' }}</button>
    </div>
</form>
