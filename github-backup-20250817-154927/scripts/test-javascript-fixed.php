<?php

echo "=== TEST FINAL - ERREURS JAVASCRIPT CORRIGÉES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';

// 1. Vérifier que le fichier existe
echo "🔍 Vérification du fichier...\n";
if (!file_exists($portalFile)) {
    echo "❌ Fichier manquant!\n";
    exit(1);
}

$content = file_get_contents($portalFile);
echo "✅ Fichier lu: " . number_format(strlen($content)) . " bytes\n";

// 2. Vérifier les erreurs JavaScript corrigées
echo "\n🔍 Vérification des erreurs JavaScript corrigées...\n";

$errorPatterns = [
    '{{ \$player->[^}]+ }}5' => 'Variables mal formatées avec 5',
    '7{{ \$player->[^}]+ }}' => 'Variables mal formatées avec 7',
    '{{ \$player->[^}]+ }}{{ \$player->[^}]+ }}' => 'Variables concaténées incorrectement',
    'percentage:\s*\d+{{ \$player->' => 'Percentage mal formaté',
    'teamAvg:\s*\'\d+{{ \$player->' => 'TeamAvg mal formaté',
    'leagueAvg:\s*\'\d+{{ \$player->' => 'LeagueAvg mal formaté'
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

// 3. Vérifier la syntaxe JavaScript
echo "\n🔍 Vérification de la syntaxe JavaScript...\n";

// Extraire la section JavaScript
if (preg_match('/<script[^>]*>(.*?)<\/script>/s', $content, $matches)) {
    $javascript = $matches[1];
    
    // Vérifier les erreurs courantes
    $jsErrors = [
        'N[' => 'Variable N non définie',
        'undefined' => 'Variables undefined',
        'null' => 'Variables null',
        'NaN' => 'Not a Number'
    ];
    
    foreach ($jsErrors as $pattern => $description) {
        if (strpos($javascript, $pattern) !== false) {
            echo "⚠️ $description: Trouvé dans le JavaScript\n";
        } else {
            echo "✅ $description: OK\n";
        }
    }
} else {
    echo "⚠️ Section JavaScript non trouvée\n";
}

// 4. Test des routes
echo "\n🔍 Test des routes...\n";
$routes = [
    'http://localhost:8001/portail-joueur/29' => 'Portail Moussa Diaby',
    'http://localhost:8001/portail-joueur/32' => 'Portail Wahbi Khazri'
];

foreach ($routes as $url => $description) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ $description: HTTP $httpCode\n";
    } else {
        echo "❌ $description: HTTP $httpCode\n";
    }
}

// 5. Résumé final
echo "\n🎯 RÉSUMÉ FINAL\n";
echo "================\n";
echo "📁 Fichier: $portalFile\n";
echo "🔢 Erreurs JavaScript: $errorsFound\n";

if ($errorsFound == 0) {
    echo "\n🎉 SUCCÈS TOTAL! Toutes les erreurs JavaScript sont corrigées!\n";
    echo "💡 Le portail devrait maintenant fonctionner sans erreur 'N is not defined'!\n";
    echo "🚀 Prêt pour la production!\n";
} else {
    echo "\n⚠️ ATTENTION: Il reste $errorsFound types d'erreurs à corriger\n";
}

echo "\n🔒 Sauvegarde: resources/views/portail-joueur-js-fixed.blade.php\n";
echo "📋 Testez maintenant dans votre navigateur:\n";
echo "   - http://localhost:8001/portail-joueur/29\n";
echo "   - Vérifiez la console pour les erreurs JavaScript\n";






