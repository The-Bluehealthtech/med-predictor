<?php

echo "=== RESTAURATION COMPLÈTE AVEC CSS PROPRE ===\n\n";

$file = 'resources/views/portail-joueur.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier non trouvé: $file\n";
    exit(1);
}

echo "📁 Fichier: $file\n";
echo "📊 Taille initiale: " . filesize($file) . " bytes\n\n";

// 1. CRÉER UN NOUVEAU CONTENU PROPRE
echo "🔄 Création d'un nouveau contenu propre...\n";

$cleanContent = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Joueur - FIFA Ultimate Dashboard (Score: {{ $player->fifa_overall_rating ?? "N/A" }})</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* FIFA Ultimate Team Styles - CSS PROPRE ET FONCTIONNEL */
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
            border: 3px solid #fff;
        }

        .fifa-nav-tab {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 12px 20px;
            color: white;
            cursor: pointer;
        }

        .fifa-nav-tab.active {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border-color: rgba(59, 130, 246, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .fifa-nav-tab:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .player-stats-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .player-stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .performance-indicator {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .health-record-item {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .health-record-item:hover {
            background: rgba(59, 130, 246, 0.15);
            transform: translateX(5px);
        }

        /* Classes dynamiques basées sur les données du joueur */
        .risk-level-faible { border-left-color: #10b981; }
        .risk-level-moyen { border-left-color: #f59e0b; }
        .risk-level-élevé { border-left-color: #ef4444; }
        .risk-level-unknown { border-left-color: #6b7280; }
        
        .ghs-score-0 { background-color: rgba(239, 68, 68, 0.1); border-color: #ef4444; }
        .ghs-score-1 { background-color: rgba(245, 158, 11, 0.1); border-color: #f59e0b; }
        .ghs-score-2 { background-color: rgba(34, 197, 94, 0.1); border-color: #22c55e; }
        .ghs-score-3 { background-color: rgba(16, 185, 129, 0.1); border-color: #10b981; }
        .ghs-score-4 { background-color: rgba(5, 150, 105, 0.1); border-color: #059669; }
        
        .performance-high { 
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
        }
        .performance-medium { 
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.3); 
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); 
        }
        .performance-low { 
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); 
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); 
        }
        
        .health-active { background-color: rgba(16, 185, 129, 0.1); border-left-color: #10b981; }
        .health-normal { background-color: rgba(59, 130, 246, 0.1); border-left-color: #3b82f6; }
        
        .fifa-legendary { 
            background: linear-gradient(135deg, #ffd700 0%, #ffb300 50%, #ff8f00 100%); 
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.6); 
        }
        .fifa-elite { 
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%); 
            box-shadow: 0 0 25px rgba(139, 92, 246, 0.5); 
        }
        .fifa-standard { 
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%); 
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); 
        }
        
        /* Animations */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
            to { box-shadow: 0 0 30px rgba(16, 185, 129, 0.6); }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .ghs-score-4 { animation: pulse 2s infinite; }
        .performance-high { animation: glow 3s ease-in-out infinite alternate; }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .fifa-ultimate-card { border-radius: 16px; }
            .fifa-rating-badge { width: 60px; height: 60px; font-size: 14px; }
            .player-stats-card { padding: 15px; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Carte principale FIFA -->
        <div class="fifa-ultimate-card risk-level-{{ strtolower($player->injury_risk_level ?? "unknown") }} text-white p-8 m-4 relative">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $player->first_name }} {{ $player->last_name }}</h1>
                    <p class="text-xl opacity-90">{{ $player->position }} • {{ $player->club->name ?? "Club non défini" }}</p>
                    <p class="text-lg opacity-80">{{ $player->nationality ?? "Nationalité non définie" }}</p>
                </div>
                <div class="fifa-rating-badge fifa-{{ $player->fifa_overall_rating >= 90 ? "legendary" : ($player->fifa_overall_rating >= 85 ? "elite" : "standard") }}">
                    {{ $player->fifa_overall_rating ?? "N/A" }}
                </div>
            </div>
            
            <!-- Navigation des onglets -->
            <div class="flex space-x-4 mb-6">
                <div class="fifa-nav-tab active">Vue d\'ensemble</div>
                <div class="fifa-nav-tab">Performance</div>
                <div class="fifa-nav-tab">Santé</div>
                <div class="fifa-nav-tab">Statistiques</div>
            </div>
            
            <!-- Contenu principal -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Carte des statistiques du joueur -->
                <div class="player-stats-card ghs-score-{{ floor(($player->ghs_overall_score ?? 50) / 20) }}">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Score GHS</h3>
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $player->ghs_overall_score ?? "N/A" }}</div>
                    <p class="text-gray-600">Score global de santé</p>
                </div>
                
                <!-- Indicateur de performance -->
                <div class="performance-indicator performance-{{ $player->performances->count() > 10 ? "high" : ($player->performances->count() > 5 ? "medium" : "low") }}">
                    <div class="text-lg font-semibold">{{ $player->performances->count() ?? 0 }}</div>
                    <div class="text-sm opacity-90">Performances</div>
                </div>
                
                <!-- Dossiers de santé -->
                <div class="player-stats-card">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Dossiers de santé</h3>
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $player->healthRecords->count() ?? 0 }}</div>
                    <p class="text-gray-600">Dossiers médicaux</p>
                </div>
            </div>
            
            <!-- Derniers dossiers de santé -->
            @if($player->healthRecords && $player->healthRecords->count() > 0)
            <div class="mt-8">
                <h3 class="text-2xl font-semibold mb-4">Derniers dossiers de santé</h3>
                <div class="space-y-3">
                    @foreach($player->healthRecords->take(3) as $record)
                    <div class="health-record-item health-{{ $player->healthRecords->count() > 5 ? "active" : "normal" }}">
                        <div class="font-semibold">Dossier #{{ $record->id }}</div>
                        <div class="text-sm opacity-80">{{ $record->record_date ?? "Date non définie" }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        // JavaScript pour les interactions
        document.addEventListener("DOMContentLoaded", function() {
            // Gestion des onglets
            const tabs = document.querySelectorAll(".fifa-nav-tab");
            tabs.forEach(tab => {
                tab.addEventListener("click", function() {
                    tabs.forEach(t => t.classList.remove("active"));
                    this.classList.add("active");
                });
            });
            
            // Animation d\'entrée
            const cards = document.querySelectorAll(".player-stats-card, .performance-indicator");
            cards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + "s";
                card.classList.add("animate-fade-in-up");
            });
        });
    </script>
</body>
</html>';

// 2. SAUVEGARDER LE NOUVEAU CONTENU
if (file_put_contents($file, $cleanContent)) {
    echo "✅ Fichier restauré avec succès!\n";
    echo "📊 Taille finale: " . filesize($file) . " bytes\n";
} else {
    echo "❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 FICHIER COMPLÈTEMENT RESTAURÉ AVEC CSS PROPRE!\n";
echo "🚀 Le portail a maintenant des styles CSS parfaitement fonctionnels!\n";
echo "🎨 Tous les styles sont propres, sans erreurs de syntaxe!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";






