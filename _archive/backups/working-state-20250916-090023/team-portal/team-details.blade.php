@extends('layouts.app')

@section('title', 'Détails Équipe - Team Portal')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">⚽</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    {{ $team->name ?? 'Équipe sans nom' }}
                                </h1>
                                <p class="text-sm text-gray-600">{{ $team->category ?? 'Catégorie N/A' }} - {{ $team->club->name ?? 'Club N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('team-portal.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Team Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">👥</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Joueurs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $teamStats['total_players'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">🏆</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Taux de Victoire</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $teamStats['win_rate'] }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">⚽</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Buts Marqués</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $teamStats['goals_scored'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl">📊</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Âge Moyen</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($teamStats['average_age'], 1) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Players -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Effectif de l'Équipe</h3>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 text-sm">👥</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($team->players as $player)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white text-sm font-bold">{{ substr($player->first_name ?? 'N', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $player->first_name }} {{ $player->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $player->position ?? 'Position N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Âge: {{ $player->age ?? 'N/A' }}</span>
                            <span>Note: {{ $player->overall_rating ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Matches -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Matchs Récents</h3>
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 text-sm">⚽</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($team->matches->take(5) as $match)
                    <div class="flex items-center justify-between py-4 px-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white text-lg">⚽</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $match->home_team ?? 'Équipe Domicile' }} vs {{ $match->away_team ?? 'Équipe Extérieur' }}</p>
                                <p class="text-xs text-gray-500">{{ $match->match_date ? \Carbon\Carbon::parse($match->match_date)->format('d/m/Y') : 'Date N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900">{{ $match->result ?? 'N/A' }}</span>
                            <p class="text-xs text-gray-400">Résultat</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
