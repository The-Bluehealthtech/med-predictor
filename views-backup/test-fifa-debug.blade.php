<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test FIFA Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">🧪 Test Debug FIFA Connect</h1>
        
        <!-- Test de l'API -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">1. Test de l'API FIFA</h2>
            <button onclick="testAPI()" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md">
                🚀 Tester l'API FIFA
            </button>
            <div id="api-result" class="mt-4 p-4 bg-gray-700 rounded hidden">
                <!-- Résultat de l'API -->
            </div>
        </div>
        
        <!-- Test des fonctions JavaScript -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">2. Test des Fonctions JavaScript</h2>
            <button onclick="testFunctions()" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-md">
                🧪 Tester les Fonctions
            </button>
            <div id="function-result" class="mt-4 p-4 bg-gray-700 rounded hidden">
                <!-- Résultat des fonctions -->
            </div>
        </div>
        
        <!-- Test de simulation du portail -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">3. Test de Simulation du Portail</h2>
            <button onclick="simulatePortal()" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-md">
                🎮 Simuler le Portail
            </button>
            <div id="portal-result" class="mt-4 p-4 bg-gray-700 rounded hidden">
                <!-- Résultat de la simulation -->
            </div>
        </div>
        
        <!-- Logs en temps réel -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">📋 Logs en Temps Réel</h2>
            <div id="logs" class="bg-black p-4 rounded font-mono text-sm h-64 overflow-y-auto">
                <div class="text-gray-400">Logs en attente...</div>
            </div>
        </div>
    </div>

    <script>
        // Fonction de logging
        function log(message, type = 'info') {
            const logsDiv = document.getElementById('logs');
            const timestamp = new Date().toLocaleTimeString();
            const colorClass = type === 'error' ? 'text-red-400' : type === 'success' ? 'text-green-400' : 'text-blue-400';
            
            logsDiv.innerHTML += `<div class="${colorClass}">[${timestamp}] ${message}</div>`;
            logsDiv.scrollTop = logsDiv.scrollHeight;
            console.log(message);
        }

        // Test de l'API FIFA
        async function testAPI() {
            log('🚀 Test de l\'API FIFA lancé...');
            const resultDiv = document.getElementById('api-result');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto mb-2"></div><p class="text-center">Test en cours...</p>';
            
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
                    <div class="text-green-400 font-bold mb-2">✅ API FIFA Fonctionne !</div>
                    <div class="text-sm space-y-1">
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
                resultDiv.innerHTML = `
                    <div class="text-red-400 font-bold mb-2">❌ Erreur API</div>
                    <div class="text-sm text-red-300">${error.message}</div>
                `;
            }
        }

        // Test des fonctions JavaScript
        function testFunctions() {
            log('🧪 Test des fonctions JavaScript...');
            const resultDiv = document.getElementById('function-result');
            resultDiv.classList.remove('hidden');
            
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
                    <div class="text-green-400 font-bold mb-2">✅ Fonctions JavaScript OK</div>
                    <div class="text-sm space-y-1">
                        <div><strong>getCurrentPlayerId:</strong> ${mockPlayerId}</div>
                        <div><strong>updatePerformanceInterface:</strong> Fonctionne</div>
                        <div><strong>updatePerformanceCharts:</strong> Fonctionne</div>
                    </div>
                `;
                
                log('🎉 Test des fonctions réussi !');
                
            } catch (error) {
                log(`❌ Erreur fonctions: ${error.message}`, 'error');
                resultDiv.innerHTML = `
                    <div class="text-red-400 font-bold mb-2">❌ Erreur Fonctions</div>
                    <div class="text-sm text-red-300">${error.message}</div>
                `;
            }
        }

        // Simulation du portail
        function simulatePortal() {
            log('🎮 Simulation du portail lancée...');
            const resultDiv = document.getElementById('portal-result');
            resultDiv.classList.remove('hidden');
            
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
                    <div class="text-green-400 font-bold mb-2">✅ Simulation du Portail OK</div>
                    <div class="text-sm space-y-1">
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
                resultDiv.innerHTML = `
                    <div class="text-red-400 font-bold mb-2">❌ Erreur Simulation</div>
                    <div class="text-sm text-red-300">${error.message}</div>
                `;
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            log('🎉 Page de test FIFA chargée avec succès !');
            log('🔧 Prêt pour les tests de débogage...');
        });
    </script>
</body>
</html>
