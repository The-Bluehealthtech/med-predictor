<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug FIFA Affichage Visuel</title>
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
        .element-value { background: #374151; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #4b5563; }
        .fifa-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 15px 0; }
        .stat-card { background: #374151; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #4b5563; }
        .stat-value { font-size: 32px; font-weight: bold; margin: 10px 0; }
        .stat-label { color: #9ca3af; font-size: 14px; }
    </style>
</head>
<body>
    <h1>🔍 Debug FIFA Affichage Visuel</h1>
    
    <div class="debug-section">
        <h2>1. Test de l'API FIFA</h2>
        <button onclick="testAPIFIFA()">🚀 Tester l'API FIFA</button>
        <div id="api-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>2. Vérification Visuelle des Éléments FIFA</h2>
        <button onclick="checkVisualElements()">👁️ Vérifier Affichage Visuel</button>
        <div id="visual-check-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>3. Test de Mise à Jour Visuelle</h2>
        <button onclick="testVisualUpdate()">🎨 Tester Mise à Jour Visuelle</button>
        <div id="visual-update-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>4. Affichage FIFA Visuel</h2>
        <div id="fifa-visual-display" class="data-display">
            <div class="warning">Données FIFA en attente...</div>
        </div>
    </div>
    
    <div class="debug-section">
        <h2>5. Logs en Temps Réel</h2>
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

        // Vérification visuelle des éléments FIFA
        function checkVisualElements() {
            log('👁️ Vérification visuelle des éléments FIFA...');
            const resultDiv = document.getElementById('visual-check-result');
            
            if (!window.fifaData) {
                log('⚠️ Aucune donnée FIFA disponible. Testez d\'abord l\'API.', 'warning');
                resultDiv.innerHTML = '<div class="warning">⚠️ Testez d\'abord l\'API FIFA</div>';
                return;
            }
            
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
            let elementsValues = '';
            
            fifaElements.forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element) {
                    foundCount++;
                    const currentValue = element.textContent;
                    const expectedValue = getExpectedValue(elementId, window.fifaData);
                    
                    elementsStatus += `<span class="element-status element-found">${elementId} ✅</span>`;
                    elementsValues += `
                        <div class="element-value">
                            <strong>${elementId}:</strong>
                            <div>Valeur actuelle: <span style="color: #f59e0b;">"${currentValue}"</span></div>
                            <div>Valeur attendue: <span style="color: #10b981;">"${expectedValue}"</span></div>
                            <div>Statut: <span style="color: ${currentValue === expectedValue ? '#10b981' : '#ef4444'}">${currentValue === expectedValue ? '✅ CORRECT' : '❌ INCORRECT'}</span></div>
                        </div>
                    `;
                    
                    log(`✅ ${elementId}: "${currentValue}" (attendu: "${expectedValue}")`);
                } else {
                    notFoundCount++;
                    elementsStatus += `<span class="element-status element-not-found">${elementId} ❌</span>`;
                    log(`❌ Élément NON trouvé: ${elementId}`, 'error');
                }
            });
            
            resultDiv.innerHTML = `
                <div class="info">🔍 Vérification Visuelle des Éléments FIFA</div>
                <div style="margin-top: 10px;">
                    <div><strong>Éléments trouvés:</strong> ${foundCount}/${fifaElements.length}</div>
                    <div><strong>Éléments manquants:</strong> ${notFoundCount}</div>
                </div>
                <div style="margin-top: 15px;">
                    <h4>Statut des éléments:</h4>
                    ${elementsStatus}
                </div>
                <div style="margin-top: 15px;">
                    <h4>Valeurs des éléments:</h4>
                    ${elementsValues}
                </div>
            `;
            
            if (notFoundCount === 0) {
                log('🎉 Tous les éléments HTML FIFA sont présents !');
            } else {
                log(`⚠️ ${notFoundCount} élément(s) HTML FIFA manquant(s)`, 'warning');
            }
        }

        // Obtenir la valeur attendue pour un élément
        function getExpectedValue(elementId, fifaData) {
            const valueMap = {
                'dynamic-overall-rating': fifaData.overall_rating || 0,
                'dynamic-goals': fifaData.goals_scored || 0,
                'dynamic-assists': fifaData.assists || 0,
                'dynamic-shots-on-target': fifaData.shots_on_target || 0,
                'dynamic-shot-accuracy': (fifaData.shot_accuracy || 0) + '%',
                'dynamic-tackles-won': fifaData.tackles_won || 0,
                'dynamic-interceptions': fifaData.interceptions || 0,
                'dynamic-clearances': fifaData.clearances || 0,
                'dynamic-duels-won': fifaData.duels_won || 0,
                'dynamic-distance': (fifaData.distance_covered || 0) + 'km',
                'dynamic-max-speed': (fifaData.max_speed || 0) + 'km/h',
                'dynamic-sprints': fifaData.sprints_count || 0,
                'dynamic-fitness': (fifaData.fitness_score || 0) + '%',
                'dynamic-matches-played': fifaData.matches_played || 0,
                'dynamic-minutes-played': fifaData.minutes_played || 0,
                'dynamic-passes-completed': fifaData.passes_completed || 0,
                'quick-goals': fifaData.goals_scored || 0,
                'quick-assists': fifaData.assists || 0,
                'quick-form': fifaData.form_percentage || 0
            };
            
            return valueMap[elementId] || 'N/A';
        }

        // Test de mise à jour visuelle
        function testVisualUpdate() {
            log('🎨 Test de mise à jour visuelle...');
            const resultDiv = document.getElementById('visual-update-result');
            
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
                
                // Mettre à jour l'affichage visuel
                updateFIFAVisualDisplay(window.fifaData);
                
            } catch (error) {
                log(`❌ Erreur test updatePerformanceInterface: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Fonction de mise à jour de l'affichage visuel FIFA
        function updateFIFAVisualDisplay(fifaData) {
            log('🔄 Mise à jour de l\'affichage visuel FIFA...');
            const displayDiv = document.getElementById('fifa-visual-display');
            
            try {
                displayDiv.innerHTML = `
                    <div class="success">🎯 Données FIFA Affichées Visuellement !</div>
                    <div class="fifa-stats">
                        <div class="stat-card">
                            <div class="stat-label">Rating FIFA</div>
                            <div class="stat-value" style="color: #fbbf24;">${fifaData.overall_rating}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Buts Saison</div>
                            <div class="stat-value" style="color: #ef4444;">${fifaData.goals_scored}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Passes Saison</div>
                            <div class="stat-value" style="color: #3b82f6;">${fifaData.assists}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Forme Actuelle</div>
                            <div class="stat-value" style="color: #10b981;">${fifaData.form_percentage}%</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Matchs Joués</div>
                            <div class="stat-value" style="color: #8b5cf6;">${fifaData.matches_played}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Distance Parcourue</div>
                            <div class="stat-value" style="color: #10b981;">${fifaData.distance_covered}km</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Vitesse Max</div>
                            <div class="stat-value" style="color: #f59e0b;">${fifaData.max_speed}km/h</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Fitness</div>
                            <div class="stat-value" style="color: #3b82f6;">${fifaData.fitness_score}%</div>
                        </div>
                    </div>
                `;
                
                log('✅ Affichage visuel FIFA mis à jour avec succès !');
                
            } catch (error) {
                log(`❌ Erreur mise à jour affichage visuel: ${error.message}`, 'error');
                displayDiv.innerHTML = `<div class="error">❌ Erreur mise à jour: ${error.message}</div>`;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de debug FIFA visuel chargée avec succès !');
            log('🔧 Prêt pour le débogage visuel FIFA...');
            
            // Test automatique de l'API après 1 seconde
            setTimeout(() => {
                log('🔄 Test automatique de l\'API FIFA...');
                testAPIFIFA();
            }, 1000);
        });
    </script>
</body>
</html>
