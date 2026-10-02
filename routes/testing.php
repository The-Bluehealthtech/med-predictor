<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['status' => 'ok']);
});

// Test route for PerformanceChart component
Route::get('/test-performance-chart', function () {
    $chartData = request('chartData', []);
    $chartType = request('chartType', 'line');
    $title = request('title', 'Performance Chart');
    $timeRange = request('timeRange');
    $showLegend = request('showLegend', false);
    $theme = request('theme');
    $loading = request('loading', false);
    $options = request('options', []);
    
    // Handle chartData if it's a JSON string
    if (is_string($chartData)) {
        $chartData = json_decode($chartData, true) ?: [];
    }
    
    // Handle options if it's a JSON string
    if (is_string($options)) {
        $options = json_decode($options, true) ?: [];
    }
    
    // Validation for data format test
    if (request()->has('chartData')) {
        // Check if the original request data has the required structure
        $originalChartData = request('chartData');
        
        if (is_string($originalChartData)) {
            $decodedData = json_decode($originalChartData, true);
            
            if (!is_array($decodedData) || !isset($decodedData['labels']) || !isset($decodedData['datasets']) || 
                !is_array($decodedData['labels']) || !is_array($decodedData['datasets'])) {
                return response()->json(['error' => 'Invalid chart data format'], 422);
            }
        } else {
            if (!is_array($chartData) || !isset($chartData['labels']) || !isset($chartData['datasets'])) {
                return response()->json(['error' => 'Invalid chart data format'], 422);
            }
        }
    }
    
    return view('test.performance-chart', compact('chartData', 'chartType', 'title', 'timeRange', 'showLegend', 'theme', 'loading', 'options'));
});

Route::post('/test-performance-chart-click', function () {
    return response()->json(['success' => true]);
});



// Minimal dashboard target required by permission middleware tests.
Route::get('/dashboard', function () {
    return response()->json(['status' => 'ok']);
})->name('dashboard');

// Canonical FIT metric management route used by feature tests.
Route::get(
    '/performances/fit-metrics',
    [\App\Http\Controllers\FitMetricManagementController::class, 'index']
)->middleware([
    'auth',
    'auth.unified',
    'permission.unified:record-performance-metrics',
])->name('performances.fit-metrics');

// ===== Espace fédération — Direction technique nationale (permission dtn-federation-space) =====
// Fiches joueurs, convocations, états de retour, jetons d'API de la DTN.
Route::middleware(['auth', 'auth.unified', 'permission.unified:dtn-federation-space'])->group(function () {
    Route::get(
        '/dtn',
        [\App\Http\Controllers\Dtn\FederationController::class, 'index']
    )->middleware(['auth'])->name('dtn.index');
    Route::get('/dtn/players', [\App\Http\Controllers\Dtn\FederationController::class, 'players'])->name('dtn.players.index');
    Route::get('/dtn/players/{player}', [\App\Http\Controllers\Dtn\FederationController::class, 'player'])->whereNumber('player')->name('dtn.players.show');
    Route::get('/dtn/selections/create', [\App\Http\Controllers\Dtn\FederationController::class, 'create'])->name('dtn.selections.create');
    Route::post('/dtn/selections', [\App\Http\Controllers\Dtn\FederationController::class, 'store'])->name('dtn.selections.store');
    Route::get('/dtn/selections/{selection}', [\App\Http\Controllers\Dtn\FederationController::class, 'show'])->name('dtn.selections.show');
    Route::post('/dtn/selections/{selection}/return', [\App\Http\Controllers\Dtn\FederationController::class, 'saveReturn'])->name('dtn.selections.return');
    Route::post('/dtn/selections/{selection}/cancel', [\App\Http\Controllers\Dtn\FederationController::class, 'cancel'])->name('dtn.selections.cancel');
    Route::get('/dtn/api-access', [\App\Http\Controllers\Dtn\ApiTokenController::class, 'index'])->name('dtn.api-access');
    Route::post('/dtn/api-access', [\App\Http\Controllers\Dtn\ApiTokenController::class, 'store'])->name('dtn.api-access.store');
    Route::delete('/dtn/api-access/{token}', [\App\Http\Controllers\Dtn\ApiTokenController::class, 'destroy'])->whereNumber('token')->name('dtn.api-access.destroy');
});

