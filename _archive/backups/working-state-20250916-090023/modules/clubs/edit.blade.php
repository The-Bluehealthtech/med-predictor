<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier {{ $club->name }} - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                ✏️ Modifier le Club
            </h1>
            <p class="text-lg text-gray-600">
                Modifier les informations de {{ $club->name }}
            </p>
        </div>

        <!-- Navigation -->
        <div class="mb-8 flex justify-between items-center">
            <a href="/clubs-view" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                ← Retour aux clubs
            </a>
            
            <a href="/modules" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                📋 Retour aux modules
            </a>
        </div>

        <!-- Messages de succès/erreur -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Formulaire d'édition -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form method="POST" action="{{ route('clubs-view.update', $club->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informations de base -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom du club *</label>
                            <input type="text" name="name" value="{{ old('name', $club->name) }}" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent {{ $errors->has('name') ? 'border-red-500' : '' }}">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom abrégé</label>
                            <input type="text" name="short_name" value="{{ old('short_name', $club->short_name) }}" 
                                   placeholder="Ex: EST, CA, etc." maxlength="50"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Nom court utilisé pour l'affichage</p>
                        </div>
                    </div>
                    
                    <!-- Localisation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                            <input type="text" name="country" value="Tunisie" readonly 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                            <input type="text" name="city" value="{{ $club->city ?? 'N/A' }}" readonly 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                        </div>
                    </div>
                    
                    <!-- Contact -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                            <textarea name="address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $club->address }}</textarea>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                <input type="tel" name="phone" value="{{ $club->phone }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ $club->email }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations supplémentaires -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Site web</label>
                            <input type="url" name="website" value="{{ $club->website }}" 
                                   placeholder="https://..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Année de fondation</label>
                            <input type="number" name="founded_year" value="{{ $club->founded_year }}" 
                                   min="1800" max="2030" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Statut -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" {{ $club->status === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ $club->status === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="pending" {{ $club->status === 'pending' ? 'selected' : '' }}>En attente</option>
                        </select>
                    </div>
                    
                    <!-- Logo du club -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo du club</label>
                        <div class="flex items-center space-x-4">
                            @if($club->logo_path)
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $club->logo_path) }}" 
                                         alt="Logo actuel" 
                                         class="h-20 w-20 object-contain rounded-lg border-2 border-gray-200">
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" name="logo" accept="image/*" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-sm text-gray-500 mt-1">Laissez vide pour conserver le logo actuel</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="flex space-x-4 pt-6 border-t border-gray-200">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            💾 Sauvegarder les modifications
                        </button>
                        <a href="{{ route('clubs-view.show', ['id' => $club->id]) }}" 
                           class="flex-1 px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors text-center">
                            ❌ Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
