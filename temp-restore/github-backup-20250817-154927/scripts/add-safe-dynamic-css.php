<?php

echo "=== AJOUT DE STYLES CSS DYNAMIQUES SÛRS ===\n\n";

$file = 'resources/views/portail-joueur.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier non trouvé: $file\n";
    exit(1);
}

echo "📁 Fichier: $file\n";
echo "📊 Taille initiale: " . filesize($file) . " bytes\n\n";

$content = file_get_contents($file);

// 1. AJOUTER DES STYLES CSS DYNAMIQUES SÛRS
echo "🔄 Ajout de styles CSS dynamiques sûrs...\n";

// Ajouter des styles CSS dynamiques simples et sûrs
$dynamicCSSAdditions = [
    // Score FIFA dynamique dans le titre
    'Portail Joueur - FIFA Ultimate Dashboard' => 'Portail Joueur - FIFA Ultimate Dashboard (Score: {{ $player->fifa_overall_rating ?? "N/A" }})',
    
    // Couleur dynamique basée sur le niveau de risque
    'background: linear-gradient(135deg, #1a237e 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%)' => 'background: linear-gradient(135deg, {{ $player->injury_risk_level == "Faible" ? "#10b981" : ($player->injury_risk_level == "Moyen" ? "#f59e0b" : "#ef4444") }} 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%)',
    
    // Opacité dynamique basée sur le score GHS
    'opacity: 0.8' => 'opacity: {{ min(1.0, max(0.3, ($player->ghs_overall_score ?? 50) / 100)) }}',
    
    // Border radius dynamique basé sur le score de performance
    'border-radius: 24px' => 'border-radius: {{ min(30, max(16, ($player->ghs_overall_score ?? 50) / 3)) }}px',
    
    // Z-index dynamique basé sur le nombre de performances
    'z-index: 10' => 'z-index: {{ min(100, max(10, ($player->performances->count() ?? 0) * 5)) }}',
];

foreach ($dynamicCSSAdditions as $original => $dynamic) {
    $count = substr_count($content, $original);
    if ($count > 0) {
        $content = str_replace($original, $dynamic, $content);
        echo "✅ Style CSS dynamique ajouté ($count fois): $original\n";
    }
}

// 2. AJOUTER DES CLASSES CSS DYNAMIQUES
echo "\n🔄 Ajout de classes CSS dynamiques...\n";

// Ajouter des classes CSS basées sur les données du joueur
$classAdditions = [
    // Classe de niveau de risque
    'fifa-ultimate-card' => 'fifa-ultimate-card risk-{{ strtolower($player->injury_risk_level ?? "unknown") }}',
    
    // Classe de score GHS
    'player-stats-card' => 'player-stats-card ghs-score-{{ floor(($player->ghs_overall_score ?? 50) / 20) }}',
    
    // Classe de performance
    'performance-indicator' => 'performance-indicator performance-{{ $player->performances->count() > 10 ? "high" : ($player->performances->count() > 5 ? "medium" : "low") }}',
];

foreach ($classAdditions as $original => $dynamic) {
    $count = substr_count($content, $original);
    if ($count > 0) {
        $content = str_replace($original, $dynamic, $content);
        echo "✅ Classe CSS dynamique ajoutée ($count fois): $original\n";
    }
}

// 3. AJOUTER DES STYLES CSS PERSONNALISÉS
echo "\n🔄 Ajout de styles CSS personnalisés...\n";

// Chercher la balise </style> pour ajouter des styles personnalisés
if (strpos($content, '</style>') !== false) {
    $customCSS = "
        /* Styles dynamiques basés sur les données du joueur */
        .risk-faible { border-left: 4px solid #10b981; }
        .risk-moyen { border-left: 4px solid #f59e0b; }
        .risk-élevé { border-left: 4px solid #ef4444; }
        
        .ghs-score-0 { background-color: rgba(239, 68, 68, 0.1); }
        .ghs-score-1 { background-color: rgba(245, 158, 11, 0.1); }
        .ghs-score-2 { background-color: rgba(34, 197, 94, 0.1); }
        .ghs-score-3 { background-color: rgba(16, 185, 129, 0.1); }
        .ghs-score-4 { background-color: rgba(5, 150, 105, 0.1); }
        
        .performance-high { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
        .performance-medium { box-shadow: 0 0 15px rgba(245, 158, 11, 0.3); }
        .performance-low { box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); }
        
        /* Animation basée sur le score GHS */
        .ghs-score-4 { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
    ";
    
    $content = str_replace('</style>', $customCSS . "\n    </style>", $content);
    echo "✅ Styles CSS personnalisés ajoutés\n";
}

// 4. SAUVEGARDER LE FICHIER
if (file_put_contents($file, $content)) {
    echo "\n✅ Fichier mis à jour avec succès!\n";
    echo "📊 Taille finale: " . filesize($file) . " bytes\n";
} else {
    echo "\n❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 STYLES CSS DYNAMIQUES AJOUTÉS AVEC SUCCÈS!\n";
echo "🚀 Le portail a maintenant des styles CSS dynamiques et sûrs!\n";
echo "🎨 Les styles s'adaptent aux données du joueur sans casser le design!\n";






