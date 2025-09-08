@extends('layouts.app')

@section('title', 'Prédictions Médicales - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">🔮 Prédictions Médicales</h1>
                    <p class="text-gray-600 mt-2">Gestion et analyse des prédictions médicales des joueurs</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('medical-predictions.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Nouvelle Prédiction</span>
                    </a>
                    <a href="{{ route('medical-predictions.dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Tableau de Bord</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('medical-predictions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Joueur, condition..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les types</option>
                        <option value="injury_risk" {{ request('type') == 'injury_risk' ? 'selected' : '' }}>Risque de Blessure</option>
                        <option value="performance_prediction" {{ request('type') == 'performance_prediction' ? 'selected' : '' }}>Prédiction de Performance</option>
                        <option value="health_condition" {{ request('type') == 'health_condition' ? 'selected' : '' }}>État de Santé</option>
                        <option value="recovery_prediction" {{ request('type') == 'recovery_prediction' ? 'selected' : '' }}>Prédiction de Récupération</option>
                        <option value="fitness_assessment" {{ request('type') == 'fitness_assessment' ? 'selected' : '' }}>Évaluation de Forme</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expiré</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Vérifié</option>
                        <option value="false_positive" {{ request('status') == 'false_positive' ? 'selected' : '' }}>Faux Positif</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Predictions List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Liste des Prédictions ({{ $predictions->total() }})</h2>
            </div>
            
            @if($predictions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risque</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confiance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($predictions as $prediction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($prediction->player)
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($prediction->player->player_face_url)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $prediction->player->player_face_url }}" alt="{{ $prediction->player->full_name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">{{ substr($prediction->player->first_name, 0, 1) }}{{ substr($prediction->player->last_name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $prediction->player->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $prediction->player->position }} - {{ $prediction->player->club->name ?? 'N/A' }}</div>
                                                </div>
                                            @else
                                                <span class="text-gray-500">Joueur supprimé</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $prediction->prediction_type == 'injury_risk' ? 'bg-red-100 text-red-800' : 
                                               ($prediction->prediction_type == 'performance_prediction' ? 'bg-blue-100 text-blue-800' : 
                                               ($prediction->prediction_type == 'health_condition' ? 'bg-green-100 text-green-800' : 
                                               ($prediction->prediction_type == 'recovery_prediction' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800'))) }}">
                                            @switch($prediction->prediction_type)
                                                @case('injury_risk')
                                                    Risque de Blessure
                                                    @break
                                                @case('performance_prediction')
                                                    Performance
                                                    @break
                                                @case('health_condition')
                                                    État de Santé
                                                    @break
                                                @case('recovery_prediction')
                                                    Récupération
                                                    @break
                                                @case('fitness_assessment')
                                                    Forme
                                                    @break
                                                @default
                                                    {{ $prediction->prediction_type }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $prediction->predicted_condition }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-{{ $prediction->risk_probability > 0.7 ? 'red' : ($prediction->risk_probability > 0.4 ? 'yellow' : 'green') }}-500 h-2 rounded-full" 
                                                     style="width: {{ $prediction->risk_probability * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ round($prediction->risk_probability * 100) }}%</span>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $prediction->risk_level }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-{{ $prediction->confidence_score > 0.8 ? 'green' : ($prediction->confidence_score > 0.6 ? 'yellow' : 'red') }}-500 h-2 rounded-full" 
                                                     style="width: {{ $prediction->confidence_score * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ round($prediction->confidence_score * 100) }}%</span>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $prediction->confidence_level }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $prediction->status == 'active' ? 'bg-green-100 text-green-800' : 
                                               ($prediction->status == 'expired' ? 'bg-red-100 text-red-800' : 
                                               ($prediction->status == 'verified' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                            @switch($prediction->status)
                                                @case('active')
                                                    Actif
                                                    @break
                                                @case('expired')
                                                    Expiré
                                                    @break
                                                @case('verified')
                                                    Vérifié
                                                    @break
                                                @case('false_positive')
                                                    Faux Positif
                                                    @break
                                                @default
                                                    {{ $prediction->status }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $prediction->prediction_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('medical-predictions.show', $prediction) }}" 
                                               class="text-blue-600 hover:text-blue-900">Voir</a>
                                            <a href="{{ route('medical-predictions.edit', $prediction) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                            <form action="{{ route('medical-predictions.destroy', $prediction) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette prédiction ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $predictions->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune prédiction trouvée</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle prédiction médicale.</p>
                    <div class="mt-6">
                        <a href="{{ route('medical-predictions.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nouvelle Prédiction
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 