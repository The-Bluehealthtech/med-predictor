<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Administrateur - Med Predictor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 min-h-screen">
    <!-- Header -->
    <div class="bg-white/10 backdrop-blur-lg border-b border-white/20 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-white">🏥 Tableau de bord Administrateur</h1>
                    <span class="bg-green-600 text-white px-3 py-1 rounded-full text-sm">Admin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="text-blue-300 hover:text-blue-200 text-sm underline">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Retour aux modules
                    </a>
                    <a href="{{ route('joueur.portal', 7) }}" class="text-blue-300 hover:text-blue-200 text-sm underline">
                        Voir portail joueur
                    </a>
                    <a href="{{ route('logout') }}" 
                       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Statistiques générales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-500 rounded-lg">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Total Joueurs</p>
                        <p class="text-2xl font-bold text-white">{{ $players->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-green-500 rounded-lg">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Avec Club</p>
                        <p class="text-2xl font-bold text-white">{{ $players->whereNotNull('club_id')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-500 rounded-lg">
                        <i class="fas fa-trophy text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Avec Association</p>
                        <p class="text-2xl font-bold text-white">{{ $players->whereNotNull('association_id')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-500 rounded-lg">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-300">Récents (30j)</p>
                        <p class="text-2xl font-bold text-white">{{ $players->where('created_at', '>=', now()->subDays(30))->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre de recherche -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-6 border border-white/20">
            <form method="GET" action="{{ route('players.list') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Rechercher par nom, position, club..." 
                               class="block w-full pl-10 pr-3 py-2 border border-white/20 rounded-lg bg-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select name="position" class="px-3 py-2 border border-white/20 rounded-lg bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Toutes les positions</option>
                        <option value="Gardien" {{ request('position') == 'Gardien' ? 'selected' : '' }}>Gardien</option>
                        <option value="Défenseur" {{ request('position') == 'Défenseur' ? 'selected' : '' }}>Défenseur</option>
                        <option value="Milieu" {{ request('position') == 'Milieu' ? 'selected' : '' }}>Milieu</option>
                        <option value="Attaquant" {{ request('position') == 'Attaquant' ? 'selected' : '' }}>Attaquant</option>
                    </select>
                    <select name="club" class="px-3 py-2 border border-white/20 rounded-lg bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les clubs</option>
                        @php
                            $clubs = \App\Models\Club::orderBy('name')->get();
                        @endphp
                        @foreach($clubs as $club)
                            <option value="{{ $club->name }}" {{ request('club') == $club->name ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-search mr-1"></i>
                        Rechercher
                    </button>
                    <a href="{{ route('players.list') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Effacer
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des joueurs -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Liste des Joueurs</h2>
                        <p class="text-gray-300 text-sm">Cliquez sur un joueur pour accéder à son portail</p>
                    </div>
                    <div class="text-sm text-gray-300">
                        <span id="resultsCount">{{ $players->total() }}</span> joueurs trouvés
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Joueur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stats FIFA</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Club</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Association</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($players as $player)
                            <tr class="hover:bg-white/5 transition-colors duration-200 fade-in">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            @if($player->getPlayerPictureUrlAttribute())
                                                <!-- Debug: {{ $player->getPlayerPictureUrlAttribute() }} -->
                                                <img class="h-12 w-12 rounded-full object-cover border-2 border-white/20" 
                                                     src="{{ $player->getPlayerPictureUrlAttribute() }}" 
                                                     alt="{{ $player->first_name }} {{ $player->last_name }}"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'; console.error('Erreur photo:', this.src);"
                                                     onload="console.log('Photo chargée:', this.src);">
                                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center" 
                                                     style="display: none;">
                                                    <span class="text-white font-bold text-lg">
                                                        {{ substr($player->first_name ?? 'P', 0, 1) }}{{ substr($player->first_name ?? 'P', 0, 1) }}{{ substr($player->last_name ?? 'N', 0, 1) }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                    <span class="text-white font-bold text-lg">
                                                        {{ substr($player->first_name ?? 'P', 0, 1) }}{{ substr($player->last_name ?? 'N', 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">
                                                {{ $player->first_name }} {{ $player->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-300">
                                                ID: {{ $player->id }} • {{ $player->nationality ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $player->position ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-400">OVR:</span>
                                            <span class="font-bold text-green-400">{{ $player->overall_rating ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-400">POT:</span>
                                            <span class="font-bold text-blue-400">{{ $player->potential_rating ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-400">FIT:</span>
                                            <span class="font-bold text-yellow-400">{{ $player->fitness ?? 'N/A' }}%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    @if($player->club)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                @if($player->club->logo_path)
                                                    <img class="h-8 w-8 rounded object-cover" 
                                                         src="{{ asset('storage/' . $player->club->logo_path) }}" 
                                                         alt="Logo {{ $player->club->name }}"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                @endif
                                                <div class="h-8 w-8 rounded bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center" 
                                                     style="display: {{ $player->club->logo_path ? 'none' : 'flex' }};">
                                                    <span class="text-white font-bold text-xs">🏟️</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-medium text-white">{{ $player->club->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $player->club->country ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-500">Aucun club</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    @if($player->association)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                @if($player->association->association_logo_url)
                                                    <img class="h-8 w-8 rounded object-cover" 
                                                         src="{{ asset('storage/' . $player->association->association_logo_url) }}" 
                                                         alt="Logo {{ $player->association->name }}"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                @elseif($player->association->nation_flag_url)
                                                    <img class="h-8 w-8 rounded object-cover" 
                                                         src="{{ asset('storage/' . $player->association->nation_flag_url) }}" 
                                                         alt="Drapeau {{ $player->association->country ?? 'N/A' }}"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                @endif
                                                <div class="h-8 w-8 rounded bg-gradient-to-br from-yellow-500 to-red-600 flex items-center justify-center" 
                                                     style="display: {{ ($player->association->association_logo_url || $player->association->nation_flag_url) ? 'none' : 'flex' }};">
                                                    <span class="text-white font-bold text-xs">🏆</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-medium text-white">{{ $player->association->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $player->association->country ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-500">Aucune association</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/test-portail-joueur-simple?player_id={{ $player->id }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                            FIT Portal
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-white mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    <button class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center py-2 px-4 rounded-lg transition-colors duration-200">
                        Exporter les données
                    </button>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-white mb-4">FIT</h3>
                <div class="space-y-2 text-sm text-gray-300">
                    <div class="flex justify-between">
                        <span>Version FIT:</span>
                        <span class="text-white">1.0.0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Base de données FIT:</span>
                        <span class="text-white">SQLite</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dernière mise à jour FIT:</span>
                        <span class="text-white">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-white mb-4">Aide</h3>
                <div class="space-y-2 text-sm text-gray-300">
                    <p>• Cliquez sur "FIT Portal" pour accéder au portail FIT du joueur</p>
                    <p>• Utilisez la barre de navigation pour passer d'un joueur à l'autre</p>
                    <p>• Les données sont maintenant 100% dynamiques</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($players->hasPages())
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mt-8 border border-white/20">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-300">
                        Affichage de {{ $players->firstItem() }} à {{ $players->lastItem() }} sur {{ $players->total() }} résultats
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $players->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Script pour gérer les photos des joueurs et la recherche -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Script de gestion des photos et recherche chargé');
            
            // Gérer toutes les photos des joueurs
            const playerPhotos = document.querySelectorAll('img[alt*=" "]');
            
            playerPhotos.forEach(function(photo) {
                console.log('📸 Gestion photo:', photo.src);
                
                // Gestion d'erreur personnalisée
                photo.addEventListener('error', function() {
                    console.error('❌ Erreur chargement photo:', this.src);
                    this.style.display = 'none';
                    
                    // Afficher les initiales
                    const fallback = this.nextElementSibling;
                    if (fallback) {
                        fallback.style.display = 'flex';
                        console.log('✅ Fallback affiché pour:', this.alt);
                    }
                });
                
                // Gestion de succès
                photo.addEventListener('load', function() {
                    console.log('✅ Photo chargée avec succès:', this.src);
                    // Cacher le fallback
                    const fallback = this.nextElementSibling;
                    if (fallback) {
                        fallback.style.display = 'none';
                    }
                });
            });

            // La recherche est maintenant gérée côté serveur via le formulaire
        });
    </script>
</body>
</html>
