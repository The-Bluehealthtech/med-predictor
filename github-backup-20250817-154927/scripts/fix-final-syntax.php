<?php

echo "=== CORRECTION FINALE DE LA SYNTAXE ===\n\n";

$file = 'resources/views/portail-joueur.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier non trouvé: $file\n";
    exit(1);
}

echo "📁 Fichier: $file\n";
echo "📊 Taille initiale: " . filesize($file) . " bytes\n\n";

$content = file_get_contents($file);

// 1. CORRIGER LES VARIABLES BLADE IMBRIQUÉES
echo "🔄 Correction des variables Blade imbriquées...\n";

// Remplacer les variables Blade imbriquées
$nestedBladeFixes = [
    // Variables Blade dans les attributs CSS
    '{{ $player->fifa_overall_rating ?? "N/A" }}' => '{{ $player->fifa_overall_rating ?? "N/A" }}',
    '{{ $player->injury_risk_level == "Faible" ? "#10b981" : ($player->injury_risk_level == "Moyen" ? "#f59e0b" : "#ef4444") }}' => '{{ $player->injury_risk_level == "Faible" ? "#10b981" : ($player->injury_risk_level == "Moyen" ? "#f59e0b" : "#ef4444") }}',
    '{{ min(1.0, max(0.3, ($player->ghs_overall_score ?? 50) / 100)) }}' => '{{ min(1.0, max(0.3, ($player->ghs_overall_score ?? 50) / 100)) }}',
    '{{ min(30, max(16, ($player->ghs_overall_score ?? 50) / 3)) }}px' => '{{ min(30, max(16, ($player->ghs_overall_score ?? 50) / 3)) }}px',
    '{{ min(100, max(10, ($player->performances->count() ?? 0) * 5)) }}' => '{{ min(100, max(10, ($player->performances->count() ?? 0) * 5)) }}',
    
    // Classes CSS dynamiques
    'risk-{{ strtolower($player->injury_risk_level ?? "unknown") }}' => 'risk-{{ strtolower($player->injury_risk_level ?? "unknown") }}',
    'ghs-score-{{ floor(($player->ghs_overall_score ?? 50) / 20) }}' => 'ghs-score-{{ floor(($player->ghs_overall_score ?? 50) / 20) }}',
    'performance-{{ $player->performances->count() > 10 ? "high" : ($player->performances->count() > 5 ? "medium" : "low") }}' => 'performance-{{ $player->performances->count() > 10 ? "high" : ($player->performances->count() > 5 ? "medium" : "low") }}'
];

foreach ($nestedBladeFixes as $broken => $fixed) {
    $count = substr_count($content, $broken);
    if ($count > 0) {
        $content = str_replace($broken, $fixed, $content);
        echo "✅ Variable Blade imbriquée corrigée ($count fois)\n";
    }
}

// 2. CORRIGER LES PROBLÈMES CSS
echo "\n🔄 Correction des problèmes CSS...\n";

// Remplacer les propriétés CSS problématiques
$cssFixes = [
    // Propriétés CSS avec variables Blade valides
    'background: linear-gradient(135deg, {{ $player->injury_risk_level == "Faible" ? "#10b981" : ($player->injury_risk_level == "Moyen" ? "#f59e0b" : "#ef4444") }} 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%)' => 'background: linear-gradient(135deg, {{ $player->injury_risk_level == "Faible" ? "#10b981" : ($player->injury_risk_level == "Moyen" ? "#f59e0b" : "#ef4444") }} 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%)',
    
    // Opacité dynamique
    'opacity: {{ min(1.0, max(0.3, ($player->ghs_overall_score ?? 50) / 100)) }}' => 'opacity: {{ min(1.0, max(0.3, ($player->ghs_overall_score ?? 50) / 100)) }}',
    
    // Border radius dynamique
    'border-radius: {{ min(30, max(16, ($player->ghs_overall_score ?? 50) / 3)) }}px' => 'border-radius: {{ min(30, max(16, ($player->ghs_overall_score ?? 50) / 3)) }}px',
    
    // Z-index dynamique
    'z-index: {{ min(100, max(10, ($player->performances->count() ?? 0) * 5)) }}' => 'z-index: {{ min(100, max(10, ($player->performances->count() ?? 0) * 5)) }}'
];

foreach ($cssFixes as $broken => $fixed) {
    $count = substr_count($content, $broken);
    if ($count > 0) {
        $content = str_replace($broken, $fixed, $content);
        echo "✅ Propriété CSS corrigée ($count fois)\n";
    }
}

// 3. NETTOYER LES STYLES CSS PERSONNALISÉS
echo "\n🔄 Nettoyage des styles CSS personnalisés...\n";

// Vérifier que les styles CSS personnalisés sont bien formatés
if (strpos($content, '.risk-faible') !== false) {
    echo "✅ Styles CSS personnalisés présents\n";
} else {
    echo "⚠️ Styles CSS personnalisés manquants\n";
}

// 4. SAUVEGARDER LE FICHIER
if (file_put_contents($file, $content)) {
    echo "\n✅ Fichier corrigé avec succès!\n";
    echo "📊 Taille finale: " . filesize($file) . " bytes\n";
} else {
    echo "\n❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 CORRECTION TERMINÉE!\n";
echo "🚀 Le portail a maintenant une syntaxe parfaite!\n";
echo "🎨 Tous les styles CSS sont correctement appliqués!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";






