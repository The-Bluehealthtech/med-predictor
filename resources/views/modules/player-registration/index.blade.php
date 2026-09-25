@extends('layouts.app')

@section('title', 'Liste des Joueurs - Med Predictor')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">👥 Liste des Joueurs</h1>
                    <p class="mt-2 text-gray-600">Gestion des joueurs enregistrés dans le système</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('player-registration.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        ➕ Nouveau Joueur
                    </a>
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        ← Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Liste des joueurs -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Joueurs Enregistrés</h2>
            </div>
            
            @if($players->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Association</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">FIFA Connect ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                                    {{ $player->nationality }} • {{ $player->date_of_birth ? $player->date_of_birth->age . ' ans' : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $player->position ?? 'N/A' }}
                                        </span>
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
                                                        <div class="h-8 w-8 rounded bg-gradient-to-br from-yellow-500 to-red-600 flex items-center justify-center">
                                                            <span class="text-white font-bold text-xs">🏆</span>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <code class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">
                                            {{ $player->fifa_connect_id ?? 'N/A' }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="/fifa-portal?player_id={{ $player->id }}" 
                                               class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded text-xs">
                                                FIFA Portal
                                            </a>
                                            <a href="{{ route('player-licenses.request.create', $player) }}"
                                               class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-2 py-1 rounded text-xs">
                                                📋 Licence
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $players->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun joueur trouvé</h3>
                    <p class="text-gray-500 mb-4">Commencez par créer votre premier joueur.</p>
                    <a href="{{ route('player-registration.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        ➕ Créer le premier joueur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
