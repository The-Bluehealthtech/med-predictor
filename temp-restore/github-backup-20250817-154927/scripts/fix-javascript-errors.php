<?php

echo "=== CORRECTION DES ERREURS JAVASCRIPT ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-js-fixed.blade.php';

// 1. Créer une sauvegarde
echo "🔒 Création d'une sauvegarde...\n";
if (copy($portalFile, $backupFile)) {
    echo "✅ Sauvegarde créée: $backupFile\n";
} else {
    echo "❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

// 2. Lire le contenu
echo "\n📖 Lecture du fichier...\n";
$content = file_get_contents($portalFile);
if (!$content) {
    echo "❌ Impossible de lire le fichier\n";
    exit(1);
}

echo "📊 Taille: " . strlen($content) . " bytes\n";

// 3. CORRECTION DES ERREURS JAVASCRIPT
echo "\n🔧 Correction des erreurs JavaScript...\n";

// Problème 1: Variables Blade mal formatées dans les objets JavaScript
$content = preg_replace(
    '/percentage:\s*(\d+){{ \$player->([^}]+) }}/',
    'percentage: $1, teamAvg: "{{ $player->$2 }}"',
    $content
);

// Problème 2: Concaténation incorrecte dans les chaînes
$content = preg_replace(
    '/display:\s*\'(\d+){{ \$player->([^}]+) }}/',
    'display: "$1{{ $player->$2 }}"',
    $content
);

// Problème 3: Variables dans les valeurs numériques
$content = preg_replace(
    '/percentage:\s*{{ \$player->([^}]+) }}5/',
    'percentage: {{ $player->$1 }}',
    $content
);

$content = preg_replace(
    '/percentage:\s*7{{ \$player->([^}]+) }}/',
    'percentage: {{ $player->$1 }}',
    $content
);

$content = preg_replace(
    '/teamAvg:\s*\'(\d+){{ \$player->([^}]+) }}/',
    'teamAvg: "{{ $player->$2 }}"',
    $content
);

$content = preg_replace(
    '/leagueAvg:\s*\'(\d+){{ \$player->([^}]+) }}/',
    'leagueAvg: "{{ $player->$2 }}"',
    $content
);

// Problème 4: Concaténation dans les chaînes de caractères
$content = preg_replace(
    '/display:\s*\'(\d+){{ \$player->([^}]+) }}/',
    'display: "{{ $player->$2 }}"',
    $content
);

// Problème 5: Variables dans les pourcentages
$content = preg_replace(
    '/percentage:\s*7{{ \$player->([^}]+) }}%/',
    'percentage: {{ $player->$1 }}',
    $content
);

// Problème 6: Variables dans les moyennes d'équipe
$content = preg_replace(
    '/teamAvg:\s*\'(\d+)\.{{ \$player->([^}]+) }}/',
    'teamAvg: "{{ $player->$2 }}"',
    $content
);

// Problème 7: Variables dans les moyennes de ligue
$content = preg_replace(
    '/leagueAvg:\s*\'(\d+)\.{{ \$player->([^}]+) }}/',
    'leagueAvg: "{{ $player->$2 }}"',
    $content
);

// Problème 8: Concaténation dans les distances
$content = preg_replace(
    '/display:\s*\'(\d+){{ \$player->([^}]+) }} km/',
    'display: "{{ $player->$2 }} km"',
    $content
);

// Problème 9: Variables dans les vitesses
$content = preg_replace(
    '/teamAvg:\s*\'(\d+){{ \$player->([^}]+) }} km/',
    'teamAvg: "{{ $player->$2 }} km"',
    $content
);

// Problème 10: Variables dans les sprints
$content = preg_replace(
    '/teamAvg:\s*\'{{ \$player->([^}]+) }}{{ \$player->([^}]+) }}/',
    'teamAvg: "{{ $player->$1 }}"',
    $content
);

// Problème 11: Variables dans les accélérations
$content = preg_replace(
    '/teamAvg:\s*(\d+){{ \$player->([^}]+) }}/',
    'teamAvg: "{{ $player->$2 }}"',
    $content
);

// Problème 12: Variables dans les décélérations
$content = preg_replace(
    '/teamAvg:\s*1{{ \$player->([^}]+) }}/',
    'teamAvg: "{{ $player->$1 }}"',
    $content
);

// Problème 13: Variables dans les changements de direction
$content = preg_replace(
    '/leagueAvg:\s*\'{{ \$player->([^}]+) }}/',
    'leagueAvg: "{{ $player->$1 }}"',
    $content
);

// Problème 14: Variables dans les sautes
$content = preg_replace(
    '/leagueAvg:\s*\'(\d+)\.{{ \$player->([^}]+) }}/',
    'leagueAvg: "{{ $player->$2 }}"',
    $content
);

echo "✅ Corrections JavaScript appliquées\n";

// 4. Écrire le fichier corrigé
echo "\n💾 Écriture du fichier corrigé...\n";
if (file_put_contents($portalFile, $content)) {
    echo "✅ Fichier corrigé avec succès\n";
    echo "📊 Nouvelle taille: " . strlen($content) . " bytes\n";
} else {
    echo "❌ Erreur lors de l'écriture\n";
    exit(1);
}

// 5. Vérification des corrections
echo "\n🔍 Vérification des corrections...\n";
$errorPatterns = [
    '{{ $player->[^}]+ }}5' => 'Variables mal formatées avec 5',
    '7{{ $player->[^}]+ }}' => 'Variables mal formatées avec 7',
    '{{ $player->[^}]+ }}{{ $player->[^}]+ }}' => 'Variables concaténées incorrectement'
];

$errorsFound = 0;
foreach ($errorPatterns as $pattern => $description) {
    if (preg_match_all($pattern, $content, $matches)) {
        echo "❌ $description: " . count($matches[0]) . " occurrences\n";
        $errorsFound++;
    } else {
        echo "✅ $description: Corrigé\n";
    }
}

echo "\n🎉 CORRECTION TERMINÉE!\n";
if ($errorsFound == 0) {
    echo "✅ Toutes les erreurs JavaScript ont été corrigées!\n";
    echo "🚀 Le portail devrait maintenant fonctionner sans erreur!\n";
} else {
    echo "⚠️ Il reste $errorsFound types d'erreurs à corriger\n";
}

echo "🔒 Sauvegarde: $backupFile\n";
echo "📁 Fichier principal: $portalFile\n";






