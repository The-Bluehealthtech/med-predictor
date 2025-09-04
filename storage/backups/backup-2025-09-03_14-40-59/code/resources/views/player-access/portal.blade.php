@extends('layouts.app')

@section('title', 'Portail Personnel - ' . $player->first_name . ' ' . $player->last_name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">⚽</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Portail Personnel FIFA
                        </h1>
                        <p class="text-sm text-gray-600">Bienvenue sur votre espace personnel</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Connecté en tant que</span>
                    <div class="flex items-center space-x-2">
                        @if($player->player_picture)
                            <img src="{{ $player->player_picture }}" alt="{{ $player->first_name }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">{{ substr($player->first_name, 0, 1) }}{{ substr($player->last_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="font-medium text-gray-900">{{ $player->first_name }} {{ $player->last_name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                            🚪 Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Message -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">🎉 Bienvenue {{ $player->first_name }} !</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Vous êtes maintenant connecté à votre portail personnel FIFA Connect
                    </p>
                    <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        ✅ Connexion réussie
                    </div>
                </div>
            </div>
        </div>

        <!-- Player Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Personal Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        👤 Informations Personnelles
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nom complet</span>
                            <span class="font-medium">{{ $portalData['personalInfo']['name'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Position</span>
                            <span class="font-medium">{{ $portalData['personalInfo']['position'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Club</span>
                            <span class="font-medium">{{ $portalData['personalInfo']['club'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nationalité</span>
                            <span class="font-medium">{{ $portalData['personalInfo']['nationality'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Âge</span>
                            <span class="font-medium">{{ $portalData['personalInfo']['age'] }} ans</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FIFA Ratings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        ⭐ Notes FIFA
                    </h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-1">{{ $portalData['personalInfo']['overall_rating'] }}</div>
                            <div class="text-sm text-gray-600">Note Globale</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 mb-1">{{ $portalData['personalInfo']['potential_rating'] }}</div>
                            <div class="text-sm text-gray-600">Potentiel</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Overview -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        ❤️ Santé Globale
                    </h3>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 mb-2">{{ $portalData['healthMetrics']['ghs_overall_score'] }}/100</div>
                        <div class="text-sm text-gray-600">Score GHS</div>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $portalData['healthMetrics']['injury_risk_level'] === 'Faible' ? 'bg-green-100 text-green-800' : 
                                   ($portalData['healthMetrics']['injury_risk_level'] === 'Moyen' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                Risque de blessure: {{ $portalData['healthMetrics']['injury_risk_level'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-blue-600 mb-2">{{ $portalData['performanceStats']['total_matches'] }}</div>
                <div class="text-sm text-gray-600">Matchs Joués</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-green-600 mb-2">{{ $portalData['performanceStats']['total_health_records'] }}</div>
                <div class="text-sm text-gray-600">Bilans Santé</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-purple-600 mb-2">{{ $portalData['performanceStats']['total_pcma'] }}</div>
                <div class="text-sm text-gray-600">PCMA</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-orange-600 mb-2">{{ $portalData['performanceStats']['contribution_score'] }}</div>
                <div class="text-sm text-gray-600">Score Contribution</div>
            </div>
        </div>

        <!-- Access to Full Portal -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-8 text-center">
            <div class="max-w-2xl mx-auto">
                <h3 class="text-2xl font-bold text-white mb-4">🚀 Accédez à Votre Portail Complet</h3>
                <p class="text-blue-100 mb-6">
                    Découvrez toutes vos données, performances, bilans de santé et plus encore dans votre portail FIFA complet
                </p>
                <a 
                    href="/portail-joueur" 
                    class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 transition-all duration-200 transform hover:scale-105"
                >
                    🎯 Ouvrir le Portail Complet
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    📅 Activité Récente
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-2">Dernier Bilan Santé</div>
                        <div class="font-medium">
                            @if($portalData['recentActivity']['last_health_check'])
                                {{ $portalData['recentActivity']['last_health_check']->format('d/m/Y') }}
                            @else
                                Aucun
                            @endif
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-2">Dernier Match</div>
                        <div class="font-medium">
                            @if($portalData['recentActivity']['last_match'])
                                {{ $portalData['recentActivity']['last_match']->format('d/m/Y') }}
                            @else
                                Aucun
                            @endif
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-2">Dernier PCMA</div>
                        <div class="font-medium">
                            @if($portalData['recentActivity']['last_pcma'])
                                {{ $portalData['recentActivity']['last_pcma']->format('d/m/Y') }}
                            @else
                                Aucun
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




