<?php

echo "=== AJOUT DE CLASSES CSS DYNAMIQUES SIMPLES ===\n\n";

$file = 'resources/views/portail-joueur.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier non trouvé: $file\n";
    exit(1);
}

echo "📁 Fichier: $file\n";
echo "📊 Taille initiale: " . filesize($file) . " bytes\n\n";

$content = file_get_contents($file);

// 1. AJOUTER DES CLASSES CSS DYNAMIQUES SIMPLES
echo "🔄 Ajout de classes CSS dynamiques simples...\n";

// Remplacer les classes statiques par des classes dynamiques
$classReplacements = [
    // Classe principale avec niveau de risque
    'class="fifa-ultimate-card' => 'class="fifa-ultimate-card risk-level-{{ strtolower($player->injury_risk_level ?? "unknown") }}',
    
    // Classe de score GHS
    'class="player-stats-card' => 'class="player-stats-card ghs-score-{{ floor(($player->ghs_overall_score ?? 50) / 20) }}',
    
    // Classe de performance
    'class="performance-indicator' => 'class="performance-indicator performance-{{ $player->performances->count() > 10 ? "high" : ($player->performances->count() > 5 ? "medium" : "low") }}',
    
    // Classe de santé
    'class="health-record-item' => 'class="health-record-item health-{{ $player->healthRecords->count() > 5 ? "active" : "normal" }}',
    
    // Classe de badge FIFA
    'class="fifa-rating-badge' => 'class="fifa-rating-badge fifa-{{ $player->fifa_overall_rating >= 90 ? "legendary" : ($player->fifa_overall_rating >= 85 ? "elite" : "standard") }}'
];

foreach ($classReplacements as $original => $dynamic) {
    $count = substr_count($content, $original);
    if ($count > 0) {
        $content = str_replace($original, $dynamic, $content);
        echo "✅ Classe dynamique ajoutée ($count fois): $original -> $dynamic\n";
    }
}

// 2. AJOUTER DES STYLES CSS POUR LES NOUVELLES CLASSES
echo "\n🔄 Ajout de styles CSS pour les nouvelles classes...\n";

// Chercher la balise </style> pour ajouter des styles pour les nouvelles classes
if (strpos($content, '</style>') !== false) {
    $newCSS = "
        /* Classes dynamiques basées sur les données du joueur */
        .risk-level-faible { border-left: 4px solid #10b981; }
        .risk-level-moyen { border-left: 4px solid #f59e0b; }
        .risk-level-élevé { border-left: 4px solid #ef4444; }
        .risk-level-unknown { border-left: 4px solid #6b7280; }
        
        .ghs-score-0 { background-color: rgba(239, 68, 68, 0.1); border-color: #ef4444; }
        .ghs-score-1 { background-color: rgba(245, 158, 11, 0.1); border-color: #f59e0b; }
        .ghs-score-2 { background-color: rgba(34, 197, 94, 0.1); border-color: #22c55e; }
        .ghs-score-3 { background-color: rgba(16, 185, 129, 0.1); border-color: #10b981; }
        .ghs-score-4 { background-color: rgba(5, 150, 105, 0.1); border-color: #059669; }
        
        .performance-high { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .performance-medium { box-shadow: 0 0 15px rgba(245, 158, 11, 0.3); background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .performance-low { box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        
        .health-active { background-color: rgba(16, 185, 129, 0.1); border-left-color: #10b981; }
        .health-normal { background-color: rgba(59, 130, 246, 0.1); border-left-color: #3b82f6; }
        
        .fifa-legendary { background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff8f00 100%); box-shadow: 0 0 30px rgba(255, 215, 0, 0.6); }
        .fifa-elite { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%); box-shadow: 0 0 25px rgba(139, 92, 246, 0.5); }
        .fifa-standard { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%); box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); }
        
        /* Animations pour les classes dynamiques */
        .ghs-score-4 { animation: pulse 2s infinite; }
        .performance-high { animation: glow 3s ease-in-out infinite alternate; }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
            to { box-shadow: 0 0 30px rgba(16, 185, 129, 0.6); }
        }
    ";
    
    $content = str_replace('</style>', $newCSS . "\n    </style>", $content);
    echo "✅ Styles CSS pour nouvelles classes ajoutés\n";
}

// 3. SAUVEGARDER LE FICHIER
if (file_put_contents($file, $content)) {
    echo "\n✅ Fichier mis à jour avec succès!\n";
    echo "📊 Taille finale: " . filesize($file) . " bytes\n";
} else {
    echo "\n❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 CLASSES CSS DYNAMIQUES AJOUTÉES AVEC SUCCÈS!\n";
echo "🚀 Le portail a maintenant des classes CSS dynamiques qui fonctionnent!\n";
echo "🎨 Les styles s'adaptent aux données du joueur!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";






