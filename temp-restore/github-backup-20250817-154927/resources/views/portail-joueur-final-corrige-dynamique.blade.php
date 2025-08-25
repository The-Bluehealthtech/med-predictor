




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->first_name }} {{ $player->last_name }} - Portail Joueur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Barre de navigation avec recherche (GARDER CETTE PARTIE) -->
    <div class="bg-gray-800 border-b border-gray-700 p-4">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between space-y-4 lg:space-y-0">
            <!-- Navigation gauche -->
            <div class="flex items-center space-x-4">
                <button id="btn-previous" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors" onclick="navigatePlayer('previous')">
                    ← Précédent
                </button>
                <button id="btn-next" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors" onclick="navigatePlayer('next')">
                    Suivant →
                </button>
                <span class="text-gray-300 font-medium" id="player-counter">Chargement...</span>
                    </div>
                    
            <!-- Barre de recherche centrale -->
            <div class="flex-1 max-w-2xl mx-4">
                <div class="relative">
                    <input type="text" 
                           id="player-search"
                           placeholder="Rechercher par nom, club ou association..." 
                           class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                        </div>
                    
                    <!-- Résultats de recherche -->
                    <div id="search-results" class="absolute top-full left-0 right-0 bg-gray-800 border border-gray-600 rounded-lg mt-1 shadow-lg z-50 hidden max-h-96 overflow-y-auto">
                        <!-- Les résultats s'afficheront ici -->
                        </div>
                            </div>
                        </div>
                        
            <!-- Navigation droite -->
            <div class="flex items-center space-x-4">
                <button class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Joueur
                    </button>
                <a href="/players/list" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors no-underline">
                    ← Retour à la liste
                </a>
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Déconnexion
                    </button>
            </div>
        </div>
                        </div>
                        
          <!-- Contenu principal -->
      <div class="max-w-full mx-auto p-4">
                <!-- Section FIT Card - NOUVELLE DISPOSITION 3 COLONNES -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-2 mb-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-yellow-400">🏃‍♂️ FIT Card</h2>
                        </div>
                        
            <div class="grid grid-cols-3 gap-8">
                                <!-- COLONNE GAUCHE : Photo du joueur + Drapeau nationalité (w-32 h-32 chacun) -->
                <div class="flex gap-2">
                    <!-- Photo du joueur (w-32 h-32) -->
                    <div class="w-32 h-32 bg-blue-100 rounded-lg p-2 flex items-center justify-center relative group">
                        @if($player->player_picture || $player->player_face_url)
                            <img src="{{ $player->getPlayerPictureUrlAttribute() }}" 
                                 alt="Photo de {{ $player->first_name }} {{ $player->last_name }}" 
                                 class="w-full h-full object-cover rounded-lg">
                        @else
                            <div class="text-4xl text-gray-400">👤</div>
                        @endif
                        
                        <!-- Bouton Gérer qui apparaît au survol -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <a href="/joueur/{{ $player->id }}/photo/upload" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                📷 Gérer
                            </a>
                        </div>
                    </div>
                    
                    <!-- Drapeau nationalité (w-32 h-32) -->
                    <div class="w-32 h-32 bg-green-100 rounded-lg p-2 flex items-center justify-center relative group">
                        @if($player->nationality)
                            @php
                                $countryCode = \App\Helpers\CountryHelper::getCountryCode($player->nationality);
                                /*$flagUrl = match(strtolower($player->nationality)) {
                                    // Europe
                                    'france' => 'https://www.xn--icne-wqa.com/images/flag-fr.png',
                                    'england' => 'https://www.xn--icne-wqa.com/images/flag-gb.png',
                                    'germany' => 'https://www.xn--icne-wqa.com/images/flag-de.png',
                                    'spain' => 'https://www.xn--icne-wqa.com/images/flag-es.png',
                                    'italy' => 'https://www.xn--icne-wqa.com/images/flag-it.png',
                                    'norway' => 'https://www.xn--icne-wqa.com/images/flag-no.png',
                                    'belgium' => 'https://www.xn--icne-wqa.com/images/flag-be.png',
                                    'portugal' => 'https://www.xn--icne-wqa.com/images/flag-pt.png',
                                    'switzerland' => 'https://www.xn--icne-wqa.com/images/flag-ch.png',
                                    
                                    // Afrique
                                    'morocco' => 'https://www.xn--icne-wqa.com/images/flag-ma.png',
                                    'tunisia' => 'https://www.xn--icne-wqa.com/images/flag-tn.png',
                                    'algeria' => 'https://www.xn--icne-wqa.com/images/flag-dz.png',
                                    'egypt' => 'https://www.xn--icne-wqa.com/images/flag-eg.png',
                                    'senegal' => 'https://www.xn--icne-wqa.com/images/flag-sn.png',
                                    'cameroon' => 'https://www.xn--icne-wqa.com/images/flag-cm.png',
                                    'nigeria' => 'https://www.xn--icne-wqa.com/images/flag-ng.png',
                                    'ghana' => 'https://www.xn--icne-wqa.com/images/flag-gh.png',
                                    
                                    // Amérique du Sud
                                    'brazil' => 'https://www.xn--icne-wqa.com/images/flag-br.png',
                                    'argentina' => 'https://www.xn--icne-wqa.com/images/flag-ar.png',
                                    
                                    // Fallback pour les autres pays
                                    default => 'https://flagcdn.com/w128/' . strtolower($player->nationality) . '.png'
                                }; */
                            @endphp
                            @if($countryCode)
                                <x-country-flag 
                                    :countryCode="$countryCode" 
                                    :countryName="$player->nationality"
                                    size="xl"
                                    format="svg"
                                    class="w-full h-full"
                                />
                        @else
                            <div class="text-4xl text-blue-400">🏳️</div>
                        @endif
                        @endif
                        
                        <!-- Bouton Gérer qui apparaît au survol -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <a href="/joueur/{{ $player->id }}/photo/upload?type=nationality" 
                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                🏳️ Gérer
                            </a>
                        </div>
                    </div>
                </div>

                                <!-- COLONNE CENTRE : Prénom/Nom + Rating + Position -->
                <div class="flex flex-col items-center justify-center text-center">
                    <!-- Prénom et Nom en BLANC -->
                    <div class="text-xl font-bold text-white mb-2">
                        {{ $player->first_name ?? 'Prénom' }} {{ $player->last_name ?? 'Nom' }}
                    </div>
                    
                                    <!-- Rating -->
                <div class="grid grid-cols-3 gap-2 mb-2">
                    <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded-lg text-xs font-bold">
                        OVR: {{ $player->rating ?? '84' }}
                    </div>
                    <div class="bg-green-100 text-green-800 px-2 py-1 rounded-lg text-xs font-bold">
                        POT: {{ $player->potential ?? '88' }}
                    </div>
                    <div class="bg-purple-100 text-purple-800 px-2 py-1 rounded-lg text-xs font-bold">
                        FIT: {{ $player->fitness ?? '92' }}
                    </div>
                </div>
                    
                    <!-- Position -->
                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">
                        {{ $player->position ?? 'LW' }}
                    </div>
                </div>
                
                                <!-- COLONNE DROITE : Logo club + Logo association + Drapeau pays (w-40 h-40 chacun) -->
                <div class="flex gap-3">
                    <!-- Logo du club (w-40 h-40) -->
                    <div class="w-40 h-40 bg-purple-100 rounded-lg p-3 flex items-center justify-center relative group">
                        @if($player->club)
                            <x-club-logo-working 
                                :club="$player->club"
                                class="w-full h-full"
                            />
                        @else
                            <div class="text-center">
                                <div class="text-4xl text-gray-400 mb-2">🏟️</div>
                                <div class="text-xs text-gray-500">Aucun club</div>
                            </div>
                        @endif
                        
                        <!-- Bouton Gérer qui apparaît au survol -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            @if($player->club)
                                <a href="/joueur/{{ $player->id }}/photo/upload?type=club" 
                                   class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                    🏟️ Gérer
                                </a>
                            @else
                                <span class="bg-gray-500 text-white px-3 py-1 rounded text-sm font-medium">
                                    ❌ Pas de club
                                </span>
                        @endif
                        </div>
                    </div>
                    
                    <!-- Logo de l'association (w-40 h-40) -->
                    <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
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
                    
                    <!-- Drapeau pays de l'association (w-40 h-40) -->
                    <div class="w-40 h-40 bg-orange-100 rounded-lg p-3 flex items-center justify-center relative group">
                        @if($player->association && $player->association->country)
                            @php
                                $associationCountryCode = \App\Helpers\CountryCodeHelper::getCountryCode($player->association->country);
                                /*$associationFlagUrl = match(strtolower($player->association->country)) {
                                    // Europe
                                    'france' => 'https://www.xn--icne-wqa.com/images/flag-fr.png',
                                    'england' => 'https://www.xn--icne-wqa.com/images/flag-gb.png',
                                    'germany' => 'https://www.xn--icne-wqa.com/images/flag-de.png',
                                    'spain' => 'https://www.xn--icne-wqa.com/images/flag-es.png',
                                    'italy' => 'https://www.xn--icne-wqa.com/images/flag-it.png',
                                    'norway' => 'https://www.xn--icne-wqa.com/images/flag-no.png',
                                    'belgium' => 'https://www.xn--icne-wqa.com/images/flag-be.png',
                                    'portugal' => 'https://www.xn--icne-wqa.com/images/flag-pt.png',
                                    'switzerland' => 'https://www.xn--icne-wqa.com/images/flag-ch.png',
                                    
                                    // Afrique
                                    'morocco' => 'https://www.xn--icne-wqa.com/images/flag-ma.png',
                                    'tunisia' => 'https://www.xn--icne-wqa.com/images/flag-tn.png',
                                    'algeria' => 'https://www.xn--icne-wqa.com/images/flag-dz.png',
                                    'egypt' => 'https://www.xn--icne-wqa.com/images/flag-eg.png',
                                    'senegal' => 'https://www.xn--icne-wqa.com/images/flag-sn.png',
                                    'cameroon' => 'https://www.xn--icne-wqa.com/images/flag-cm.png',
                                    'nigeria' => 'https://www.xn--icne-wqa.com/images/flag-ng.png',
                                    'ghana' => 'https://www.xn--icne-wqa.com/images/flag-gh.png',
                                    
                                    // Amérique du Sud
                                    'brazil' => 'https://www.xn--icne-wqa.com/images/flag-br.png',
                                    'argentina' => 'https://www.xn--icne-wqa.com/images/flag-ar.png',
                                    
                                    // Fallback pour les autres pays
                                    default => 'https://flagcdn.com/w160/' . strtolower($player->association->country) . '.png'
                                }; */
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
                            <div class="text-4xl text-gray-400">🏴</div>
                        @endif
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
                </div>
                                    </div>
                                    
            
                            </div>
                            

                            
        <!-- NOUVELLE RÉPARTITION HARMONIEUSE DES ZONES -->
        
        <!-- Ligne 1: Informations Agent + Attributs du Joueur -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <!-- Section Informations Agent - RÉDUITE -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-2">
                                    <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-bold text-green-400 flex items-center">
                        👨‍💼 Informations Agent
                    </h3>
                    <button class="bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded-lg transition-colors text-sm">
                        {{ $player->agent_name ?? 'Agent non défini' }}
                    </button>
                    </div>

                                            <div class="grid grid-cols-2 gap-2">
                    <!-- Téléphone -->
                    <div class="bg-white rounded-lg p-1 border border-green-200">
                        <div class="flex items-center mb-1">
                            <div class="text-green-600 text-xs mr-1">📞</div>
                            <h4 class="font-semibold text-green-800 text-xs">Téléphone</h4>
                                                    </div>
                        <p class="text-gray-800 text-xs">{{ $player->agent_phone ?? 'Non renseigné' }}</p>
                                            </div>
                    
                    <!-- Email -->
                    <div class="bg-white rounded-lg p-1 border border-green-200">
                        <div class="flex items-center mb-1">
                            <div class="text-green-600 text-xs mr-1">📧</div>
                            <h4 class="font-semibold text-green-800 text-xs">Email</h4>
                                                    </div>
                        <p class="text-gray-800 text-xs">{{ $player->agent_email ?? 'Non renseigné' }}</p>
                                            </div>
                    
                    <!-- Agence -->
                    <div class="bg-white rounded-lg p-1 border border-green-200">
                        <div class="flex items-center mb-1">
                            <div class="text-green-600 text-xs mr-1">🏢</div>
                            <h4 class="font-semibold text-green-800 text-xs">Agence</h4>
                                        </div>
                        <p class="text-gray-800 text-xs">{{ $player->agent_agency ?? 'Agence non définie' }}</p>
                    </div>

                    <!-- Commission -->
                    <div class="bg-white rounded-lg p-1 border border-green-200">
                        <div class="flex items-center mb-1">
                            <div class="text-green-600 text-xs mr-1">💰</div>
                            <h4 class="font-semibold text-green-800 text-xs">Commission</h4>
                                                </div>
                        <p class="text-gray-800 text-xs">{{ $player->agent_commission ?? '5' }}%</p>
                                            </div>
                                </div>
                            </div>
                            
            <!-- Section Attributs du Joueur - RÉDUITE -->
            <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg p-2">
                <h3 class="text-lg font-bold text-gray-300 mb-3">Attributs du Joueur</h3>
                
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-white rounded-lg p-1 border border-gray-200 text-center">
                        <div class="text-lg font-bold text-gray-800 mb-1">{{ $player->age ?? '31' }}</div>
                        <div class="text-xs text-gray-600">Âge</div>
                    </div>

                    <div class="bg-white rounded-lg p-1 border border-gray-200 text-center">
                        <div class="text-lg font-bold text-gray-800 mb-1">{{ $player->height ?? '171' }}cm</div>
                        <div class="text-xs text-gray-600">Taille</div>
                                        </div>
                    
                    <div class="bg-white rounded-lg p-1 border border-gray-200 text-center">
                        <div class="text-lg font-bold text-gray-800 mb-1">{{ $player->weight ?? '80' }}kg</div>
                        <div class="text-xs text-gray-600">Poids</div>
                                        </div>
                    
                    <div class="bg-white rounded-lg p-1 border border-gray-200 text-center">
                        <div class="text-lg font-bold text-gray-800 mb-1">{{ $player->preferred_foot ?? 'Droit' }}</div>
                        <div class="text-xs text-gray-600">Pied</div>
                </div>
            </div>


                        </div>
                    </div>

        <!-- Ligne 2: Palmarès + Statistiques Saison -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <!-- Section Palmarès - RÉDUITE -->
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg p-2">
                <h3 class="text-lg font-bold text-yellow-400 mb-3">🏆 Palmarès</h3>
                
                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-white rounded-lg p-2 border border-yellow-200 text-center">
                        <div class="text-lg font-bold text-yellow-800 mb-1">0x</div>
                        <div class="text-xs text-gray-800">Ballon d'Or</div>
                            </div>
                            
                    <div class="bg-white rounded-lg p-2 border border-blue-200 text-center">
                        <div class="text-lg font-bold text-blue-800 mb-1">0+</div>
                        <div class="text-xs text-gray-800">Buts</div>
                        </div>
                        
                    <div class="bg-white rounded-lg p-2 border border-green-200 text-center">
                        <div class="text-lg font-bold text-green-800 mb-1">0+</div>
                        <div class="text-xs text-gray-800">Assists</div>
                                    </div>
                                </div>
                                
                <div class="mt-2 grid grid-cols-3 gap-2">
                    <div class="bg-white rounded-lg p-2 border border-purple-200 text-center">
                        <div class="text-xs text-gray-800">Finaliste</div>
                    </div>

                    <div class="bg-white rounded-lg p-2 border border-orange-200 text-center">
                        <div class="text-xs text-gray-800">0+ Buts saison</div>
                            </div>
                            
                    <div class="bg-white rounded-lg p-2 border border-red-200 text-center">
                        <div class="text-xs text-gray-800">0x Ligue 1</div>
                                    </div>
                                </div>
                            </div>
                            
            <!-- Section Statistiques Saison - RÉDUITE -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg p-4">
                <h3 class="text-lg font-bold text-blue-400 mb-3">Saison 2024-25</h3>
                
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-white rounded-lg p-2 border border-blue-200">
                        <div class="text-xl font-bold text-blue-800">0</div>
                        <div class="text-xs text-gray-800">Buts</div>
                            </div>
                            
                    <div class="bg-white rounded-lg p-2 border border-blue-200">
                        <div class="text-xl font-bold text-blue-800">0</div>
                        <div class="text-xs text-gray-800">Assists</div>
                            </div>
                            
                    <div class="bg-white rounded-lg p-2 border border-blue-200">
                        <div class="text-xl font-bold text-blue-800">0</div>
                        <div class="text-xs text-gray-800">Matchs</div>
                            </div>
                            </div>
                            
                <div class="mt-2 text-center">
                    <div class="text-xs text-blue-800">N/A% complétée</div>
                    <div class="text-xs text-blue-800">N/A matchs restants</div>
                            </div>
                            </div>
                            </div>
                            
        <!-- Ligne 3: Performances Récentes + Risque de Blessure -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <!-- Section Performances Récentes - RÉDUITE -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4">
                <h3 class="text-lg font-bold text-green-400 mb-3">Performances récentes</h3>
                
                <div class="mb-3">
                    <div class="text-sm text-green-800 mb-2">5 derniers matchs:</div>
                    <div class="flex space-x-1">
                        <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">W</span>
                        <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">W</span>
                        <span class="w-6 h-6 bg-yellow-500 text-white rounded-full flex items-center justify-center text-xs font-bold">D</span>
                        <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">W</span>
                        <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">W</span>
                            </div>
                        </div>
                    </div>

            <!-- Section Risque de Blessure - RÉDUITE -->
            <div class="bg-gradient-to-r from-red-500 to-pink-500 rounded-lg p-4">
                <h3 class="text-lg font-bold text-red-400 mb-3">Risque de blessure</h3>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-800 mb-1">15%</div>
                    <div class="text-sm text-red-800 mb-2">MODÉRÉ</div>
                    
                    <div class="w-full bg-red-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: 15%"></div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        
        <!-- Ligne 4: Valeur Marchande + Disponibilité -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <!-- Section Valeur Marchande - RÉDUITE -->
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg p-4">
                <h3 class="text-lg font-bold text-yellow-400 mb-3">Valeur marchande</h3>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-800 mb-1" id="hero-market-value">€6M</div>
                    <div class="text-sm text-yellow-800">↗️ +€0M ce mois</div>
                                        </div>
                                    </div>

            <!-- Section Disponibilité - RÉDUITE -->
            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg p-4">
                <h3 class="text-lg font-bold text-blue-400 mb-3">Disponibilité</h3>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-800 mb-1">✅ LIMITÉ</div>
                    <div class="text-sm text-blue-800">Prochain: Sunday</div>
                                            </div>
                                        </div>
                                    </div>

        <!-- Ligne 5: Forme et Moral (pleine largeur) -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-4 mb-4">
            <h3 class="text-lg font-bold text-purple-400 mb-3">Forme et Moral</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-800 mb-1" id="hero-form-percentage">92%</div>
                    <div class="text-sm text-purple-800">Forme</div>
                                        </div>
                                        
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-800 mb-1" id="hero-morale-percentage">95%</div>
                    <div class="text-sm text-purple-800">Moral</div>
                                        </div>
                                    </div>
                                                    </div>
                                                
        <!-- Navigation des Onglets -->
        <nav class="bg-gray-800 border-b border-gray-700 mb-6">
            <div class="container mx-auto px-6">
                <div class="flex space-x-1 overflow-x-auto">
                    <button 
                        onclick="showTab('performance')"
                        class="tab-button active px-6 py-3 rounded-t-lg font-medium transition-all duration-200 text-white bg-blue-600"
                    >
                        <i class="fas fa-chart-line mr-2"></i>📊 Performance
                    </button>
                    <button 
                        onclick="showTab('notifications')"
                        class="tab-button px-6 py-3 rounded-t-lg font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-gray-700"
                    >
                        <i class="fas fa-bell mr-2"></i>🔔 Notifications
                        <span class="ml-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs">12</span>
                    </button>
                    <button 
                        onclick="showTab('health')"
                        class="tab-button px-6 py-3 rounded-t-lg font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-gray-700"
                    >
                        <i class="fas fa-heartbeat mr-2"></i>❤️ Santé & Bien-être
                    </button>
                    <button 
                        onclick="showTab('medical')"
                        class="tab-button px-6 py-3 rounded-t-lg font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-gray-700"
                    >
                        <i class="fas fa-user-md mr-2"></i>🏥 Médical
                        <span class="ml-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs">4</span>
                    </button>
                    <button 
                        onclick="showTab('devices')"
                        class="tab-button px-6 py-3 rounded-t-lg font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-gray-700"
                    >
                        <i class="fas fa-mobile-alt mr-2"></i>📱 Devices
                        <span class="ml-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">3</span>
                    </button>
                    <button 
                        onclick="showTab('doping')"
                        class="tab-button px-6 py-3 rounded-t-lg font-medium transition-all duration-200 text-gray-400 hover:text-white hover:bg-gray-700"
                    >
                        <i class="fas fa-flask mr-2"></i>🧪 Dopage
                        <span class="ml-2 bg-orange-500 text-white px-2 py-1 rounded-full text-xs">historique</span>
                    </button>
                                                </div>
            </div>
        </nav>

        <!-- Contenu des Onglets -->
        <div class="container mx-auto px-6 py-8">
            <!-- Onglet Performance -->
            <div id="performance-tab" class="tab-content space-y-6">
                <!-- Système de Performances Enrichi -->
                <div id="performance-dashboard">
                    <!-- Navigation des onglets de performance -->
                    <div class="performance-tabs mb-6">
                        <div class="flex space-x-1 bg-gray-800 rounded-lg p-1">
                            <button 
                                onclick="changePerformanceTab('overview')"
                                class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 bg-blue-600 text-white shadow-lg"
                            >
                                <i class="fas fa-chart-line mr-2"></i>Vue d'ensemble
                            </button>
                            <button 
                                onclick="changePerformanceTab('advanced')"
                                class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-400 hover:text-gray-200 hover:bg-gray-700"
                            >
                                <i class="fas fa-chart-bar mr-2"></i>Statistiques avancées
                            </button>
                            <button 
                                onclick="changePerformanceTab('match')"
                                class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-400 hover:text-gray-200 hover:bg-gray-700"
                            >
                                <i class="fas fa-futbol mr-2"></i>Statistiques de match
                            </button>
                            <button 
                                onclick="changePerformanceTab('comparison')"
                                class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-400 hover:text-gray-200 hover:bg-gray-700"
                            >
                                <i class="fas fa-balance-scale mr-2"></i>Analyse comparative
                            </button>
                            <button 
                                onclick="changePerformanceTab('trends')"
                                class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-400 hover:text-gray-200 hover:bg-gray-700"
                            >
                                <i class="fas fa-trending-up mr-2"></i>Tendances
                            </button>
                        </div>
                    </div>

                    <!-- Contenu des onglets de performance -->
                    <div class="tab-content">
                        <!-- Vue d'ensemble -->
                        <div id="overview-tab" class="space-y-6">
                            <!-- En-tête des Performances -->
                            <div class="performance-header bg-gradient-to-r from-blue-900 to-purple-900 rounded-xl p-6 mb-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-2xl font-bold text-white mb-2">
                                            <i class="fas fa-chart-line mr-3"></i>Centre de Performances FIFA Connect
                                        </h2>
                                        <p class="text-blue-200">Données dynamiques basées sur vos vraies statistiques FIFA</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-4xl font-bold text-yellow-400" id="dynamic-overall-rating">{{ $player->overall_rating ?? '88' }}</div>
                                        <div class="text-sm text-blue-200">Rating Global FIFA</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Indicateur de chargement -->
                            <div id="performance-loading" class="text-center py-8">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                                <p class="text-gray-400">Chargement des performances FIFA en cours...</p>
                                <button onclick="loadFIFAPerformanceData()" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-md text-white">
                                    🔄 Forcer le Chargement FIFA
                                </button>
                            </div>
                            
                            <!-- Contenu dynamique des performances -->
                            <div id="performance-content" class="space-y-6">

                            <!-- Grille principale des performances -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                                <!-- Statistiques Offensives -->
                                <div class="bg-gray-800 rounded-xl p-6">
                                    <h3 class="text-lg font-bold mb-4 text-red-400">
                                        <i class="fas fa-bullseye mr-2"></i>Statistiques Offensives
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Buts marqués</span>
                                            <span class="text-2xl font-bold text-red-400" id="dynamic-goals">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Passes décisives</span>
                                            <span class="text-2xl font-bold text-blue-400" id="dynamic-assists">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Tirs cadrés</span>
                                            <span class="text-2xl font-bold text-yellow-400" id="dynamic-shots-on-target">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Précision des tirs</span>
                                            <span class="text-2xl font-bold text-green-400" id="dynamic-shot-accuracy">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistiques Défensives -->
                                <div class="bg-gray-800 rounded-xl p-6">
                                    <h3 class="text-lg font-bold mb-4 text-blue-400">
                                        <i class="fas fa-shield-alt mr-2"></i>Statistiques Défensives
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Tacles réussis</span>
                                            <span class="text-2xl font-bold text-blue-400" id="dynamic-tackles-won">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Interceptions</span>
                                            <span class="text-2xl font-bold text-green-400" id="dynamic-interceptions">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Dégagements</span>
                                            <span class="text-2xl font-bold text-yellow-400" id="dynamic-clearances">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Duels gagnés</span>
                                            <span class="text-2xl font-bold text-purple-400" id="dynamic-duels-won">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistiques Physiques -->
                                <div class="bg-gray-800 rounded-xl p-6">
                                    <h3 class="text-lg font-bold mb-4 text-green-400">
                                        <i class="fas fa-running mr-2"></i>Statistiques Physiques
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Distance parcourue</span>
                                            <span class="text-2xl font-bold text-green-400" id="dynamic-distance">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Vitesse max</span>
                                            <span class="text-2xl font-bold text-yellow-400" id="dynamic-max-speed">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Sprint</span>
                                            <span class="text-2xl font-bold text-red-400" id="dynamic-sprints">-</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-300">Endurance</span>
                                            <span class="text-2xl font-bold text-blue-400" id="dynamic-fitness">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Graphiques de performance -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                <!-- Évolution des performances -->
                                <div class="bg-gray-800 rounded-xl p-6">
                                    <h3 class="text-lg font-bold mb-4 text-blue-300">
                                        <i class="fas fa-chart-line mr-2"></i>Évolution des Performances
                                    </h3>
                                    <div class="h-64">
                                        <canvas id="performanceChart"></canvas>
                                    </div>
                                </div>

                                <!-- Radar des compétences -->
                                <div class="bg-gray-800 rounded-xl p-6">
                                    <h3 class="text-lg font-bold mb-4 text-green-300">
                                        <i class="fas fa-radar-chart mr-2"></i>Radar des Compétences
                                    </h3>
                                    <div class="h-64">
                                        <canvas id="skillsRadar"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistiques détaillées par match -->
                            <div class="bg-gray-800 rounded-xl p-6">
                                <h3 class="text-lg font-bold mb-4 text-purple-300">
                                    <i class="fas fa-list-alt mr-2"></i>Performances par Match (Données FIFA)
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-700">
                                                <th class="text-left py-2 text-gray-300">Métrique</th>
                                                <th class="text-center py-2 text-gray-300">Valeur</th>
                                                <th class="text-center py-2 text-gray-300">Par Match</th>
                                                <th class="text-center py-2 text-gray-300">Classement</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dynamic-match-stats">
                                            <tr class="border-b border-gray-700/50">
                                                <td class="py-2 text-gray-300">Matchs joués</td>
                                                <td class="text-center py-2 text-blue-400 font-bold" id="dynamic-matches-played">-</td>
                                                <td class="text-center py-2 text-gray-300">-</td>
                                                <td class="text-center py-2">
                                                    <span class="px-2 py-1 rounded text-xs font-bold bg-gray-600 text-white">-</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-gray-700/50">
                                                <td class="py-2 text-gray-300">Minutes jouées</td>
                                                <td class="text-center py-2 text-green-400 font-bold" id="dynamic-minutes-played">-</td>
                                                <td class="text-center py-2 text-gray-300">-</td>
                                                <td class="text-center py-2">
                                                    <span class="px-2 py-1 rounded text-xs font-bold bg-gray-600 text-white">-</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-gray-700/50">
                                                <td class="py-2 text-gray-300">Passes réussies</td>
                                                <td class="text-center py-2 text-yellow-400 font-bold" id="dynamic-passes-completed">-</td>
                                                <td class="text-center py-2 text-gray-300">-</td>
                                                <td class="text-center py-2">
                                                    <span class="px-2 py-1 rounded text-xs font-bold bg-gray-600 text-white">-</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques avancées -->
                        <div id="advanced-tab" class="space-y-6" style="display: none;">
                            <div class="bg-gray-800 rounded-xl p-6">
                                <h3 class="text-lg font-bold mb-4 text-blue-300">
                                    <i class="fas fa-chart-bar mr-2"></i>Statistiques Avancées
                                </h3>
                                <p class="text-gray-300">Module en cours de développement - Intégration des composants Vue.js en cours</p>
                            </div>
                        </div>

                        <!-- Statistiques de match -->
                        <div id="match-tab" class="space-y-6" style="display: none;">
                            <div class="bg-gray-800 rounded-xl p-6">
                                <h3 class="text-lg font-bold mb-4 text-green-300">
                                    <i class="fas fa-futbol mr-2"></i>Statistiques de Match
                                </h3>
                                <p class="text-gray-300">Module en cours de développement - Intégration des composants Vue.js en cours</p>
                            </div>
                        </div>

                        <!-- Analyse comparative -->
                        <div id="comparison-tab" class="space-y-6" style="display: none;">
                            <div class="bg-gray-800 rounded-xl p-6">
                                <h3 class="text-lg font-bold mb-4 text-purple-300">
                                    <i class="fas fa-balance-scale mr-2"></i>Analyse Comparative
                                </h3>
                                <p class="text-gray-300">Module en cours de développement - Intégration des composants Vue.js en cours</p>
                            </div>
                        </div>

                        <!-- Tendances -->
                        <div id="trends-tab" class="space-y-6" style="display: none;">
                            <div class="bg-gray-800 rounded-xl p-6">
                                <h3 class="text-lg font-bold mb-4 text-orange-300">
                                    <i class="fas fa-trending-up mr-2"></i>Analyse des Tendances
                                </h3>
                                <p class="text-gray-300">Module en cours de développement - Intégration des composants Vue.js en cours</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicateurs de performance rapides -->
                    <div class="quick-stats mt-8">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-gradient-to-r from-blue-900 to-blue-700 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-white mb-1">{{ $player->overall_rating ?? '88' }}</div>
                                <div class="text-xs text-blue-200">Rating Global</div>
                            </div>
                            <div class="bg-gradient-to-r from-green-900 to-green-700 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-white mb-1" id="quick-goals">-</div>
                                <div class="text-xs text-green-200">Buts Saison</div>
                            </div>
                            <div class="bg-gradient-to-r from-purple-900 to-purple-700 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-white mb-1" id="quick-assists">-</div>
                                <div class="text-xs text-purple-200">Passes Saison</div>
                            </div>
                            <div class="bg-gradient-to-r from-red-900 to-red-700 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold text-white mb-1" id="quick-form">-</div>
                                <div class="text-xs text-red-200">Forme Actuelle</div>
                            </div>
                        </div>
                    </div>


            <!-- Onglet Notifications -->
            <div id="notifications-tab" class="tab-content space-y-6" style="display: none;">
                <div class="bg-gray-800 rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-6 text-yellow-300">
                        <i class="fas fa-bell mr-2"></i>Centre de Notifications
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-blue-900/30 rounded-lg border-l-4 border-blue-500">
                            <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Nouveau Challenge</h4>
                                <p class="text-sm text-gray-300">Défi de vitesse disponible - Améliorez votre sprint !</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-green-900/30 rounded-lg border-l-4 border-green-500">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Objectif Atteint</h4>
                                <p class="text-sm text-gray-300">Félicitations ! Vous avez amélioré votre endurance de 5 points.</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-orange-900/30 rounded-lg border-l-4 border-orange-500">
                            <i class="fas fa-exclamation-triangle text-orange-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Rappel Entraînement</h4>
                                <p class="text-sm text-gray-300">N'oubliez pas votre session d'entraînement technique aujourd'hui.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Santé & Bien-être -->
            <div id="health-tab" class="tab-content space-y-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4 text-green-300">
                            <i class="fas fa-heartbeat mr-2"></i>État de Santé
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span>Récupération</span>
                                <div class="w-32 bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                                </div>
                                <span class="text-green-400 font-bold">85%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Énergie</span>
                                <div class="w-32 bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 72%"></div>
                                </div>
                                <span class="text-blue-400 font-bold">72%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Hydratation</span>
                                <div class="w-32 bg-gray-700 rounded-full h-2">
                                    <div class="bg-cyan-500 h-2 rounded-full" style="width: 90%"></div>
                                </div>
                                <span class="text-cyan-400 font-bold">90%</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4 text-purple-300">
                            <i class="fas fa-dumbbell mr-2"></i>Répartition des Charges
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span>Cardio</span>
                                <span class="text-red-400 font-bold">Élevée</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Musculation</span>
                                <span class="text-yellow-400 font-bold">Modérée</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Technique</span>
                                <span class="text-green-400 font-bold">Faible</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Médical -->
            <div id="medical-tab" class="tab-content space-y-6" style="display: none;">
                <div class="bg-gray-800 rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-6 text-red-300">
                        <i class="fas fa-user-md mr-2"></i>Suivi Médical
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-red-900/30 rounded-lg">
                            <i class="fas fa-thermometer-half text-3xl text-red-400 mb-2"></i>
                            <div class="text-2xl font-bold text-red-400">36.8°C</div>
                            <div class="text-sm text-gray-400">Température</div>
                        </div>
                        <div class="text-center p-4 bg-blue-900/30 rounded-lg">
                            <i class="fas fa-heartbeat text-3xl text-blue-400 mb-2"></i>
                            <div class="text-2xl font-bold text-blue-400">68 BPM</div>
                            <div class="text-sm text-gray-400">Fréquence Cardiaque</div>
                        </div>
                        <div class="text-center p-4 bg-green-900/30 rounded-lg">
                            <i class="fas fa-lungs text-3xl text-green-400 mb-2"></i>
                            <div class="text-2xl font-bold text-green-400">95%</div>
                            <div class="text-sm text-gray-400">Saturation O2</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Devices -->
            <div id="devices-tab" class="tab-content space-y-6" style="display: none;">
                <div class="bg-gray-800 rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-6 text-cyan-300">
                        <i class="fas fa-mobile-alt mr-2"></i>Mes Devices Connectés
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-cyan-900/30 rounded-lg border border-cyan-500/30">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-watch text-2xl text-cyan-400"></i>
                                <div>
                                    <h4 class="font-semibold">Apple Watch Series 9</h4>
                                    <p class="text-sm text-gray-300">Connecté • 85% batterie</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-purple-900/30 rounded-lg border border-purple-500/30">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-mobile-alt text-2xl text-purple-400"></i>
                                <div>
                                    <h4 class="font-semibold">iPhone 15 Pro</h4>
                                    <p class="text-sm text-gray-300">Connecté • 92% batterie</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Dopage -->
            <div id="doping-tab" class="tab-content space-y-6" style="display: none;">
                <div class="bg-gray-800 rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-6 text-orange-300">
                        <i class="fas fa-flask mr-2"></i>Contrôles Anti-Dopage
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-green-900/30 rounded-lg border-l-4 border-green-500">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Dernier Contrôle</h4>
                                <p class="text-sm text-gray-300">15/01/2025 - Résultat : Négatif</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-blue-900/30 rounded-lg border-l-4 border-blue-500">
                            <i class="fas fa-calendar text-blue-400 mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Prochain Contrôle</h4>
                                <p class="text-sm text-gray-300">15/02/2025 - Contrôle programmé</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                                                
    <style>
        /* Styles pour les onglets */
        .tab-button {
            transition: all 0.3s ease;
        }
        
        .tab-button:hover {
            transform: translateY(-2px);
        }
        
        .tab-content {
            transition: all 0.3s ease;
        }
        
        .notification-badge {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                                
    <script>
        // Fonction pour afficher les onglets
        function showTab(tabName) {
            // Masquer tous les onglets
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            
            // Retirer la classe active de tous les boutons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active', 'text-white', 'bg-blue-600');
                button.classList.add('text-gray-400', 'hover:text-white', 'hover:bg-gray-700');
            });
            
            // Afficher l'onglet sélectionné
            const selectedTab = document.getElementById(tabName + '-tab');
            if (selectedTab) {
                selectedTab.style.display = 'block';
            }
            
            // Activer le bouton sélectionné
            const activeButton = document.querySelector(`[onclick="showTab('${tabName}')"]`);
            if (activeButton) {
                activeButton.classList.remove('text-gray-400', 'hover:text-white', 'hover:bg-gray-700');
                activeButton.classList.add('active', 'text-white', 'bg-blue-600');
            }
        }
        
                // Initialisation des graphiques et chargement des données FIFA
        function initCharts() {
            console.log('🎯 Initialisation des graphiques et chargement FIFA Connect...');
            
            // Charger les données FIFA Connect en premier
            loadFIFAPerformanceData();
            
            // Les graphiques seront créés dynamiquement après le chargement des données
            console.log('✅ Initialisation FIFA Connect lancée');
        }
        
        // ===== NOUVELLE FONCTION : Chargement des données FIFA Connect =====
        
        /**
         * Charger les performances dynamiques depuis l'API FIFA Connect
         */
        async function loadFIFAPerformanceData() {
            const playerId = getCurrentPlayerId();
            if (!playerId) {
                console.error('❌ Impossible de récupérer l\'ID du joueur');
                return;
            }
            
            console.log('🚀 Chargement des performances FIFA pour le joueur:', playerId);
            
            try {
                // Afficher le loading
                document.getElementById('performance-loading').classList.remove('hidden');
                document.getElementById('performance-content').classList.add('hidden');
                
                // Appel à l'API FIFA Connect
                const response = await fetch(`/api/player-performance/${playerId}`);
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const responseData = await response.json();
                console.log('✅ Données FIFA récupérées:', responseData);
                
                // Extraire les données du joueur (responseData.data contient les vraies données)
                const playerData = responseData.data;
                console.log('📊 Données du joueur extraites:', playerData);
                
                // Mettre à jour l'interface avec les vraies données
                updatePerformanceInterface(playerData);
                
                // Mettre à jour les graphiques avec les vraies données
                updatePerformanceCharts(playerData);
                
                // Masquer le loading et afficher le contenu
                document.getElementById('performance-loading').classList.add('hidden');
                document.getElementById('performance-content').classList.remove('hidden');
                
                // Forcer l'affichage avec style inline pour éviter les conflits CSS
                const performanceContent = document.getElementById('performance-content');
                if (performanceContent) {
                    performanceContent.style.display = 'block';
                    performanceContent.style.visibility = 'visible';
                    performanceContent.style.opacity = '1';
                    console.log('✅ Conteneur performance-content forcé en visible');
                }
                
                                    // Vérifier que les éléments FIFA sont bien visibles
                    setTimeout(() => {
                        console.log('🔍 Vérification de la visibilité des éléments FIFA...');
                        const fifaElements = ['dynamic-goals', 'dynamic-assists', 'quick-goals', 'quick-assists'];
                        fifaElements.forEach(elementId => {
                            const element = document.getElementById(elementId);
                            if (element) {
                                // Test de visibilité plus robuste
                                const computedStyle = window.getComputedStyle(element);
                                const display = computedStyle.display;
                                const visibility = computedStyle.visibility;
                                const opacity = computedStyle.opacity;
                                const rect = element.getBoundingClientRect();
                                const isVisible = display !== 'none' && visibility !== 'hidden' && opacity !== '0' && rect.width > 0 && rect.height > 0;
                                const currentValue = element.textContent;
                                console.log(`👁️ ${elementId}: visible=${isVisible}, valeur="${currentValue}"`);
                                console.log(`   Styles: display=${display}, visibility=${visibility}, opacity=${opacity}`);
                                console.log(`   Dimensions: ${rect.width}px × ${rect.height}px`);
                            } else {
                                console.warn(`⚠️ Élément ${elementId} non trouvé`);
                            }
                        });
                    }, 100);
                
                console.log('🎉 Interface des performances mise à jour avec succès !');
                
            } catch (error) {
                console.error('❌ Erreur lors du chargement des performances FIFA:', error);
                
                // Afficher un message d'erreur
                document.getElementById('performance-loading').innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-400 text-6xl mb-4">⚠️</div>
                        <p class="text-red-400 font-bold mb-2">Erreur de chargement</p>
                        <p class="text-gray-400">Impossible de récupérer les performances FIFA</p>
                        <button onclick="loadFIFAPerformanceData()" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-md text-white">
                            Réessayer
                        </button>
                    </div>
                `;
            }
        }
        
        /**
         * Mettre à jour l'interface avec les VRAIES données FIFA (synchronisation complète)
         */
        function updatePerformanceInterface(data) {
            console.log('🔄 Mise à jour de l\'interface avec données FIFA:', data);
            
            // Vérifier que les éléments HTML existent avant de les mettre à jour
            const elementsToUpdate = {
                'dynamic-goals': data.goals_scored || 0,
                'dynamic-assists': data.assists || 0,
                'dynamic-shots-on-target': data.shots_on_target || 0,
                'dynamic-shot-accuracy': (data.shot_accuracy || 0) + '%',
                'dynamic-tackles-won': data.tackles_won || 0,
                'dynamic-interceptions': data.interceptions || 0,
                'dynamic-clearances': data.clearances || 0,
                'dynamic-duels-won': data.duels_won || 0,
                'dynamic-distance': (data.distance_covered || 0) + 'km',
                'dynamic-max-speed': (data.max_speed || 0) + 'km/h',
                'dynamic-sprints': data.sprints_count || 0,
                'dynamic-fitness': (data.fitness_score || 0) + '%',
                'dynamic-matches-played': data.matches_played || 0,
                'dynamic-minutes-played': data.minutes_played || 0,
                'dynamic-passes-completed': data.passes_completed || 0,
                'quick-goals': data.goals_scored || 0,
                'quick-assists': data.assists || 0,
                'quick-form': data.form_percentage || 0,
                'dynamic-overall-rating': data.overall_rating || 0
            };
            
            // Mettre à jour tous les éléments avec vérification
            Object.entries(elementsToUpdate).forEach(([elementId, value]) => {
                const element = document.getElementById(elementId);
                if (element) {
                    element.textContent = value;
                    console.log(`✅ ${elementId} mis à jour avec: ${value}`);
                } else {
                    console.warn(`⚠️ Élément ${elementId} non trouvé dans le DOM`);
                }
            });
            
            // Toutes les mises à jour sont maintenant gérées par le système unifié ci-dessus
            
            // ===== NOUVELLE FONCTION : Synchronisation de la zone héro =====
            updateHeroZoneWithFIFAData(data);
            
            console.log('✅ Interface et zone héro synchronisées avec les données FIFA !');
        }
        
        /**
         * Synchroniser la zone héro avec les VRAIES données FIFA
         */
        function updateHeroZoneWithFIFAData(fifaData) {
            console.log('🎯 Synchronisation de la zone héro avec FIFA:', fifaData);
            
            try {
                // Mise à jour des informations de base dans la zone héro
                const heroElements = {
                    // Rating FIFA principal
                    'hero-overall-rating': fifaData.overall_rating || 'N/A',
                    'hero-potential-rating': fifaData.potential_rating || 'N/A',
                    'hero-position': fifaData.position || 'N/A',
                    'hero-age': fifaData.age || 'N/A',
                    'hero-height': fifaData.height ? `${fifaData.height}cm` : 'N/A',
                    'hero-weight': fifaData.weight ? `${fifaData.weight}kg` : 'N/A',
                    
                    // Scores de forme et condition
                    'hero-fitness-score': fifaData.fitness_score ? `${fifaData.fitness_score}%` : 'N/A',
                    'hero-form-percentage': fifaData.form_percentage ? `${fifaData.form_percentage}%` : 'N/A',
                    'hero-morale-percentage': fifaData.morale_percentage ? `${fifaData.morale_percentage}%` : 'N/A',
                    
                    // Attributs techniques FIFA
                    'hero-skill-moves': fifaData.skill_moves || 'N/A',
                    'hero-weak-foot': fifaData.weak_foot || 'N/A',
                    'hero-work-rate': fifaData.work_rate || 'N/A',
                    'hero-international-reputation': fifaData.international_reputation || 'N/A',
                    
                    // Scores GHS (santé et bien-être)
                    'hero-ghs-physical': fifaData.ghs_physical_score || 'N/A',
                    'hero-ghs-mental': fifaData.ghs_mental_score || 'N/A',
                    'hero-ghs-civic': fifaData.ghs_civic_score || 'N/A',
                    'hero-ghs-sleep': fifaData.ghs_sleep_score || 'N/A',
                    'hero-ghs-overall': fifaData.ghs_overall_score || 'N/A',
                    'hero-ghs-color': fifaData.ghs_color_code || 'green',
                    
                    // Données économiques FIFA
                    'hero-market-value': fifaData.market_value ? `€${(fifaData.market_value / 1000000).toFixed(1)}M` : 'N/A',
                    'hero-wage': fifaData.wage_eur ? `€${(fifaData.wage_eur / 1000).toFixed(0)}K` : 'N/A',
                    'hero-release-clause': fifaData.release_clause_eur ? `€${(fifaData.release_clause_eur / 1000000).toFixed(1)}M` : 'N/A',
                    
                    // Statistiques de contribution
                    'hero-contribution-score': fifaData.contribution_score || 'N/A',
                    'hero-data-value': fifaData.data_value_estimate ? `€${(fifaData.data_value_estimate / 1000).toFixed(0)}K` : 'N/A',
                    'hero-matches-contributed': fifaData.matches_contributed || 'N/A',
                    'hero-training-sessions': fifaData.training_sessions_logged || 'N/A',
                    'hero-health-records': fifaData.health_records_contributed || 'N/A'
                };
                
                // Mettre à jour tous les éléments de la zone héro
                Object.entries(heroElements).forEach(([elementId, value]) => {
                    const element = document.getElementById(elementId);
                    if (element) {
                        element.textContent = value;
                        
                        // Mise à jour spéciale pour les scores GHS avec couleurs
                        if (elementId.includes('ghs-color')) {
                            const colorClass = getGHSColorClass(value);
                            element.className = `text-${colorClass}-400 font-bold`;
                        }
                    }
                });
                
                // Mise à jour des indicateurs visuels
                updateHeroVisualIndicators(fifaData);
                
                console.log('✅ Zone héro synchronisée avec succès !');
                
            } catch (error) {
                console.error('❌ Erreur lors de la synchronisation de la zone héro:', error);
            }
        }
        
        /**
         * Mettre à jour les indicateurs visuels de la zone héro
         */
        function updateHeroVisualIndicators(fifaData) {
            // Mise à jour des barres de progression
            updateProgressBar('hero-fitness-bar', fifaData.fitness_score || 0);
            updateProgressBar('hero-form-bar', fifaData.form_percentage || 0);
            updateProgressBar('hero-morale-bar', fifaData.morale_percentage || 0);
            updateProgressBar('hero-ghs-bar', fifaData.ghs_overall_score || 0);
            
            // Mise à jour des indicateurs de statut
            updateStatusIndicator('hero-availability', fifaData.availability || 'Available');
            updateStatusIndicator('hero-injury-risk', fifaData.injury_risk || 20);
        }
        
        /**
         * Mettre à jour une barre de progression
         */
        function updateProgressBar(elementId, percentage) {
            const element = document.getElementById(elementId);
            if (element) {
                element.style.width = `${percentage}%`;
                element.className = `h-2 rounded-full transition-all duration-500 ${getProgressBarColor(percentage)}`;
            }
        }
        
        /**
         * Obtenir la couleur d'une barre de progression selon le pourcentage
         */
        function getProgressBarColor(percentage) {
            if (percentage >= 80) return 'bg-green-500';
            if (percentage >= 60) return 'bg-yellow-500';
            if (percentage >= 40) return 'bg-orange-500';
            return 'bg-red-500';
        }
        
        /**
         * Mettre à jour un indicateur de statut
         */
        function updateStatusIndicator(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                const statusClass = getStatusClass(value);
                element.className = `px-2 py-1 rounded-full text-xs font-medium ${statusClass}`;
                element.textContent = value;
            }
        }
        
        /**
         * Obtenir la classe CSS pour un statut
         */
        function getStatusClass(value) {
            if (typeof value === 'string') {
                const status = value.toLowerCase();
                if (status === 'available') return 'bg-green-100 text-green-800';
                if (status === 'injured') return 'bg-red-100 text-red-800';
                if (status === 'suspended') return 'bg-yellow-100 text-yellow-800';
                if (status === 'international duty') return 'bg-blue-100 text-blue-800';
                return 'bg-gray-100 text-gray-800';
            } else if (typeof value === 'number') {
                if (value <= 20) return 'bg-green-100 text-green-800';
                if (value <= 40) return 'bg-yellow-100 text-yellow-800';
                if (value <= 60) return 'bg-orange-100 text-orange-800';
                return 'bg-red-100 text-red-800';
            }
            return 'bg-gray-100 text-gray-800';
        }
        
        /**
         * Obtenir la classe de couleur GHS
         */
        function getGHSColorClass(color) {
            const colorLower = color.toLowerCase();
            if (colorLower === 'green') return 'green';
            if (colorLower === 'yellow') return 'yellow';
            if (colorLower === 'orange') return 'orange';
            if (colorLower === 'red') return 'red';
            return 'gray';
        }
        
        /**
         * Mettre à jour les graphiques avec les VRAIES données FIFA (100% dynamiques)
         */
        function updatePerformanceCharts(data) {
            console.log('🔄 Mise à jour des graphiques avec données FIFA:', data);
            
            // Mise à jour du graphique de performance FIFA
            const performanceCtx = document.getElementById('performanceChart');
            if (performanceCtx) {
                // Détruire l'ancien graphique s'il existe
                if (window.performanceChart && typeof window.performanceChart.destroy === 'function') {
                    try {
                        window.performanceChart.destroy();
                        console.log('✅ Ancien graphique performance détruit');
                    } catch (error) {
                        console.warn('⚠️ Erreur lors de la destruction du graphique:', error);
                    }
                }
                
                // Générer des données de match DYNAMIQUES basées sur les vraies données FIFA
                const matchData = generateDynamicMatchData(data);
                
                // Vérifier que Chart est disponible
                if (typeof Chart === 'undefined') {
                    console.error('❌ Chart.js n\'est pas chargé !');
                    return;
                }
                
                // Créer le nouveau graphique avec les VRAIES données FIFA
                try {
                    window.performanceChart = new Chart(performanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['J1', 'J2', 'J3', 'J4', 'J5', 'J6', 'J7', 'J8'],
                        datasets: [{
                            label: `Rating Match FIFA (${data.overall_rating})`,
                            data: matchData.ratings,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: `Buts + Passes FIFA (${data.goals_scored + data.assists} total)`,
                            data: matchData.goalsAssists,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: `Distance FIFA (${data.distance_covered}km total)`,
                            data: matchData.distance,
                            borderColor: '#F59E0B',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: `Performances FIFA Dynamiques - ${data.first_name || 'Joueur'} ${data.last_name || ''}`,
                                color: '#D1D5DB'
                            },
                            legend: {
                                labels: { color: '#D1D5DB' }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#374151' },
                                ticks: { color: '#D1D5DB' }
                            },
                            x: {
                                grid: { color: '#374151' },
                                ticks: { color: '#D1D5DB' }
                            }
                        }
                    }
                });
                console.log('✅ Graphique performance FIFA créé avec succès');
            } catch (error) {
                console.error('❌ Erreur lors de la création du graphique performance:', error);
            }
        }
        
        // Mise à jour du radar des compétences FIFA
            const skillsCtx = document.getElementById('skillsRadar');
            if (skillsCtx) {
                // Détruire l'ancien graphique s'il existe
                if (window.skillsRadar && typeof window.skillsRadar.destroy === 'function') {
                    try {
                        window.skillsRadar.destroy();
                        console.log('✅ Ancien graphique radar détruit');
                    } catch (error) {
                        console.warn('⚠️ Erreur lors de la destruction du graphique radar:', error);
                    }
                }
                
                // Vérifier que Chart est disponible
                if (typeof Chart === 'undefined') {
                    console.error('❌ Chart.js n\'est pas chargé !');
                    return;
                }
                
                // Créer le nouveau radar avec les VRAIES données FIFA
                try {
                    window.skillsRadar = new Chart(skillsCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Attaque', 'Défense', 'Physique', 'Technique', 'Mental', 'Forme'],
                        datasets: [{
                            label: `Compétences FIFA - ${data.first_name || 'Joueur'}`,
                            data: [
                                data.attacking_rating || 0,
                                data.defending_rating || 0,
                                data.physical_rating || 0,
                                data.technical_rating || 0,
                                data.mental_rating || 0,
                                data.form_percentage || 0
                            ],
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139, 92, 244, 0.2)',
                            pointBackgroundColor: '#8B5CF6',
                            pointBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: `Radar FIFA Dynamique - ${data.first_name || 'Joueur'} ${data.last_name || ''}`,
                                color: '#D1D5DB'
                            },
                            legend: {
                                labels: { color: '#D1D5DB' }
                            }
                        },
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100,
                                grid: { color: '#374151' },
                                ticks: { color: '#D1D5DB', stepSize: 20 },
                                pointLabels: { color: '#D1D5DB' }
                            }
                        }
                    }
                });
                console.log('✅ Graphique radar FIFA créé avec succès');
            } catch (error) {
                console.error('❌ Erreur lors de la création du graphique radar:', error);
            }
        }
        
        console.log('✅ Graphiques FIFA 100% dynamiques mis à jour !');
        }
        
        /**
         * Générer des données de match 100% DYNAMIQUES basées sur les VRAIES données FIFA
         */
        function generateDynamicMatchData(fifaData) {
            console.log('🔄 Génération de données dynamiques FIFA:', fifaData);
            
            const ratings = [];
            const goalsAssists = [];
            const distance = [];
            
            // Générer 8 matchs avec des variations réalistes basées sur les vraies données FIFA
            for (let i = 0; i < 8; i++) {
                // 1. RATINGS DYNAMIQUES basés sur le rating FIFA + forme + fatigue
                const baseRating = fifaData.overall_rating;
                const form = fifaData.form_percentage;
                const fitness = fifaData.fitness_score;
                
                // Variation selon la forme et la condition physique
                const formVariation = (form - 75) / 100; // -0.25 à +0.25
                const fitnessVariation = (fitness - 80) / 100; // -0.2 à +0.2
                
                // Fatigue progressive sur les 8 matchs
                const fatigueFactor = i * 0.02; // Fatigue croissante
                
                // Variation aléatoire réaliste
                const randomVariation = (Math.random() - 0.5) * 0.15; // ±0.075
                
                const finalRating = baseRating + (formVariation * 5) + (fitnessVariation * 3) - (fatigueFactor * 100) + (randomVariation * 100);
                ratings.push(Math.max(50, Math.min(99, Math.round(finalRating))));
                
                // 2. BUTS + PASSES DYNAMIQUES basés sur les vraies statistiques FIFA
                const goalsPerMatch = fifaData.matches_played > 0 ? fifaData.goals_scored / fifaData.matches_played : 0;
                const assistsPerMatch = fifaData.matches_played > 0 ? fifaData.assists / fifaData.matches_played : 0;
                
                // Variation selon la forme et la fatigue
                const formMultiplier = 0.8 + (form / 100) * 0.4; // 0.8 à 1.2
                const fatigueMultiplier = 1 - (fatigueFactor * 0.3); // Diminution progressive
                
                const matchGoals = Math.max(0, Math.round((goalsPerMatch * formMultiplier * fatigueMultiplier + (Math.random() - 0.5) * 0.3) * 10) / 10);
                const matchAssists = Math.max(0, Math.round((goalsPerMatch * formMultiplier * fatigueMultiplier + (Math.random() - 0.5) * 0.2) * 10) / 10);
                
                goalsAssists.push(matchGoals + matchAssists);
                
                // 3. DISTANCE DYNAMIQUE basée sur la vraie distance FIFA
                const distancePerMatch = fifaData.matches_played > 0 ? fifaData.distance_covered / fifaData.matches_played : 10.5;
                
                // Variation selon la condition physique et la fatigue
                const fitnessMultiplier = 0.9 + (fitness / 100) * 0.2; // 0.9 à 1.1
                const distanceFatigueMultiplier = 1 - (fatigueFactor * 0.2); // Diminution progressive
                
                const matchDistance = Math.max(8, Math.min(15, Math.round((distancePerMatch * fitnessMultiplier * distanceFatigueMultiplier + (Math.random() - 0.5) * 1) * 10) / 10));
                distance.push(matchDistance);
            }
            
            console.log('📊 Données dynamiques générées:', { ratings, goalsAssists, distance });
            
            return {
                ratings,
                goalsAssists,
                distance
            };
        }
        
        /**
         * Générer des données de rating de match réalistes basées sur le rating FIFA
         */
        function generateMatchRatings(overallRating, formPercentage) {
            const baseRating = overallRating || 75;
            const form = formPercentage || 75;
            
            // Générer 8 ratings de match basés sur le rating FIFA et la forme
            return Array.from({ length: 8 }, () => {
                const formVariation = (form - 75) / 100; // -0.25 à +0.25
                const randomVariation = (Math.random() - 0.5) * 0.1; // ±0.05
                const finalRating = baseRating + (formVariation * 5) + (randomVariation * 100);
                return Math.max(50, Math.min(99, Math.round(finalRating)));
            });
        }
        
        /**
         * Générer des données de buts/assists basées sur les vraies statistiques
         */
        function generateGoalsAssistsData(goals, assists, matches) {
            const goalsPerMatch = matches > 0 ? goals / matches : 0;
            const assistsPerMatch = matches > 0 ? assists / matches : 0;
            
            // Générer 8 matchs avec des variations réalistes
            return Array.from({ length: 8 }, () => {
                const goalsVariation = (Math.random() - 0.5) * 0.5; // ±0.25
                const assistsVariation = (Math.random() - 0.5) * 0.5; // ±0.25
                
                const matchGoals = Math.max(0, Math.round((goalsPerMatch + goalsVariation) * 10) / 10);
                const matchAssists = Math.max(0, Math.round((assistsPerMatch + assistsVariation) * 10) / 10);
                
                return matchGoals + matchAssists;
            });
        }
        
        /**
         * Récupérer l'ID du joueur actuel depuis l'URL
         */
        function getCurrentPlayerId() {
            const urlMatch = window.location.pathname.match(/\/portail-joueur\/(\d+)/);
            return urlMatch ? urlMatch[1] : null;
        }
        
        // ===== FIN DES NOUVELLES FONCTIONS =====
        
        // Fonction de recherche de joueurs
        function searchPlayers(query) {
            console.log('🔍 Recherche lancée pour:', query);
            
            if (query.length < 2) {
                hideSearchResults();
                return;
            }
            
            // Afficher l'indicateur de chargement
            const resultsDiv = document.getElementById('search-results');
            resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Recherche en cours...</div>';
            resultsDiv.classList.remove('hidden');
            
            const searchUrl = `/admin/search-players?search=${encodeURIComponent(query)}`;
            console.log('🌐 URL de recherche:', searchUrl);
            
            // Appel à l'API de recherche
            fetch(searchUrl)
                .then(response => {
                    console.log('📡 Réponse reçue:', response.status, response.statusText);
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Données reçues:', data);
                    if (data.players && data.players.length > 0) {
                        console.log('✅ Joueurs trouvés:', data.players.length);
                        displaySearchResults(data.players);
                    } else {
                        console.log('❌ Aucun joueur trouvé');
                        resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-400">Aucun joueur trouvé</div>';
                    }
                })
                .catch(error => {
                    console.error('❌ Erreur lors de la recherche:', error);
                    resultsDiv.innerHTML = '<div class="p-4 text-center text-red-400">Erreur lors de la recherche</div>';
                });
        }
        
        // Affichage des résultats de recherche
        function displaySearchResults(players) {
            const resultsDiv = document.getElementById('search-results');
            let html = '';
            
            players.forEach(player => {
                html += `
                    <div class="p-3 hover:bg-gray-700 cursor-pointer border-b border-gray-600 last:border-b-0" 
                         onclick="selectPlayer(${player.id})">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                ${player.first_name ? player.first_name.charAt(0) : 'J'}
                                ${player.last_name ? player.last_name.charAt(0) : 'P'}
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-white">
                                    ${player.first_name || ''} ${player.last_name || ''}
                                </div>
                                <div class="text-sm text-gray-400">
                                    ${player.club ? player.club.name : 'Club non défini'} • ${player.position || 'Position non définie'}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-blue-400">${player.overall_rating || 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = html;
        }
        
        // Sélection d'un joueur
        function selectPlayer(playerId) {
            // Rediriger vers le portail du joueur sélectionné
            window.location.href = `/portail-joueur/${playerId}`;
        }
        
        // Masquer les résultats de recherche
        function hideSearchResults() {
            const resultsDiv = document.getElementById('search-results');
            resultsDiv.classList.add('hidden');
        }
        
        // Navigation entre joueurs
        function navigatePlayer(direction) {
            console.log(`🔄 Navigation ${direction} demandée`);
            
            // Récupérer l'ID du joueur actuel depuis l'URL
            const currentUrl = window.location.pathname;
            const playerIdMatch = currentUrl.match(/\/portail-joueur\/(\d+)/);
            
            if (!playerIdMatch) {
                console.log('❌ Impossible de récupérer l\'ID du joueur actuel');
                showNavigationMessage('URL invalide pour la navigation');
                return;
            }
            
            const currentPlayerId = parseInt(playerIdMatch[1]);
            console.log(`👤 ID du joueur actuel: ${currentPlayerId}`);
            
            // Récupérer la liste des joueurs depuis le contrôleur
            fetch('/admin/search-players?search=')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.players || data.players.length === 0) {
                        showNavigationMessage('Aucun joueur disponible pour la navigation');
                        return;
                    }
                    
                    // Trouver l'index du joueur actuel dans la liste
                    const currentIndex = data.players.findIndex(player => player.id === currentPlayerId);
                    
                    if (currentIndex === -1) {
                        showNavigationMessage('Joueur actuel non trouvé dans la liste');
                        return;
                    }
                    
                    // Calculer le nouvel index
                    let newIndex;
                    if (direction === 'next') {
                        newIndex = currentIndex + 1;
                        if (newIndex >= data.players.length) {
                            showNavigationMessage('Vous êtes déjà au dernier joueur');
                            return;
                        }
                    } else {
                        newIndex = currentIndex - 1;
                        if (newIndex < 0) {
                            showNavigationMessage('Vous êtes déjà au premier joueur');
                            return;
                        }
                    }
                    
                    // Récupérer le joueur suivant/précédent
                    const nextPlayer = data.players[newIndex];
                    console.log(`🔄 Navigation vers le joueur: ${nextPlayer.first_name} ${nextPlayer.last_name} (ID: ${nextPlayer.id})`);
                    
                    // Naviguer vers le nouveau joueur
                    window.location.href = `/portail-joueur/${nextPlayer.id}`;
                })
                .catch(error => {
                    console.error('❌ Erreur lors de la récupération de la liste des joueurs:', error);
                    showNavigationMessage('Erreur lors de la navigation: ' + error.message);
                });
        }
        
        // Afficher un message de navigation
        function showNavigationMessage(message) {
            // Créer une notification temporaire
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Supprimer après 3 secondes
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }
        
        // Mettre à jour le compteur de joueurs
        function updatePlayerCounter() {
            const currentUrl = window.location.pathname;
            const playerIdMatch = currentUrl.match(/\/portail-joueur\/(\d+)/);
            
            if (!playerIdMatch) {
                document.getElementById('player-counter').textContent = 'Erreur';
                return;
            }
            
            const currentPlayerId = parseInt(playerIdMatch[1]);
            
            // Récupérer la liste des joueurs pour calculer la position
            fetch('/admin/search-players?search=')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.players || data.players.length === 0) {
                        document.getElementById('player-counter').textContent = 'Erreur';
                        return;
                    }
                    
                    // Trouver l'index du joueur actuel dans la liste
                    const currentIndex = data.players.findIndex(player => player.id === currentPlayerId);
                    
                    if (currentIndex === -1) {
                        document.getElementById('player-counter').textContent = 'Erreur';
                        return;
                    }
                    
                    // Mettre à jour le compteur (index + 1 car les index commencent à 0)
                    const position = currentIndex + 1;
                    const total = data.players.length;
                    document.getElementById('player-counter').textContent = `${position} / ${total}`;
                    
                    console.log(`📊 Compteur mis à jour: ${position} / ${total}`);
                })
                .catch(error => {
                    console.error('❌ Erreur lors de la mise à jour du compteur:', error);
                    document.getElementById('player-counter').textContent = 'Erreur';
                });
        }
        
        // Gestion des onglets de performance
        let activePerformanceTab = 'overview';
        const performanceTabs = [
            { id: 'overview', name: 'Vue d\'ensemble', icon: 'fas fa-chart-line' },
            { id: 'advanced', name: 'Statistiques avancées', icon: 'fas fa-chart-bar' },
            { id: 'match', name: 'Statistiques de match', icon: 'fas fa-futbol' },
            { id: 'comparison', name: 'Analyse comparative', icon: 'fas fa-balance-scale' },
            { id: 'trends', name: 'Tendances', icon: 'fas fa-trending-up' }
        ];

        // Fonction pour changer d'onglet de performance
        function changePerformanceTab(tabId) {
            activePerformanceTab = tabId;
            
            // Masquer tous les contenus
            const tabContents = document.querySelectorAll('#performance-dashboard .tab-content > div');
            tabContents.forEach(content => content.style.display = 'none');
            
            // Afficher le contenu de l'onglet sélectionné
            const selectedContent = document.querySelector(`#performance-dashboard [v-if="activePerformanceTab === '${tabId}'"]`);
            if (selectedContent) {
                selectedContent.style.display = 'block';
            }
            
            // Mettre à jour les boutons actifs
            const tabButtons = document.querySelectorAll('#performance-dashboard .performance-tabs button');
            tabButtons.forEach(button => {
                button.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
                button.classList.add('text-gray-400', 'hover:text-gray-200', 'hover:bg-gray-700');
            });
            
            const activeButton = document.querySelector(`#performance-dashboard .performance-tabs button[onclick="changePerformanceTab('${tabId}')"]`);
            if (activeButton) {
                activeButton.classList.remove('text-gray-400', 'hover:text-gray-200', 'hover:bg-gray-700');
                activeButton.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
            }
        }

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Portail joueur chargé avec succès');
            
            // Mettre à jour le compteur de joueurs
            updatePlayerCounter();
            
            // Initialiser les onglets de performance EN PREMIER
            setTimeout(() => {
                console.log('🎯 Initialisation des onglets de performance...');
                const performanceDashboard = document.getElementById('performance-dashboard');
                if (performanceDashboard) {
                    // Remplacer v-for par des boutons statiques
                    const tabsContainer = performanceDashboard.querySelector('.performance-tabs .flex');
                    if (tabsContainer) {
                        tabsContainer.innerHTML = performanceTabs.map(tab => `
                            <button 
                                onclick="changePerformanceTab('${tab.id}')"
                                class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 ${tab.id === 'overview' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-700'}"
                            >
                                <i class="${tab.icon} mr-2"></i>
                                ${tab.name}
                            </button>
                        `).join('');
                        console.log('✅ Boutons des onglets de performance créés');
                    }
                    
                    // Remplacer v-if par des divs avec style display
                    const tabContents = performanceDashboard.querySelectorAll('.tab-content > div');
                    tabContents.forEach((content, index) => {
                        if (index === 0) {
                            content.style.display = 'block';
                            console.log('✅ Onglet Overview affiché');
                        } else {
                            content.style.display = 'none';
                        }
                    });
                    
                    // Afficher l'onglet Performance par défaut APRÈS l'initialisation
                    showTab('performance');
                    console.log('✅ Onglet Performance affiché par défaut');
                    
                    // S'assurer que le contenu de performance est visible
                    const performanceContent = document.getElementById('performance-content');
                    if (performanceContent) {
                        performanceContent.style.display = 'block';
                        performanceContent.style.visibility = 'visible';
                        performanceContent.style.opacity = '1';
                        console.log('✅ Conteneur performance-content initialisé en visible');
                    }
                }
            }, 100);
            
            // Initialiser les graphiques après un délai pour s'assurer que les canvas sont visibles
            setTimeout(initCharts, 300);
            
            // Charger automatiquement les données FIFA après un délai
            setTimeout(() => {
                console.log('🚀 Chargement automatique des données FIFA...');
                loadFIFAPerformanceData();
            }, 800);
            
            // Initialiser la recherche
            const searchInput = document.getElementById('player-search');
            if (searchInput) {
                let searchTimeout;
                
                // Recherche en temps réel
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length >= 2) {
                        searchTimeout = setTimeout(() => {
                            searchPlayers(query);
                        }, 300);
                    } else {
                        hideSearchResults();
                    }
                });
                
                // Masquer les résultats quand on clique ailleurs
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('#player-search') && !e.target.closest('#search-results')) {
                        hideSearchResults();
                    }
                });
                
                // Recherche avec la touche Entrée
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const query = this.value.trim();
                        if (query.length >= 2) {
                            searchPlayers(query);
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>