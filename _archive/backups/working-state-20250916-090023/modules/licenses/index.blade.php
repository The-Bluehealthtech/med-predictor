<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FIT - Football Intelligence & Tracking') }} - {{ ucfirst($footballType) }} Licensing</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">FIT</span>
                                </div>
                                <div class="ml-3">
                                    <h1 class="text-2xl font-bold text-gray-900">
                                        📋 Gestion des Licences - Clubs
                                    </h1>
                                    <p class="text-sm text-gray-600">Demande de licences par les clubs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/modules" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux modules</a>
                        <a href="/clubs-view" class="text-gray-600 hover:text-gray-900 text-sm font-medium">🏟️ Voir les clubs</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Message d'information -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <span class="text-blue-600 text-xl">ℹ️</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Module Clubs - Demande de Licence</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Cette page est destinée aux <strong>clubs</strong> pour demander des licences pour leurs joueurs.</p>
                            <p class="mt-1">Pour la <strong>validation des licences par l'association</strong>, utilisez le module "Validation des Licences".</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Module de Gestion des Licences</h2>
                <p class="text-gray-600 mb-4">
                    Ce module permet aux clubs de demander des licences pour leurs joueurs.
                    Sélectionnez un joueur dans la liste ci-dessous pour initier une demande de licence.
                </p>
                
                <!-- Liste des Joueurs pour Demande de Licence -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏃‍♂️ Joueurs - Demande de Licence</h3>
                    <p class="text-gray-600 mb-6">
                        Liste des joueurs disponibles pour une demande de licence. Cliquez sur "📋 Demander Licence" pour initier le processus.
                        Les données existantes du joueur seront automatiquement pré-remplies dans le formulaire.
                    </p>
                    
                    @if($players->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Photo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Joueur
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Club
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Association
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut Licence
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($players as $player)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    @if($player->player_picture)
                                                        <img class="h-12 w-12 rounded-full object-cover" 
                                                             src="{{ asset('storage/' . $player->player_picture) }}" 
                                                             alt="Photo {{ $player->first_name }}">
                                                    @else
                                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                                            <span class="text-white font-bold text-sm">{{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $player->first_name }} {{ $player->last_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $player->date_of_birth ? $player->date_of_birth->format('d/m/Y') : 'N/A' }} • {{ $player->nationality ?? 'N/A' }}
                    </div>
                                                        <div class="text-xs text-gray-400">
                                                            {{ $player->position ?? 'N/A' }}
                    </div>
                    </div>
                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($player->club)
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                            @if($player->club->logo_path)
                                                                <img class="h-8 w-8 rounded object-cover" 
                                                                     src="{{ asset('storage/' . $player->club->logo_path) }}" 
                                                                     alt="Logo {{ $player->club->name }}">
                                                            @else
                                                                <div class="h-8 w-8 rounded bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center">
                                                                    <span class="text-white font-bold text-xs">{{ substr($player->club->name, 0, 2) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="font-medium">{{ $player->club->name }}</div>
                                                            @if($player->club->short_name)
                                                                <div class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded mb-1">
                                                                    {{ $player->club->short_name }}
                                                                </div>
                                                            @endif
                                                            <div class="text-xs text-gray-500">{{ $player->club->city ?? 'N/A' }}</div>
                                                            @if($player->club->founded_year)
                                                                <div class="text-xs text-gray-400">Fondé en {{ $player->club->founded_year }}</div>
                                                            @endif
                                                        </div>
                            </div>
                                                @else
                                                    <span class="text-gray-500">Aucun club</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($player->association)
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                            @if($player->association->association_logo_url)
                                                                <img class="h-8 w-8 rounded object-cover" 
                                                                     src="{{ asset('storage/' . $player->association->association_logo_url) }}" 
                                                                     alt="Logo {{ $player->association->name }}">
                                                            @elseif($player->association->logo_path)
                                                                <img class="h-8 w-8 rounded object-cover" 
                                                                     src="{{ asset('storage/' . $player->association->logo_path) }}" 
                                                                     alt="Logo {{ $player->association->name }}">
                                                            @else
                                                                <div class="h-8 w-8 rounded bg-gradient-to-br from-red-500 to-yellow-600 flex items-center justify-center">
                                                                    <span class="text-white font-bold text-xs">{{ substr($player->association->name, 0, 2) }}</span>
                            </div>
                                                            @endif
                            </div>
                                                        <div>
                                                            <div class="font-medium">{{ $player->association->name }}</div>
                                                            @if($player->association->short_name)
                                                                <div class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded mb-1">
                                                                    {{ $player->association->short_name }}
                            </div>
                                                            @endif
                                                            <div class="text-xs text-gray-500">{{ $player->association->country ?? 'N/A' }}</div>
                                                            @if($player->association->confederation)
                                                                <div class="text-xs text-gray-400">{{ $player->association->confederation }}</div>
                                                            @endif
                    </div>
                </div>
                                                @else
                                                    <span class="text-gray-500">Aucune association</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($player->licenses && $player->licenses->count() > 0)
                                                    @php
                                                        $latestLicense = $player->licenses->sortByDesc('created_at')->first();
                                                        $statusColor = match($latestLicense->status ?? 'unknown') {
                                                            'valid' => 'bg-green-100 text-green-800',
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'expired' => 'bg-red-100 text-red-800',
                                                            'suspended' => 'bg-orange-100 text-orange-800',
                                                            'revoked' => 'bg-gray-100 text-gray-800',
                                                            default => 'bg-gray-100 text-gray-800'
                                                        };
                                                    @endphp
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                                        {{ ucfirst($latestLicense->status ?? 'N/A') }}
                                                    </span>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $latestLicense->license_number ?? 'N/A' }}
                                                    </div>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Aucune licence
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('player-registration.create', ['player_id' => $player->id]) }}" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                                    📋 Demander Licence
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">👥</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun joueur enregistré</h3>
                            <p class="text-gray-500 mb-6">Commencez par enregistrer des joueurs</p>
                            <a href="{{ route('player-registration.create') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                ➕ Enregistrer un Joueur
                            </a>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html> 