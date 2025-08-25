<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Administrateur - Med Predictor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                        <p class="text-2xl font-bold text-white">{{ $players->count() }}</p>
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

        <!-- Liste des joueurs -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/20">
                <h2 class="text-xl font-semibold text-white">Liste des Joueurs</h2>
                <p class="text-gray-300 text-sm">Cliquez sur un joueur pour accéder à son portail</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Joueur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Club</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Association</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($players as $player)
                            <tr class="hover:bg-white/5 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($player->getPlayerPictureUrlAttribute())
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="{{ $player->getPlayerPictureUrlAttribute() }}" 
                                                     alt="{{ $player->first_name }} {{ $player->last_name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                    <span class="text-white font-semibold">
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
                                                ID: {{ $player->id }}
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
                                    @if($player->club)
                                        <div class="flex items-center">
                                            <span class="mr-2">🏟️</span>
                                            <span>{{ $player->club->name }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $player->club->country ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-gray-500">Aucun club</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    @if($player->association)
                                        <div class="flex items-center">
                                            <span class="mr-2">🏆</span>
                                            <span>{{ $player->association->name }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $player->association->country ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-gray-500">Aucune association</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="/fifa-portal?player_id={{ $player->id }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                        FIFA Portal
                                    </a>
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
                <h3 class="text-lg font-semibold text-white mb-4">Système</h3>
                <div class="space-y-2 text-sm text-gray-300">
                    <div class="flex justify-between">
                        <span>Version:</span>
                        <span class="text-white">1.0.0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Base de données:</span>
                        <span class="text-white">SQLite</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dernière mise à jour:</span>
                        <span class="text-white">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-white mb-4">Aide</h3>
                <div class="space-y-2 text-sm text-gray-300">
                    <p>• Cliquez sur "FIFA Portal" pour accéder au portail FIFA du joueur</p>
                    <p>• Utilisez la barre de navigation pour passer d'un joueur à l'autre</p>
                    <p>• Les données sont maintenant 100% dynamiques</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
