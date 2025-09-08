<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->first_name ?? 'Joueur' }} {{ $player->last_name ?? 'Test' }} - Portail Joueur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto p-6">
        <!-- En-tête du portail -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">🏥 Portail Patient</h1>
                    <p class="text-gray-600 mt-2">Bienvenue, <strong>{{ $player->first_name ?? 'Joueur' }} {{ $player->last_name ?? 'Test' }}</strong></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">ID: {{ $player->id }}</p>
                    <p class="text-sm text-gray-500">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Section Informations de l'Association -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-6 text-blue-600">🏆 Informations de l'Association</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Logo de l'association -->
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group mx-auto">
                    @if($player->association)
                        <x-association-logo-working 
                            :association="$player->association"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    @else
                        <div class="text-center">
                            <div class="text-4xl text-gray-400 mb-2">🏆</div>
                            <div class="text-xs text-gray-500">Aucune association</div>
                        </div>
                    @endif
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        @if($player->association)
                            <a href="{{ route('associations.edit-logo', $player->association->id) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                🏆 Gérer
                            </a>
                        @else
                            <span class="bg-gray-500 text-white px-3 py-1 rounded text-sm font-medium">
                                ❌ Pas d'association
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Drapeau pays de l'association -->
                <div class="w-40 h-40 bg-orange-100 rounded-lg p-3 flex items-center justify-center relative group mx-auto">
                    @if($player->association && $player->association->country)
                        @php
                            $associationCountryCode = \App\Helpers\CountryCodeHelper::getCountryCode($player->association->country);
                        @endphp
                        @if($associationCountryCode)
                            <x-country-flag 
                                :countryCode="$associationCountryCode" 
                                :countryName="$player->association->country"
                                size="2xl"
                                format="svg"
                                class="w-full h-full"
                            />
                        @else
                            <div class="text-center">
                                <div class="text-4xl text-gray-400 mb-2">🏴</div>
                                <div class="text-xs text-gray-500">Code pays invalide</div>
                            </div>
                        @endif
                    @else
                        <div class="text-center">
                            <div class="text-4xl text-gray-400 mb-2">🏴</div>
                            <div class="text-xs text-gray-500">Aucune association</div>
                        </div>
                    @endif
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        @if($player->association)
                            <a href="{{ route('associations.edit-logo', $player->association->id) }}" 
                               class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                🏴 Gérer
                            </a>
                        @else
                            <span class="bg-gray-500 text-white px-3 py-1 rounded text-sm font-medium">
                                ❌ Pas d'association
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Informations de l'association -->
                <div class="w-40 h-40 bg-blue-100 rounded-lg p-3 flex items-center justify-center relative group mx-auto">
                    @if($player->association)
                        <div class="text-center">
                            <div class="text-2xl mb-2">📋</div>
                            <div class="text-sm font-medium text-gray-800">{{ $player->association->name }}</div>
                            <div class="text-xs text-gray-600 mt-1">{{ $player->association->country }}</div>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="text-2xl mb-2">📋</div>
                            <div class="text-xs text-gray-500">Aucune association</div>
                        </div>
                    @endif
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        @if($player->association)
                            <a href="{{ route('associations.edit-logo', $player->association->id) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                📋 Gérer
                            </a>
                        @else
                            <span class="bg-gray-500 text-white px-3 py-1 rounded text-sm font-medium">
                                ❌ Pas d'association
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    <strong>Joueur ID:</strong> {{ $player->id }} | 
                    @if($player->association)
                        <strong>Association:</strong> {{ $player->association->name }} | 
                        <strong>Pays:</strong> {{ $player->association->country }}
                    @else
                        <strong>Association:</strong> <span class="text-red-500">Aucune</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Section Informations du Joueur -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-6 text-green-600">👤 Informations du Joueur</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3">📝 Données personnelles</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Nom complet:</strong> {{ $player->first_name ?? 'N/A' }} {{ $player->last_name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $player->email ?? 'Non renseigné' }}</p>
                        <p><strong>Téléphone:</strong> {{ $player->phone ?? 'Non renseigné' }}</p>
                        <p><strong>Date de naissance:</strong> {{ $player->birth_date ?? 'Non renseignée' }}</p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-3">🏟️ Informations sportives</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>Club:</strong> {{ $player->club->name ?? 'Aucun club' }}</p>
                        <p><strong>Position:</strong> {{ $player->position ?? 'Non définie' }}</p>
                        <p><strong>Numéro:</strong> {{ $player->jersey_number ?? 'Non défini' }}</p>
                        <p><strong>Statut:</strong> {{ $player->status ?? 'Actif' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-6 text-purple-600">⚙️ Actions Disponibles</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($player->association)
                    <a href="{{ route('associations.edit-logo', $player->association->id) }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg text-center transition-colors font-medium">
                        🏆 Gérer le logo de l'association
                    </a>
                @else
                    <span class="bg-gray-400 text-white px-6 py-3 rounded-lg text-center font-medium">
                        🏆 Gérer le logo (pas d'association)
                    </span>
                @endif
                
                <a href="{{ route('demo.logos.officiels') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-center transition-colors font-medium">
                    🎯 Voir les logos officiels
                </a>
                
                <a href="{{ route('test.logos.portail') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-center transition-colors font-medium">
                    🧪 Tester les composants
                </a>
            </div>
        </div>
    </div>
</body>
</html>

