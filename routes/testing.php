<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/account-request', [\App\Http\Controllers\AccountRequestController::class, 'store'])->name('account-request.store');

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

// Évaluation de rôle depuis le cockpit : mêmes routes et middleware qu'en production.
Route::post(
    '/modules/coach-cockpit/role-evaluation/import',
    [\App\Http\Controllers\CoachCockpitController::class, 'importRoleEvaluationData']
)->middleware(['auth', 'auth.unified', 'permission.unified:record-performance-metrics'])->name('modules.coach-cockpit.role-evaluation.import');
Route::post(
    '/modules/coach-cockpit/role-evaluation/compute',
    [\App\Http\Controllers\CoachCockpitController::class, 'computeRoleEvaluations']
)->middleware(['auth', 'auth.unified', 'permission.unified:record-performance-metrics'])->name('modules.coach-cockpit.role-evaluation.compute');
Route::middleware(['auth', 'auth.unified', 'permission.unified:record-performance-metrics'])
    ->prefix('/modules/coach-cockpit/role-evaluation/settings')->name('modules.coach-cockpit.role-evaluation.settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\RoleEvaluationSettingsController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\RoleEvaluationSettingsController::class, 'store'])->name('.store');
        Route::post('/import', [\App\Http\Controllers\RoleEvaluationSettingsController::class, 'import'])->name('.import');
        Route::put('/{version}', [\App\Http\Controllers\RoleEvaluationSettingsController::class, 'update'])->name('.update');
        Route::get('/{version}/export', [\App\Http\Controllers\RoleEvaluationSettingsController::class, 'export'])->name('.export');
        Route::post('/{version}/publish', [\App\Http\Controllers\RoleEvaluationSettingsController::class, 'publish'])->name('.publish');
        Route::post('/{version}/archive', [\App\Http\Controllers\RoleEvaluationSettingsController::class, 'archive'])->name('.archive');
    });

// Analyse des performances : même contrôleur qu'en production.
Route::get(
    '/performances/analytics',
    [\App\Http\Controllers\PerformanceAnalyticsController::class, 'index']
)->middleware(['auth'])->name('performances.analytics');

