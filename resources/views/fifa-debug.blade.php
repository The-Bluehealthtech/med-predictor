<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA Portal Debug - {{ $player->first_name }} {{ $player->last_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🔍 FIFA Portal Debug - Données Exactes</h1>
        
        <!-- Informations du joueur -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">👤 Informations du Joueur</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <strong>ID:</strong> {{ $player->id }}<br>
                    <strong>Nom:</strong> {{ $player->first_name }} {{ $player->last_name }}<br>
                    <strong>Position:</strong> {{ $player->position ?? 'N/A' }}<br>
                    <strong>Nationalité:</strong> {{ $player->nationality ?? 'N/A' }}
                </div>
                <div>
                    <strong>Photo:</strong> {{ $player->player_picture ?? 'NULL' }}<br>
                    <strong>Photo URL:</strong> {{ $player->player_face_url ?? 'NULL' }}<br>
                    <strong>Profile Image:</strong> {{ $player->profile_image ?? 'NULL' }}
                </div>
            </div>
        </div>

        <!-- Informations du club -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">🏟️ Informations du Club</h2>
            @if($player->club)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong>Nom:</strong> {{ $player->club->name }}<br>
                        <strong>ID:</strong> {{ $player->club->id }}<br>
                        <strong>Logo Path:</strong> {{ $player->club->logo_path ?? 'NULL' }}<br>
                        <strong>Logo:</strong> {{ $player->club->logo ?? 'NULL' }}<br>
                        <strong>Logo URL:</strong> {{ $player->club->logo_url ?? 'NULL' }}
                    </div>
                    <div>
                        <strong>Ville:</strong> {{ $player->club->city ?? 'N/A' }}<br>
                        <strong>Pays:</strong> {{ $player->club->country ?? 'N/A' }}
                    </div>
                </div>
                
                <!-- Test d'affichage du logo du club -->
                <div class="mt-4 p-4 bg-gray-50 rounded">
                    <h3 class="font-semibold mb-2">🖼️ Test d'affichage du logo du club :</h3>
                    @if($player->club->logo_path)
                        <img src="{{ asset('storage/' . $player->club->logo_path) }}" 
                             alt="Logo {{ $player->club->name }}" 
                             class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->club->logo_path }}</p>
                    @elseif($player->club->logo)
                        <img src="{{ asset('storage/' . $player->club->logo) }}" 
                             alt="Logo {{ $player->club->name }}" 
                             class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->club->logo }}</p>
                    @elseif($player->club->logo_url)
                        <img src="{{ $player->club->logo_url }}" 
                             alt="Logo {{ $player->club->name }}" 
                             class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->club->logo_url }}</p>
                    @else
                        <div class="h-24 w-24 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center mx-auto">
                            <span class="text-white font-bold text-2xl">{{ substr($player->club->name, 0, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-center">Pas de logo</p>
                    @endif
                </div>
            @else
                <p class="text-red-500">❌ Aucun club associé à ce joueur</p>
            @endif
        </div>

        <!-- Informations de l'association -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">🏛️ Informations de l'Association</h2>
            @if($player->association)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong>Nom:</strong> {{ $player->association->name }}<br>
                        <strong>ID:</strong> {{ $player->association->id }}<br>
                        <strong>Association Logo URL:</strong> {{ $player->association->association_logo_url ?? 'NULL' }}<br>
                        <strong>Logo:</strong> {{ $player->association->logo ?? 'NULL' }}<br>
                        <strong>Logo Path:</strong> {{ $player->association->logo_path ?? 'NULL' }}
                    </div>
                    <div>
                        <strong>Nation Flag URL:</strong> {{ $player->association->nation_flag_url ?? 'NULL' }}<br>
                        <strong>Flag URL:</strong> {{ $player->association->flag_url ?? 'NULL' }}<br>
                        <strong>Pays:</strong> {{ $player->association->country ?? 'N/A' }}
                    </div>
                </div>
                
                <!-- Test d'affichage du logo de l'association -->
                <div class="mt-4 p-4 bg-gray-50 rounded">
                    <h3 class="font-semibold mb-2">🖼️ Test d'affichage du logo de l'association :</h3>
                    @if($player->association->association_logo_url)
                        <img src="{{ asset('storage/' . $player->association->association_logo_url) }}" 
                             alt="Logo {{ $player->association->name }}" 
                             class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->association->association_logo_url }}</p>
                    @elseif($player->association->logo)
                        <img src="{{ asset('storage/' . $player->association->logo) }}" 
                             alt="Logo {{ $player->association->name }}" 
                             class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->association->logo }}</p>
                    @elseif($player->association->logo_path)
                        <img src="{{ asset('storage/' . $player->association->logo_path) }}" 
                             alt="Logo {{ $player->association->name }}" 
                             class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->association->logo_path }}</p>
                    @else
                        <div class="h-24 w-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mx-auto">
                            <span class="text-white font-bold text-2xl">{{ substr($player->association->name, 0, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-center">Pas de logo</p>
                    @endif
                </div>
                
                <!-- Test d'affichage du drapeau -->
                <div class="mt-4 p-4 bg-gray-50 rounded">
                    <h3 class="font-semibold mb-2">🏁 Test d'affichage du drapeau :</h3>
                    @if($player->association->nation_flag_url)
                        <img src="{{ asset('storage/' . $player->association->nation_flag_url) }}" 
                             alt="Drapeau {{ $player->nationality }}" 
                             class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->association->nation_flag_url }}</p>
                    @elseif($player->association->flag_url)
                        <img src="{{ asset('storage/' . $player->association->flag_url) }}" 
                             alt="Drapeau {{ $player->nationality }}" 
                             class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->association->flag_url }}</p>
                    @else
                        <div class="h-24 w-24 bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center mx-auto">
                            <span class="text-white font-bold text-2xl">{{ substr($player->nationality ?? 'TN', 0, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-center">Pas de drapeau</p>
                    @endif
                </div>
            @else
                <p class="text-red-500">❌ Aucune association associée à ce joueur</p>
            @endif
        </div>

        <!-- Test d'affichage de la photo du joueur -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">📸 Test d'affichage de la photo du joueur</h2>
            @if($player->player_picture)
                <img src="{{ asset('storage/' . $player->player_picture) }}" 
                     alt="Photo de {{ $player->first_name }} {{ $player->last_name }}" 
                     class="h-48 w-48 object-cover rounded-lg border-2 border-gray-200 mx-auto">
                <p class="text-xs text-gray-500 mt-1 text-center">{{ $player->player_picture }}</p>
            @else
                <div class="h-48 w-48 bg-gradient-to-br from-gray-400 to-gray-600 rounded-lg flex items-center justify-center mx-auto">
                    <span class="text-white font-bold text-4xl">{{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-1 text-center">Pas de photo</p>
            @endif
        </div>

        <!-- Liens de test -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">🔗 Liens de test</h2>
            <div class="flex gap-4">
                <a href="/fifa-portal?player_id={{ $player->id }}" 
                   class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    🏟️ FIFA Portal (avec corrections)
                </a>
                <a href="/test-fifa-same-method/{{ $player->id }}" 
                   class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    ✅ Page de test qui fonctionne
                </a>
                <a href="/associations-view/show/1" 
                   class="px-6 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                    🏛️ Associations View (référence)
                </a>
            </div>
        </div>
    </div>
</body>
</html>


