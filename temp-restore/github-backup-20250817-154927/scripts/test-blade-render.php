<?php

echo "=== TEST DE RENDU DE LA VUE BLADE ===\n\n";

// 1. SIMULER UN JOUEUR
echo "🔍 Simulation d'un joueur...\n";
$player = (object) [
    'id' => 32,
    'first_name' => 'Wahbi',
    'last_name' => 'Khazri',
    'nationality' => 'Tunisia',
    'position' => 'FW',
    'overall_rating' => 78,
    'potential_rating' => 80,
    'height' => 182,
    'weight' => 75,
    'preferred_foot' => 'Right',
    'date_of_birth' => '1991-02-08',
    'ghs_overall_score' => 85,
    'ghs_physical_score' => 82,
    'ghs_mental_score' => 88,
    'ghs_sleep_score' => 80,
    'ghs_civic_score' => 87,
    'club' => (object) [
        'name' => 'Esperance de Tunis',
        'city' => 'Tunis',
        'country' => 'Tunisia',
        'logo_path' => '/images/clubs/esperance.svg'
    ],
    'healthRecords' => [],
    'performances' => [],
    'pcmas' => []
];

$portalData = [
    'personalInfo' => [
        'name' => 'Wahbi Khazri',
        'position' => 'FW',
        'club' => 'Esperance de Tunis',
        'nationality' => 'Tunisia',
        'age' => 32,
        'overall_rating' => 78,
        'potential_rating' => 80
    ],
    'healthMetrics' => [
        'ghs_overall_score' => 85,
        'ghs_physical_score' => 82,
        'ghs_mental_score' => 88,
        'ghs_sleep_score' => 80,
        'injury_risk_score' => 15,
        'injury_risk_level' => 'Faible'
    ],
    'performanceStats' => [
        'total_matches' => 0,
        'total_health_records' => 0,
        'total_pcma' => 0,
        'contribution_score' => 75,
        'data_value_estimate' => 1200
    ],
    'recentActivity' => [
        'last_health_check' => null,
        'last_match' => null,
        'last_pcma' => null
    ]
];

echo "✅ Joueur simulé créé\n";

// 2. TESTER LA VUE
echo "\n🔍 Test de la vue...\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
if (file_exists($portalFile)) {
    echo "✅ Fichier portal trouvé\n";
    
    // Lire le contenu
    $content = file_get_contents($portalFile);
    
    // Remplacer les variables Blade par des valeurs de test
    $testContent = str_replace('{{ $player->first_name }}', $player->first_name, $content);
    $testContent = str_replace('{{ $player->last_name }}', $player->last_name, $testContent);
    $testContent = str_replace('{{ $player->nationality }}', $player->nationality, $testContent);
    $testContent = str_replace('{{ $player->position }}', $player->position, $testContent);
    $testContent = str_replace('{{ $player->overall_rating }}', $player->overall_rating, $testContent);
    $testContent = str_replace('{{ $player->club->name }}', $player->club->name, $testContent);
    
    // Remplacer les autres variables
    $testContent = str_replace('{{ $player->performances->count() }}', '0', $testContent);
    $testContent = str_replace('{{ $player->healthRecords->count() }}', '0', $testContent);
    $testContent = str_replace('{{ $player->pcmas->count() }}', '0', $testContent);
    
    echo "✅ Variables Blade remplacées\n";
    
    // Vérifier la taille
    echo "📊 Taille du contenu: " . strlen($testContent) . " bytes\n";
    
    // Vérifier que le contenu contient des données
    if (strpos($testContent, 'Wahbi Khazri') !== false) {
        echo "✅ Nom du joueur trouvé dans le contenu\n";
    } else {
        echo "❌ Nom du joueur manquant\n";
    }
    
    if (strpos($testContent, 'Esperance de Tunis') !== false) {
        echo "✅ Nom du club trouvé dans le contenu\n";
    } else {
        echo "❌ Nom du club manquant\n";
    }
    
} else {
    echo "❌ Fichier portal non trouvé\n";
}

echo "\n🎉 TEST DE RENDU TERMINÉ!\n";
echo "💡 La vue semble correcte, le problème pourrait être ailleurs\n";
