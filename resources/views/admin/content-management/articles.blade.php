@extends('layouts.app')

@section('title', 'Articles - Content Management')

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
                                <span class="text-white font-bold text-lg">📰</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Articles
                                </h1>
                                <p class="text-sm text-gray-600">Gérer les articles de presse et actualités</p>
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
            <h2 class="text-lg font-medium text-gray-900">Liste des Articles</h2>
            <a href="{{ route('admin.content-management.create', ['type' => 'article']) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                ➕ Nouvel Article
            </a>
        </div>

        <!-- Liste des articles -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($articles->count() > 0)
                    <div class="space-y-4">
                        @foreach($articles as $article)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $article['title'] }}</h3>
                                            @if($article['status'] === 'published')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Publié
                                                </span>
                                            @elseif($article['status'] === 'draft')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Brouillon
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Archivé
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-gray-600 mb-3">{{ $article['excerpt'] }}</p>
                                        
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><strong>Auteur:</strong> {{ $article['author'] }}</span>
                                            <span><strong>Créé:</strong> {{ $article['created_at']->format('d/m/Y H:i') }}</span>
                                            <span><strong>Vues:</strong> {{ $article['views'] }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="ml-6 flex-shrink-0">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.content-management.edit', ['id' => $article['id'], 'type' => 'article']) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                Modifier
                                            </a>
                                            <form action="{{ route('admin.content-management.destroy', $article['id']) }}" method="POST" class="inline">
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
                        <p class="text-gray-500 mb-4">Aucun article trouvé.</p>
                        <a href="{{ route('admin.content-management.create', ['type' => 'article']) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            ➕ Créer le premier article
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

