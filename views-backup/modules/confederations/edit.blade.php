<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier {{ $confederation->name }} - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="/confederations-view/show?id={{ $confederation->id }}" class="flex items-center text-purple-600 hover:text-purple-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour aux détails
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Modifier la confédération</h1>
            </div>
            <a href="/confederations-view" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                🏠 Retour à la liste
            </a>
        </div>

        <!-- Messages d'erreur/succès -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Formulaire d'édition -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="/confederations-view/update/{{ $confederation->id }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Colonne gauche - Informations de base -->
                    <div class="space-y-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 Informations de base</h2>

                        <!-- Nom de la confédération -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom de la confédération *</label>
                            <input type="text" name="name" value="{{ old('name', $confederation->name) }}" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('name') ? 'border-red-500' : '' }}">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nom abrégé -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom abrégé *</label>
                            <input type="text" name="short_name" value="{{ old('short_name', $confederation->short_name) }}" required 
                                   placeholder="Ex: CAF, UEFA, CONMEBOL" maxlength="10"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('short_name') ? 'border-red-500' : '' }}">
                            @error('short_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Continent/Pays -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Continent/Pays *</label>
                            <input type="text" name="country" value="{{ old('country', $confederation->country) }}" required 
                                   placeholder="Ex: Afrique, Europe, Amérique du Sud"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('country') ? 'border-red-500' : '' }}">
                            @error('country')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Année de fondation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Année de fondation</label>
                            <input type="number" name="founded_year" value="{{ old('founded_year', $confederation->founded_year) }}" 
                                   placeholder="Ex: 1957" min="1800" max="{{ date('Y') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('founded_year') ? 'border-red-500' : '' }}">
                            @error('founded_year')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut *</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('status') ? 'border-red-500' : '' }}">
                                <option value="active" {{ old('status', $confederation->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $confederation->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $confederation->status) === 'suspended' ? 'selected' : '' }}>Suspendue</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Colonne droite - FIFA Connect et Logo -->
                    <div class="space-y-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">⚽ FIFA Connect</h2>

                        <!-- Classement FIFA -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Classement FIFA</label>
                            <input type="text" name="fifa_ranking" value="{{ old('fifa_ranking', $confederation->fifa_ranking) }}" 
                                   placeholder="Ex: 1, 2, 3"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('fifa_ranking') ? 'border-red-500' : '' }}">
                            @error('fifa_ranking')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Version FIFA -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Version FIFA</label>
                            <input type="text" name="fifa_version" value="{{ old('fifa_version', $confederation->fifa_version) }}" 
                                   placeholder="Ex: FIFA 24, FIFA 23"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('fifa_version') ? 'border-red-500' : '' }}">
                            @error('fifa_version')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Logo de la confédération -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo de la confédération</label>
                            
                            <!-- Logo actuel -->
                            @if($confederation->confederation_logo_url)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Logo actuel :</p>
                                    <img src="{{ asset('storage/' . $confederation->confederation_logo_url) }}" 
                                         alt="Logo actuel" 
                                         class="h-20 w-20 object-contain rounded-lg border-2 border-gray-200">
                                </div>
                            @endif

                            <!-- Upload du nouveau logo -->
                            <input type="file" name="confederation_logo" accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent {{ $errors->has('confederation_logo') ? 'border-red-500' : '' }}">
                            <p class="text-xs text-gray-500 mt-1">Formats acceptés : JPEG, PNG, JPG, GIF (max 2MB)</p>
                            @error('confederation_logo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Statut FIFA actuel -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Statut FIFA actuel</h3>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Synchronisation :</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $confederation->fifa_sync_status === 'synced' ? 'bg-green-100 text-green-800' : ($confederation->fifa_sync_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $confederation->fifa_sync_status }}
                                    </span>
                                </div>
                                @if($confederation->fifa_sync_date)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Dernière sync :</span>
                                    <span class="text-sm font-medium text-gray-800">{{ $confederation->fifa_sync_date->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                                @if($confederation->fifa_last_error)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Dernière erreur :</span>
                                    <span class="text-sm text-red-600">{{ $confederation->fifa_last_error }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="/confederations-view/show?id={{ $confederation->id }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        💾 Sauvegarder les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>





