<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test JavaScript Player Display</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <div class="container mx-auto p-4">
        <h1>Test Affichage Informations Joueur</h1>
        
        <!-- Test dropdown -->
        <div class="mb-4">
            <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                Patient/Joueur *
            </label>
            <select id="player_id" name="player_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <option value="">Sélectionner un patient/joueur</option>
                <option value="1">Test Player 1 (Test Club A)</option>
                <option value="2">Test Player 2 (Test Club B)</option>
                <option value="3">Test Player 3 (Test Club C)</option>
            </select>
        </div>

        <!-- Player Information Display -->
        <div id="player-info" class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
            <h4 class="text-sm font-semibold text-blue-900 mb-2">👤 Informations du Patient Sélectionné</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-blue-800">Nom complet:</span>
                    <span id="player-full-name" class="text-blue-900"></span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Date de naissance:</span>
                    <span id="player-birthdate" class="text-blue-900"></span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Club:</span>
                    <span id="player-club" class="text-blue-900"></span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Position:</span>
                    <span id="player-position" class="text-blue-900"></span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Âge:</span>
                    <span id="player-age" class="text-blue-900"></span>
                </div>
                <div>
                    <span class="font-medium text-blue-800">Nationalité:</span>
                    <span id="player-nationality" class="text-blue-900"></span>
                </div>
            </div>
        </div>

        <!-- Debug info -->
        <div id="debug-info" class="mt-4 p-4 bg-gray-100 rounded">
            <h3>Debug Info:</h3>
            <div id="debug-output"></div>
        </div>
    </div>

    <script>
        console.log('🔍 Test JavaScript Player Display - Initialisation...');
        
        // Gestion de l'affichage des informations du joueur
        const playerSelect = document.getElementById('player_id');
        const playerInfo = document.getElementById('player-info');
        const debugOutput = document.getElementById('debug-output');
        
        function logDebug(message) {
            console.log(message);
            debugOutput.innerHTML += '<div>' + message + '</div>';
        }
        
        logDebug('🔍 Initialisation de l\'affichage des informations du joueur...');
        logDebug('playerSelect: ' + (playerSelect ? 'Trouvé' : 'Non trouvé'));
        logDebug('playerInfo: ' + (playerInfo ? 'Trouvé' : 'Non trouvé'));
        
        if (playerSelect) {
            logDebug('✅ Élément player_id trouvé, ajout de l\'écouteur d\'événement...');
            playerSelect.addEventListener('change', function() {
                const selectedPlayerId = this.value;
                logDebug('🔄 Changement de joueur sélectionné: ' + selectedPlayerId);
                
                if (selectedPlayerId) {
                    logDebug('📡 Récupération des informations du joueur...');
                    
                    // Test avec des données simulées d'abord
                    const mockPlayer = {
                        id: selectedPlayerId,
                        full_name: 'Test Player ' + selectedPlayerId,
                        name: 'Test Player ' + selectedPlayerId,
                        date_of_birth: '1995-01-15T00:00:00.000000Z',
                        age: 30,
                        position: 'ST',
                        nationality: 'Testland',
                        club: { name: 'Test Club ' + (selectedPlayerId === '1' ? 'A' : selectedPlayerId === '2' ? 'B' : 'C') }
                    };
                    
                    logDebug('👤 Données du joueur simulées: ' + JSON.stringify(mockPlayer));
                    
                    const fullNameElement = document.getElementById('player-full-name');
                    const birthdateElement = document.getElementById('player-birthdate');
                    const clubElement = document.getElementById('player-club');
                    const positionElement = document.getElementById('player-position');
                    const ageElement = document.getElementById('player-age');
                    const nationalityElement = document.getElementById('player-nationality');
                    
                    logDebug('🔍 Éléments DOM: ' + 
                        'fullName=' + (fullNameElement ? 'Trouvé' : 'Non trouvé') + ', ' +
                        'birthdate=' + (birthdateElement ? 'Trouvé' : 'Non trouvé') + ', ' +
                        'club=' + (clubElement ? 'Trouvé' : 'Non trouvé') + ', ' +
                        'position=' + (positionElement ? 'Trouvé' : 'Non trouvé') + ', ' +
                        'age=' + (ageElement ? 'Trouvé' : 'Non trouvé') + ', ' +
                        'nationality=' + (nationalityElement ? 'Trouvé' : 'Non trouvé')
                    );
                    
                    if (fullNameElement) fullNameElement.textContent = mockPlayer.full_name || mockPlayer.name;
                    if (birthdateElement) birthdateElement.textContent = mockPlayer.date_of_birth ? new Date(mockPlayer.date_of_birth).toLocaleDateString('fr-FR') : 'N/A';
                    if (clubElement) clubElement.textContent = mockPlayer.club ? mockPlayer.club.name : 'N/A';
                    if (positionElement) positionElement.textContent = mockPlayer.position || 'N/A';
                    if (ageElement) ageElement.textContent = mockPlayer.age ? mockPlayer.age + ' ans' : 'N/A';
                    if (nationalityElement) nationalityElement.textContent = mockPlayer.nationality || 'N/A';
                    
                    if (playerInfo) {
                        playerInfo.style.display = 'block';
                        logDebug('✅ Informations du joueur affichées');
                    }
                    
                    // Maintenant testons l'API réelle
                    logDebug('🌐 Test de l\'API réelle...');
                    fetch(`/api/players/${selectedPlayerId}`)
                        .then(response => {
                            logDebug('📥 Réponse API reçue: ' + response.status);
                            return response.json();
                        })
                        .then(player => {
                            logDebug('👤 Données API réelles: ' + JSON.stringify(player));
                        })
                        .catch(error => {
                            logDebug('❌ Erreur API: ' + error.message);
                        });
                        
                } else {
                    logDebug('🚫 Aucun joueur sélectionné, masquage des informations');
                    if (playerInfo) playerInfo.style.display = 'none';
                }
            });
        } else {
            logDebug('❌ Élément player_id non trouvé dans le DOM');
        }
        
        logDebug('✅ Script JavaScript chargé et initialisé');
    </script>
</body>
</html>
