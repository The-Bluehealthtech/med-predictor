<?php

use App\Http\Controllers\PosturalAssessmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/health-records/{healthRecord}/postural-assessments', [PosturalAssessmentController::class, 'index'])
        ->name('postural-assessments.index');

    Route::post('/health-records/{healthRecord}/postural-assessments', [PosturalAssessmentController::class, 'store'])
        ->name('postural-assessments.store');

    Route::get('/postural-assessments/{assessment}', [PosturalAssessmentController::class, 'show'])
        ->name('postural-assessments.show');

    Route::put('/postural-assessments/{assessment}', [PosturalAssessmentController::class, 'update'])
        ->name('postural-assessments.update');

    Route::post('/postural-assessments/{assessment}/complete', [PosturalAssessmentController::class, 'complete'])
        ->name('postural-assessments.complete');

    Route::post('/postural-assessments/{assessment}/validate', [PosturalAssessmentController::class, 'validateAssessment'])
        ->name('postural-assessments.validate');

    Route::get('/postural-assessments/{assessment}/compare/{other}', [PosturalAssessmentController::class, 'compare'])
        ->name('postural-assessments.compare');
});
