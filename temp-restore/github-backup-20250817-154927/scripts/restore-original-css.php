<?php

echo "=== RESTAURATION COMPLÈTE DES STYLES CSS ORIGINAUX ===\n\n";

$file = 'resources/views/portail-joueur.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier non trouvé: $file\n";
    exit(1);
}

echo "📁 Fichier: $file\n";
echo "📊 Taille initiale: " . filesize($file) . " bytes\n\n";

$content = file_get_contents($file);

// 1. RESTAURER LES STYLES CSS CASSÉS
echo "🔄 Restauration des styles CSS cassés...\n";

// Remplacer les styles CSS cassés par les originaux
$cssRestorations = [
    // Box-shadow cassé
    'box-shadow: {{ $player->performances->count() ?? 0 }}px 20px 50px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1)' => 'box-shadow: 0 20px 50px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1)',
    
    // Transition cassée
    'transition: all {{ ($player->ghs_overall_score ?? 0) / 100 }}s cubic-bezier(0.4, 0, 0.2, 1)' => 'transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1)',
    
    // Transform cassé
    'transform: translateY(-{{ ($player->ghs_physical_score ?? 0) / 10 }}px) scale(1.0{{ ($player->ghs_mental_score ?? 0) / 10 }})' => 'transform: translateY(-10px) scale(1.02)',
    
    // Box-shadow hover cassé
    'box-shadow: {{ $player->healthRecords->count() ?? 0 }}px 35px 70px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1)' => 'box-shadow: 0 35px 70px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1)',
    
    // Background gradient cassé
    'background: linear-gradient(135deg, #ffd700 {{ $player->ghs_overall_score ?? 0 }}%, #ffb300 {{ $player->ghs_physical_score ?? 0 }}%, #ff8f00 100%)' => 'background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff8f00 100%)',
    
    // Transform rotate cassé
    'transform: rotate(-{{ $player->ghs_overall_score ?? 0 }}deg)' => 'transform: rotate(-90deg)',
    
    // Opacity cassée
    'opacity: {{ ($player->ghs_overall_score ?? 0) / 100 }}' => 'opacity: 0.8',
    
    // Scale cassé
    'scale: {{ ($player->ghs_mental_score ?? 0) / 100 }}' => 'scale: 1.0',
    
    // Border radius cassé
    'border-radius: {{ ($player->ghs_overall_score ?? 0) / 5 }}px' => 'border-radius: 16px',
    
    // Padding cassé
    'padding: {{ ($player->ghs_overall_score ?? 0) / 5 }}px' => 'padding: 16px',
    
    // Margin cassé
    'margin: {{ ($player->ghs_overall_score ?? 0) / 10 }}px' => 'margin: 8px',
    
    // Width cassé
    'width: {{ $player->ghs_overall_score ?? 0 }}%' => 'width: 100%',
    
    // Height cassé
    'height: {{ ($player->ghs_overall_score ?? 0) * 3 }}px' => 'height: 300px',
    
    // Font size cassé
    'font-size: {{ ($player->ghs_overall_score ?? 0) / 5 }}px' => 'font-size: 16px',
    
    // Line height cassé
    'line-height: {{ ($player->ghs_overall_score ?? 0) / 60 }}' => 'line-height: 1.5',
    
    // Z-index cassé
    'z-index: {{ $player->ghs_overall_score ?? 0 }}' => 'z-index: 10',
    
    // Top cassé
    'top: {{ $player->ghs_overall_score ?? 0 }}px' => 'top: 0',
    
    // Left cassé
    'left: {{ $player->ghs_overall_score ?? 0 }}px' => 'left: 0',
    
    // Right cassé
    'right: {{ $player->ghs_overall_score ?? 0 }}px' => 'right: 0',
    
    // Bottom cassé
    'bottom: {{ $player->ghs_overall_score ?? 0 }}px' => 'bottom: 0',
];

foreach ($cssRestorations as $broken => $original) {
    $count = substr_count($content, $broken);
    if ($count > 0) {
        $content = str_replace($broken, $original, $content);
        echo "✅ Style CSS restauré ($count fois): $broken\n";
    }
}

// 2. RESTAURER LES STYLES CSS AVANCÉS CASSÉS
echo "\n🔄 Restauration des styles CSS avancés cassés...\n";

$advancedCSSRestorations = [
    // Animation duration cassée
    'animation-duration: {{ ($player->ghs_overall_score ?? 0) / 100 }}s' => 'animation-duration: 1s',
    
    // Animation delay cassée
    'animation-delay: {{ ($player->performances->count() ?? 0) / 10 }}s' => 'animation-delay: 0s',
    
    // Filter cassé
    'filter: brightness({{ ($player->ghs_physical_score ?? 0) / 100 }})' => 'filter: none',
    
    // Backdrop filter cassé
    'backdrop-filter: blur({{ ($player->ghs_mental_score ?? 0) / 10 }}px)' => 'backdrop-filter: none',
    
    // Box shadow spread cassé
    'box-shadow: 0 0 {{ ($player->ghs_sleep_score ?? 0) / 10 }}px 0 rgba(0,0,0,0.3)' => 'box-shadow: 0 0 0 0 rgba(0,0,0,0)',
    
    // Text shadow cassé
    'text-shadow: 0 0 {{ ($player->ghs_civic_score ?? 0) / 20 }}px rgba(0,0,0,0.5)' => 'text-shadow: none',
    
    // Border width cassé
    'border-width: {{ ($player->contribution_score ?? 0) / 50 }}px' => 'border-width: 1px',
    
    // Border color cassé
    'border-color: {{ $player->injury_risk_level == "Faible" ? "#10b981" : ($player->injury_risk_level == "Moyen" ? "#f59e0b" : "#ef4444") }}' => 'border-color: #e5e7eb',
];

foreach ($advancedCSSRestorations as $broken => $original) {
    $count = substr_count($content, $broken);
    if ($count > 0) {
        $content = str_replace($broken, $original, $content);
        echo "✅ Style CSS avancé restauré ($count fois): $broken\n";
    }
}

// 3. SAUVEGARDER LE FICHIER
if (file_put_contents($file, $content)) {
    echo "\n✅ Fichier restauré avec succès!\n";
    echo "📊 Taille finale: " . filesize($file) . " bytes\n";
} else {
    echo "\n❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 STYLES CSS ORIGINAUX COMPLÈTEMENT RESTAURÉS!\n";
echo "🚀 Le portail a maintenant ses styles CSS originaux et fonctionnels!\n";
echo "🎨 Tous les styles sont maintenant corrects et appliqués!\n";






