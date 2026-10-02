<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAssistantController;

/*
|--------------------------------------------------------------------------
| Google Assistant Routes
|--------------------------------------------------------------------------
|
| Routes pour l'intégration Google Assistant avec FIT
| Ces routes sont accessibles via webhook depuis Google Actions
|
*/

Route::prefix('google-assistant')->name('google.assistant.')->group(function () {
    
    // Points d'entrée d'écriture : authentification du webhook obligatoire.
    Route::post('/webhook', [GoogleAssistantController::class, 'handleIntent'])
        ->name('webhook')
        ->middleware('google.assistant.auth');

    Route::post('/submit-pcma', [GoogleAssistantController::class, 'submitPcmaToFit'])
        ->name('submit-pcma')
        ->middleware('google.assistant.auth');

    // Endpoints de santé uniquement, sans données métier.
    Route::get('/health', [GoogleAssistantController::class, 'health'])
        ->name('health');

    Route::get('/fit-health', [GoogleAssistantController::class, 'checkFitApiHealth'])
        ->name('fit-health');
    
    // Endpoint de test pour le développement
    Route::post('/test', [GoogleAssistantController::class, 'handleIntent'])
        ->name('test')
        ->middleware('google.assistant.auth');
});

// Route de fallback pour les requêtes non reconnues
Route::fallback(function () {
    return response()->json([
        'fulfillmentText' => 'Désolé, je n\'ai pas compris votre demande. Dites "commence un PCMA" pour démarrer un nouveau formulaire médical.',
        'fulfillmentMessages' => [
            [
                'text' => [
                    'text' => ['Désolé, je n\'ai pas compris votre demande. Dites "commence un PCMA" pour démarrer un nouveau formulaire médical.']
                ]
            ]
        ]
    ]);
});
