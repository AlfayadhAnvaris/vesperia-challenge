<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\JsonResponse;

class FormController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Form::query()
                ->select(['id', 'name', 'slug', 'created_at', 'updated_at'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function show(Form $form): JsonResponse
    {
        return response()->json(['data' => $form]);
    }
}