// ===== Espace club — Sélections nationales (permission club-selections-space) =====
// Convocations reçues, états de départ, retours de sélection, jetons d'API du club.
Route::middleware(['auth', 'auth.unified', 'permission.unified:club-selections-space'])->group(function () {
    Route::get('/club/selections', [\App\Http\Controllers\Club\SelectionController::class, 'index'])->name('club.selections.index');
    Route::get('/club/selections/returns', [\App\Http\Controllers\Club\SelectionController::class, 'returns'])->name('club.selections.returns');
    Route::get('/club/selections/api-access', [\App\Http\Controllers\Club\ApiTokenController::class, 'index'])->name('club.selections.api-access');
    Route::post('/club/selections/api-access', [\App\Http\Controllers\Club\ApiTokenController::class, 'store'])->name('club.selections.api-access.store');
    Route::delete('/club/selections/api-access/{token}', [\App\Http\Controllers\Club\ApiTokenController::class, 'destroy'])->whereNumber('token')->name('club.selections.api-access.destroy');
    Route::get('/club/selections/{selection}', [\App\Http\Controllers\Club\SelectionController::class, 'show'])->whereNumber('selection')->name('club.selections.show');
    Route::post('/club/selections/{selection}/departure', [\App\Http\Controllers\Club\SelectionController::class, 'saveDeparture'])->name('club.selections.departure');
    Route::post('/club/selections/{selection}/acknowledge', [\App\Http\Controllers\Club\SelectionController::class, 'acknowledge'])->name('club.selections.acknowledge');
});

// Cockpit entraîneur : même contrôleur et même middleware qu'en production.
Route::get(
    '/modules/coach-cockpit',
    [\App\Http\Controllers\CoachCockpitController::class, 'show']
)->middleware(['auth'])->name('modules.coach-cockpit');

// Analyse des performances : même contrôleur qu'en production.
Route::get(
    '/performances/analytics',
    [\App\Http\Controllers\PerformanceAnalyticsController::class, 'index']
)->middleware(['auth'])->name('performances.analytics');

// Minimal language switch target required by the application layout during tests.
Route::post('/language', function () {
    return redirect()->back();
})->name('language.update');

// Minimal modules target required by the application layout during tests.
Route::get('/modules', function () {
    return response()->json(['status' => 'ok']);
})->name('modules.index');


// Minimal login target required by auth middleware during feature tests.
Route::get('/login', function () {
    return response()->json(['status' => 'login']);
})->name('login');


// Current player portal route used by security feature tests.
// Uses the same controller and auth middleware as production.
Route::get(
    '/test-portail-joueur-simple',
    [\App\Http\Controllers\PlayerPortalSimpleController::class, 'show']
)->middleware(['auth'])->name('test.portail.joueur.simple');


// Player directory endpoints used by portal authorization tests.
Route::get('/players/list', [\App\Http\Controllers\AdminController::class, 'playersList'])
    ->middleware(['auth'])->name('players.list');
Route::get('/admin/search-players', [\App\Http\Controllers\AdminController::class, 'searchPlayers'])
    ->middleware(['auth'])->name('admin.search.players');

// Organization card actions use the same controller and auth middleware as web routes.
Route::middleware(['auth'])->prefix('organization-cards')->name('organization-cards.')->group(function () {
    Route::get('/{type}/create', [\App\Http\Controllers\OrganizationCardController::class, 'create'])->name('create');
    Route::post('/{type}', [\App\Http\Controllers\OrganizationCardController::class, 'store'])->name('store');
    Route::get('/{type}/{id}/edit', [\App\Http\Controllers\OrganizationCardController::class, 'edit'])->name('edit');
    Route::put('/{type}/{id}', [\App\Http\Controllers\OrganizationCardController::class, 'update'])->name('update');
});
