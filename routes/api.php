<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Middleware\OptionalSanctumAuthentication;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/forms', [FormController::class, 'index']);
Route::get('/forms/{form:slug}', [FormController::class, 'show']);
Route::middleware(EnsureFrontendRequestsAreStateful::class)->group(function () {
    Route::get('/forms/{form:slug}/submissions', [SubmissionController::class, 'index'])
        ->middleware('auth:sanctum');
    Route::post('/forms/{form:slug}/submissions', [SubmissionController::class, 'store'])
        ->middleware(OptionalSanctumAuthentication::class);
});
