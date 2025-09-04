<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Joueurs - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fifa-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .fifa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .club-logo {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 8px;
            background: white;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .club-logo:hover {
            transform: scale(1.1);
            border-color: #667eea;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Liste des Joueurs</h1>
            <p class="text-xl text-gray-600">Plateforme Fédération Internationale de Tunisie</p>
            <div class="mt-4">
                <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ $players->count() }} joueurs enregistrés
                </span>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="mb-8">
            <div class="max-w-md mx-auto">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Rechercher un joueur..." 
                           class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Players Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="playersGrid">
            @foreach($players as $player)
            <div class="fifa-card p-6 text-white player-card" data-name="{{ strtolower($player->first_name . ' ' . $player->last_name) }}">
                <!-- Player Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <!-- Player Photo -->
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-white/20 backdrop-blur-sm">
                            @if($player->player_picture)
                                <img src="{{ asset('storage/' . $player->player_picture) }}" 
                                     alt="{{ $player->first_name }} {{ $player->last_name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.src='/images/defaults/player-avatar.png'">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Player Info -->
                        <div>
                            <h3 class="text-lg font-bold">{{ $player->first_name }} {{ $player->last_name }}</h3>
                            <p class="text-sm text-white/80">{{ $player->position }}</p>
                        </div>
                    </div>
                    
                    <!-- Player Number -->
                    <div class="text-right">
                        <span class="text-2xl font-bold text-white/90">#{{ $player->id }}</span>
                    </div>
                </div>

                <!-- Club Section -->
                @if($player->club)
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-3">
                        <!-- Club Logo -->
                        <div class="flex-shrink-0">
                            @if($player->club->logo_path)
                                <img src="{{ asset('storage/' . $player->club->logo_path) }}" 
                                     alt="{{ $player->club->name }}" 
                                     class="club-logo"
                                     onerror="this.src='/images/defaults/club-logo.png'">
                            @else
                                <div class="club-logo flex items-center justify-center bg-white/20">
                                    <svg class="w-4 h-4 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Club Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ $player->club->name }}</p>
                            <p class="text-xs text-white/70">{{ $player->club->short_name }}</p>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="club-logo flex items-center justify-center bg-white/20">
                            <svg class="w-4 h-4 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" />
                            </svg>
                        </div>
                        <p class="text-sm text-white/80">Aucun club assigné</p>
                    </div>
                </div>
                @endif

                <!-- Player Stats -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-center">
                        <p class="text-white/60 text-xs">Nationalité</p>
                        <p class="font-semibold">{{ $player->nationality ?? 'N/A' }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-white/60 text-xs">ID</p>
                        <p class="font-semibold">{{ $player->id }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 pt-4 border-t border-white/20">
                    <div class="flex space-x-2">
                        <a href="http://localhost:8080/fifa-portal?player_id={{ $player->id }}&cache={{ time() }}" 
                           class="flex-1 bg-white/20 hover:bg-white/30 text-white text-xs font-medium py-2 px-3 rounded-lg transition-colors text-center">
                            👁️ Voir
                        </a>
                        <button class="flex-1 bg-white/20 hover:bg-white/30 text-white text-xs font-medium py-2 px-3 rounded-lg transition-colors">
                            ✏️ Modifier
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="hidden text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun joueur trouvé</h3>
            <p class="mt-1 text-sm text-gray-500">Essayez de modifier vos critères de recherche.</p>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const playerCards = document.querySelectorAll('.player-card');
            let visibleCards = 0;

            playerCards.forEach(card => {
                const playerName = card.dataset.name;
                if (playerName.includes(searchTerm)) {
                    card.style.display = 'block';
                    visibleCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no results message
            const noResults = document.getElementById('noResults');
            if (visibleCards === 0 && searchTerm !== '') {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
