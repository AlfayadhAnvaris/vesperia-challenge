<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use JsonException;

class FormController extends Controller
{
    public function index(): View
    {
        return view('admin.forms.index', [
            'forms' => Form::withCount('submissions')->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.forms.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateForm($request);
        $form = Form::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'schema' => $validated['schema'],
        ]);

        return redirect()->route('admin.forms.edit', $form)->with('success', 'Form berhasil dibuat.');
    }

    public function edit(Form $form): View
    {
        return view('admin.forms.edit', compact('form'));
    }

    public function update(Request $request, Form $form): RedirectResponse
    {
        $validated = $this->validateForm($request, $form);
        $form->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'schema' => $validated['schema'],
        ]);

        return redirect()->route('admin.forms.edit', $form)->with('success', 'Form berhasil diperbarui.');
    }

    public function destroy(Form $form): RedirectResponse
    {
        if ($form->submissions()->exists()) {
            return redirect()->route('admin.forms.index')
                ->with('error', 'Form masih memiliki laporan. Hapus laporan terkait terlebih dahulu.');
        }

        $form->delete();

        return redirect()->route('admin.forms.index')->with('success', 'Form berhasil dihapus.');
    }

    private function validateForm(Request $request, ?Form $form = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:120',
                'alpha_dash',
                Rule::unique('forms', 'slug')->ignore($form?->id),
            ],
            'schema_json' => ['required', 'string', 'max:500000'],
        ]);

        try {
            $schema = json_decode($validated['schema_json'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'schema_json' => 'Schema harus berisi JSON yang valid.',
            ]);
        }

        $schemaValidator = Validator::make(['schema' => $schema], [
            'schema' => ['required', 'array', 'min:1'],
            'schema.*.name' => ['required', 'string', 'max:255'],
            'schema.*.payloads' => ['required', 'array'],
            'schema.*.payloads.*.id' => ['required', 'string', 'max:255'],
            'schema.*.payloads.*.label' => ['required', 'string', 'max:255'],
            'schema.*.payloads.*.type' => ['required', Rule::in(['text', 'long_text', 'radio_button', 'checkbox'])],
            'schema.*.payloads.*.options' => ['present', 'array'],
            'schema.*.payloads.*.options.*.id' => ['required', 'string', 'max:255'],
            'schema.*.payloads.*.options.*.label' => ['required', 'string', 'max:255'],
        ]);

        if ($schemaValidator->fails()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'schema_json' => 'Struktur JSON tidak sesuai: '.implode(' ', $schemaValidator->errors()->all()),
            ]);
        }

        $validated['schema'] = $schema;

        return $validated;
    }
}
