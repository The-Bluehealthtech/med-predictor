<?php

// Test script pour la route médicale
require_once 'vendor/autoload.php';

// Configuration Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Test Route Médicale ===\n";

// Test de récupération d'un joueur
try {
    $player = \App\Models\Player::with(['club', 'healthRecords'])->find(3);
    
    if ($player) {
        echo "✅ Joueur trouvé:\n";
        echo "ID: " . $player->id . "\n";
        echo "Nom: " . $player->name . "\n";
        echo "Prénom: " . $player->first_name . "\n";
        echo "Nom de famille: " . $player->last_name . "\n";
        echo "Date de naissance: " . $player->date_of_birth . "\n";
        echo "Nationalité: " . $player->nationality . "\n";
        echo "Position: " . $player->position . "\n";
        
        if ($player->club) {
            echo "Club: " . $player->club->name . "\n";
        }
        
        echo "Dossiers médicaux: " . $player->healthRecords->count() . "\n";
    } else {
        echo "❌ Joueur non trouvé\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Test Athletes ===\n";

// Test de récupération des athletes
try {
    $athletes = DB::table('athletes')->limit(3)->get();
    
    echo "✅ Athletes trouvés: " . $athletes->count() . "\n";
    foreach ($athletes as $athlete) {
        echo "- " . $athlete->name . " (ID: " . $athlete->id . ", FIFA: " . $athlete->fifa_id . ")\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Test Appointments ===\n";

// Test de récupération des appointments
try {
    $appointments = DB::table('appointments')
        ->join('athletes', 'appointments.athlete_id', '=', 'athletes.id')
        ->select(
            'appointments.*',
            'athletes.name',
            'athletes.dob as date_of_birth',
            'athletes.fifa_id as fifa_connect_id',
            'athletes.nationality',
            'athletes.position'
        )
        ->orderBy('appointments.appointment_date', 'desc')
        ->limit(3)
        ->get();
    
    echo "✅ Appointments trouvés: " . $appointments->count() . "\n";
    foreach ($appointments as $appointment) {
        echo "- " . $appointment->name . " - " . $appointment->title . " (" . $appointment->status . ")\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

?>
