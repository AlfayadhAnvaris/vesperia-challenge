<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Services\SubmissionAnswerValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request, Form $form): JsonResponse
    {
        $submissions = $request->user()->isAdmin()
            ? $form->submissions()
            : $request->user()->submissions()->where('form_id', $form->id);

        return response()->json([
            'data' => $submissions
                ->latest()
                ->paginate(20)
                ->items(),
        ]);
    }

    public function store(Request $request, Form $form, SubmissionAnswerValidator $answerValidator): JsonResponse
    {
        abort_if($request->user()?->isAdmin(), 403, 'Akun admin hanya dapat memonitor laporan.');

        $request->validate([
            'answers' => ['required', 'array'],
        ]);

        $submission = $form->submissions()->create([
            'user_id' => $request->user()?->id,
            'answers' => $answerValidator->validate($form, $request->input('answers')),
        ]);

        return response()->json([
            'message' => 'Laporan berhasil dikirim.',
            'data' => $submission,
            'redirect_url' => $request->user() ? route('my-reports.index') : null,
        ], 201);
    }
}
