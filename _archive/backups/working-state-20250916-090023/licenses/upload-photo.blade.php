<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Photo Joueur - Système de Licences</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">📸 Upload Photo Joueur - Système de Licences</h1>
        
        <!-- Formulaire d'Upload -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">📤 Upload de Photo</h2>
            
            <form action="{{ route('license.upload.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Sélection du Club -->
                <div>
                    <label for="club_id" class="block text-sm font-medium text-gray-700 mb-2">🏟️ Club</label>
                    @if($preSelectedClub)
                        <div class="flex items-center space-x-2">
                            <select name="club_id" id="club_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100" disabled>
                                <option value="{{ $preSelectedClub->id }}" selected>{{ $preSelectedClub->name }}</option>
                            </select>
                            <span class="text-sm text-green-600">✅ Pré-sélectionné</span>
                        </div>
                        <input type="hidden" name="club_id" value="{{ $preSelectedClub->id }}">
                    @else
                        <select name="club_id" id="club_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionnez un club</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}">{{ $club->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                
                <!-- Sélection du Joueur -->
                <div>
                    <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">👤 Joueur</label>
                    @if($preSelectedPlayer)
                        <div class="flex items-center space-x-2">
                            <select name="player_id" id="player_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100" disabled>
                                <option value="{{ $preSelectedPlayer->id }}" selected>{{ $preSelectedPlayer->first_name }} {{ $preSelectedPlayer->last_name }}</option>
                            </select>
                            <span class="text-sm text-green-600">✅ Pré-sélectionné</span>
                        </div>
                        <input type="hidden" name="player_id" value="{{ $preSelectedPlayer->id }}">
                    @else
                        <select name="player_id" id="player_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionnez d\'abord un club</option>
                        </select>
                    @endif
                </div>
                
                <!-- Upload de Photo -->
                <div>
                    <label for="player_photo" class="block text-sm font-medium text-gray-700 mb-2">📷 Photo du Joueur</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="player_photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Télécharger un fichier</span>
                                    <input id="player_photo" name="player_photo" type="file" class="sr-only" accept="image/*" required>
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG jusqu'à 5MB</p>
                        </div>
                    </div>
                </div>
                
                <!-- Type de Licence -->
                <div>
                    <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">🏆 Type de Licence</label>
                    <select name="license_type" id="license_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionnez le type de licence</option>
                        <option value="amateur">Amateur</option>
                        <option value="semi_pro">Semi-Professionnel</option>
                        <option value="professional">Professionnel</option>
                        <option value="international">International</option>
                    </select>
                </div>
                
                <!-- Note: Utilise l'ancien système de licences existant -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <p class="text-sm text-blue-800">
                        ℹ️ Cette licence sera créée dans le système de licences existant de FIT.
                    </p>
                </div>
                
                <!-- Bouton Submit -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        📤 Uploader la Photo et Créer la Licence
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Aperçu des Photos Uploadées -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">🖼️ Photos Uploadées Récemment</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($recentPhotos as $photo)
                <div class="text-center">
                    <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                         alt="Photo {{ $photo->player->first_name }}" 
                         class="w-20 h-20 mx-auto mb-2 object-cover rounded-lg">
                    <p class="text-sm text-gray-600">{{ $photo->player->first_name }} {{ $photo->player->last_name }}</p>
                    <p class="text-xs text-gray-400">{{ $photo->club->name }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Dynamique de sélection club/joueur avec débogage
        document.addEventListener('DOMContentLoaded', function() {
            const clubSelect = document.getElementById('club_id');
            const playerSelect = document.getElementById('player_id');
            
            if (clubSelect && playerSelect) {
                console.log('✅ Éléments de sélection trouvés');
                
                // Vérifier si les sélecteurs sont pré-remplis
                const isPreSelected = clubSelect.disabled && playerSelect.disabled;
                
                if (isPreSelected) {
                    console.log('🎯 Formulaire pré-rempli détecté - pas de gestion dynamique nécessaire');
                    return;
                }
                
                // Configuration de l'événement change seulement si pas pré-rempli
                clubSelect.addEventListener('change', function() {
                    const clubId = this.value;
                    console.log('🏟️ Club sélectionné:', clubId);
                    
                    if (clubId) {
                        // Afficher un indicateur de chargement
                        playerSelect.innerHTML = '<option value="">Chargement des joueurs...</option>';
                        playerSelect.disabled = true;
                        
                        // Charger les joueurs du club sélectionné
                        const apiUrl = `/api/clubs/${clubId}/players`;
                        console.log('🌐 Appel API:', apiUrl);
                        
                        fetch(apiUrl)
                            .then(response => {
                                console.log('📡 Réponse API:', response.status, response.statusText);
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(players => {
                                console.log('👥 Joueurs reçus:', players);
                                playerSelect.innerHTML = '<option value="">Sélectionnez un joueur</option>';
                                
                                if (players && players.length > 0) {
                                    players.forEach(player => {
                                        const option = document.createElement('option');
                                        option.value = player.id;
                                        option.textContent = `${player.first_name} ${player.last_name}`;
                                        playerSelect.appendChild(option);
                                    });
                                    console.log(`✅ ${players.length} joueurs chargés`);
                                } else {
                                    playerSelect.innerHTML = '<option value="">Aucun joueur trouvé dans ce club</option>';
                                    console.log('⚠️ Aucun joueur trouvé');
                                }
                                
                                playerSelect.disabled = false;
                            })
                            .catch(error => {
                                console.error('❌ Erreur lors du chargement des joueurs:', error);
                                playerSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                                playerSelect.disabled = false;
                            });
                    } else {
                        playerSelect.innerHTML = '<option value="">Sélectionnez d\'abord un club</option>';
                        playerSelect.disabled = true;
                        console.log('🔄 Réinitialisation du sélecteur de joueur');
                    }
                });
                
                console.log('🎯 Événement change configuré pour le club');
            } else {
                console.error('❌ Éléments de sélection non trouvés');
            }
        });
    </script>
</body>
</html>


