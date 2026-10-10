<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('my-reports.index', [
            'reports' => $request->user()->submissions()
                ->with('form')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function show(Request $request, Submission $report): View
    {
        abort_unless($report->user_id === $request->user()->id, 404);
        $report->load('form');
        $answers = $report->answers;
        $sections = [];

        foreach ($report->form->schema as $section) {
            $fields = [];

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
                    'label' => $field['label'],
                    'value' => is_array($value) ? json_encode($value) : $value,
                ];
            }

            $sections[] = [
                'name' => $section['name'],
                'fields' => $fields,
            ];
        }

        return view('my-reports.show', compact('report', 'sections'));
    }
}
