<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection des Joueurs - FIT Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
            <div class="container mx-auto px-6 py-4">
                <h1 class="text-3xl font-bold">🏆 Sélection des Joueurs</h1>
                <p class="text-blue-100">Choisissez un joueur pour accéder à son portail</p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-6 py-8">
            <!-- Filtres -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">🔍 Filtres</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nationalité</label>
                        <select id="nationality-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Toutes les nationalités</option>
                            @foreach($nationalities as $nationality)
                                <option value="{{ $nationality->name }}">{{ $nationality->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <select id="position-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Toutes les positions</option>
                            <option value="FW">Attaquant (FW)</option>
                            <option value="MF">Milieu (MF)</option>
                            <option value="DF">Défenseur (DF)</option>
                            <option value="GK">Gardien (GK)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                        <select id="club-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Tous les clubs</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->name }}">{{ $club->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                        <input type="text" id="search-filter" placeholder="Nom du joueur..." class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                </div>
            </div>

            <!-- Liste des Joueurs -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">⚽ Joueurs Disponibles ({{ $players->count() }})</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationalité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score FIFA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="players-table-body">
                            @foreach($players as $player)
                            <tr class="hover:bg-gray-50 player-row" 
                                data-nationality="{{ $player->nationality }}"
                                data-position="{{ $player->position }}"
                                data-club="{{ $player->club->name ?? 'Sans club' }}"
                                data-name="{{ $player->first_name }} {{ $player->last_name }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <img class="h-12 w-12 rounded-full object-cover" 
                                                 src="{{ $player->photo_path ?? '/images/players/default_player.svg' }}" 
                                                 alt="Photo de {{ $player->first_name }} {{ $player->last_name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $player->first_name }} {{ $player->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $player->date_of_birth ?? 'Date de naissance non définie' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($player->position == 'FW') bg-red-100 text-red-800
                                        @elseif($player->position == 'MF') bg-blue-100 text-blue-800
                                        @elseif($player->position == 'DF') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $player->position ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($player->club)
                                            <img class="h-8 w-8 rounded-full object-cover mr-2" 
                                                 src="{{ $player->club->logo_path ?? '/images/clubs/default_club.svg' }}" 
                                                 alt="Logo de {{ $player->club->name }}">
                                            <span class="text-sm text-gray-900">{{ $player->club->name }}</span>
                                        @else
                                            <span class="text-sm text-gray-500">Sans club</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="h-6 w-8 rounded mr-2" 
                                             src="{{ $player->nationality_flag_path ?? '/images/flags/default_flag.svg' }}" 
                                             alt="Drapeau de {{ $player->nationality }}">
                                        <span class="text-sm text-gray-900">{{ $player->nationality ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $player->overall_rating ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Potentiel: {{ $player->potential_rating ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('joueur.portal', $player->id) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        Accéder au Portail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Filtrage en temps réel
        function filterPlayers() {
            const nationality = document.getElementById('nationality-filter').value.toLowerCase();
            const position = document.getElementById('position-filter').value.toLowerCase();
            const club = document.getElementById('club-filter').value.toLowerCase();
            const search = document.getElementById('search-filter').value.toLowerCase();
            
            const rows = document.querySelectorAll('.player-row');
            
            rows.forEach(row => {
                const playerNationality = row.dataset.nationality?.toLowerCase() || '';
                const playerPosition = row.dataset.position?.toLowerCase() || '';
                const playerClub = row.dataset.club?.toLowerCase() || '';
                const playerName = row.dataset.name?.toLowerCase() || '';
                
                const matchNationality = !nationality || playerNationality.includes(nationality);
                const matchPosition = !position || playerPosition === position;
                const matchClub = !club || playerClub.includes(club);
                const matchSearch = !search || playerName.includes(search);
                
                if (matchNationality && matchPosition && matchClub && matchSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Événements de filtrage
        document.getElementById('nationality-filter').addEventListener('change', filterPlayers);
        document.getElementById('position-filter').addEventListener('change', filterPlayers);
        document.getElementById('club-filter').addEventListener('change', filterPlayers);
        document.getElementById('search-filter').addEventListener('input', filterPlayers);
    </script>
</body>
</html>






