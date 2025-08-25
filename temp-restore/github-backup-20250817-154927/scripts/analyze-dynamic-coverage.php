<?php

echo "=== ANALYSE DU TAUX DE COUVERTURE DYNAMIQUE ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';

if (!file_exists($portalFile)) {
    echo "❌ Fichier portal non trouvé: $portalFile\n";
    exit(1);
}

echo "📁 Fichier portal: $portalFile\n";
echo "📊 Taille: " . filesize($portalFile) . " bytes\n\n";

// 1. LIRE LE CONTENU
$content = file_get_contents($portalFile);

// 2. ANALYSE DES DONNÉES DYNAMIQUES
echo "🔍 ANALYSE DES DONNÉES DYNAMIQUES...\n\n";

// Variables Blade présentes
$bladeVariables = [
    'Informations joueur' => [
        '$player->first_name' => 0,
        '$player->last_name' => 0,
        '$player->date_of_birth' => 0,
        '$player->nationality' => 0,
        '$player->position' => 0,
        '$player->height' => 0,
        '$player->weight' => 0,
        '$player->preferred_foot' => 0
    ],
    'Scores FIFA' => [
        '$player->fifa_overall_rating' => 0,
        '$player->fifa_physical_rating' => 0,
        '$player->fifa_speed_rating' => 0,
        '$player->fifa_technical_rating' => 0,
        '$player->fifa_mental_rating' => 0
    ],
    'Scores GHS' => [
        '$player->ghs_overall_score' => 0,
        '$player->ghs_physical_score' => 0,
        '$player->ghs_mental_score' => 0,
        '$player->ghs_sleep_score' => 0,
        '$player->ghs_civic_score' => 0
    ],
    'Informations club' => [
        '$player->club->name' => 0,
        '$player->club->logo_path' => 0,
        '$player->club->country' => 0
    ],
    'Compteurs' => [
        '$player->performances->count()' => 0,
        '$player->healthRecords->count()' => 0,
        '$player->pcmas->count()' => 0
    ],
    'Images' => [
        '$player->photo_path' => 0,
        '$player->nationality_flag_path' => 0
    ]
];

// Compter les occurrences de chaque variable
foreach ($bladeVariables as $category => $variables) {
    foreach ($variables as $variable => $count) {
        $bladeVariables[$category][$variable] = substr_count($content, $variable);
    }
}

// 3. ANALYSE DES DONNÉES STATIQUES
echo "📊 ANALYSE DES DONNÉES STATIQUES...\n\n";

$staticData = [
    'Noms de joueurs' => ['Lionel Messi', 'Sadio Mané', 'Cristiano Ronaldo'],
    'Noms de clubs' => ['Chelsea FC', 'Al Nassr', 'Inter Miami', 'PSG', 'Barcelona'],
    'Pays' => ['Argentina', 'Senegal', 'Portugal', 'France', 'Spain'],
    'Scores numériques' => ['89', '87', '88', '90', '85', '92', '91', '95'],
    'Mesures' => ['1.70m', '72kg'],
    'Dates' => ['1987-06-24', '1992-04-10'],
    'Compteurs' => ['15', '8', '12']
];

$staticDataFound = [];
foreach ($staticData as $category => $items) {
    $staticDataFound[$category] = [];
    foreach ($items as $item) {
        if (strpos($content, $item) !== false) {
            $staticDataFound[$category][] = $item;
        }
    }
}

// 4. CALCUL DU TAUX DE COUVERTURE
echo "📈 CALCUL DU TAUX DE COUVERTURE...\n\n";

$totalDynamicVariables = 0;
$totalStaticData = 0;
$dynamicVariablesFound = 0;
$staticDataFoundCount = 0;

foreach ($bladeVariables as $category => $variables) {
    echo "🏷️ $category:\n";
    foreach ($variables as $variable => $count) {
        $totalDynamicVariables++;
        if ($count > 0) {
            $dynamicVariablesFound++;
            echo "  ✅ $variable: $count occurrences\n";
        } else {
            echo "  ❌ $variable: 0 occurrence\n";
        }
    }
    echo "\n";
}

echo "📊 DONNÉES STATIQUES TROUVÉES:\n";
foreach ($staticDataFound as $category => $items) {
    if (!empty($items)) {
        echo "⚠️ $category: " . implode(', ', $items) . "\n";
        $staticDataFoundCount += count($items);
    }
}

$totalStaticData = array_sum(array_map('count', $staticData));

// 5. RÉSULTATS FINAUX
echo "\n🎯 RÉSULTATS FINAUX:\n";
echo "=====================================\n";

$dynamicCoverage = ($dynamicVariablesFound / $totalDynamicVariables) * 100;
$staticDataPercentage = ($staticDataFoundCount / $totalStaticData) * 100;

echo "📊 TAUX DE COUVERTURE DYNAMIQUE: " . number_format($dynamicCoverage, 1) . "%\n";
echo "📊 DONNÉES DYNAMIQUES: $dynamicVariablesFound / $totalDynamicVariables\n";
echo "📊 DONNÉES STATIQUES RESTANTES: $staticDataFoundCount / $totalStaticData\n";
echo "📊 POURCENTAGE DE DONNÉES STATIQUES: " . number_format($staticDataPercentage, 1) . "%\n";

// 6. RECOMMANDATIONS
echo "\n💡 RECOMMANDATIONS:\n";
echo "==================\n";

if ($dynamicCoverage >= 90) {
    echo "🎉 EXCELLENT! La page est presque entièrement dynamique!\n";
} elseif ($dynamicCoverage >= 75) {
    echo "✅ TRÈS BIEN! La page est majoritairement dynamique!\n";
} elseif ($dynamicCoverage >= 50) {
    echo "⚠️ MOYEN! La page est partiellement dynamique.\n";
} else {
    echo "❌ FAIBLE! La page contient encore beaucoup de données statiques.\n";
}

if ($staticDataFoundCount > 0) {
    echo "\n🔧 DONNÉES STATIQUES À REMPLACER:\n";
    foreach ($staticDataFound as $category => $items) {
        if (!empty($items)) {
            echo "  - $category: " . implode(', ', $items) . "\n";
        }
    }
}

echo "\n🎯 OBJECTIF: 100% de données dynamiques depuis la base de données!\n";
echo "✨ Plus de données fixes, tout doit venir de la base de données!\n";






