@extends('layouts.app')

@section('title', 'Joueurs - Gestion des Licences - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📋</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Gestion des Licences
                                </h1>
                                <p class="text-sm text-gray-600">Enregistrement et validation des joueurs</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">📋 Gestion des Licences FIFA</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Système d'enregistrement et de validation des licences des joueurs
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            {{ $players->total() }} joueurs enregistrés
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('player-registration.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ➕ Nouveau Joueur
                </a>

                <a href="{{ route('modules.licenses.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Gestion des Licences
                </a>
                <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🏠 Retour aux Modules
                </a>
            </div>
        </div>

        <!-- Players List with License Focus -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Joueurs Enregistrés - Focus Licences</h2>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-semibold">{{ $players->total() }}</span> joueurs
                        </div>
                        <a href="{{ route('player-registration.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                            + Ajouter
                        </a>
                    </div>
                </div>
            </div>
            
            @if($players->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
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
                                    FIFA Connect
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
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if($player->player_picture)
                                                    <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200" 
                                                         src="{{ asset('storage/' . $player->player_picture) }}" 
                                                         alt="{{ $player->first_name }} {{ $player->last_name }}">
                                                @else
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                        <span class="text-white font-bold text-lg">
                                                            {{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $player->first_name }} {{ $player->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $player->nationality }} • {{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) . ' ans' : 'N/A' }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    ID: {{ $player->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($player->club)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                    @if($player->club->logo)
                                                        <img class="h-8 w-8 rounded object-cover" 
                                                             src="{{ asset('storage/' . $player->club->logo) }}" 
                                                             alt="Logo {{ $player->club->name }}">
                                                    @else
                                                        <div class="h-8 w-8 rounded bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center">
                                                            <span class="text-white font-bold text-xs">🏟️</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $player->club->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $player->club->city ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-500">Aucun club</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($player->association)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                    @if($player->association->logo)
                                                        <img class="h-8 w-8 rounded object-cover" 
                                                             src="{{ asset('storage/' . $player->association->logo) }}" 
                                                             alt="Logo {{ $player->association->name }}">
                                                    @else
                                                        <div class="h-8 w-8 rounded bg-gradient-to-br from-red-500 to-yellow-600 flex items-center justify-center">
                                                            <span class="text-white font-bold text-xs">🏛️</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $player->association->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $player->association->country ?? 'N/A' }}</div>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($player->fifa_connect_id)
                                            <div class="flex items-center">
                                                <span class="text-green-600">✅</span>
                                                <span class="ml-2 text-xs">{{ $player->fifa_connect_id }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <span class="text-yellow-600">⏳</span>
                                                <span class="ml-2 text-xs">En attente</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('player-registration.show', $player->id) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                👁️ Voir
                                            </a>
                                            <a href="{{ route('player-registration.edit', $player->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                ✏️ Modifier
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $players->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">👥</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun joueur enregistré</h3>
                    <p class="text-gray-500 mb-6">Commencez par enregistrer votre premier joueur</p>
                    <a href="{{ route('player-registration.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ➕ Enregistrer un Joueur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
