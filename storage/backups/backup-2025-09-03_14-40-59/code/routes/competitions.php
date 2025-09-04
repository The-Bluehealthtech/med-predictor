<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompetitionController;

/*
|--------------------------------------------------------------------------
| Routes Compétitions - Module FIT
|--------------------------------------------------------------------------
|
| Routes isolées pour le module Compétitions
| Aucune modification des routes existantes
|
*/

// Routes publiques pour les compétitions
Route::prefix('competitions')->name('competitions.')->group(function () {
    
    // Dashboard principal des compétitions
    Route::get('/', [CompetitionController::class, 'index'])->name('index');
    
    // Côté Club
    Route::prefix('club')->name('club.')->group(function () {
        Route::get('/engagements', [CompetitionController::class, 'clubEngagements'])->name('engagements');
        Route::get('/effectif', [CompetitionController::class, 'clubEffectif'])->name('effectif');
        Route::get('/calendrier', [CompetitionController::class, 'clubCalendrier'])->name('calendrier');
        Route::get('/feuilles-match', [CompetitionController::class, 'clubFeuillesMatch'])->name('feuilles-match');
        Route::get('/discipline', [CompetitionController::class, 'clubDiscipline'])->name('discipline');
        
        // API pour les actions
        Route::post('/feuille-match/{match}', [CompetitionController::class, 'soumettreFeuilleMatch'])->name('soumettre-feuille');
        Route::get('/effectif/verification', [CompetitionController::class, 'verifierEffectif'])->name('verifier-effectif');
    });
    
    // Côté Association/Ligue
    Route::prefix('association')->name('association.')->group(function () {
        Route::get('/supervision', [CompetitionController::class, 'associationSupervision'])->name('supervision');
        Route::get('/engagements-clubs', [CompetitionController::class, 'associationEngagementsClubs'])->name('engagements-clubs');
        Route::get('/calendrier-global', [CompetitionController::class, 'associationCalendrierGlobal'])->name('calendrier-global');
        Route::get('/resultats-classements', [CompetitionController::class, 'associationResultatsClassements'])->name('resultats-classements');
        Route::get('/discipline-sanctions', [CompetitionController::class, 'associationDisciplineSanctions'])->name('discipline-sanctions');
        Route::get('/rapports-statistiques', [CompetitionController::class, 'associationRapportsStatistiques'])->name('rapports-statistiques');
        
        // API pour les actions
        Route::post('/valider-feuille/{feuille}', [CompetitionController::class, 'validerFeuilleMatch'])->name('valider-feuille');
        Route::post('/reprogrammer-match/{match}', [CompetitionController::class, 'reprogrammerMatch'])->name('reprogrammer-match');
        Route::post('/mettre-a-jour-resultat/{match}', [CompetitionController::class, 'mettreAJourResultat'])->name('mettre-a-jour-resultat');
        Route::post('/ajouter-sanction', [CompetitionController::class, 'ajouterSanction'])->name('ajouter-sanction');
        Route::get('/export-rapport/{type}', [CompetitionController::class, 'exportRapport'])->name('export-rapport');
    });
    
    // API générales
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/competitions', [CompetitionController::class, 'apiCompetitions'])->name('competitions');
        Route::get('/matches/{competition}', [CompetitionController::class, 'apiMatches'])->name('matches');
        Route::get('/classements/{competition}', [CompetitionController::class, 'apiClassements'])->name('classements');
        Route::get('/effectif/{club}', [CompetitionController::class, 'apiEffectif'])->name('effectif');
    });
});