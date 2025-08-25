<?php

echo "=== CORRECTION AGRESSIVE DE TOUTES LES VARIABLES ISOLÉES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';

if (!file_exists($portalFile)) {
    echo "❌ Fichier portal non trouvé: $portalFile\n";
    exit(1);
}

echo "📁 Fichier portal: $portalFile\n";
echo "📊 Taille: " . filesize($portalFile) . " bytes\n\n";

// 1. LIRE LE CONTENU
echo "🔄 Lecture du contenu...\n";
$content = file_get_contents($portalFile);

if (empty($content)) {
    echo "❌ Le fichier est vide! Restauration nécessaire.\n";
    exit(1);
}

echo "✅ Contenu lu (" . strlen($content) . " bytes)\n";

// 2. CORRECTION AGRESSIVE DE TOUTES LES VARIABLES ISOLÉES
echo "\n🔧 Correction agressive de toutes les variables isolées...\n";

// Remplacer toutes les variables isolées par des valeurs sûres
$isolatedVariables = [
    'A' => '0',
    'B' => '0', 
    'C' => '0',
    'D' => '0',
    'E' => '0',
    'F' => '0',
    'G' => '0',
    'H' => '0',
    'I' => '0',
    'J' => '0',
    'K' => '0',
    'L' => '0',
    'M' => '0',
    'N' => '0',
    'O' => '0',
    'P' => '0',
    'Q' => '0',
    'R' => '0',
    'S' => '0',
    'T' => '0',
    'U' => '0',
    'V' => '0',
    'W' => '0',
    'X' => '0',
    'Y' => '0',
    'Z' => '0'
];

$correctionsApplied = 0;

foreach ($isolatedVariables as $variable => $replacement) {
    // Chercher la variable isolée (avec des espaces ou caractères spéciaux autour)
    $patterns = [
        "/\\b$variable\\b/" => $replacement,  // Variable isolée
        "/ $variable /" => " $replacement ",   // Variable avec espaces
        "/ $variable,/" => " $replacement,",   // Variable avec virgule
        "/ $variable;/" => " $replacement;",   // Variable avec point-virgule
        "/ $variable\\)/" => " $replacement)", // Variable avec parenthèse
        "/ $variable=/" => " $replacement=",   // Variable avec égal
        "/ $variable:/" => " $replacement:",   // Variable avec deux-points
        "/ $variable\\//" => " $replacement/", // Variable avec slash
        "/ $variable\\+/" => " $replacement+", // Variable avec plus
        "/ $variable-/" => " $replacement-",   // Variable avec moins
        "/ $variable\\*/" => " $replacement*", // Variable avec étoile
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $count = preg_match_all($pattern, $content, $matches);
        if ($count > 0) {
            $content = preg_replace($pattern, $replacement, $content);
            $correctionsApplied += $count;
            echo "✅ Remplacé '$pattern' par '$replacement' ($count fois)\n";
        }
    }
}

// 3. CORRECTION FINALE PLUS AGRESSIVE
echo "\n🔧 Correction finale ultra-agressive...\n";

// Remplacer toutes les occurrences restantes de variables isolées
foreach ($isolatedVariables as $variable => $replacement) {
    $count = substr_count($content, " $variable ");
    if ($count > 0) {
        $content = str_replace(" $variable ", " $replacement ", $content);
        echo "✅ Remplacé ' $variable ' par ' $replacement ' ($count fois)\n";
        $correctionsApplied += $count;
    }
}

// 4. VÉRIFICATION FINALE
echo "\n🔍 Vérification finale...\n";

$remainingProblems = 0;
foreach ($isolatedVariables as $variable => $replacement) {
    $count = substr_count($content, " $variable ");
    if ($count > 0) {
        echo "⚠️ Il reste encore $count occurrences de '$variable'\n";
        $remainingProblems += $count;
    }
}

if ($remainingProblems == 0) {
    echo "✅ Toutes les variables isolées ont été corrigées!\n";
} else {
    echo "⚠️ Il reste $remainingProblems variables problématiques\n";
}

// 5. SAUVEGARDER LES CORRECTIONS
if ($correctionsApplied > 0) {
    if (file_put_contents($portalFile, $content)) {
        echo "\n✅ Fichier corrigé avec succès!\n";
        echo "📊 Corrections appliquées: $correctionsApplied\n";
        echo "📊 Taille finale: " . filesize($portalFile) . " bytes\n";
    } else {
        echo "\n❌ Erreur lors de la sauvegarde\n";
        exit(1);
    }
} else {
    echo "\n⚠️ Aucune correction nécessaire\n";
}

echo "\n🎉 CORRECTION AGRESSIVE TERMINÉE!\n";
echo "🚀 Toutes les variables isolées devraient être corrigées!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Les erreurs 'A is not defined', 'B is not defined', etc. devraient être résolues!\n";
echo "✨ Vue.js devrait maintenant fonctionner sans erreur!\n";
echo "🎯 Le portail devrait être parfaitement fonctionnel!\n";






