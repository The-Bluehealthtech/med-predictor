@extends('layouts.app')

@section('title', 'Annonces - Content Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-orange-600 to-red-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📢</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Annonces
                                </h1>
                                <p class="text-sm text-gray-600">Gérer les annonces officielles</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.content-management.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Content Management</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Actions -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-medium text-gray-900">Liste des Annonces</h2>
            <a href="{{ route('admin.content-management.create', ['type' => 'announcement']) }}" 
               class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                ➕ Nouvelle Annonce
            </a>
        </div>

        <!-- Liste des annonces -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($announcements->count() > 0)
                    <div class="space-y-4">
                        @foreach($announcements as $announcement)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $announcement['title'] }}</h3>
                                            @if($announcement['status'] === 'active')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @elseif($announcement['status'] === 'expired')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Expirée
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En attente
                                                </span>
                                            @endif
                                            
                                            @if($announcement['priority'] === 'high')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    🔴 Haute Priorité
                                                </span>
                                            @elseif($announcement['priority'] === 'medium')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    🟡 Moyenne Priorité
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    🟢 Basse Priorité
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-gray-600 mb-3">{{ Str::limit($announcement['content'], 150) }}</p>
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><strong>Créé:</strong> {{ $announcement['created_at']->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-6 flex-shrink-0">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.content-management.edit', ['id' => $announcement['id'], 'type' => 'announcement']) }}" 
                                               class="text-orange-600 hover:text-orange-900 text-sm">
                                                Modifier
                                            </a>
                                            <form action="{{ route('admin.content-management.destroy', $announcement['id']) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">Aucune annonce trouvée.</p>
                        <a href="{{ route('admin.content-management.create', ['type' => 'announcement']) }}" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                            ➕ Créer la première annonce
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


