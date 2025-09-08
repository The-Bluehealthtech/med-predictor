<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountRequestController;

// Account Request Routes (completely public, no middleware)
Route::post('/account-request', [AccountRequestController::class, 'store'])->name('account-request.store');

// Account Request Data Routes (public)
Route::get('/account-request/football-types', function () {
    return response()->json([
        'success' => true,
        'data' => [
            '11-a-side' => 'Football 11 à 11',
            'futsal' => 'Futsal',
            'women' => 'Football Féminin',
            'beach-soccer' => 'Beach Soccer',
            'indoor' => 'Football en Salle',
            'street' => 'Street Football'
        ]
    ]);
});

Route::get('/account-request/organization-types', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'club' => 'Club de Football',
            'association' => 'Association Nationale',
            'federation' => 'Fédération',
            'league' => 'Ligue',
            'academy' => 'Académie',
            'school' => 'École de Football',
            'other' => 'Autre'
        ]
    ]);
});

Route::get('/account-request/fifa-associations', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'UEFA' => [
                ['id' => 'fra', 'full_name' => 'Fédération Française de Football'],
                ['id' => 'ger', 'full_name' => 'Deutscher Fußball-Bund'],
                ['id' => 'esp', 'full_name' => 'Real Federación Española de Fútbol'],
                ['id' => 'ita', 'full_name' => 'Federazione Italiana Giuoco Calcio'],
                ['id' => 'eng', 'full_name' => 'The Football Association']
            ],
            'CONMEBOL' => [
                ['id' => 'bra', 'full_name' => 'Confederação Brasileira de Futebol'],
                ['id' => 'arg', 'full_name' => 'Asociación del Fútbol Argentino'],
                ['id' => 'uru', 'full_name' => 'Asociación Uruguaya de Fútbol'],
                ['id' => 'chi', 'full_name' => 'Federación de Fútbol de Chile'],
                ['id' => 'col', 'full_name' => 'Federación Colombiana de Fútbol']
            ]
        ]
    ]);
});

Route::get('/account-request/fifa-connect-types', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'club_admin' => 'Administrateur de Club',
            'club_manager' => 'Manager de Club',
            'club_medical' => 'Staff Médical de Club',
            'association_admin' => 'Administrateur d\'Association',
            'association_registrar' => 'Registraire d\'Association',
            'association_medical' => 'Staff Médical d\'Association',
            'referee' => 'Arbitre',
            'assistant_referee' => 'Arbitre Assistant',
            'fourth_official' => '4ème Arbitre',
            'var_official' => 'Officiel VAR',
            'match_commissioner' => 'Commissaire de Match',
            'match_official' => 'Officiel de Match',
            'team_doctor' => 'Médecin d\'Équipe',
            'physiotherapist' => 'Physiothérapeute',
            'sports_scientist' => 'Scientifique du Sport'
        ]
    ]);
});
