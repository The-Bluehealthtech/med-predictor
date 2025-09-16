<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Joueurs avec Logos de Clubs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Test - Joueurs avec Logos de Clubs</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($players as $player)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <!-- Photo du joueur -->
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200">
                        @if($player->player_picture)
                            <img src="{{ asset('storage/' . $player->player_picture) }}" 
                                 alt="{{ $player->name }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='/images/defaults/player-avatar.png'">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Informations du joueur -->
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $player->first_name }} {{ $player->last_name }}</h3>
                        <p class="text-sm text-gray-600">{{ $player->position }}</p>
                        <p class="text-xs text-gray-500">{{ $player->nationality }}</p>
                    </div>
                </div>
                
                <!-- Logo du club -->
                @if($player->club)
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 rounded overflow-hidden bg-white border border-gray-200">
                        @if($player->club->logo_path)
                            <img src="{{ asset('storage/' . $player->club->logo_path) }}" 
                                 alt="{{ $player->club->name }}" 
                                 class="w-full h-full object-contain"
                                 onerror="this.src='/images/defaults/club-logo.png'">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $player->club->name }}</p>
                        <p class="text-xs text-gray-500">{{ $player->club->short_name }}</p>
                    </div>
                </div>
                @else
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Aucun club assigné</p>
                </div>
                @endif
                
                <!-- Informations supplémentaires -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">ID:</span>
                            <span class="font-medium">{{ $player->id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Club ID:</span>
                            <span class="font-medium">{{ $player->club_id ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
            <h2 class="text-lg font-semibold text-blue-800 mb-2">Informations de Debug</h2>
            <p class="text-sm text-blue-700">Nombre de joueurs affichés: {{ $players->count() }}</p>
            <p class="text-sm text-blue-700">Joueurs avec club: {{ $players->whereNotNull('club_id')->count() }}</p>
            <p class="text-sm text-blue-700">Clubs avec logo: {{ $players->whereNotNull('club.logo_path')->count() }}</p>
        </div>
    </div>
</body>
</html>





