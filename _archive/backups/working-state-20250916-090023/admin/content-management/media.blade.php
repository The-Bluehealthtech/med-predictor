@extends('layouts.app')

@section('title', 'Médias - Content Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🎬</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Médias
                                </h1>
                                <p class="text-sm text-gray-600">Gérer les fichiers multimédias</p>
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
            <h2 class="text-lg font-medium text-gray-900">Bibliothèque Médias</h2>
            <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                📁 Uploader des Fichiers
            </button>
        </div>

        <!-- Liste des médias -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($media->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($media as $file)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center space-x-3 mb-3">
                                    @if($file['type'] === 'image')
                                        <span class="text-2xl">🖼️</span>
                                    @elseif($file['type'] === 'video')
                                        <span class="text-2xl">🎥</span>
                                    @else
                                        <span class="text-2xl">📄</span>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">{{ $file['name'] }}</h3>
                                        <p class="text-xs text-gray-500">{{ $file['size'] }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                    <span>{{ $file['uploaded_at']->format('d/m/Y') }}</span>
                                    <span class="capitalize">{{ $file['type'] }}</span>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ $file['url'] }}" target="_blank" 
                                       class="text-purple-600 hover:text-purple-900 text-xs">
                                        Voir
                                    </a>
                                    <button class="text-gray-600 hover:text-gray-900 text-xs">
                                        Modifier
                                    </button>
                                    <button class="text-red-600 hover:text-red-900 text-xs">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">Aucun fichier média trouvé.</p>
                        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                            📁 Uploader le premier fichier
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


