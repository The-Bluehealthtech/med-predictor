@extends('layouts.app')

@section('title', 'Players - FIT Platform')

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
                                <span class="text-white font-bold text-lg">👥</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Players
                                </h1>
                                <p class="text-sm text-gray-600">Gestion des joueurs</p>
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
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">👥 Gestion des Joueurs</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Gestion complète des joueurs, profils et informations
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            {{ $players->count() }} joueurs enregistrés
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
                <a href="{{ route('club.player-licenses.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📋 Licences
                </a>
                <a href="{{ route('player-passports.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🛂 Passeports
                </a>
                <a href="{{ route('health-records.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🏥 Dossiers Médicaux
                </a>
            </div>
        </div>

        <!-- Players List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Liste des Joueurs</h2>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-semibold">{{ $players->count() }}</span> joueurs
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
                                    Position
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut Licence
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dernière Mise à Jour
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
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-blue-600 font-semibold text-sm">
                                                        {{ substr($player->first_name ?? 'A', 0, 1) . substr($player->last_name ?? 'B', 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $player->first_name ?? 'N/A' }} {{ $player->last_name ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $player->fifa_connect_id ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $player->club->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $player->club->city ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $player->position ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $latestLicense = $player->licenses->sortByDesc('created_at')->first();
                                            $licenseStatus = $latestLicense ? $latestLicense->status : 'no_license';
                                        @endphp
                                        
                                        @if($licenseStatus === 'approved')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                ✅ Approuvée
                                            </span>
                                        @elseif($licenseStatus === 'pending')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                ⏳ En Attente
                                            </span>
                                        @elseif($licenseStatus === 'rejected')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                ❌ Rejetée
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                📋 Aucune Licence
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($latestLicense)
                                            {{ $latestLicense->updated_at ? $latestLicense->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('health-records.index', ['player_id' => $player->id]) }}" 
                                               class="text-blue-600 hover:text-blue-900" title="Dossier Médical">
                                                🏥
                                            </a>
                                            <a href="{{ route('licenses.create', ['player_id' => $player->id]) }}" 
                                               class="text-green-600 hover:text-green-900" title="Licence">
                                                📋
                                            </a>
                                            <a href="#" class="text-purple-600 hover:text-purple-900" title="Passeport">
                                                🛂
                                            </a>
                                            <a href="#" class="text-yellow-600 hover:text-yellow-900" title="Modifier">
                                                ✏️
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6">
                    <div class="text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun joueur enregistré</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter un nouveau joueur.</p>
                        <div class="mt-6">
                            <a href="{{ route('player-registration.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Ajouter un joueur
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Statistics -->
        @if($players->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $players->count() }}</div>
                        <div class="text-sm text-gray-600">Total Joueurs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $players->filter(function($player) { 
                                return $player->licenses->where('status', 'approved')->count() > 0; 
                            })->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Licences Approuvées</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ $players->filter(function($player) { 
                                return $player->licenses->where('status', 'pending')->count() > 0; 
                            })->count() }}
                        </div>
                        <div class="text-sm text-gray-600">En Attente</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">
                            {{ $players->filter(function($player) { 
                                return $player->licenses->where('status', 'rejected')->count() > 0; 
                            })->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Rejetées</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 