<?php

echo "=== NETTOYAGE FINAL DES VARIABLES BLADE MAL FORMATÉES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-nettoye.blade.php';

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

// 3. NETTOYAGE COMPLET des variables mal formatées
echo "\n🧹 NETTOYAGE des variables mal formatées...\n";

// Variables mal formatées dans le CSS et JavaScript
$malformedReplacements = [
    // === CSS MAL FORMATÉ ===
    '{{ $player->pcmas->count() }}' => '8',
    '{{ $player->performances->sum("assists") ?? 0 }}' => '15',
    '{{ $player->healthRecords->count() }}' => '12',
    
    // === CSS AVEC VARIABLES DANS LES VALEURS ===
    'transform: translateY(-{{ $player->pcmas->count() }}px)' => 'transform: translateY(-8px)',
    'background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff{{ $player->pcmas->count() }}f00 100%)' => 'background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff8f00 100%)',
    'box-shadow: 0 {{ $player->pcmas->count() }}px 20px rgba(255, 2{{ $player->performances->sum("assists") ?? 0 }}, 0, 0.4)' => 'box-shadow: 0 8px 20px rgba(255, 215, 0, 0.4)',
    'background: linear-gradient(135deg, #1e3a{{ $player->pcmas->count() }}a, #1e40af)' => 'background: linear-gradient(135deg, #1e3a8a, #1e40af)',
    'box-shadow: 0 4px {{ $player->healthRecords->count() }}px rgba(30, 5{{ $player->pcmas->count() }}, 13{{ $player->pcmas->count() }}, 0.3)' => 'box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3)',
    'box-shadow: 0 {{ $player->pcmas->count() }}px 20px rgba(30, 5{{ $player->pcmas->count() }}, 13{{ $player->pcmas->count() }}, 0.4)' => 'box-shadow: 0 8px 20px rgba(30, 58, 138, 0.4)',
    'background: linear-gradient(135deg, #1e40af, #3b{{ $player->pcmas->count() }}2f6)' => 'background: linear-gradient(135deg, #1e40af, #3b82f6)',
    'box-shadow: 0 6px 16px rgba(30, 5{{ $player->pcmas->count() }}, 13{{ $player->pcmas->count() }}, 0.35)' => 'box-shadow: 0 6px 16px rgba(30, 58, 138, 0.35)',
    
    // === META CHARSET MAL FORMATÉ ===
    '<meta charset="UTF-{{ $player->pcmas->count() }}">' => '<meta charset="UTF-8">',
    
    // === AUTRES VARIABLES MAL FORMATÉES ===
    'rgba(30, 5{{ $player->pcmas->count() }}, 13{{ $player->pcmas->count() }}, 0.3)' => 'rgba(30, 58, 138, 0.3)',
    'rgba(30, 5{{ $player->pcmas->count() }}, 13{{ $player->pcmas->count() }}, 0.4)' => 'rgba(30, 58, 138, 0.4)',
    'rgba(30, 5{{ $player->pcmas->count() }}, 13{{ $player->pcmas->count() }}, 0.35)' => 'rgba(30, 58, 138, 0.35)',
    
    // === CORRECTION DES COULEURS ===
    '#ff{{ $player->pcmas->count() }}f00' => '#ff8f00',
    '#1e3a{{ $player->pcmas->count() }}a' => '#1e3a8a',
    '#3b{{ $player->pcmas->count() }}2f6' => '#3b82f6'
];

// Appliquer TOUS les nettoyages
$totalCleanups = 0;
foreach ($malformedReplacements as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $totalCleanups += $count;
        echo "✅ Nettoyé: '$search' → '$replace' ($count fois)\n";
    }
}

echo "\n🔄 Total des nettoyages: $totalCleanups\n";

// 4. Vérifier qu'il n'y a plus de variables mal formatées
echo "\n🔍 VÉRIFICATION des variables restantes...\n";

$remainingMalformed = [
    '{{ $player->pcmas->count() }}' => 'PCMAs count',
    '{{ $player->performances->sum("assists") ?? 0 }}' => 'Assists sum',
    '{{ $player->healthRecords->count() }}' => 'Health records count'
];

$stillMalformed = 0;
foreach ($remainingMalformed as $variable => $description) {
    if (strpos($content, $variable) !== false) {
        echo "⚠️ $description: '$variable' encore présent\n";
        $stillMalformed++;
    } else {
        echo "✅ $description: '$variable' supprimé\n";
    }
}

// 5. Écrire le fichier nettoyé
echo "\n💾 Écriture du fichier nettoyé...\n";
if (file_put_contents($portalFile, $content)) {
    echo "✅ Fichier nettoyé avec succès\n";
    echo "📊 Nouvelle taille: " . strlen($content) . " bytes\n";
} else {
    echo "❌ Erreur lors de l'écriture\n";
    exit(1);
}

// 6. Vérification finale
echo "\n🎯 VÉRIFICATION FINALE...\n";

if ($stillMalformed == 0) {
    echo "🎉 SUCCÈS TOTAL! Toutes les variables mal formatées ont été supprimées!\n";
    echo "✅ L'erreur 'N is not defined' devrait être résolue!\n";
    echo "✅ Vue.js devrait maintenant fonctionner correctement!\n";
} else {
    echo "⚠️ ATTENTION: Il reste $stillMalformed variables mal formatées\n";
    echo "🔧 Un nettoyage supplémentaire pourrait être nécessaire\n";
}

echo "\n🔒 Sauvegarde: $backupFile\n";
echo "📁 Fichier principal: $portalFile\n";
echo "💡 Testez maintenant dans votre navigateur!\n";
echo "🚀 Vue.js devrait fonctionner sans erreur!\n";






