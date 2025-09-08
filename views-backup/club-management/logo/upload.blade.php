@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">🏟️ Gestion du Logo</h1>
                <a href="{{ route('joueur.portal', request()->query('player_id', 1)) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    ← Retour au Portail
                </a>
            </div>

            <!-- Club Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-blue-800 mb-2">{{ $club->name }}</h2>
                <p class="text-blue-600">{{ $club->country ?? 'Pays non défini' }}</p>
                
                @if($club->logo_url)
                    <div class="mt-3">
                        <p class="text-sm text-blue-600 mb-2">Logo actuel :</p>
                        <img src="{{ $club->logo_url }}" 
                             alt="Logo {{ $club->name }}" 
                             class="w-20 h-20 object-contain rounded-lg border border-blue-200">
                    </div>
                @else
                    <div class="mt-3">
                        <p class="text-sm text-blue-600 mb-2">Aucun logo actuellement</p>
                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 text-xs">
                            {{ strtoupper(substr($club->name, 0, 2)) }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Upload Form -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📤 Uploader un nouveau logo</h3>
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('club.logo.store', $club) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                            Sélectionner un fichier image
                        </label>
                        <input type="file" 
                               id="logo" 
                               name="logo" 
                               accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                               required>
                        <p class="mt-1 text-sm text-gray-500">
                            Formats acceptés : JPEG, PNG, JPG, GIF, SVG (max 2MB)
                        </p>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                            📤 Uploader le logo
                        </button>
                        
                        <a href="{{ route('joueur.portal', request()->query('player_id', 1)) }}" 
                           class="text-gray-600 hover:text-gray-800">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>

            <!-- Instructions -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-yellow-800 mb-2">💡 Instructions</h4>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Utilisez des images de haute qualité (recommandé : 200x200px minimum)</li>
                    <li>• Les formats SVG sont recommandés pour une meilleure qualité</li>
                    <li>• Le logo sera automatiquement redimensionné si nécessaire</li>
                    <li>• L'ancien logo sera remplacé par le nouveau</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