Route::middleware(['auth'])->group(function () {
    // Module Passeports : passeport médical (résumé IPS HL7/IHE) et passeport de transfert (format FIFA).
    Route::get('/passports/medical', [\App\Http\Controllers\Passports\PassportsController::class, 'medicalIndex'])->name('passports.medical.index');
    Route::get('/passports/medical/{player}', [\App\Http\Controllers\Passports\PassportsController::class, 'medicalShow'])->whereNumber('player')->name('passports.medical.show');
    Route::get('/passports/medical/{player}/pdf', [\App\Http\Controllers\Passports\PassportsController::class, 'medicalPdf'])->whereNumber('player')->name('passports.medical.pdf');
    Route::post('/passports/medical/{player}/attest', [\App\Http\Controllers\Passports\PassportsController::class, 'medicalAttest'])->whereNumber('player')->name('passports.medical.attest');
    Route::get('/passports/medical/{player}/fhir', [\App\Http\Controllers\Passports\PassportsController::class, 'medicalFhir'])->whereNumber('player')->name('passports.medical.fhir');
    Route::post('/passports/medical/{player}/ips/publish', [\App\Http\Controllers\Passports\PassportsController::class, 'medicalIpsPublish'])->whereNumber('player')->name('passports.medical.ips.publish');
    Route::get('/passports/medical/{player}/ips/{document}', [\App\Http\Controllers\Passports\PassportsController::class, 'medicalIpsShow'])->whereNumber('player')->name('passports.medical.ips.show');
    // Visionneuse commune des fichiers médicaux (PCMA, documents du pré-accueil)
    Route::get('/medical-files/pcma/{pcma}/{field}', [App\Http\Controllers\MedicalFileViewerController::class, 'pcma'])->name('medical-files.pcma');
    Route::get('/medical-files/pcma/{pcma}/{field}/frame', [App\Http\Controllers\MedicalFileViewerController::class, 'pcmaFrame'])->middleware('throttle:180,1')->name('medical-files.pcma.frame');
    Route::get('/medical-files/documents/{document}', [App\Http\Controllers\MedicalFileViewerController::class, 'document'])->whereNumber('document')->name('medical-files.document');
    Route::get('/medical-files/documents/{document}/frame', [App\Http\Controllers\MedicalFileViewerController::class, 'documentFrame'])->whereNumber('document')->middleware('throttle:180,1')->name('medical-files.document.frame');
    Route::get('/medical-files/documents/{document}/source', [App\Http\Controllers\MedicalFileViewerController::class, 'documentSource'])->whereNumber('document')->name('medical-files.document.source');
    Route::get('/pcma/players/search', [App\Http\Controllers\PCMAController::class, 'searchPlayers'])->name('pcma.players.search');
    Route::get('/pcma/{pcma}/files/{field}', [App\Http\Controllers\PcmaDocumentController::class, 'file'])->name('pcma.file');
    Route::get('/clinical/players/{player}/external-data', [\App\Http\Controllers\Clinical\ExternalClinicalDataController::class, 'show'])->whereNumber('player')->name('clinical.external-data');
    Route::post('/clinical/players/{player}/external-data/integrate', [\App\Http\Controllers\Clinical\ExternalClinicalDataController::class, 'integrate'])->whereNumber('player')->name('clinical.external-data.integrate');
    Route::get('/clinical/players/{player}/imaging/{study}', [\App\Http\Controllers\Clinical\ExternalImagingController::class, 'show'])->whereNumber('player')->where('study', '[0-9.]{3,64}')->name('clinical.dicomweb.study');
    Route::get('/clinical/players/{player}/imaging/{study}/series/{series}/instances/{instance}/frame', [\App\Http\Controllers\Clinical\ExternalImagingController::class, 'frame'])
        ->whereNumber('player')->where(['study' => '[0-9.]{3,64}', 'series' => '[0-9.]{3,64}', 'instance' => '[0-9.]{3,64}'])->middleware('throttle:300,1')->name('clinical.dicomweb.frame');
    Route::get('/passports/transfer', [\App\Http\Controllers\Passports\PassportsController::class, 'transferIndex'])->name('passports.transfer.index');
    Route::get('/passports/transfer/{player}', [\App\Http\Controllers\Passports\PassportsController::class, 'transferShow'])->whereNumber('player')->name('passports.transfer.show');
    Route::get('/passports/transfer/{player}/pdf', [\App\Http\Controllers\Passports\PassportsController::class, 'transferPdf'])->whereNumber('player')->name('passports.transfer.pdf');
    Route::post('/passports/medical/{player}/digital-signature', [\App\Http\Controllers\Passports\PassportDocumentSignatureController::class, 'medical'])->whereNumber('player')->name('passports.medical.digital-signature');
    Route::post('/passports/transfer/{player}/digital-signature', [\App\Http\Controllers\Passports\PassportDocumentSignatureController::class, 'transfer'])->whereNumber('player')->name('passports.transfer.digital-signature');
    Route::post('/passports/{player}/digital-signature/{signature}/sync', [\App\Http\Controllers\Passports\PassportDocumentSignatureController::class, 'sync'])->whereNumber('player')->whereNumber('signature')->name('passports.digital-signature.sync');
    Route::get('/passports/{player}/digital-signature/{signature}/download', [\App\Http\Controllers\Passports\PassportDocumentSignatureController::class, 'download'])->whereNumber('player')->whereNumber('signature')->name('passports.digital-signature.download');

    // Dirigeants et staff des clubs (fiches au format FIFA Connect).
    Route::get('/club-officials', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'index'])->name('club-officials.index');
    Route::get('/clubs/{club}/officials', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'club'])->name('club-officials.club');
    Route::get('/clubs/{club}/officials/create', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'create'])->name('club-officials.create');
    Route::post('/clubs/{club}/officials', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'store'])->name('club-officials.store');
    Route::get('/clubs/{club}/officials/{official}', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'show'])->whereNumber('official')->name('club-officials.show');
    Route::get('/clubs/{club}/officials/{official}/edit', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'edit'])->whereNumber('official')->name('club-officials.edit');
    Route::put('/clubs/{club}/officials/{official}', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'update'])->whereNumber('official')->name('club-officials.update');
    Route::get('/clubs/{club}/officials/{official}/pdf', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'pdf'])->whereNumber('official')->name('club-officials.pdf');
    Route::post('/clubs/{club}/officials/{official}/digital-signature', [\App\Http\Controllers\ClubOfficials\ClubOfficialSignatureController::class, 'store'])->whereNumber('official')->name('club-officials.digital-signature');
    Route::post('/clubs/{club}/officials/{official}/digital-signature/{signature}/sync', [\App\Http\Controllers\ClubOfficials\ClubOfficialSignatureController::class, 'sync'])->whereNumber('official')->whereNumber('signature')->name('club-officials.digital-signature.sync');
    Route::get('/clubs/{club}/officials/{official}/digital-signature/{signature}/download', [\App\Http\Controllers\ClubOfficials\ClubOfficialSignatureController::class, 'download'])->whereNumber('official')->whereNumber('signature')->name('club-officials.digital-signature.download');
});

// Exports « Player statistics » des clubs (Excel/CSV) : reconnaissance, aperçu, import.
Route::middleware(['auth', 'auth.unified', 'permission.unified:record-performance-metrics'])->prefix('/modules/coach-cockpit/player-stats-import')->name('player-stats-import.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PlayerStatsImportController::class, 'create'])->name('create');
    Route::post('/preview', [\App\Http\Controllers\PlayerStatsImportController::class, 'preview'])->name('preview');
    Route::post('/', [\App\Http\Controllers\PlayerStatsImportController::class, 'store'])->name('store');
});

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
