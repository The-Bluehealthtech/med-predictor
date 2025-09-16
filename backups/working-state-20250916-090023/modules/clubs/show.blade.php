 !DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
page    <title>{{ $club->name }} - Détails du Club - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="/clubs-view" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour aux clubs
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Détails du Club</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('clubs-view.edit', $club->id) }}" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    ✏️ Modifier
                </a>
                <a href="/modules" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    📋 Retour aux modules
                </a>
            </div>
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

        <!-- Club Details -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Header du club -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                                                <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $club->name }}</h2>
                        @if($club->short_name)
                            <span class="text-lg font-medium text-blue-600 bg-blue-100 px-3 py-1 rounded-full">
                                {{ $club->short_name }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Nom abrégé du club -->
                    @if($club->short_name)
                    <div class="mb-3">
                        <span class="text-sm text-gray-500">Nom abrégé :</span>
                        <span class="ml-2 text-lg font-semibold text-blue-700">{{ $club->short_name }}</span>
                    </div>
                    @endif
                            <p class="text-lg text-gray-600">{{ $club->city }}, {{ $club->country }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 rounded-full text-sm font-medium {{ $club->status === 'active' ? 'bg-green-100 text-green-800' : ($club->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $club->status === 'active' ? 'Actif' : ($club->status === 'inactive' ? 'Inactif' : 'En attente') }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Informations de base -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        @if($club->founded_year)
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-blue-600">📅</span>
                            <div>
                                <p class="text-sm text-gray-500">Fondé en</p>
                                <p class="font-medium">{{ $club->founded_year }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($club->players && $club->players->count() > 0)
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-blue-600">👥</span>
                            <div>
                                <p class="text-sm text-gray-500">Joueurs</p>
                                <p class="font-medium">{{ $club->players->count() }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Hiérarchie CONFÉDÉRATION → ASSOCIATION → CLUB -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                            <span class="mr-2">🌍</span>
                            Affiliation
                        </h4>
                        <div class="space-y-2">
                            @if($club->confederation)
                            <div class="flex items-center">
                                <span class="w-5 h-5 mr-3 text-blue-600">🏆</span>
                                <div>
                                    <p class="text-sm text-gray-500">Confédération</p>
                                    <p class="font-medium text-blue-700">{{ $club->confederation->name }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($club->association)
                            <div class="flex items-center">
                                <span class="w-5 h-5 mr-3 text-blue-600">🏛️</span>
                                <div>
                                    <p class="text-sm text-gray-500">Association</p>
                                    <p class="font-medium text-blue-700">{{ $club->association->name }}</p>
                                </div>
                            </div>
                            @endif
                            
                            <div class="flex items-center">
                                <span class="w-5 h-5 mr-3 text-blue-600">🏟️</span>
                                <div>
                                    <p class="text-sm text-gray-500">Club</p>
                                    <p class="font-medium text-blue-700">{{ $club->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">📞</span>
                        Informations de contact
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($club->address)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-3">Adresse</h4>
                            <p class="text-gray-600">{{ $club->address }}</p>
                        </div>
                        @endif
                        
                        @if($club->phone)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-3">Téléphone</h4>
                            <p class="text-gray-600">{{ $club->phone }}</p>
                        </div>
                        @endif
                        
                        @if($club->email)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-3">Email</h4>
                            <p class="text-gray-600">{{ $club->email }}</p>
                        </div>
                        @endif
                        
                        @if($club->website)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-3">Site web</h4>
                            <a href="{{ $club->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                {{ $club->website }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Association affiliée -->
                @if($club->association)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">🏛️</span>
                        Association affiliée
                    </h3>
                    
                    <div class="flex items-center space-x-4">
                        @if($club->association->association_logo_url)
                            <img src="{{ asset('storage/' . $club->association->association_logo_url) }}" 
                                 alt="Logo {{ $club->association->name }}" 
                                 class="h-16 w-16 object-contain rounded-lg border-2 border-gray-200">
                        @elseif($club->association->logo_path)
                            <img src="{{ asset('storage/' . $club->association->logo_path) }}" 
                                 alt="Logo {{ $club->association->name }}" 
                                 class="h-16 w-16 object-contain rounded-lg border-2 border-gray-200">
                        @else
                            <div class="h-16 w-16 bg-gradient-to-br from-red-500 to-yellow-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-xl">{{ substr($club->association->name, 0, 2) }}</span>
                            </div>
                        @endif
                        
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">{{ $club->association->name }}</h4>
                            @if($club->association->short_name)
                                <p class="text-sm font-medium text-red-600 bg-red-100 px-2 py-1 rounded mb-1">{{ $club->association->short_name }}</p>
                            @endif
                            <p class="text-gray-600">{{ $club->association->country ?? 'Tunisie' }}</p>
                            @if($club->association->confederation)
                                <p class="text-xs text-gray-500">{{ $club->association->confederation }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($club->association->fifa_ranking)
                                <div class="text-center">
                                    <div class="text-lg font-bold text-yellow-600">{{ $club->association->fifa_ranking }}</div>
                                    <div class="text-xs text-gray-500">Classement FIFA</div>
                                </div>
                                @endif
                                @if($club->association->fifa_sync_status)
                                <div class="text-center">
                                    <div class="text-sm font-medium {{ $club->association->fifa_sync_status === 'synced' ? 'text-green-600' : ($club->association->fifa_sync_status === 'failed' ? 'text-red-600' : 'text-yellow-600') }}">
                                        {{ $club->association->fifa_sync_status }}
                                    </div>
                                    <div class="text-xs text-gray-500">Statut FIFA</div>
                                </div>
                                @endif
                            </div>
                            <a href="/associations-view" class="text-sm text-blue-600 hover:text-blue-800 underline">
                                Voir l'association →
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <!-- Section des équipes -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Équipes du club</h3>
                        <a href="/modules/teams/create?club_id={{ $club->id }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-plus mr-1"></i>
                            Ajouter une équipe
                        </a>
                    </div>
                    
                    @if($club->teams && $club->teams->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($club->teams as $team)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-900 text-lg">{{ $team->name }}</h4>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($team->status === 'active') bg-green-100 text-green-800
                                            @elseif($team->status === 'inactive') bg-red-100 text-red-800
                                            @elseif($team->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($team->status ?? 'N/A') }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between py-1">
                                            <span class="text-gray-600 flex items-center">
                                                <i class="fas fa-trophy w-4 h-4 mr-2 text-yellow-500"></i>
                                                Niveau:
                                            </span>
                                            <span class="font-medium text-gray-900">{{ ucfirst($team->level ?? 'N/A') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between py-1">
                                            <span class="text-gray-600 flex items-center">
                                                <i class="fas fa-calendar w-4 h-4 mr-2 text-blue-500"></i>
                                                Catégorie:
                                            </span>
                                            <span class="font-medium text-gray-900">{{ $team->age_category ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between py-1">
                                            <span class="text-gray-600 flex items-center">
                                                <i class="fas fa-futbol w-4 h-4 mr-2 text-green-500"></i>
                                                Discipline:
                                            </span>
                                            <span class="font-medium text-gray-900">{{ ucfirst($team->discipline ?? 'N/A') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex space-x-2">
                                        <a href="/modules/teams/{{ $team->id }}" 
                                           class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-2 rounded text-sm text-center transition-colors">
                                            <i class="fas fa-eye mr-1"></i>
                                            Voir
                                        </a>
                                        <a href="/modules/teams/{{ $team->id }}/edit" 
                                           class="flex-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-2 rounded text-sm text-center transition-colors">
                                            <i class="fas fa-edit mr-1"></i>
                                            Modifier
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-lg font-medium mb-2">Aucune équipe trouvée</p>
                            <p class="text-sm mb-6">Ce club n'a pas encore d'équipes enregistrées.</p>
                            <a href="/modules/teams/create?club_id={{ $club->id }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Créer la première équipe
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Logo du club -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Logo du club</h3>
                    
                    @if($club->logo_path)
                        <img src="{{ asset('storage/' . $club->logo_path) }}" 
                             alt="Logo {{ $club->name }}" 
                             class="h-32 w-32 object-contain mx-auto rounded-lg border-2 border-gray-200">
                    @else
                        <div class="h-32 w-32 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mx-auto">
                            <span class="text-white font-bold text-4xl">{{ substr($club->name, 0, 2) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('clubs-view.edit', ['id' => $club->id]) }}" 
                           class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors text-center block">
                            ✏️ Modifier le club
                        </a>
                        
                        <a href="/clubs-view" 
                           class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-center block">
                            👁️ Voir tous les clubs
                        </a>
                        
                        <a href="/modules" 
                           class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-center block">
                            📋 Retour aux modules
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
