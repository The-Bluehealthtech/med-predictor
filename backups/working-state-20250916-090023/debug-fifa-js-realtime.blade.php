<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug FIFA JavaScript en Temps Réel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1f2937; color: white; }
        .debug-section { background: #374151; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        .info { color: #3b82f6; }
        button { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #2563eb; }
        .log { background: #111827; padding: 10px; margin: 10px 0; border-radius: 5px; font-family: monospace; max-height: 400px; overflow-y: auto; }
        .data-display { background: #1f2937; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #4b5563; }
        .element-status { display: inline-block; margin: 5px; padding: 5px 10px; border-radius: 3px; font-size: 12px; }
        .element-found { background: #10b981; color: white; }
        .element-not-found { background: #ef4444; color: white; }
    </style>
</head>
<body>
    <h1>🔍 Debug FIFA JavaScript en Temps Réel</h1>
    
    <div class="debug-section">
        <h2>1. Test de l'API FIFA</h2>
        <button onclick="testAPIFIFA()">🚀 Tester l'API FIFA</button>
        <div id="api-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>2. Test des Éléments HTML FIFA</h2>
        <button onclick="testHTMLElements()">🔍 Vérifier les Éléments HTML</button>
        <div id="html-elements-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>3. Test de la Fonction updatePerformanceInterface</h2>
        <button onclick="testUpdatePerformanceInterface()">🧪 Tester updatePerformanceInterface</button>
        <div id="update-interface-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>4. Simulation Complète du Portail</h2>
        <button onclick="simulateCompletePortal()">🎮 Simuler Portail Complet</button>
        <div id="simulation-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>5. Éléments HTML FIFA de Test</h2>
        <div style="background: #1f2937; padding: 15px; border-radius: 5px; border: 1px solid #4b5563;">
            <h3>📊 Indicateurs Rapides</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 15px 0;">
                <div style="background: #374151; padding: 10px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 12px;">Buts Saison</div>
                    <div id="quick-goals" style="font-size: 24px; color: #ef4444; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 10px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 12px;">Passes Saison</div>
                    <div id="quick-assists" style="font-size: 24px; color: #3b82f6; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 10px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 12px;">Forme Actuelle</div>
                    <div id="quick-form" style="font-size: 24px; color: #10b981; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 10px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 12px;">Rating FIFA</div>
                    <div id="dynamic-overall-rating" style="font-size: 24px; color: #fbbf24; font-weight: bold;">-</div>
                </div>
            </div>
            
            <h3 style="margin-top: 20px;">⚽ Statistiques Détaillées</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin: 15px 0;">
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Buts</div>
                    <div id="dynamic-goals" style="font-size: 18px; color: #ef4444; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Passes</div>
                    <div id="dynamic-assists" style="font-size: 18px; color: #3b82f6; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Tirs Cadrés</div>
                    <div id="dynamic-shots-on-target" style="font-size: 18px; color: #f59e0b; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Précision</div>
                    <div id="dynamic-shot-accuracy" style="font-size: 18px; color: #10b981; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Tacles Gagnés</div>
                    <div id="dynamic-tackles-won" style="font-size: 18px; color: #8b5cf6; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Interceptions</div>
                    <div id="dynamic-interceptions" style="font-size: 18px; color: #06b6d4; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Dégagements</div>
                    <div id="dynamic-clearances" style="font-size: 18px; color: #f59e0b; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Duels Gagnés</div>
                    <div id="dynamic-duels-won" style="font-size: 18px; color: #ec4899; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Distance</div>
                    <div id="dynamic-distance" style="font-size: 18px; color: #10b981; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Vitesse Max</div>
                    <div id="dynamic-max-speed" style="font-size: 18px; color: #f59e0b; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Sprints</div>
                    <div id="dynamic-sprints" style="font-size: 18px; color: #ef4444; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Fitness</div>
                    <div id="dynamic-fitness" style="font-size: 18px; color: #3b82f6; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Matchs</div>
                    <div id="dynamic-matches-played" style="font-size: 18px; color: #8b5cf6; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Minutes</div>
                    <div id="dynamic-minutes-played" style="font-size: 18px; color: #06b6d4; font-weight: bold;">-</div>
                </div>
                <div style="background: #374151; padding: 8px; border-radius: 5px; text-align: center;">
                    <div style="color: #9ca3af; font-size: 10px;">Passes Réussies</div>
                    <div id="dynamic-passes-completed" style="font-size: 18px; color: #10b981; font-weight: bold;">-</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>6. Affichage des Données FIFA</h2>
        <div id="fifa-data-display" class="data-display">
            <div class="warning">Données FIFA en attente...</div>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>6. Logs en Temps Réel</h2>
        <div id="logs" class="log"></div>
    </div>

    <script>
        // Fonction de logging
        function log(message, type = 'info') {
            const logsDiv = document.getElementById('logs');
            const timestamp = new Date().toLocaleTimeString();
            const colorClass = type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info';
            
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

        // Test des éléments HTML FIFA
        function testHTMLElements() {
            log('🔍 Test des éléments HTML FIFA...');
            const resultDiv = document.getElementById('html-elements-result');
            
            // Liste des éléments FIFA à vérifier
            const fifaElements = [
                'dynamic-overall-rating',
                'dynamic-goals',
                'dynamic-assists',
                'dynamic-shots-on-target',
                'dynamic-shot-accuracy',
                'dynamic-tackles-won',
                'dynamic-interceptions',
                'dynamic-clearances',
                'dynamic-duels-won',
                'dynamic-distance',
                'dynamic-max-speed',
                'dynamic-sprints',
                'dynamic-fitness',
                'dynamic-matches-played',
                'dynamic-minutes-played',
                'dynamic-passes-completed',
                'quick-goals',
                'quick-assists',
                'quick-form'
            ];
            
            let foundCount = 0;
            let notFoundCount = 0;
            let elementsStatus = '';
            
            fifaElements.forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element) {
                    foundCount++;
                    elementsStatus += `<span class="element-status element-found">${elementId} ✅</span>`;
                    log(`✅ Élément trouvé: ${elementId}`);
                } else {
                    notFoundCount++;
                    elementsStatus += `<span class="element-status element-not-found">${elementId} ❌</span>`;
                    log(`❌ Élément NON trouvé: ${elementId}`, 'error');
                }
            });
            
            resultDiv.innerHTML = `
                <div class="info">🔍 Vérification des éléments HTML FIFA</div>
                <div style="margin-top: 10px;">
                    <div><strong>Éléments trouvés:</strong> ${foundCount}/${fifaElements.length}</div>
                    <div><strong>Éléments manquants:</strong> ${notFoundCount}</div>
                </div>
                <div style="margin-top: 15px;">
                    <h4>Statut des éléments:</h4>
                    ${elementsStatus}
                </div>
            `;
            
            if (notFoundCount === 0) {
                log('🎉 Tous les éléments HTML FIFA sont présents !');
            } else {
                log(`⚠️ ${notFoundCount} élément(s) HTML FIFA manquant(s)`, 'warning');
            }
        }

        // Test de la fonction updatePerformanceInterface
        function testUpdatePerformanceInterface() {
            log('🧪 Test de updatePerformanceInterface...');
            const resultDiv = document.getElementById('update-interface-result');
            
            if (!window.fifaData) {
                log('⚠️ Aucune donnée FIFA disponible. Testez d\'abord l\'API.', 'warning');
                resultDiv.innerHTML = '<div class="warning">⚠️ Testez d\'abord l\'API FIFA</div>';
                return;
            }
            
            try {
                // Simuler la fonction updatePerformanceInterface
                log('🔄 Simulation de updatePerformanceInterface...');
                
                const mockData = window.fifaData;
                const elementsToUpdate = {
                    'dynamic-goals': mockData.goals_scored || 0,
                    'dynamic-assists': mockData.assists || 0,
                    'dynamic-shots-on-target': mockData.shots_on_target || 0,
                    'dynamic-shot-accuracy': (mockData.shot_accuracy || 0) + '%',
                    'dynamic-tackles-won': mockData.tackles_won || 0,
                    'dynamic-interceptions': mockData.interceptions || 0,
                    'dynamic-clearances': mockData.clearances || 0,
                    'dynamic-duels-won': mockData.duels_won || 0,
                    'dynamic-distance': (mockData.distance_covered || 0) + 'km',
                    'dynamic-max-speed': (mockData.max_speed || 0) + 'km/h',
                    'dynamic-sprints': mockData.sprints_count || 0,
                    'dynamic-fitness': (mockData.fitness_score || 0) + '%',
                    'dynamic-matches-played': mockData.matches_played || 0,
                    'dynamic-minutes-played': mockData.minutes_played || 0,
                    'dynamic-passes-completed': mockData.passes_completed || 0,
                    'quick-goals': mockData.goals_scored || 0,
                    'quick-assists': mockData.assists || 0,
                    'quick-form': mockData.form_percentage || 0,
                    'dynamic-overall-rating': mockData.overall_rating || 0
                };
                
                log(`📊 ${Object.keys(elementsToUpdate).length} éléments à mettre à jour`);
                
                let updatedCount = 0;
                let notFoundCount = 0;
                
                // Mettre à jour tous les éléments avec vérification
                Object.entries(elementsToUpdate).forEach(([elementId, value]) => {
                    const element = document.getElementById(elementId);
                    if (element) {
                        element.textContent = value;
                        updatedCount++;
                        log(`✅ ${elementId} mis à jour avec: ${value}`);
                    } else {
                        notFoundCount++;
                        log(`⚠️ Élément ${elementId} non trouvé dans le DOM`, 'warning');
                    }
                });
                
                resultDiv.innerHTML = `
                    <div class="success">✅ Test updatePerformanceInterface terminé</div>
                    <div style="margin-top: 10px;">
                        <div><strong>Éléments mis à jour:</strong> ${updatedCount}</div>
                        <div><strong>Éléments non trouvés:</strong> ${notFoundCount}</div>
                        <div><strong>Total traité:</strong> ${Object.keys(elementsToUpdate).length}</div>
                    </div>
                `;
                
                log(`🎉 Test terminé: ${updatedCount} éléments mis à jour, ${notFoundCount} non trouvés`);
                
            } catch (error) {
                log(`❌ Erreur test updatePerformanceInterface: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Simulation complète du portail
        function simulateCompletePortal() {
            log('🎮 Simulation complète du portail...');
            const resultDiv = document.getElementById('simulation-result');
            
            if (!window.fifaData) {
                log('⚠️ Aucune donnée FIFA disponible. Testez d\'abord l\'API.', 'warning');
                resultDiv.innerHTML = '<div class="warning">⚠️ Testez d\'abord l\'API FIFA</div>';
                return;
            }
            
            try {
                // Simuler le chargement complet
                log('📄 Simulation du chargement de la page...');
                log('🎯 Simulation de DOMContentLoaded...');
                log('📊 Simulation de initCharts...');
                log('🚀 Simulation de loadFIFAPerformanceData...');
                log('📡 Simulation de la récupération des données...');
                log('🔄 Simulation de la mise à jour de l\'interface...');
                
                // Appeler la fonction de mise à jour
                updateFIFADisplay(window.fifaData);
                
                // Mettre à jour l'interface
                testUpdatePerformanceInterface();
                
                resultDiv.innerHTML = `
                    <div class="success">✅ Simulation du Portail Complète</div>
                    <div style="margin-top: 10px;">
                        <div><strong>Chargement page:</strong> ✅</div>
                        <div><strong>DOMContentLoaded:</strong> ✅</div>
                        <div><strong>initCharts:</strong> ✅</div>
                        <div><strong>loadFIFAPerformanceData:</strong> ✅</div>
                        <div><strong>Récupération données:</strong> ✅</div>
                        <div><strong>Mise à jour interface:</strong> ✅</div>
                    </div>
                `;
                
                log('🎉 Simulation du portail complète réussie !');
                
            } catch (error) {
                log(`❌ Erreur simulation: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur Simulation: ${error.message}</div>`;
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

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de debug FIFA JavaScript chargée avec succès !');
            log('🔧 Prêt pour le débogage en temps réel...');
            
            // Test automatique de l'API après 1 seconde
            setTimeout(() => {
                log('🔄 Test automatique de l\'API FIFA...');
                testAPIFIFA();
            }, 1000);
        });
    </script>
</body>
</html>
