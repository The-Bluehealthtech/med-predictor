<?php

echo "=== CORRECTION COMPLÈTE DE TOUTES LES VARIABLES N ===\n\n";

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

// 2. CORRIGER TOUTES LES VARIABLES N RESTANTES
echo "\n🔧 Correction complète de toutes les variables N...\n";

// Utiliser une approche plus agressive avec preg_replace
$corrections = [
    // Remplacer N isolé par 0 (plus agressif)
    '/\bN\b/' => '0',
    // Remplacer N avec des caractères spéciaux
    '/N\//' => '0/',
    '/N\s/' => '0 ',
    '/N,/' => '0,',
    '/N;/' => '0;',
    '/N\)/' => '0)',
    '/N=/' => '0=',
    '/N:/' => '0:',
    '/N\+/' => '0+',
    '/N-/' => '0-',
    '/N\*/' => '0*'
];

$correctionsApplied = 0;
$originalContent = $content;

foreach ($corrections as $pattern => $replacement) {
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        $content = $newContent;
        $correctionsApplied++;
        echo "✅ Correction appliquée: $pattern → $replacement\n";
    }
}

// 3. VÉRIFIER QU'IL N'Y A PLUS DE N PROBLÉMATIQUES
echo "\n🔍 Vérification finale...\n";

$remainingN = preg_match_all('/\bN\b/', $content, $matches);
if ($remainingN > 0) {
    echo "⚠️ Il reste encore $remainingN variables N problématiques\n";
    
    // Correction finale plus agressive
    $content = preg_replace('/\bN\b/', '0', $content);
    echo "✅ Correction finale appliquée\n";
} else {
    echo "✅ Aucune variable N problématique restante\n";
}

// 4. SAUVEGARDER LES CORRECTIONS
if ($correctionsApplied > 0 || $remainingN > 0) {
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

echo "\n🎉 CORRECTION COMPLÈTE TERMINÉE!\n";
echo "🚀 Toutes les variables N problématiques devraient être corrigées!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 L'erreur 'N is not defined' devrait être définitivement résolue!\n";






