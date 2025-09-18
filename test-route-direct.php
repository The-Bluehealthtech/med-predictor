<?php

// Test direct de la route API
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== Test Route API Directe ===\n";

try {
    // Simuler une requête
    $request = new Request();
    $request->merge(['status' => 'confirmed', 'type' => 'consultation']);
    
    $statusFilter = $request->get('status');
    $typeFilter = $request->get('type');
    $dateFilter = $request->get('date');

    echo "Filtres: status=$statusFilter, type=$typeFilter, date=$dateFilter\n";

    // Construire la requête pour récupérer les patients avec leurs RDV
    $query = DB::table('appointments')
        ->join('athletes', 'appointments.athlete_id', '=', 'athletes.id')
        ->select(
            'appointments.*',
            'athletes.id as athlete_id',
            'athletes.name',
            'athletes.dob as date_of_birth',
            'athletes.fifa_id as fifa_connect_id',
            'athletes.nationality',
            'athletes.position'
        )
        ->orderBy('appointments.appointment_date', 'desc');

    // Appliquer les filtres
    if ($statusFilter) {
        $query->where('appointments.status', $statusFilter);
    }
    if ($typeFilter) {
        $query->where('appointments.type', $typeFilter);
    }
    if ($dateFilter) {
        $query->whereDate('appointments.appointment_date', $dateFilter);
    }

    $patients = $query->limit(50)->get();

    $response = [
        'success' => true,
        'patients' => $patients,
        'total' => $patients->count(),
        'filters' => [
            'status' => $statusFilter,
            'type' => $typeFilter,
            'date' => $dateFilter
        ]
    ];

    echo "✅ Réponse générée avec succès:\n";
    echo "Total patients: " . $response['total'] . "\n";
    echo "Patients trouvés:\n";
    foreach ($response['patients'] as $patient) {
        echo "- " . $patient->name . " (" . $patient->status . ", " . $patient->type . ")\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}

?>
