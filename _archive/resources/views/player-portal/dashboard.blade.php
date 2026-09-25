@extends('layouts.app')

@section('title', 'Portail Joueur - Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">⚽ Portail Joueur</h1>
                    <p class="text-gray-600 mt-2">Bienvenue, {{ $player->first_name ?? '' }} {{ $player->last_name ?? '' }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">{{ $player->club->name ?? 'Club non défini' }}</p>
                        <p class="text-sm text-gray-500">{{ $player->position ?? 'Position non définie' }}</p>
                    </div>
                    @if($player->club && $player->club->logo_url)
                        <img src="{{ $player->club->logo_url }}" alt="Logo club" class="w-12 h-12 object-contain">
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Score de santé -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">🏥 Score de Santé</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-{{ $stats['health_score'] >= 80 ? 'green' : ($stats['health_score'] >= 60 ? 'yellow' : 'red') }}-600">
                            {{ $stats['health_score'] }}%
                        </div>
                        <div class="text-sm text-gray-600 mt-2">Score de santé global</div>
                    </div>
                    <div class="flex-1 ml-8">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-{{ $stats['health_score'] >= 80 ? 'green' : ($stats['health_score'] >= 60 ? 'yellow' : 'red') }}-500 h-3 rounded-full" 
                                 style="width: {{ $stats['health_score'] }}%"></div>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            @if($stats['health_score'] >= 80)
                                Excellent état de santé
                            @elseif($stats['health_score'] >= 60)
                                Bon état de santé
                            @else
                                Attention requise
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-blue-600">📋</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Dossiers Médicaux</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_health_records'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-green-600">📊</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">PCMA</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_pcma'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="text-purple-600">🔮</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Prédictions</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_predictions'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <span class="text-orange-600">⚽</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Performances</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_performances'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Prochaine PCMA -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">📊 Prochaine PCMA</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 mb-2">
                            {{ $stats['next_pcma_due'] }}
                        </div>
                        <p class="text-sm text-gray-600">
                            @if(str_contains($stats['next_pcma_due'], 'retard'))
                                <span class="text-red-600">⚠️ PCMA en retard - Contactez votre médecin</span>
                            @elseif(str_contains($stats['next_pcma_due'], 'Aucune'))
                                <span class="text-yellow-600">⚠️ Aucune PCMA effectuée</span>
                            @else
                                <span class="text-green-600">✅ PCMA à jour</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dernier contrôle médical -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">🏥 Dernier Contrôle</h3>
                </div>
                <div class="p-6">
                    @if($stats['last_health_check'])
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 mb-2">
                                {{ $stats['last_health_check']->record_date->format('d/m/Y') }}
                            </div>
                            <p class="text-sm text-gray-600">
                                {{ $stats['last_health_check']->doctor_name ?? 'Médecin non spécifié' }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $stats['last_health_check']->diagnosis ?? 'Aucun diagnostic' }}
                            </p>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-400 mb-2">-</div>
                            <p class="text-sm text-gray-500">Aucun contrôle récent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Matchs récents -->
        @if(!empty($stats['recent_matches']))
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">⚽ Matchs Récents</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($stats['recent_matches'] as $match)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $match['home_team'] }} vs {{ $match['away_team'] }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $match['date'] }}</div>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $match['score'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">⚡ Actions Rapides</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('player-portal.profile') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-blue-600">👤</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Mon Profil</h4>
                            <p class="text-sm text-gray-500">Voir et modifier mes informations</p>
                        </div>
                    </a>

                    <a href="{{ route('player-portal.medical-records') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-green-600">📋</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Dossiers Médicaux</h4>
                            <p class="text-sm text-gray-500">Consulter mes dossiers médicaux</p>
                        </div>
                    </a>

                    <a href="{{ route('player-portal.predictions') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-purple-600">🔮</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Prédictions</h4>
                            <p class="text-sm text-gray-500">Voir mes prédictions médicales</p>
                        </div>
                    </a>

                    <a href="{{ route('player-portal.performances') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-orange-600">📈</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Performances</h4>
                            <p class="text-sm text-gray-500">Suivre mes performances</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
