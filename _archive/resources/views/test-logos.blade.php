<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test des Logos - FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Test d'Affichage des Logos</h1>
        
        <!-- Test des Logos de Clubs -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">🏟️ Logos des Clubs</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($clubs as $club)
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-2 bg-gray-200 rounded-lg flex items-center justify-center">
                        @if($club->logo_path)
                            <img src="{{ asset($club->logo_path) }}" 
                                 alt="Logo {{ $club->name }}" 
                                 class="w-full h-full object-contain rounded-lg"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-blue-500 text-white rounded-lg flex items-center justify-center text-xs font-bold" style="display: none;">
                                {{ substr($club->name, 0, 2) }}
                            </div>
                        @else
                            <div class="w-full h-full bg-gray-500 text-white rounded-lg flex items-center justify-center text-xs font-bold">
                                {{ substr($club->name, 0, 2) }}
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">{{ $club->name }}</p>
                    <p class="text-xs text-gray-400">{{ $club->logo_path ?? 'Pas de logo' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Test des Logos d'Associations -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">🏛️ Logos des Associations</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($associations as $association)
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-2 bg-gray-200 rounded-lg flex items-center justify-center">
                        @if($association->logo_path)
                            @if(str_starts_with($association->logo_path, 'http'))
                                <img src="{{ $association->logo_path }}" 
                                     alt="Logo {{ $association->name }}" 
                                     class="w-full h-full object-contain rounded-lg"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @else
                                <img src="{{ asset($association->logo_path) }}" 
                                     alt="Logo {{ $association->name }}" 
                                     class="w-full h-full object-contain rounded-lg"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="w-full h-full bg-green-500 text-white rounded-lg flex items-center justify-center text-xs font-bold" style="display: none;">
                                {{ substr($association->name, 0, 2) }}
                            </div>
                        @else
                            <div class="w-full h-full bg-gray-500 text-white rounded-lg flex items-center justify-center text-xs font-bold">
                                {{ substr($association->name, 0, 2) }}
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">{{ $association->name }}</p>
                    <p class="text-xs text-gray-400">{{ $association->logo_path ?? 'Pas de logo' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Test des Photos de Joueurs -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">👤 Photos des Joueurs</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($players as $player)
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-2 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                        @if($player->player_picture)
                            <img src="{{ asset($player->player_picture) }}" 
                                 alt="Photo {{ $player->first_name }}" 
                                 class="w-full h-full object-cover rounded-lg"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full bg-purple-500 text-white rounded-lg flex items-center justify-center text-xs font-bold" style="display: none;">
                                {{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}
                            </div>
                        @else
                            <div class="w-full h-full bg-gray-500 text-white rounded-lg flex items-center justify-center text-xs font-bold">
                                {{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">{{ $player->first_name }} {{ $player->last_name }}</p>
                    <p class="text-xs text-gray-400">{{ $player->player_picture ?? 'Pas de photo' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Informations de Debug -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">🔍 Informations de Debug</h3>
            <div class="text-sm text-blue-700">
                <p><strong>Base URL:</strong> {{ url('/') }}</p>
                <p><strong>Asset URL:</strong> {{ asset('') }}</p>
                <p><strong>Storage URL:</strong> {{ asset('storage/') }}</p>
                <p><strong>Images URL:</strong> {{ asset('images/') }}</p>
            </div>
        </div>
    </div>
</body>
</html>


