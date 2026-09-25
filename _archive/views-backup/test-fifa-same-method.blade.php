<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test FIFA - Même méthode que /associations-view/show/1</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="/" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour à l'accueil
                </a>
                <h1 class="text-3xl font-bold text-gray-800">🧪 Test FIFA - Même méthode que /associations-view/show/1</h1>
            </div>
            <div class="flex space-x-3">
                <a href="/fifa-portal?player_id={{ $player->id }}" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    🎮 FIFA Portal Original
                </a>
                <a href="/associations-view/show/{{ $player->association->id ?? 1 }}" class="px-6 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                    🏛️ Association View
                </a>
            </div>
        </div>

        <!-- Informations du test -->
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">
                        <strong>Test :</strong> Cette page utilise EXACTEMENT la même méthode que <code>/associations-view/show/1</code> pour afficher les données.
                        <br><strong>Objectif :</strong> Vérifier que les images s'affichent correctement avec la même logique.
                    </p>
                </div>
            </div>
        </div>

        <!-- Données du joueur -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header du joueur -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-2xl font-bold text-gray-800">{{ $player->first_name }} {{ $player->last_name }}</h2>
                                @if($player->position)
                                    <span class="text-lg font-medium text-blue-600 bg-blue-100 px-3 py-1 rounded-full">
                                        {{ $player->position }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-lg text-gray-600">{{ $player->nationality }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                ID: {{ $player->id }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Photo du joueur et informations -->
                    <div class="flex justify-center items-center space-x-6 mb-6">
                        <!-- Photo du joueur -->
                        <div class="text-center">
                            <h4 class="text-sm font-medium text-gray-600 mb-2">Photo du Joueur</h4>
                            @if($player->player_picture)
                                <img src="{{ asset('storage/' . $player->player_picture) }}" 
                                     alt="Photo {{ $player->first_name }} {{ $player->last_name }}" 
                                     class="h-24 w-24 object-cover rounded-lg border-2 border-gray-200">
                                <p class="text-xs text-gray-500 mt-1">{{ $player->player_picture }}</p>
                            @else
                                <div class="h-24 w-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-2xl">{{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Pas de photo</p>
                            @endif
                        </div>
                        
                        <!-- Informations de base -->
                        <div class="text-center">
                            <h4 class="text-sm font-medium text-gray-600 mb-2">Informations</h4>
                            <div class="space-y-2 text-sm">
                                <p><strong>Âge:</strong> {{ $player->age ?? 'N/A' }} ans</p>
                                <p><strong>Date de naissance:</strong> {{ $player->date_of_birth ? $player->date_of_birth->format('d/m/Y') : 'N/A' }}</p>
                                <p><strong>Nationalité:</strong> {{ $player->nationality ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Club et Association -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Club -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">🏟️ Club</h3>
                        @if($player->club)
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-600 mb-2">Logo du Club</h4>
                                @if($player->club->logo_path)
                                    <img src="{{ asset('storage/' . $player->club->logo_path) }}" 
                                         alt="Logo {{ $player->club->name }}" 
                                         class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                                    <p class="text-xs text-gray-500 mt-1">{{ $player->club->logo_path }}</p>
                                @elseif($player->club->logo)
                                    <img src="{{ asset('storage/' . $player->club->logo) }}" 
                                         alt="Logo {{ $player->club->name }}" 
                                         class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                                    <p class="text-xs text-gray-500 mt-1">{{ $player->club->logo }}</p>
                                @elseif($player->club->logo_url)
                                    <img src="{{ $player->club->logo_url }}" 
                                         alt="Logo {{ $player->club->name }}" 
                                         class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                                    <p class="text-xs text-gray-500 mt-1">{{ $player->club->logo_url }}</p>
                                @else
                                    <div class="h-24 w-24 bg-gradient-to-br from-red-500 to-pink-600 rounded-lg flex items-center justify-center mx-auto">
                                        <span class="text-white font-bold text-2xl">{{ substr($player->club->name, 0, 2) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Pas de logo</p>
                                @endif
                                <h5 class="text-lg font-semibold text-gray-800 mt-2">{{ $player->club->name }}</h5>
                            </div>
                        @else
                            <p class="text-gray-500 text-center">Aucun club associé</p>
                        @endif
                    </div>
                    
                    <!-- Association -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">🏛️ Association</h3>
                        @if($player->association)
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-600 mb-2">Logo de l'Association</h4>
                                @if($player->association->association_logo_url)
                                    <img src="{{ asset('storage/' . $player->association->association_logo_url) }}" 
                                         alt="Logo {{ $player->association->name }}" 
                                         class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                                    <p class="text-xs text-gray-500 mt-1">{{ $player->association->association_logo_url }}</p>
                                @elseif($player->association->logo)
                                    <img src="{{ asset('storage/' . $player->association->logo) }}" 
                                         alt="Logo {{ $player->association->name }}" 
                                         class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                                    <p class="text-xs text-gray-500 mt-1">{{ $player->association->logo }}</p>
                                @elseif($player->association->logo_path)
                                    <img src="{{ asset('storage/' . $player->association->logo_path) }}" 
                                         alt="Logo {{ $player->association->name }}" 
                                         class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200 mx-auto">
                                    <p class="text-xs text-gray-500 mt-1">{{ $player->association->logo_path }}</p>
                                @else
                                    <div class="h-24 w-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mx-auto">
                                        <span class="text-white font-bold text-2xl">{{ substr($player->association->name, 0, 2) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Pas de logo</p>
                                @endif
                                <h5 class="text-lg font-semibold text-gray-800 mt-2">{{ $player->association->name }}</h5>
                            </div>
                        @else
                            <p class="text-gray-500 text-center">Aucune association associée</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sidebar avec détails -->
            <div class="space-y-6">
                <!-- Détails du joueur -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">📊 Détails du Joueur</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID FIFA:</span>
                            <span class="font-medium">{{ $player->fifa_connect_id ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Note OVR:</span>
                            <span class="font-medium">{{ $player->overall_rating ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Potentiel:</span>
                            <span class="font-medium">{{ $player->potential_rating ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Forme:</span>
                            <span class="font-medium">{{ $player->form_percentage ?? 'N/A' }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fitness:</span>
                            <span class="font-medium">{{ $player->fitness_score ?? 'N/A' }}%</span>
                        </div>
                    </div>
                </div>
                
                <!-- Drapeau du pays -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">🏁 Drapeau du Pays</h3>
                    <div class="text-center">
                        @if($player->association && $player->association->nation_flag_url)
                            <img src="{{ asset('storage/' . $player->association->nation_flag_url) }}" 
                                 alt="Drapeau {{ $player->nationality }}" 
                                 class="h-24 w-32 object-cover rounded-lg border-2 border-gray-200 mx-auto">
                            <p class="text-xs text-gray-500 mt-1">{{ $player->association->nation_flag_url }}</p>
                        @elseif($player->association && $player->association->flag_url)
                            <img src="{{ asset('storage/' . $player->association->flag_url) }}" 
                                 alt="Drapeau {{ $player->nationality }}" 
                                 class="h-24 w-32 object-cover rounded-lg border-2 border-gray-200 mx-auto">
                            <p class="text-xs text-gray-500 mt-1">{{ $player->association->flag_url }}</p>
                        @else
                            <div class="h-24 w-32 bg-gradient-to-br from-blue-500 to-white rounded-lg flex items-center justify-center mx-auto">
                                <span class="text-blue-600 font-bold text-lg">{{ substr($player->nationality, 0, 2) }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pas de drapeau</p>
                        @endif
                        <h5 class="text-lg font-semibold text-gray-800 mt-2">{{ $player->nationality }}</h5>
                    </div>
                </div>
                
                <!-- Debug des données -->
                <div class="bg-gray-100 rounded-lg p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">🐛 Debug des Données</h3>
                    <div class="text-xs text-gray-600 space-y-1">
                        <p><strong>Player ID:</strong> {{ $player->id }}</p>
                        <p><strong>Player Picture:</strong> {{ $player->player_picture ?? 'NULL' }}</p>
                        @if($player->club)
                            <p><strong>Club Logo Path:</strong> {{ $player->club->logo_path ?? 'NULL' }}</p>
                            <p><strong>Club Logo:</strong> {{ $player->club->logo ?? 'NULL' }}</p>
                            <p><strong>Club Logo URL:</strong> {{ $player->club->logo_url ?? 'NULL' }}</p>
                        @endif
                        @if($player->association)
                            <p><strong>Association Logo:</strong> {{ $player->association->logo ?? 'NULL' }}</p>
                            <p><strong>Association Logo URL:</strong> {{ $player->association->association_logo_url ?? 'NULL' }}</p>
                            <p><strong>Association Logo Path:</strong> {{ $player->association->logo_path ?? 'NULL' }}</p>
                            <p><strong>Nation Flag URL:</strong> {{ $player->association->nation_flag_url ?? 'NULL' }}</p>
                            <p><strong>Flag URL:</strong> {{ $player->association->flag_url ?? 'NULL' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Comparaison avec les méthodes -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">🔍 Comparaison des Méthodes</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold text-green-600 mb-2">✅ Cette page (Méthode qui fonctionne)</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Utilise <code>Player::with(['club', 'association'])</code></li>
                        <li>• Accède directement aux champs : <code>$player->association->association_logo_url</code></li>
                        <li>• Utilise <code>asset('storage/' . $path)</code> pour les images</li>
                        <li>• Même logique que <code>/associations-view/show/1</code></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-red-600 mb-2">❌ FIFA Portal (Méthode qui ne fonctionne pas)</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Utilise des données transformées via l'API</li>
                        <li>• Accède aux champs via <code>fifaData.player.association.logo</code></li>
                        <li>• Utilise des chemins différents</li>
                        <li>• Logique différente de <code>/associations-view/show/1</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
