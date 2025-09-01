<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $confederation->name }} - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="/confederations-view" class="flex items-center text-purple-600 hover:text-purple-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour aux confédérations
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Détails de la Confédération</h1>
            </div>
            <div class="flex space-x-3">
                <a href="/confederations-view/edit/{{ $confederation->id }}" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    ✏️ Modifier
                </a>
                <a href="/modules" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    🏠 Modules
                </a>
            </div>
        </div>

        <!-- Confederation Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header de la confédération -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-2xl font-bold text-gray-800">{{ $confederation->name }}</h2>
                                @if($confederation->short_name)
                                <span class="text-lg font-medium text-purple-600 bg-purple-100 px-3 py-1 rounded-full">
                                    {{ $confederation->short_name }}
                                </span>
                                @endif
                            </div>
                            <p class="text-lg text-gray-600">{{ $confederation->country }} - Confédération continentale</p>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 rounded-full text-sm font-medium {{ $confederation->status === 'active' ? 'bg-green-100 text-green-800' : ($confederation->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $confederation->status === 'active' ? 'Active' : ($confederation->status === 'inactive' ? 'Inactive' : 'Suspendue') }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Logo de la confédération -->
                    <div class="flex justify-center mb-6">
                        @if($confederation->confederation_logo_url)
                            <img src="{{ asset('storage/' . $confederation->confederation_logo_url) }}" 
                                 alt="Logo {{ $confederation->name }}" 
                                 class="h-32 w-32 object-contain rounded-lg border-2 border-gray-200">
                        @else
                            <div class="h-32 w-32 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-4xl">{{ $confederation->short_name }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Informations de base -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-purple-600">🌍</span>
                            <div>
                                <p class="text-sm text-gray-500">Continent</p>
                                <p class="font-medium">{{ $confederation->country }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-purple-600">🏆</span>
                            <div>
                                <p class="text-sm text-gray-500">Classement FIFA</p>
                                <p class="font-medium">{{ $confederation->fifa_ranking ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-purple-600">🏛️</span>
                            <div>
                                <p class="text-sm text-gray-500">Associations affiliées</p>
                                <p class="font-medium">{{ $confederation->associations->count() }}</p>
                            </div>
                        </div>
                        @if($confederation->founded_year)
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-purple-600">📅</span>
                            <div>
                                <p class="text-sm text-gray-500">Année de fondation</p>
                                <p class="font-medium">{{ $confederation->founded_year }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Informations FIFA Connect -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">⚽</span>
                        Informations FIFA Connect
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-medium text-purple-800 mb-3">Statut de synchronisation</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-purple-700">Statut:</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $confederation->fifa_sync_status === 'synced' ? 'bg-green-100 text-green-800' : ($confederation->fifa_sync_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $confederation->fifa_sync_status }}
                                    </span>
                                </div>
                                @if($confederation->fifa_version)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-purple-700">Version FIFA:</span>
                                    <span class="font-medium text-purple-800">{{ $confederation->fifa_version }}</span>
                                </div>
                                @endif
                                @if($confederation->fifa_sync_date)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-purple-700">Dernière sync:</span>
                                    <span class="font-medium text-purple-800">{{ $confederation->fifa_sync_date->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-3">Actions FIFA</h4>
                            <div class="space-y-2">
                                <button onclick="syncWithFifa({{ $confederation->id }})" 
                                        class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    🔄 Synchroniser avec FIFA
                                </button>
                                <a href="/fifa/dashboard?confederation_id={{ $confederation->id }}" 
                                   class="block w-full px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors text-center">
                                    📊 Voir statistiques FIFA
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Associations affiliées -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">🏛️</span>
                        Associations affiliées ({{ $confederation->associations->count() }})
                    </h3>
                    
                    @if($confederation->associations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($confederation->associations as $association)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center space-x-3">
                                    @if($association->association_logo_url)
                                        <img src="{{ asset('storage/' . $association->association_logo_url) }}" 
                                             alt="Logo {{ $association->name }}" 
                                             class="h-12 w-12 object-contain rounded-lg">
                                    @else
                                        <div class="h-12 w-12 bg-gradient-to-br from-red-500 to-yellow-600 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ substr($association->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-800">{{ $association->name }}</h4>
                                        @if($association->short_name)
                                        <p class="text-sm text-red-600">{{ $association->short_name }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">{{ $association->country }}</p>
                                    </div>
                                    <a href="/associations-view/show/{{ $association->id }}" 
                                       class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-lg hover:bg-blue-200 transition-colors">
                                        Voir →
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="/associations-view?confederation_id={{ $confederation->id }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <span class="mr-2">🏛️</span>
                                Voir toutes les associations de cette confédération
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-4xl mb-4">🏛️</div>
                            <h4 class="text-lg font-medium text-gray-600 mb-2">Aucune association affiliée</h4>
                            <p class="text-gray-500">Cette confédération n'a pas encore d'associations affiliées.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistiques rapides -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Statistiques</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Associations</span>
                            <span class="font-bold text-purple-600">{{ $confederation->associations->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Clubs totaux</span>
                            <span class="font-bold text-blue-600">{{ $confederation->associations->sum(function($assoc) { return $assoc->clubs->count(); }) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Joueurs totaux</span>
                            <span class="font-bold text-green-600">{{ $confederation->associations->sum(function($assoc) { return $assoc->players->count(); }) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Actions rapides</h3>
                    <div class="space-y-3">
                        <a href="/associations-view?confederation_id={{ $confederation->id }}" 
                           class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700 transition-colors">
                            🏛️ Gérer les associations
                        </a>
                        <a href="/clubs-view?confederation_id={{ $confederation->id }}" 
                           class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors">
                            🏟️ Voir tous les clubs
                        </a>
                        <a href="/modules/licenses?confederation_id={{ $confederation->id }}" 
                           class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition-colors">
                            📋 Licences de la confédération
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function syncWithFifa(confederationId) {
            const button = event.target;
            const originalText = button.innerHTML;
            
            // Désactiver le bouton et montrer le chargement
            button.disabled = true;
            button.innerHTML = '⏳ Synchronisation...';
            button.classList.add('opacity-50');

            // Appel AJAX pour la synchronisation
            fetch(`/confederations-view/sync/${confederationId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Succès
                    button.innerHTML = '✅ Synchronisé !';
                    button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    button.classList.add('bg-green-600');
                    
                    // Recharger la page après 2 secondes
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    // Erreur
                    button.innerHTML = '❌ Erreur';
                    button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    button.classList.add('bg-red-600');
                    
                    // Restaurer après 3 secondes
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = originalText;
                        button.classList.remove('bg-red-600', 'opacity-50');
                        button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                button.innerHTML = '❌ Erreur réseau';
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-red-600');
                
                // Restaurer après 3 secondes
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalText;
                    button.classList.remove('bg-red-600', 'opacity-50');
                    button.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 3000);
            });
        }
    </script>
</body>
</html>
