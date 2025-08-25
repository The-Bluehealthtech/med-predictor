<?php

echo "=== CORRECTION SPÉCIFIQUE DE LA VARIABLE N ===\n\n";

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

// 2. CORRIGER SPÉCIFIQUEMENT LA VARIABLE N PROBLÉMATIQUE
echo "\n🔧 Correction spécifique de la variable N...\n";

// Remplacer les occurrences spécifiques de N problématiques
$corrections = [
    // Ligne 2229: pointBackgroundColor: '#10b9N1' → '#10b901'
    '#10b9N1' => '#10b901',
    // Autres occurrences similaires
    'N/' => '0/',
    'N ' => '0 ',
    'N,' => '0,',
    'N;' => '0;',
    'N)' => '0)',
    'N=' => '0=',
    'N:' => '0:',
    'N+' => '0+',
    'N-' => '0-',
    'N*' => '0*'
];

$correctionsApplied = 0;

foreach ($corrections as $old => $new) {
    $count = substr_count($content, $old);
    if ($count > 0) {
        $content = str_replace($old, $new, $content);
        $correctionsApplied += $count;
        echo "✅ Remplacé '$old' par '$new' ($count fois)\n";
    }
}

// 3. SAUVEGARDER LES CORRECTIONS
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
echo "\n💡 L'erreur 'N is not defined' devrait être résolue!\n";






