<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confédérations FIFA - Plateforme FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">🌍 Confédérations FIFA</h1>
                <p class="text-lg text-gray-600">Gestion des confédérations continentales et internationales</p>
            </div>
            <a href="/modules" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center">
                <span class="mr-2">🏠</span>
                Retour aux modules
            </a>
        </div>

        <!-- Informations -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <span class="text-blue-600 mr-2">ℹ️</span>
                <p class="text-blue-800">
                    <strong>Hiérarchie FIFA :</strong> Confédération → Association → Club → Équipe → Joueur
                </p>
            </div>
        </div>

        <!-- Liste des confédérations -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($confederations as $confederation)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Header de la carte -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-semibold text-gray-800">{{ $confederation->name }}</h3>
                                @if($confederation->short_name)
                                <span class="text-sm font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded-full">
                                    {{ $confederation->short_name }}
                                </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">{{ $confederation->country }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $confederation->status === 'active' ? 'bg-green-100 text-green-800' : ($confederation->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $confederation->status === 'active' ? 'Active' : ($confederation->status === 'inactive' ? 'Inactive' : 'Suspendue') }}
                            </span>
                        </div>
                    </div>

                    <!-- Logo de la confédération -->
                    <div class="flex justify-center mb-4">
                        @if($confederation->logo_url)
                            <img src="{{ $confederation->logo_url }}" 
                                 alt="Logo {{ $confederation->name }}" 
                                 class="h-20 w-20 object-contain rounded-lg border-2 border-gray-200">
                        @else
                            <div class="h-20 w-20 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-2xl">{{ $confederation->short_name }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Informations FIFA Connect -->
                    <div class="space-y-2 mb-4">
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-purple-600">🏆</span>
                                <span>Classement FIFA: {{ $confederation->fifa_ranking ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-purple-600">📱</span>
                                <span>Version: {{ $confederation->fifa_version ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="w-4 h-4 mr-2 text-purple-600">🔄</span>
                                <span class="text-xs">Sync FIFA:</span>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $confederation->fifa_sync_status === 'synced' ? 'bg-green-100 text-green-800' : ($confederation->fifa_sync_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $confederation->fifa_sync_status }}
                            </span>
                        </div>

                        @if($confederation->founded_year)
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-purple-600">📅</span>
                            <span class="text-xs">Fondée en {{ $confederation->founded_year }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="/confederations-view/show?id={{ $confederation->id }}" 
                           class="flex-1 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors">
                            👁️ Voir détails
                        </a>
                        <a href="/associations-view?confederation_id={{ $confederation->id }}" 
                           class="flex-1 px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition-colors">
                            🏛️ Associations
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Statistiques -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📊 Statistiques</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $confederations->count() }}</div>
                    <div class="text-sm text-gray-600">Confédérations</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $confederations->where('status', 'active')->count() }}</div>
                    <div class="text-sm text-gray-600">Actives</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $confederations->where('fifa_sync_status', 'synced')->count() }}</div>
                    <div class="text-sm text-gray-600">Synchronisées FIFA</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $confederations->where('fifa_sync_status', 'pending')->count() }}</div>
                    <div class="text-sm text-gray-600">En attente</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
