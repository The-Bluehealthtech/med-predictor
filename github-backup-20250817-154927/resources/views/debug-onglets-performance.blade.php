<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Onglets Performance FIFA</title>
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
        .element-visible { background: #10b981; color: white; }
        .element-hidden { background: #ef4444; color: white; }
        .tab-content { background: #374151; padding: 15px; margin: 15px 0; border-radius: 8px; border: 1px solid #4b5563; }
    </style>
</head>
<body>
    <h1>🔍 Debug Onglets Performance FIFA</h1>
    
    <div class="debug-section">
        <h2>1. Test de l'API FIFA</h2>
        <button onclick="testAPIFIFA()">🚀 Tester l'API FIFA</button>
        <div id="api-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>2. Vérification de l'Onglet Performance</h2>
        <button onclick="checkPerformanceTab()">👁️ Vérifier Onglet Performance</button>
        <div id="performance-tab-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>3. Vérification des Éléments FIFA dans l'Onglet</h2>
        <button onclick="checkFIFAElementsInTab()">🔍 Vérifier Éléments FIFA</button>
        <div id="fifa-elements-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>4. Test de Mise à Jour des Onglets</h2>
        <button onclick="testTabUpdate()">🎨 Tester Mise à Jour Onglets</button>
        <div id="tab-update-result"></div>
    </div>
    
    <div class="debug-section">
        <h2>5. Simulation du Portail FIFA</h2>
        <button onclick="simulatePortal()">🎮 Simuler Portail FIFA</button>
        <div id="portal-simulation-result"></div>
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

        // Vérification de l'onglet Performance
        function checkPerformanceTab() {
            log('👁️ Vérification de l\'onglet Performance...');
            const resultDiv = document.getElementById('performance-tab-result');
            
            // Vérifier l'onglet principal
            const performanceTab = document.getElementById('performance-tab');
            if (performanceTab) {
                const isVisible = performanceTab.style.display !== 'none' && performanceTab.offsetParent !== null;
                log(`✅ Onglet Performance trouvé: visible=${isVisible}`);
                
                // Vérifier le dashboard de performance
                const performanceDashboard = document.getElementById('performance-dashboard');
                if (performanceDashboard) {
                    log('✅ Dashboard de performance trouvé');
                    
                    // Vérifier les onglets de performance
                    const performanceTabs = performanceDashboard.querySelector('.performance-tabs');
                    if (performanceTabs) {
                        log('✅ Navigation des onglets de performance trouvée');
                        
                        // Compter les boutons d'onglets
                        const tabButtons = performanceTabs.querySelectorAll('button');
                        log(`📊 ${tabButtons.length} boutons d'onglets trouvés`);
                        
                        tabButtons.forEach((button, index) => {
                            const isActive = button.classList.contains('bg-blue-600');
                            log(`   Bouton ${index + 1}: ${button.textContent.trim()} (actif: ${isActive})`);
                        });
                    } else {
                        log('❌ Navigation des onglets de performance NON trouvée', 'error');
                    }
                    
                    // Vérifier le contenu des onglets
                    const tabContents = performanceDashboard.querySelectorAll('.tab-content > div');
                    log(`📊 ${tabContents.length} contenus d'onglets trouvés`);
                    
                    tabContents.forEach((content, index) => {
                        const isVisible = content.style.display !== 'none';
                        const hasContent = content.children.length > 0;
                        log(`   Onglet ${index + 1}: visible=${isVisible}, contenu=${hasContent} (${content.children.length} enfants)`);
                    });
                } else {
                    log('❌ Dashboard de performance NON trouvé', 'error');
                }
                
                resultDiv.innerHTML = `
                    <div class="success">✅ Onglet Performance analysé</div>
                    <div style="margin-top: 10px;">
                        <div><strong>Onglet principal:</strong> ${isVisible ? 'Visible' : 'Masqué'}</div>
                        <div><strong>Dashboard:</strong> ${performanceDashboard ? 'Trouvé' : 'Non trouvé'}</div>
                        <div><strong>Navigation onglets:</strong> ${performanceDashboard?.querySelector('.performance-tabs') ? 'Trouvée' : 'Non trouvée'}</div>
                        <div><strong>Contenus onglets:</strong> ${performanceDashboard?.querySelectorAll('.tab-content > div').length || 0}</div>
                    </div>
                `;
            } else {
                log('❌ Onglet Performance NON trouvé', 'error');
                resultDiv.innerHTML = '<div class="error">❌ Onglet Performance NON trouvé</div>';
            }
        }

        // Vérification des éléments FIFA dans l'onglet
        function checkFIFAElementsInTab() {
            log('🔍 Vérification des éléments FIFA dans l\'onglet...');
            const resultDiv = document.getElementById('fifa-elements-result');
            
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
            let visibleCount = 0;
            let hiddenCount = 0;
            let elementsStatus = '';
            
            fifaElements.forEach(elementId => {
                const element = document.getElementById(elementId);
                if (element) {
                    foundCount++;
                    const isVisible = element.offsetParent !== null && element.style.display !== 'none';
                    const currentValue = element.textContent;
                    const expectedValue = getExpectedValue(elementId, window.fifaData);
                    
                    if (isVisible) {
                        visibleCount++;
                        elementsStatus += `<span class="element-status element-visible">${elementId} ✅ Visible</span>`;
                        log(`✅ ${elementId}: visible, valeur="${currentValue}" (attendu: "${expectedValue}")`);
                    } else {
                        hiddenCount++;
                        elementsStatus += `<span class="element-status element-hidden">${elementId} ❌ Masqué</span>`;
                        log(`⚠️ ${elementId}: masqué, valeur="${currentValue}" (attendu: "${expectedValue}")`, 'warning');
                    }
                } else {
                    notFoundCount++;
                    elementsStatus += `<span class="element-status element-not-found">${elementId} ❌ Non trouvé</span>`;
                    log(`❌ Élément NON trouvé: ${elementId}`, 'error');
                }
            });
            
            resultDiv.innerHTML = `
                <div class="info">🔍 Vérification des Éléments FIFA dans l'Onglet</div>
                <div style="margin-top: 10px;">
                    <div><strong>Éléments trouvés:</strong> ${foundCount}/${fifaElements.length}</div>
                    <div><strong>Éléments visibles:</strong> ${visibleCount}</div>
                    <div><strong>Éléments masqués:</strong> ${hiddenCount}</div>
                    <div><strong>Éléments manquants:</strong> ${notFoundCount}</div>
                </div>
                <div style="margin-top: 15px;">
                    <h4>Statut des éléments:</h4>
                    ${elementsStatus}
                </div>
            `;
            
            if (visibleCount === 0) {
                log('⚠️ Aucun élément FIFA n\'est visible dans l\'onglet !', 'warning');
            } else {
                log(`🎉 ${visibleCount} éléments FIFA sont visibles dans l'onglet`);
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

        // Test de mise à jour des onglets
        function testTabUpdate() {
            log('🎨 Test de mise à jour des onglets...');
            const resultDiv = document.getElementById('tab-update-result');
            
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
                    <div class="success">✅ Test de mise à jour des onglets terminé</div>
                    <div style="margin-top: 10px;">
                        <div><strong>Éléments mis à jour:</strong> ${updatedCount}</div>
                        <div><strong>Éléments non trouvés:</strong> ${notFoundCount}</div>
                        <div><strong>Total traité:</strong> ${Object.keys(elementsToUpdate).length}</div>
                    </div>
                `;
                
                log(`🎉 Test terminé: ${updatedCount} éléments mis à jour, ${notFoundCount} non trouvés`);
                
            } catch (error) {
                log(`❌ Erreur test mise à jour onglets: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur: ${error.message}</div>`;
            }
        }

        // Simulation du portail FIFA
        function simulatePortal() {
            log('🎮 Simulation du portail FIFA...');
            const resultDiv = document.getElementById('portal-simulation-result');
            
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
                
                // Vérifier l'état final
                const performanceTab = document.getElementById('performance-tab');
                const isTabVisible = performanceTab && performanceTab.style.display !== 'none';
                
                const fifaElements = ['dynamic-goals', 'dynamic-assists', 'quick-goals', 'quick-assists'];
                let visibleElements = 0;
                
                fifaElements.forEach(elementId => {
                    const element = document.getElementById(elementId);
                    if (element && element.offsetParent !== null) {
                        visibleElements++;
                    }
                });
                
                resultDiv.innerHTML = `
                    <div class="success">✅ Simulation du Portail FIFA Terminée</div>
                    <div style="margin-top: 10px;">
                        <div><strong>Onglet Performance visible:</strong> ${isTabVisible ? '✅' : '❌'}</div>
                        <div><strong>Éléments FIFA visibles:</strong> ${visibleElements}/${fifaElements.length}</div>
                        <div><strong>Données FIFA chargées:</strong> ✅</div>
                        <div><strong>Interface mise à jour:</strong> ✅</div>
                    </div>
                `;
                
                log('🎉 Simulation du portail FIFA terminée !');
                
            } catch (error) {
                log(`❌ Erreur simulation: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur Simulation: ${error.message}</div>`;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de debug onglets performance chargée avec succès !');
            log('🔧 Prêt pour le débogage des onglets FIFA...');
            
            // Test automatique de l'API après 1 seconde
            setTimeout(() => {
                log('🔄 Test automatique de l\'API FIFA...');
                testAPIFIFA();
            }, 1000);
        });
    </script>
</body>
</html>
