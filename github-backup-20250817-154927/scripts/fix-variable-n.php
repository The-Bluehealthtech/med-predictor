<?php

echo "=== CORRECTION DE LA VARIABLE N PROBLÉMATIQUE ===\n\n";

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

echo "✅ Contenu lu (" . strlen($content) . " caractères)\n";

// 2. CHERCHER LA VARIABLE N PROBLÉMATIQUE
echo "\n🔍 Recherche de la variable N problématique...\n";

// Chercher les patterns problématiques
$problemPatterns = [
    'N[^a-zA-Z]' => 'Variable N isolée',
    'N\s*[=:]' => 'Variable N avec assignation',
    'N\s*[+\-*/]' => 'Variable N avec opérateur',
    'N\s*[)]' => 'Variable N avec parenthèse fermante',
    'N\s*[,;]' => 'Variable N avec virgule ou point-virgule'
];

$foundProblems = [];

foreach ($problemPatterns as $pattern => $description) {
    if (preg_match_all('/' . $pattern . '/', $content, $matches)) {
        $foundProblems[] = [
            'pattern' => $pattern,
            'description' => $description,
            'matches' => $matches[0],
            'count' => count($matches[0])
        ];
    }
}

if (empty($foundProblems)) {
    echo "✅ Aucune variable N problématique trouvée\n";
} else {
    echo "❌ Variables N problématiques trouvées:\n";
    foreach ($foundProblems as $problem) {
        echo "   - {$problem['description']}: {$problem['count']} occurrences\n";
        foreach (array_slice($problem['matches'], 0, 5) as $match) {
            echo "     * '$match'\n";
        }
    }
}

// 3. CHERCHER LE CONTEXTE AUTOUR DE LA LIGNE 2229
echo "\n🔍 Recherche du contexte autour de la ligne 2229...\n";

$lines = explode("\n", $content);
$startLine = max(0, 2225);
$endLine = min(count($lines) - 1, 2235);

echo "📄 Lignes $startLine-$endLine:\n";
for ($i = $startLine; $i <= $endLine; $i++) {
    $lineNum = $i + 1;
    $lineContent = trim($lines[$i]);
    if (!empty($lineContent)) {
        echo "   $lineNum: $lineContent\n";
    }
}

// 4. CORRIGER LES VARIABLES N PROBLÉMATIQUES
echo "\n🔧 Correction des variables N problématiques...\n";

$corrections = [
    // Remplacer N isolé par une valeur par défaut
    '/\bN\b(?!\w)/' => '0',
    // Remplacer N avec opérateurs
    '/\bN\s*[+\-*/]/' => '0$0',
    // Remplacer N avec assignation
    '/\bN\s*[=:]/' => '0$0',
    // Remplacer N avec parenthèses
    '/\bN\s*[)]/' => '0$0',
    // Remplacer N avec virgules
    '/\bN\s*[,;]/' => '0$0'
];

$originalContent = $content;
$correctionsApplied = 0;

foreach ($corrections as $pattern => $replacement) {
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        $diff = strlen($newContent) - strlen($content);
        $content = $newContent;
        $correctionsApplied++;
        echo "✅ Correction appliquée: $pattern → $replacement\n";
    }
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

echo "\n🎉 CORRECTION TERMINÉE!\n";
echo "🚀 La variable N problématique devrait être corrigée!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Si le problème persiste, une restauration complète sera nécessaire!\n";






