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

