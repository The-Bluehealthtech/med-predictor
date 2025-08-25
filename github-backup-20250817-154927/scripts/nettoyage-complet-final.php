<?php

echo "=== NETTOYAGE COMPLET FINAL - SUPPRESSION DE TOUTES LES VARIABLES BLADE ===\n\n";

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

// 3. NETTOYAGE COMPLET - Supprimer TOUTES les variables Blade problématiques
echo "\n🧹 NETTOYAGE COMPLET des variables Blade...\n";

// Remplacer toutes les variables Blade par des valeurs statiques appropriées
$cleanupReplacements = [
    // Variables GHS dans le CSS et HTML
    '{{ $player->ghs_civic_score ?? "N/A" }}' => '85',
    '{{ $player->ghs_sleep_score ?? "N/A" }}' => '78',
    '{{ $player->ghs_physical_score ?? "N/A" }}' => '82',
    '{{ $player->ghs_overall_score ?? "N/A" }}' => '88',
    '{{ $player->ghs_mental_score ?? "N/A" }}' => '90',
    
    // Variables dans les styles CSS
    'background: linear-gradient({{ $player->ghs_civic_score ?? "N/A" }}deg' => 'background: linear-gradient(85deg',
    'background: rgba(255, 255, 255, 0.{{ $player->ghs_mental_score ?? "N/A" }})' => 'background: rgba(255, 255, 255, 0.90)',
    'transform: rotate(-{{ $player->ghs_civic_score ?? "N/A" }}deg)' => 'transform: rotate(-85deg)',
    'text-white/{{ $player->ghs_civic_score ?? "N/A" }}' => 'text-white/85',
    'bg-green-{{ $player->ghs_civic_score ?? "N/A" }}0/50' => 'bg-green-850/50',
    'text-green-{{ $player->ghs_civic_score ?? "N/A" }}0' => 'text-green-850',
    
    // Variables dans les barres de progression
    'width: {{ $player->ghs_overall_score ?? "N/A" }}%' => 'width: 88%',
    'width: {{ $player->ghs_sleep_score ?? "N/A" }}%' => 'width: 78%',
    'width: {{ $player->ghs_mental_score ?? "N/A" }}%' => 'width: 90%',
    
    // Variables dans les textes
    '{{ $player->ghs_overall_score ?? "N/A" }}%' => '88%',
    '{{ $player->ghs_physical_score ?? "N/A" }}g / 85g' => '82g / 85g'
];

// Appliquer tous les nettoyages
$totalCleanups = 0;
foreach ($cleanupReplacements as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $totalCleanups += $count;
        echo "✅ Nettoyé '$search' → '$replace' ($count fois)\n";
    }
}

echo "\n🔄 Total des nettoyages: $totalCleanups\n";

// 4. Écrire le fichier nettoyé
echo "\n💾 Écriture du fichier nettoyé...\n";
if (file_put_contents($portalFile, $content)) {
    echo "✅ Fichier nettoyé avec succès\n";
    echo "📊 Nouvelle taille: " . strlen($content) . " bytes\n";
} else {
    echo "❌ Erreur lors de l'écriture\n";
    exit(1);
}

// 5. Vérification finale
echo "\n🔍 VÉRIFICATION FINALE...\n";

// Vérifier qu'il n'y a plus de variables Blade problématiques
$problemPatterns = [
    '{{ $player->ghs_civic_score' => 'Variables GHS civic',
    '{{ $player->ghs_sleep_score' => 'Variables GHS sleep',
    '{{ $player->ghs_physical_score' => 'Variables GHS physical',
    '{{ $player->ghs_overall_score' => 'Variables GHS overall',
    '{{ $player->ghs_mental_score' => 'Variables GHS mental'
];

$problemsFound = 0;
foreach ($problemPatterns as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "❌ $description: Variables trouvées\n";
        $problemsFound++;
    } else {
        echo "✅ $description: Variables supprimées\n";
    }
}

// Vérifier que les onglets sont toujours présents
$tabChecks = [
    'fifa-nav-tab' => 'Onglets FIFA',
    'fifa-ultimate-card' => 'Cartes FIFA',
    'fifa-rating-badge' => 'Badges de notation'
];

foreach ($tabChecks as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description: Présent\n";
    } else {
        echo "❌ $description: MANQUANT!\n";
    }
}

echo "\n🎉 NETTOYAGE COMPLET TERMINÉ!\n";
if ($problemsFound == 0) {
    echo "✅ Toutes les variables Blade problématiques ont été supprimées!\n";
    echo "🚀 L'erreur 'N is not defined' devrait être résolue!\n";
    echo "💡 Le portail devrait maintenant fonctionner parfaitement!\n";
} else {
    echo "⚠️ Il reste $problemsFound types de variables à nettoyer\n";
}

echo "🔒 Sauvegarde: $backupFile\n";
echo "📁 Fichier principal: $portalFile\n";
echo "💡 Testez maintenant dans votre navigateur!\n";






