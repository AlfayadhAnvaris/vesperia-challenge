<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Submission;
use App\Services\SubmissionAnswerValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Submission::with(['form', 'user'])->latest();

        if ($request->filled('form_id')) {
            $query->where('form_id', $request->integer('form_id'));
        }

        return view('admin.reports.index', [
            'reports' => $query->paginate(15)->withQueryString(),
            'forms' => Form::orderBy('name')->get(['id', 'name']),
            'selectedForm' => $request->input('form_id'),
        ]);
    }

    public function show(Submission $report): View
    {
        $report->load(['form', 'user']);

        return view('admin.reports.show', [
            'report' => $report,
            'fields' => $this->fieldsWithAnswers($report),
        ]);
    }

    public function edit(Submission $report): View
    {
        $report->load(['form', 'user']);

        return view('admin.reports.edit', compact('report'));
    }

    public function update(Request $request, Submission $report, SubmissionAnswerValidator $answerValidator): RedirectResponse
    {
        $request->validate(['answers' => ['required', 'array']]);
        $report->update([
            'answers' => $answerValidator->validate($report->form, $request->input('answers')),
        ]);

        return redirect()->route('admin.reports.show', $report)->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Submission $report): RedirectResponse
    {
        $report->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Laporan berhasil dihapus.');
    }

    private function fieldsWithAnswers(Submission $report): array
    {
        $answers = $report->answers;
        $fields = [];

        foreach ($report->form->schema as $section) {
            foreach ($section['payloads'] ?? [] as $field) {
                $value = $answers[$field['id']] ?? null;
                $options = collect($field['options'] ?? [])->keyBy('id');

                if ($field['type'] === 'radio_button' && is_string($value)) {
                    $value = $options->get($value)['label'] ?? $value;
                } elseif ($field['type'] === 'checkbox' && is_array($value)) {
                    $value = collect($value)
                        ->map(fn ($id) => $options->get($id)['label'] ?? $id)
                        ->implode(', ');
                }

                $fields[] = [
                    'section' => $section['name'],
                    'label' => $field['label'],
                    'value' => is_array($value) ? json_encode($value) : $value,
                ];
            }
        }

        return $fields;
    }
}
