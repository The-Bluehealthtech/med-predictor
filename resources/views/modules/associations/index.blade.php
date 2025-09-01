<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Démonstration Logos Officiels des Fédérations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto p-6">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">🏛️ Associations de Football</h1>
            <p class="text-xl text-gray-600">Gestion des fédérations nationales et régionales</p>
        </div>

        <!-- Liste des associations -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-green-600">🏛️ Associations Disponibles</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($associations as $association)
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-8 text-center relative group">
                    <!-- Logo de l'association -->
                    <div class="w-32 h-32 mx-auto mb-4 bg-white rounded-lg p-3 shadow-md">
                        <x-association-logo-working 
                            :association="$association"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    
                    <!-- Informations principales -->
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        @if($association->country === 'Tunisie') 🇹🇳 @elseif($association->country === 'Maroc') 🇲🇦 @else 🌍 @endif
                        {{ $association->name }}
                    </h3>
                    
                    @if($association->short_name)
                        <p class="text-sm text-green-600 font-medium mb-2">{{ $association->short_name }}</p>
                    @endif
                    
                    <p class="text-sm text-gray-600 mb-3">{{ $association->country }}</p>
                    
                    <!-- Statut -->
                    <div class="mb-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($association->status === 'active') bg-green-100 text-green-800
                            @elseif($association->status === 'inactive') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($association->status ?? 'active') }}
                        </span>
                    </div>
                    
                    <!-- Informations FIFA -->
                    <div class="text-xs text-gray-600 space-y-1 mb-3">
                        @if($association->fifa_id)
                            <p><span class="font-medium">FIFA ID:</span> {{ $association->fifa_id }}</p>
                        @endif
                        @if($association->fifa_ranking)
                            <p><span class="font-medium">Classement FIFA:</span> #{{ $association->fifa_ranking }}</p>
                        @endif
                        @if($association->fifa_version)
                            <p><span class="font-medium">Version FIFA:</span> {{ $association->fifa_version }}</p>
                        @endif
                        @if($association->founded_year)
                            <p><span class="font-medium">Fondée en:</span> {{ $association->founded_year }}</p>
                        @endif
                    </div>
                    
                    <!-- Confédération -->
                    @if($association->confederation)
                        <div class="mb-3 p-2 bg-blue-50 rounded">
                            <p class="text-xs text-blue-700 font-medium">🏆 {{ $association->confederation }}</p>
                        </div>
                    @endif
                    
                    <!-- Statistiques -->
                    <div class="mb-3 text-xs text-gray-600">
                        <p>🏟️ {{ $association->clubs_count ?? 0 }} clubs</p>
                        <p>👥 {{ $association->players_count ?? 0 }} joueurs</p>
                    </div>
                    
                    <!-- Statut de synchronisation FIFA -->
                    @if($association->fifa_sync_status)
                        <div class="mb-3 p-2 rounded text-xs
                            @if($association->fifa_sync_status === 'synced') bg-green-50 text-green-700
                            @elseif($association->fifa_sync_status === 'pending') bg-yellow-50 text-yellow-700
                            @else bg-red-50 text-red-700 @endif">
                            <p class="font-medium">Sync FIFA: {{ ucfirst($association->fifa_sync_status) }}</p>
                            @if($association->fifa_sync_date)
                                <p class="text-xs">{{ \Carbon\Carbon::parse($association->fifa_sync_date)->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Compétitions (à développer plus tard) -->
                    <div class="mb-3 p-2 bg-purple-50 rounded">
                        <p class="text-xs text-purple-700 font-medium">🏆 Compétitions</p>
                        <p class="text-xs text-purple-600">À développer</p>
                    </div>
                    
                    <!-- Boutons d'action qui apparaissent au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="flex space-x-2">
                            <a href="/associations-view/show/{{ $association->id }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                👁️ Voir
                            </a>
                            <a href="/associations-view/edit/{{ $association->id }}" 
                               class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                ✏️ Modifier
                            </a>
                            <button onclick="deleteAssociation({{ $association->id }})" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                🗑️ Supprimer
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center mb-8">
            <a href="/modules" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                🔙 Retour aux modules
            </a>
        </div>

        <!-- JavaScript pour la suppression -->
        <script>
        function deleteAssociation(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette association ?')) {
                // Ici vous pouvez ajouter la logique de suppression AJAX
                alert('Fonction de suppression à implémenter pour l\'ID: ' + id);
            }
        }
        </script>
    </div>
</body>
</html>
