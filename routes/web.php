<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('welcome', function () {
    return Inertia::render('Index');
})->name('home');

Route::get('/', function () {
    return Inertia::render('Index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->middleware('admin')->name('admin.dashboard');

    Route::get('app-dashboard', function () {
        return Inertia::render('Index', ['initialView' => 'dashboard']);
    })->name('app.dashboard');

    // Assessment routes
    Route::prefix('assessment')->name('assessment.')->group(function () {
        // Página inicial/introdução
        Route::get('/start', [AssessmentController::class, 'start'])
            ->name('start');

        // Criar/continuar avaliação
        Route::post('/create', [AssessmentController::class, 'create'])
            ->name('create');

        // Questionário
        Route::get('/{assessmentId}/questions', [AssessmentController::class, 'questions'])
            ->name('questions');

        // Salvar respostas (auto-save AJAX)
        Route::post('/{assessmentId}/save', [AssessmentController::class, 'saveResponses'])
            ->name('save');

        // Submeter avaliação completa
        Route::post('/{assessmentId}/submit', [AssessmentController::class, 'submit'])
            ->name('submit');

        // Página de processamento
        Route::get('/{assessmentId}/processing', [AssessmentController::class, 'processing'])
            ->name('processing');

        // Check status (polling AJAX)
        Route::get('/{assessmentId}/status', [AssessmentController::class, 'checkStatus'])
            ->name('status');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
