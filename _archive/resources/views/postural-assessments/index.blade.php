@extends('layouts.app')

@section('title', 'Évaluations Posturales - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📊 Évaluations Posturales</h1>
                    <p class="text-gray-600 mt-2">Gestion des analyses posturales des joueurs</p>
                </div>
                <a href="{{ route('postural-assessments.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouvelle Évaluation
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">🔍 Filtres</h2>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="player_filter" class="block text-sm font-medium text-gray-700 mb-2">Joueur</label>
                    <select name="player_id" id="player_filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Tous les joueurs</option>
                        @foreach($players ?? [] as $player)
                            <option value="{{ $player->id }}" {{ request('player_id') == $player->id ? 'selected' : '' }}>
                                {{ $player->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="type_filter" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="assessment_type" id="type_filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        <option value="">Tous les types</option>
                        <option value="routine" {{ request('assessment_type') == 'routine' ? 'selected' : '' }}>Routine</option>
                        <option value="injury" {{ request('assessment_type') == 'injury' ? 'selected' : '' }}>Blessure</option>
                        <option value="follow_up" {{ request('assessment_type') == 'follow_up' ? 'selected' : '' }}>Suivi</option>
                    </select>
                </div>
                
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input type="date" name="date_from" id="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                    <input type="date" name="date_to" id="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                
                <div class="md:col-span-4 flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        🔍 Filtrer
                    </button>
                    <a href="{{ route('postural-assessments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        🗑️ Effacer
                    </a>
                </div>
            </form>
        </div>

        <!-- Assessments List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">📋 Liste des Évaluations</h2>
            </div>
            
            @if($assessments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joueur
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vue
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Clinicien
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Résumé
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assessments as $assessment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($assessment->player->player_picture)
                                                    <img class="h-10 w-10 rounded-full" src="{{ $assessment->player->player_picture_url }}" alt="{{ $assessment->player->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ substr($assessment->player->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $assessment->player->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $assessment->player->position }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($assessment->assessment_type === 'routine') bg-green-100 text-green-800
                                            @elseif($assessment->assessment_type === 'injury') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ $assessment->type_label }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $assessment->view_label }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $assessment->clinician->name }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $assessment->formatted_date }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php $summary = $assessment->summary; @endphp
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center space-x-2">
                                                @if($summary['total_markers'] > 0)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs bg-red-100 text-red-800 rounded">
                                                        {{ $summary['total_markers'] }} marqueurs
                                                    </span>
                                                @endif
                                                @if($summary['total_angles'] > 0)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                                                        {{ $summary['total_angles'] }} angles
                                                    </span>
                                                @endif
                                                @if($summary['has_clinical_notes'])
                                                    <span class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                        Notes
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('postural-assessments.show', $assessment) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                👁️ Voir
                                            </a>
                                            <a href="{{ route('postural-assessments.edit', $assessment) }}" 
                                               class="text-green-600 hover:text-green-900">
                                                ✏️ Modifier
                                            </a>
                                            <form action="{{ route('postural-assessments.destroy', $assessment) }}" 
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?')">
                                                    🗑️ Supprimer
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
                    {{ $assessments->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune évaluation trouvée</h3>
                    <p class="text-gray-500">Aucune évaluation posturale ne correspond à vos critères de recherche.</p>
                    <div class="mt-6">
                        <a href="{{ route('postural-assessments.create') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Créer la première évaluation
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 