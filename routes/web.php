<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\MyReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/forms/{form:slug}', function (\App\Models\Form $form) {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route('dashboard');
    }

    return view('forms.show', compact('form'));
})->name('forms.show');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::view('/settings', 'settings.index')->name('settings.index');

    Route::middleware('user')->group(function () {
        Route::get('/my-reports', [MyReportController::class, 'index'])->name('my-reports.index');
        Route::get('/my-reports/{report}', [MyReportController::class, 'show'])->name('my-reports.show');
    });

    Route::middleware('admin')->group(function () {
        Route::resource('admin/reports', AdminReportController::class)
            ->only(['index', 'show'])
            ->names('admin.reports');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
