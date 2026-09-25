@extends('layouts.app')

@section('title', 'PCMA List - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 PCMA Assessments</h1>
                    <p class="text-gray-600 mt-2">Liste des évaluations médicales pré-compétition</p>
                </div>
                <a href="{{ route('pcma.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    + Nouveau PCMA
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Filtres</h2>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complété</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échoué</option>
                    </select>
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les types</option>
                        <option value="cardio" {{ request('type') === 'cardio' ? 'selected' : '' }}>Cardiovasculaire</option>
                        <option value="neurological" {{ request('type') === 'neurological' ? 'selected' : '' }}>Neurologique</option>
                        <option value="musculoskeletal" {{ request('type') === 'musculoskeletal' ? 'selected' : '' }}>Musculo-squelettique</option>
                        <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>Général</option>
                    </select>
                </div>
                
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Nom de l'athlète..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- PCMAs List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">PCMA Assessments ({{ $pcmas->total() }})</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Athlète
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assesseur
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pcmas as $pcma)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-medium text-sm">
                                                    {{ substr($pcma->athlete->name ?? 'A', 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $pcma->athlete->name ?? 'Athlète inconnu' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($pcma->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $pcma->status === 'completed' ? 'bg-green-100 text-green-800' : ($pcma->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($pcma->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $pcma->assessor->name ?? 'Non assigné' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pcma->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('pcma.show', $pcma) }}" 
                                           class="text-blue-600 hover:text-blue-900">Voir</a>
                                        <a href="{{ route('pcma.edit', $pcma) }}" 
                                           class="text-gray-600 hover:text-gray-900">Modifier</a>
                                        <a href="{{ route('pcma.pdf', $pcma) }}" 
                                           class="text-green-600 hover:text-green-900">📄 PDF</a>
                                        @if($pcma->status === 'pending')
                                            <form method="POST" action="{{ route('pcma.complete', $pcma) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Compléter</button>
                                            </form>
                                            <form method="POST" action="{{ route('pcma.fail', $pcma) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Échouer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <div class="text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun PCMA trouvé</h3>
                                        <p class="text-gray-600 mb-4">Aucune évaluation PCMA ne correspond à vos critères de recherche.</p>
                                        <a href="{{ route('pcma.create') }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                            Créer un PCMA
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($pcmas->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pcmas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 