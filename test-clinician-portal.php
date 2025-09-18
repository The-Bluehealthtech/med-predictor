<?php

// Test du portail clinicien
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Test Portail Clinicien ===\n";

try {
    // Test 1: Compter les health records
    echo "1. Test HealthRecord::count()...\n";
    $healthRecordsCount = \App\Models\HealthRecord::count();
    echo "✅ HealthRecord count: $healthRecordsCount\n";
    
    // Test 2: Compter les PCMA
    echo "2. Test PCMA::count()...\n";
    $pcmasCount = \App\Models\PCMA::count();
    echo "✅ PCMA count: $pcmasCount\n";
    
    // Test 3: Compter les Players
    echo "3. Test Player::count()...\n";
    $playersCount = \App\Models\Player::count();
    echo "✅ Player count: $playersCount\n";
    
    // Test 4: Test avec relations
    echo "4. Test HealthRecord avec relations...\n";
    $healthRecords = \App\Models\HealthRecord::with('player')->limit(5)->get();
    echo "✅ HealthRecord avec relations: " . $healthRecords->count() . " trouvés\n";
    
    foreach ($healthRecords as $record) {
        echo "  - ID: " . $record->id . ", Player ID: " . $record->player_id;
        if ($record->player) {
            echo ", Player: " . $record->player->name;
        } else {
            echo ", Player: NULL";
        }
        echo "\n";
    }
    
    // Test 5: Test PCMA avec relations
    echo "5. Test PCMA avec relations...\n";
    $pcmas = \App\Models\PCMA::with('player')->limit(5)->get();
    echo "✅ PCMA avec relations: " . $pcmas->count() . " trouvés\n";
    
    foreach ($pcmas as $pcma) {
        echo "  - ID: " . $pcma->id . ", Player ID: " . $pcma->player_id;
        if ($pcma->player) {
            echo ", Player: " . $pcma->player->name;
        } else {
            echo ", Player: NULL";
        }
        echo "\n";
    }
    
    // Test 6: Statistiques
    echo "6. Test statistiques...\n";
    $stats = [
        'total_patients' => \App\Models\Player::count(),
        'active_health_records' => \App\Models\HealthRecord::where('status', 'active')->count(),
        'pending_pcmas' => \App\Models\PCMA::where('status', 'pending')->count(),
        'completed_pcmas' => \App\Models\PCMA::where('status', 'completed')->count(),
        'consultations_today' => \App\Models\HealthRecord::whereDate('record_date', today())->count(),
        'alerts' => \App\Models\HealthRecord::where('status', 'pending')->count()
    ];
    
    echo "✅ Statistiques calculées:\n";
    foreach ($stats as $key => $value) {
        echo "  - $key: $value\n";
    }
    
    echo "\n✅ Tous les tests sont passés avec succès!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
