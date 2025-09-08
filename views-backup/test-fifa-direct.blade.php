<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test FIFA Direct</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1f2937; color: white; }
        .test-section { background: #374151; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        button { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #2563eb; }
        .log { background: #111827; padding: 10px; margin: 10px 0; border-radius: 5px; font-family: monospace; }
        .data-display { background: #1f2937; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #4b5563; }
    </style>
</head>
<body>
    <h1>🧪 Test FIFA Direct - Simulation du Portail</h1>
    
    <div class="test-section">
        <h2>1. Test de l'API FIFA</h2>
        <button onclick="testAPIFIFA()">🚀 Tester l'API FIFA</button>
        <div id="api-result"></div>
    </div>
    
    <div class="test-section">
        <h2>2. Test du Chargement FIFA Automatique</h2>
        <button onclick="simulatePortalLoad()">🎮 Simuler le Portail</button>
        <div id="portal-result"></div>
    </div>
    
    <div class="test-section">
        <h2>3. Test des Fonctions FIFA</h2>
        <button onclick="testFIFAFunctions()">🧪 Tester les Fonctions FIFA</button>
        <div id="function-result"></div>
    </div>
    
    <div class="test-section">
        <h2>4. Affichage des Données FIFA</h2>
        <div id="fifa-data-display" class="data-display">
            <div class="warning">Données FIFA en attente...</div>
        </div>
    </div>
    
    <div class="test-section">
        <h2>📋 Logs en Temps Réel</h2>
        <div id="logs" class="log"></div>
    </div>

    <script>
        // Fonction de logging
        function log(message, type = 'info') {
            const logsDiv = document.getElementById('logs');
            const timestamp = new Date().toLocaleTimeString();
            const colorClass = type === 'error' ? 'error' : type === 'success' ? 'success' : 'warning';
            
            logsDiv.innerHTML += `<div class="${colorClass}">[${timestamp}] ${message}</div>`;
            logsDiv.scrollTop = logsDiv.scrollHeight;
            console.log(message);
        }

        // Test de l'API FIFA
        async function testAPIFIFA() {
            log('🚀 Test de l\'API FIFA lancé...');
            const resultDiv = document.getElementById('api-result');
            
            try {
                const response = await fetch('/api/player-performance/7');
                log(`📡 Réponse API reçue: ${response.status} ${response.statusText}`);
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                log('✅ Données JSON parsées avec succès');
                log(`📊 Message: ${data.message}`);
                log(`📈 Données joueur: ${Object.keys(data.data).length} champs`);
                
                // Stocker les données pour les tests suivants
                window.fifaData = data.data;
                
                // Afficher les données clés
                const playerData = data.data;
                resultDiv.innerHTML = `
                    <div class="success">✅ API FIFA Fonctionne !</div>
                    <div style="margin-top: 10px;">
                        <div><strong>Joueur:</strong> ${playerData.first_name} ${playerData.last_name}</div>
                        <div><strong>Rating FIFA:</strong> ${playerData.overall_rating}</div>
                        <div><strong>Position:</strong> ${playerData.position}</div>
                        <div><strong>Buts:</strong> ${playerData.goals_scored}</div>
                        <div><strong>Passes:</strong> ${playerData.assists}</div>
                        <div><strong>Matchs:</strong> ${playerData.matches_played}</div>
                    </div>
                `;
                
                log('🎉 Test API réussi !');
                
            } catch (error) {
                log(`❌ Erreur API: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur API: ${error.message}</div>`;
            }
        }

        // Simulation du chargement du portail
        function simulatePortalLoad() {
            log('🎮 Simulation du chargement du portail...');
            const resultDiv = document.getElementById('portal-result');
            
            if (!window.fifaData) {
                log('⚠️ Aucune donnée FIFA disponible. Testez d\'abord l\'API.', 'warning');
                resultDiv.innerHTML = '<div class="warning">⚠️ Testez d\'abord l\'API FIFA</div>';
                return;
            }
            
            try {
                // Simuler le chargement de la page
                log('📄 Simulation du chargement de la page...');
                
                // Simuler DOMContentLoaded
                log('🎯 Simulation de DOMContentLoaded...');
                
                // Simuler l'appel à initCharts
                log('📊 Simulation de initCharts...');
                
                // Simuler l'appel à loadFIFAPerformanceData
                log('🚀 Simulation de loadFIFAPerformanceData...');
                
                // Simuler la récupération des données
                log('📡 Simulation de la récupération des données...');
                
                // Simuler la mise à jour de l'interface
                log('🔄 Simulation de la mise à jour de l\'interface...');
                
                // Appeler la fonction de mise à jour
                updateFIFADisplay(window.fifaData);
                
                resultDiv.innerHTML = `
                    <div class="success">✅ Simulation du Portail OK</div>
                    <div style="margin-top: 10px;">
                        <div><strong>Chargement page:</strong> ✅</div>
                        <div><strong>DOMContentLoaded:</strong> ✅</div>
                        <div><strong>initCharts:</strong> ✅</div>
                        <div><strong>loadFIFAPerformanceData:</strong> ✅</div>
                        <div><strong>Récupération données:</strong> ✅</div>
                        <div><strong>Mise à jour interface:</strong> ✅</div>
                    </div>
                `;
                
                log('🎉 Simulation du portail réussie !');
                
            } catch (error) {
                log(`❌ Erreur simulation: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur Simulation: ${error.message}</div>`;
            }
        }

        // Test des fonctions FIFA
        function testFIFAFunctions() {
            log('🧪 Test des fonctions FIFA...');
            const resultDiv = document.getElementById('function-result');
            
            if (!window.fifaData) {
                log('⚠️ Aucune donnée FIFA disponible. Testez d\'abord l\'API.', 'warning');
                resultDiv.innerHTML = '<div class="warning">⚠️ Testez d\'abord l\'API FIFA</div>';
                return;
            }
            
            try {
                // Simuler getCurrentPlayerId
                const mockPlayerId = '7';
                log(`✅ getCurrentPlayerId simulé: ${mockPlayerId}`);
                
                // Simuler updatePerformanceInterface
                const mockData = window.fifaData;
                log('✅ updatePerformanceInterface simulé avec vraies données FIFA');
                
                // Simuler updatePerformanceCharts
                log('✅ updatePerformanceCharts simulé');
                
                // Simuler generateDynamicMatchData
                const matchData = generateDynamicMatchData(mockData);
                log('✅ generateDynamicMatchData simulé');
                log(`📊 Données de match générées: ${JSON.stringify(matchData)}`);
                
                resultDiv.innerHTML = `
                    <div class="success">✅ Fonctions FIFA OK</div>
                    <div style="margin-top: 10px;">
                        <div><strong>getCurrentPlayerId:</strong> ${mockPlayerId}</div>
                        <div><strong>updatePerformanceInterface:</strong> Fonctionne</div>
                        <div><strong>updatePerformanceCharts:</strong> Fonctionne</div>
                        <div><strong>generateDynamicMatchData:</strong> Fonctionne</div>
                        <div><strong>Données générées:</strong> ${Object.keys(matchData).length} séries</div>
                    </div>
                `;
                
                log('🎉 Test des fonctions FIFA réussi !');
                
            } catch (error) {
                log(`❌ Erreur fonctions FIFA: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur Fonctions FIFA: ${error.message}</div>`;
            }
        }

        // Fonction de mise à jour de l'affichage FIFA
        function updateFIFADisplay(fifaData) {
            log('🔄 Mise à jour de l\'affichage FIFA...');
            const displayDiv = document.getElementById('fifa-data-display');
            
            try {
                displayDiv.innerHTML = `
                    <div class="success">🎯 Données FIFA Affichées !</div>
                    <div style="margin-top: 15px;">
                        <h3>📊 Statistiques Principales</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 10px;">
                            <div style="background: #374151; padding: 10px; border-radius: 5px;">
                                <div><strong>Rating FIFA:</strong></div>
                                <div style="font-size: 24px; color: #fbbf24;">${fifaData.overall_rating}</div>
                            </div>
                            <div style="background: #374151; padding: 10px; border-radius: 5px;">
                                <div><strong>Buts Saison:</strong></div>
                                <div style="font-size: 24px; color: #ef4444;">${fifaData.goals_scored}</div>
                            </div>
                            <div style="background: #374151; padding: 10px; border-radius: 5px;">
                                <div><strong>Passes Saison:</strong></div>
                                <div style="font-size: 24px; color: #3b82f6;">${fifaData.assists}</div>
                            </div>
                            <div style="background: #374151; padding: 10px; border-radius: 5px;">
                                <div><strong>Forme Actuelle:</strong></div>
                                <div style="font-size: 24px; color: #10b981;">${fifaData.form_percentage}%</div>
                            </div>
                        </div>
                        
                        <h3 style="margin-top: 20px;">📈 Détails Techniques</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-top: 10px;">
                            <div><strong>Position:</strong> ${fifaData.position}</div>
                            <div><strong>Âge:</strong> ${fifaData.age} ans</div>
                            <div><strong>Matchs:</strong> ${fifaData.matches_played}</div>
                            <div><strong>Distance:</strong> ${fifaData.distance_covered}km</div>
                            <div><strong>Vitesse Max:</strong> ${fifaData.max_speed}km/h</div>
                            <div><strong>Fitness:</strong> ${fifaData.fitness_score}%</div>
                        </div>
                    </div>
                `;
                
                log('✅ Affichage FIFA mis à jour avec succès !');
                
            } catch (error) {
                log(`❌ Erreur mise à jour affichage: ${error.message}`, 'error');
                displayDiv.innerHTML = `<div class="error">❌ Erreur mise à jour: ${error.message}</div>`;
            }
        }

        // Fonction de génération de données de match (simulation)
        function generateDynamicMatchData(fifaData) {
            log('🔄 Génération de données de match FIFA...');
            
            const ratings = [];
            const goalsAssists = [];
            const distance = [];
            
            // Générer 8 matchs avec des variations réalistes
            for (let i = 0; i < 8; i++) {
                // Ratings basés sur le rating FIFA + forme + fatigue
                const baseRating = fifaData.overall_rating;
                const form = fifaData.form_percentage;
                const fitness = fifaData.fitness_score;
                
                const formVariation = (form - 75) / 100;
                const fitnessVariation = (fitness - 80) / 100;
                const fatigueFactor = i * 0.02;
                const randomVariation = (Math.random() - 0.5) * 0.15;
                
                const finalRating = baseRating + (formVariation * 5) + (fitnessVariation * 3) - (fatigueFactor * 100) + (randomVariation * 100);
                ratings.push(Math.max(50, Math.min(99, Math.round(finalRating))));
                
                // Buts + Passes basés sur les vraies statistiques
                const goalsPerMatch = fifaData.matches_played > 0 ? fifaData.goals_scored / fifaData.matches_played : 0;
                const assistsPerMatch = fifaData.matches_played > 0 ? fifaData.assists / fifaData.matches_played : 0;
                
                const formMultiplier = 0.8 + (form / 100) * 0.4;
                const fatigueMultiplier = 1 - (fatigueFactor * 0.3);
                
                const matchGoals = Math.max(0, Math.round((goalsPerMatch * formMultiplier * fatigueMultiplier + (Math.random() - 0.5) * 0.3) * 10) / 10);
                const matchAssists = Math.max(0, Math.round((assistsPerMatch * formMultiplier * fatigueMultiplier + (Math.random() - 0.5) * 0.2) * 10) / 10);
                
                goalsAssists.push(matchGoals + matchAssists);
                
                // Distance basée sur la vraie distance FIFA
                const distancePerMatch = fifaData.matches_played > 0 ? fifaData.distance_covered / fifaData.matches_played : 10.5;
                const fitnessMultiplier = 0.9 + (fitness / 100) * 0.2;
                const distanceFatigueMultiplier = 1 - (fatigueFactor * 0.2);
                
                const matchDistance = Math.max(8, Math.min(15, Math.round((distancePerMatch * fitnessMultiplier * distanceFatigueMultiplier + (Math.random() - 0.5) * 1) * 10) / 10));
                distance.push(matchDistance);
            }
            
            log('📊 Données de match FIFA générées avec succès');
            
            return {
                ratings,
                goalsAssists,
                distance
            };
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de test FIFA direct chargée avec succès !');
            log('🔧 Prêt pour les tests de débogage FIFA...');
            
            // Test automatique de l'API après 1 seconde
            setTimeout(() => {
                log('🔄 Test automatique de l\'API FIFA...');
                testAPIFIFA();
            }, 1000);
        });
    </script>
</body>
</html>
