<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompetitionController;

Route::prefix('competitions')->name('competitions.')->group(function () {
    // Dashboard des compétitions
    Route::get('/dashboard', [CompetitionController::class, 'dashboard'])->name('dashboard');
    
    // CRUD des compétitions
    Route::get('/', [CompetitionController::class, 'index'])->name('index');
    Route::get('/create', [CompetitionController::class, 'create'])->name('create');
    Route::post('/', [CompetitionController::class, 'store'])->name('store');
    Route::get('/{competition}', [CompetitionController::class, 'show'])->name('show');
    Route::get('/{competition}/edit', [CompetitionController::class, 'edit'])->name('edit');
    Route::put('/{competition}', [CompetitionController::class, 'update'])->name('update');
    Route::delete('/{competition}', [CompetitionController::class, 'destroy'])->name('destroy');
    
    // Gestion des phases
    Route::get('/{competition}/phases', [CompetitionController::class, 'phases'])->name('phases');
    Route::post('/{competition}/phases', [CompetitionController::class, 'storePhase'])->name('phases.store');
    
    // Gestion des groupes
    Route::get('/{competition}/groups', [CompetitionController::class, 'groups'])->name('groups');
    Route::post('/{competition}/groups', [CompetitionController::class, 'storeGroup'])->name('groups.store');
    
    // API pour les actions en masse
    Route::post('/bulk-actions', [CompetitionController::class, 'bulkActions'])->name('bulk-actions');
    
    // Workflow FIFA Connect
    Route::post('/{competition}/submit', [CompetitionController::class, 'submit'])->name('submit');
    Route::post('/{competition}/validate', [CompetitionController::class, 'validate'])->name('validate');
    Route::post('/{competition}/publish', [CompetitionController::class, 'publish'])->name('publish');
    Route::post('/{competition}/reject', [CompetitionController::class, 'reject'])->name('reject');
});


