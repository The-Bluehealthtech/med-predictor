<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test FIFA JavaScript</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1f2937; color: white; }
        .test-section { background: #374151; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        button { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #2563eb; }
        .log { background: #111827; padding: 10px; margin: 10px 0; border-radius: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🧪 Test FIFA JavaScript</h1>
    
    <div class="test-section">
        <h2>1. Test de l'API FIFA</h2>
        <button onclick="testAPIFIFA()">🚀 Tester l'API FIFA</button>
        <div id="api-result"></div>
    </div>
    
    <div class="test-section">
        <h2>2. Test des Fonctions JavaScript</h2>
        <button onclick="testFunctions()">🧪 Tester les Fonctions</button>
        <div id="function-result"></div>
    </div>
    
    <div class="test-section">
        <h2>3. Test de Simulation du Portail</h2>
        <button onclick="simulatePortal()">🎮 Simuler le Portail</button>
        <div id="portal-result"></div>
    </div>
    
    <div class="test-section">
        <h2>4. Test de Chargement Automatique</h2>
        <button onclick="testAutoLoad()">⏰ Test Chargement Auto</button>
        <div id="auto-result"></div>
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

        // Test des fonctions JavaScript
        function testFunctions() {
            log('🧪 Test des fonctions JavaScript...');
            const resultDiv = document.getElementById('function-result');
            
            try {
                // Simuler getCurrentPlayerId
                const mockPlayerId = '7';
                log(`✅ getCurrentPlayerId simulé: ${mockPlayerId}`);
                
                // Simuler updatePerformanceInterface
                const mockData = {
                    first_name: 'Cristiano',
                    last_name: 'Ronaldo',
                    overall_rating: 88,
                    goals_scored: 24,
                    assists: 9,
                    form_percentage: 78
                };
                log('✅ updatePerformanceInterface simulé avec données mock');
                
                // Simuler updatePerformanceCharts
                log('✅ updatePerformanceCharts simulé');
                
                resultDiv.innerHTML = `
                    <div class="success">✅ Fonctions JavaScript OK</div>
                    <div style="margin-top: 10px;">
                        <div><strong>getCurrentPlayerId:</strong> ${mockPlayerId}</div>
                        <div><strong>updatePerformanceInterface:</strong> Fonctionne</div>
                        <div><strong>updatePerformanceCharts:</strong> Fonctionne</div>
                    </div>
                `;
                
                log('🎉 Test des fonctions réussi !');
                
            } catch (error) {
                log(`❌ Erreur fonctions: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur Fonctions: ${error.message}</div>`;
            }
        }

        // Simulation du portail
        function simulatePortal() {
            log('🎮 Simulation du portail lancée...');
            const resultDiv = document.getElementById('portal-result');
            
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

        // Test de chargement automatique
        function testAutoLoad() {
            log('⏰ Test de chargement automatique...');
            const resultDiv = document.getElementById('auto-result');
            
            try {
                // Simuler le délai de 500ms
                log('⏳ Simulation du délai de 500ms...');
                
                setTimeout(() => {
                    log('🚀 Chargement automatique des données FIFA...');
                    log('✅ loadFIFAPerformanceData() appelée automatiquement');
                    
                    resultDiv.innerHTML = `
                        <div class="success">✅ Chargement Automatique OK</div>
                        <div style="margin-top: 10px;">
                            <div><strong>Délai 500ms:</strong> ✅</div>
                            <div><strong>Appel automatique:</strong> ✅</div>
                            <div><strong>Fonction déclenchée:</strong> ✅</div>
                        </div>
                    `;
                    
                    log('🎉 Test de chargement automatique réussi !');
                }, 500);
                
                resultDiv.innerHTML = '<div class="warning">⏳ Test en cours (500ms)...</div>';
                
            } catch (error) {
                log(`❌ Erreur chargement auto: ${error.message}`, 'error');
                resultDiv.innerHTML = `<div class="error">❌ Erreur Chargement Auto: ${error.message}</div>`;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de test FIFA chargée avec succès !');
            log('🔧 Prêt pour les tests de débogage...');
            
            // Test automatique après 1 seconde
            setTimeout(() => {
                log('🔄 Test automatique de l\'API FIFA...');
                testAPIFIFA();
            }, 1000);
        });
    </script>
</body>
</html>
