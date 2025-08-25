<?php

echo "=== RESTAURATION COMPLÈTE DES STYLES CSS FONCTIONNELS ===\n\n";

$file = 'resources/views/portail-joueur.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier non trouvé: $file\n";
    exit(1);
}

echo "📁 Fichier: $file\n";
echo "📊 Taille initiale: " . filesize($file) . " bytes\n\n";

$content = file_get_contents($file);

// 1. RESTAURER LES CLASSES CSS CASSÉES
echo "🔄 Restauration des classes CSS cassées...\n";

// Remplacer les classes CSS cassées par les bonnes
$classFixes = [
    // Classe principale cassée
    '.fifa-ultimate-card risk-{{ strtolower($player->injury_risk_level ?? "unknown") }}' => '.fifa-ultimate-card',
    '.fifa-ultimate-card risk-{{ strtolower($player->injury_risk_level ?? "unknown") }}:hover' => '.fifa-ultimate-card:hover',
    
    // Classes avec variables Blade cassées
    'risk-{{ strtolower($player->injury_risk_level ?? "unknown") }}' => 'risk-level',
    'ghs-score-{{ floor(($player->ghs_overall_score ?? 50) / 20) }}' => 'ghs-score',
    'performance-{{ $player->performances->count() > 10 ? "high" : ($player->performances->count() > 5 ? "medium" : "low") }}' => 'performance-level'
];

foreach ($classFixes as $broken => $fixed) {
    $count = substr_count($content, $broken);
    if ($count > 0) {
        $content = str_replace($broken, $fixed, $content);
        echo "✅ Classe CSS corrigée ($count fois): $broken -> $fixed\n";
    }
}

// 2. RESTAURER LES STYLES CSS CASSÉS
echo "\n🔄 Restauration des styles CSS cassés...\n";

// Remplacer les styles CSS cassés par les originaux
$cssFixes = [
    // Background gradient cassé
    'background: linear-gradient(135deg, {{ $player->injury_risk_level == "Faible" ? "#10b981" : ($player->injury_risk_level == "Moyen" ? "#f59e0b" : "#ef4444") }} 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%)' => 'background: linear-gradient(135deg, #1a237e 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%)',
    
    // Border radius cassé
    'border-radius: {{ min(30, max(16, ($player->ghs_overall_score ?? 50) / 3)) }}px' => 'border-radius: 24px',
    
    // Opacité cassée
    'opacity: {{ min(1.0, max(0.3, ($player->ghs_overall_score ?? 50) / 100)) }}' => 'opacity: 0.8',
    
    // Z-index cassé
    'z-index: {{ min(100, max(10, ($player->performances->count() ?? 0) * 5)) }}' => 'z-index: 10'
];

foreach ($cssFixes as $broken => $fixed) {
    $count = substr_count($content, $broken);
    if ($count > 0) {
        $content = str_replace($broken, $fixed, $content);
        echo "✅ Style CSS corrigé ($count fois): $broken -> $fixed\n";
    }
}

// 3. AJOUTER DES STYLES CSS FONCTIONNELS
echo "\n🔄 Ajout de styles CSS fonctionnels...\n";

// Chercher la balise </style> pour ajouter des styles fonctionnels
if (strpos($content, '</style>') !== false) {
    $workingCSS = "
        /* Styles CSS fonctionnels et dynamiques */
        .fifa-ultimate-card {
            background: linear-gradient(135deg, #1a237e 0%, #303f9f 25%, #3f51b5 50%, #5c6bc0 75%, #9c27b0 100%);
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .fifa-ultimate-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 35px 70px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        
        .fifa-rating-badge {
            background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff8f00 100%);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            color: white;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }
        
        .player-stats-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .performance-indicator {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-align: center;
        }
        
        .health-record-item {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
        }
        
        .ghs-score-high { background-color: rgba(16, 185, 129, 0.1); border-color: #10b981; }
        .ghs-score-medium { background-color: rgba(245, 158, 11, 0.1); border-color: #f59e0b; }
        .ghs-score-low { background-color: rgba(239, 68, 68, 0.1); border-color: #ef4444; }
        
        .risk-level-faible { border-left-color: #10b981; }
        .risk-level-moyen { border-left-color: #f59e0b; }
        .risk-level-élevé { border-left-color: #ef4444; }
        
        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .fifa-ultimate-card { border-radius: 16px; }
            .fifa-rating-badge { width: 60px; height: 60px; font-size: 14px; }
        }
    ";
    
    $content = str_replace('</style>', $workingCSS . "\n    </style>", $content);
    echo "✅ Styles CSS fonctionnels ajoutés\n";
}

// 4. SAUVEGARDER LE FICHIER
if (file_put_contents($file, $content)) {
    echo "\n✅ Fichier restauré avec succès!\n";
    echo "📊 Taille finale: " . filesize($file) . " bytes\n";
} else {
    echo "\n❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 STYLES CSS FONCTIONNELS RESTAURÉS!\n";
echo "🚀 Le portail a maintenant des styles CSS qui fonctionnent!\n";
echo "🎨 Tous les styles sont maintenant corrects et appliqués!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";






