@extends('layouts.app')

@section('title', 'Tableau de Bord Joueur - FIT')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tableau de Bord Joueur</h1>
                    <p class="text-gray-600 mt-2">Gérez votre profil, vos performances et votre santé</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm text-gray-500">FIFA Connect ID</div>
                        <div class="text-sm font-mono text-blue-600">{{ $player->fifa_connect_id ?? 'N/A' }}</div>
                    </div>
                    @if($player->player_picture_url && !empty($player->player_picture_url))
                        <img src="{{ $player->player_picture_url }}" 
                             alt="Photo du joueur" 
                             class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                    @else
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold text-lg">
                                {{ substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Score GHS Global -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Score GHS Global</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $player->ghs_overall_score ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Risque de Blessure -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-{{ $player->injury_risk_level === 'low' ? 'green' : ($player->injury_risk_level === 'medium' ? 'yellow' : 'red') }}-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-{{ $player->injury_risk_level === 'low' ? 'green' : ($player->injury_risk_level === 'medium' ? 'yellow' : 'red') }}-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-{{ $player->injury_risk_level === 'low' ? 'green' : ($player->injury_risk_level === 'medium' ? 'yellow' : 'red') }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Risque de Blessure</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ ucfirst($player->injury_risk_level ?? 'N/A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Matchs Contribués -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Matchs Contribués</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $player->matches_contributed ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Score de Contribution -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Score de Contribution</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $player->contribution_score ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sections Principales -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Profil et Santé -->
            <div class="space-y-6">
                <!-- Profil du Joueur -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil du Joueur
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Nom</p>
                            <p class="font-medium">{{ $player->first_name }} {{ $player->last_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Position</p>
                            <p class="font-medium">{{ $player->position ?? 'Non définie' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="font-medium">{{ $player->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Club</p>
                            <p class="font-medium">{{ $player->club->name ?? 'Non affilié' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Scores GHS Détaillés -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        Scores GHS Détaillés
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Physique</span>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $player->ghs_physical_score ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $player->ghs_physical_score ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Mental</span>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $player->ghs_mental_score ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $player->ghs_mental_score ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Civique</span>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $player->ghs_civic_score ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $player->ghs_civic_score ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Sommeil</span>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $player->ghs_sleep_score ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $player->ghs_sleep_score ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activités et Recommandations -->
            <div class="space-y-6">
                <!-- Activités Récentes -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Activités Récentes
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            <span>{{ $player->training_sessions_logged ?? 0 }} sessions d'entraînement</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <span>{{ $player->health_records_contributed ?? 0 }} dossiers médicaux</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                            <span>{{ $player->data_export_count ?? 0 }} exports de données</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                            <span>Dernière mise à jour: {{ $player->ghs_last_updated ? \Carbon\Carbon::parse($player->ghs_last_updated)->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recommandations IA -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Recommandations IA
                    </h3>
                    <div class="space-y-3">
                        @if(isset($player->ghs_ai_suggestions) && is_array($player->ghs_ai_suggestions))
                            @foreach(array_slice($player->ghs_ai_suggestions, 0, 3) as $suggestion)
                                <div class="flex items-start text-sm">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full mr-3 mt-2 flex-shrink-0"></div>
                                    <span class="text-gray-700">{{ $suggestion }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">Aucune recommandation disponible</p>
                        @endif
                    </div>
                </div>

                <!-- Conseils de Santé -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        Conseils de Santé Hebdomadaires
                    </h3>
                    <div class="space-y-3">
                        @if(isset($player->weekly_health_tips) && is_array($player->weekly_health_tips))
                            @foreach(array_slice($player->weekly_health_tips, 0, 3) as $tip)
                                <div class="flex items-start text-sm">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3 mt-2 flex-shrink-0"></div>
                                    <span class="text-gray-700">{{ $tip }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">Aucun conseil disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button class="flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Mettre à jour le profil
                    </button>
                    <button class="flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Voir les statistiques
                    </button>
                    <button class="flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Exporter les données
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 