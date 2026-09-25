@extends('layouts.app')

@section('title', 'Dashboard Performance')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Performance</h1>
                    <p class="mt-1 text-sm text-gray-500">Vue d'ensemble des performances sportives</p>
                </div>
                <div class="flex space-x-3">
                    <select id="dashboardLevel" onchange="changeDashboardLevel()" 
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="federation">Niveau Fédération</option>
                        <option value="club">Niveau Club</option>
                        <option value="player">Niveau Joueur</option>
                    </select>
                    <button onclick="refreshData()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Federation Level Dashboard -->
    <div id="federationDashboard" class="dashboard-level">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Joueurs</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalPlayers ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Performances Moyennes</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($averagePerformance ?? 0, 1) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Recommandations IA</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $aiRecommendationsCount ?? 0 }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Tendance</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($performanceTrend ?? 0, 1) }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Performance by Category Chart -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Performance par Catégorie</h3>
                    <div class="h-64">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Performance Trend Chart -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des Performances</h3>
                    <div class="h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Performers and Alerts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Performers -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Meilleurs Performeurs</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($topPerformers ?? [] as $performer)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($performer->player->player_picture)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $performer->player->player_picture) }}" alt="{{ $performer->player->first_name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($performer->player->first_name, 0, 1) }}{{ substr($performer->player->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $performer->player->first_name }} {{ $performer->player->last_name }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $performer->player->club->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ number_format($performer->overall_performance_score, 1) }}</p>
                                    <p class="text-xs text-gray-500">Score global</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">Aucune donnée disponible</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Performance Alerts -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Alertes Performance</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($performanceAlerts ?? [] as $alert)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-{{ $alert->priority === 'high' ? 'red' : ($alert->priority === 'medium' ? 'yellow' : 'green') }}-400 rounded-full mt-2"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $alert->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $alert->description }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $alert->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">Aucune alerte active</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Club Level Dashboard -->
    <div id="clubDashboard" class="dashboard-level hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Club Selection -->
            <div class="mb-6">
                <label for="clubSelect" class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un Club</label>
                <select id="clubSelect" onchange="loadClubData()" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Choisir un club</option>
                    @foreach($clubs ?? [] as $club)
                        <option value="{{ $club->id }}">{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Club Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Joueurs du Club</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="clubPlayersCount">-</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Performance Moyenne</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="clubAveragePerformance">-</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Sessions d'Entraînement</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="clubTrainingSessions">-</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Recommandations</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="clubRecommendations">-</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Club Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Performance par Position</h3>
                    <div class="h-64">
                        <canvas id="positionChart"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution Mensuelle</h3>
                    <div class="h-64">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Club Players List -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Joueurs du Club</h3>
                </div>
                <div class="p-6">
                    <div id="clubPlayersList" class="space-y-4">
                        <p class="text-gray-500 text-center py-4">Sélectionnez un club pour voir les joueurs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Player Level Dashboard -->
    <div id="playerDashboard" class="dashboard-level hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Player Selection -->
            <div class="mb-6">
                <label for="playerSelect" class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un Joueur</label>
                <select id="playerSelect" onchange="loadPlayerData()" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Choisir un joueur</option>
                    @foreach($players ?? [] as $player)
                        <option value="{{ $player->id }}">{{ $player->first_name }} {{ $player->last_name }} ({{ $player->fifa_id }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Player Profile -->
            <div id="playerProfile" class="hidden">
                <div class="bg-white shadow rounded-lg p-6 mb-8">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-20 w-20">
                            <div id="playerPhoto" class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-2xl font-medium text-gray-700">-</span>
                            </div>
                        </div>
                        <div class="ml-6">
                            <h2 id="playerName" class="text-2xl font-bold text-gray-900">-</h2>
                            <p id="playerInfo" class="text-gray-500">-</p>
                            <div class="mt-2 flex space-x-4">
                                <span id="playerClub" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">-</span>
                                <span id="playerPosition" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Player Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Score Global</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="playerOverallScore">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Tendance</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="playerTrend">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Dernière Évaluation</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="playerLastEvaluation">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Recommandations</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="playerRecommendations">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Player Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Performance par Dimension</h3>
                        <div class="h-64">
                            <canvas id="dimensionChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution Temporelle</h3>
                        <div class="h-64">
                            <canvas id="playerTimelineChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Player Recommendations -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recommandations IA</h3>
                    </div>
                    <div class="p-6">
                        <div id="playerRecommendationsList" class="space-y-4">
                            <!-- Recommendations will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let categoryChart, trendChart, positionChart, monthlyChart, dimensionChart, playerTimelineChart;

function changeDashboardLevel() {
    const level = document.getElementById('dashboardLevel').value;
    
    // Hide all dashboard levels
    document.querySelectorAll('.dashboard-level').forEach(el => el.classList.add('hidden'));
    
    // Show selected level
    document.getElementById(level + 'Dashboard').classList.remove('hidden');
    
    // Initialize charts for the selected level
    if (level === 'federation') {
        initializeFederationCharts();
    } else if (level === 'club') {
        initializeClubCharts();
    } else if (level === 'player') {
        initializePlayerCharts();
    }
}

function refreshData() {
    const level = document.getElementById('dashboardLevel').value;
    
    if (level === 'federation') {
        loadFederationData();
    } else if (level === 'club') {
        loadClubData();
    } else if (level === 'player') {
        loadPlayerData();
    }
}

function initializeFederationCharts() {
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    categoryChart = new Chart(categoryCtx, {
        type: 'radar',
        data: {
            labels: ['Physique', 'Technique', 'Tactique', 'Mental', 'Social'],
            datasets: [{
                label: 'Performance Moyenne',
                data: [85, 78, 82, 75, 80],
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Performance Moyenne',
                data: [75, 78, 82, 85, 83, 87],
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function initializeClubCharts() {
    // Position Chart
    const positionCtx = document.getElementById('positionChart').getContext('2d');
    positionChart = new Chart(positionCtx, {
        type: 'bar',
        data: {
            labels: ['Attaquant', 'Milieu', 'Défenseur', 'Gardien'],
            datasets: [{
                label: 'Performance Moyenne',
                data: [82, 79, 85, 88],
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(168, 85, 247, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Performance Club',
                data: [78, 81, 84, 86, 83, 89],
                borderColor: 'rgba(34, 197, 94, 1)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function initializePlayerCharts() {
    // Dimension Chart
    const dimensionCtx = document.getElementById('dimensionChart').getContext('2d');
    dimensionChart = new Chart(dimensionCtx, {
        type: 'radar',
        data: {
            labels: ['Physique', 'Technique', 'Tactique', 'Mental', 'Social'],
            datasets: [{
                label: 'Performance Actuelle',
                data: [88, 85, 82, 78, 80],
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Player Timeline Chart
    const playerTimelineCtx = document.getElementById('playerTimelineChart').getContext('2d');
    playerTimelineChart = new Chart(playerTimelineCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Performance Joueur',
                data: [75, 78, 82, 85, 88, 90],
                borderColor: 'rgba(168, 85, 247, 1)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function loadClubData() {
    const clubId = document.getElementById('clubSelect').value;
    if (!clubId) return;

    // Simulate loading club data
    document.getElementById('clubPlayersCount').textContent = '24';
    document.getElementById('clubAveragePerformance').textContent = '82.5';
    document.getElementById('clubTrainingSessions').textContent = '156';
    document.getElementById('clubRecommendations').textContent = '12';

    // Update club players list
    const playersList = document.getElementById('clubPlayersList');
    playersList.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="flex items-center p-4 border rounded-lg">
                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                    <span class="text-sm font-medium text-gray-700">JD</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">John Doe</p>
                    <p class="text-xs text-gray-500">Attaquant • 85.2</p>
                </div>
            </div>
            <!-- Add more players as needed -->
        </div>
    `;
}

function loadPlayerData() {
    const playerId = document.getElementById('playerSelect').value;
    if (!playerId) {
        document.getElementById('playerProfile').classList.add('hidden');
        return;
    }

    document.getElementById('playerProfile').classList.remove('hidden');

    // Simulate loading player data
    document.getElementById('playerName').textContent = 'John Doe';
    document.getElementById('playerInfo').textContent = 'FIFA ID: 123456 • 25 ans';
    document.getElementById('playerClub').textContent = 'FC Example';
    document.getElementById('playerPosition').textContent = 'Attaquant';
    document.getElementById('playerOverallScore').textContent = '85.2';
    document.getElementById('playerTrend').textContent = '+2.3%';
    document.getElementById('playerLastEvaluation').textContent = 'Il y a 3 jours';
    document.getElementById('playerRecommendations').textContent = '3 actives';

    // Load player recommendations
    const recommendationsList = document.getElementById('playerRecommendationsList');
    recommendationsList.innerHTML = `
        <div class="space-y-4">
            <div class="flex items-start p-4 border rounded-lg">
                <div class="flex-shrink-0">
                    <div class="w-2 h-2 bg-red-400 rounded-full mt-2"></div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Améliorer l'endurance</p>
                    <p class="text-sm text-gray-500">Augmenter les sessions de cardio de 20%</p>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Progrès: 65%</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function loadFederationData() {
    // This would typically load data from the server
    console.log('Loading federation data...');
}

// Initialize federation dashboard by default
document.addEventListener('DOMContentLoaded', function() {
    changeDashboardLevel();
});
</script>
@endpush 