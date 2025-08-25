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
                        @if($player->club && ($player->club->logo || $player->club->club_logo_url))
                            <img src="{{ $player->club->club_logo_url ?? asset('storage/' . $player->club->logo) }}" 
                                 alt="Logo {{ $player->club->name }}" 
                                 class="w-full h-full object-contain"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="text-4xl text-gray-400" style="display: none;">🏟️</div>
                        @else
                            <div class="text-4xl text-gray-400">🏟️</div>
                        @endif
                        
                        <!-- Bouton Gérer qui apparaît au survol -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <a href="/joueur/{{ $player->id }}/photo/upload?type=club" 
                               class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                🏟️ Gérer
                            </a>
                        </div>
                    </div>
                    
                    <!-- Logo de l'association (w-40 h-40) -->
                    <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                        @if($player->association && ($player->association->logo || $player->association->logo_url))
                            <img src="{{ $player->association->logo_url ?? asset('storage/' . $player->association->logo) }}" 
                                 alt="Logo {{ $player->association->name }}" 
                                 class="w-full h-full object-contain"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="text-4xl text-gray-400" style="display: none;">🏆</div>
                        @else
                            <div class="text-4xl text-gray-400">🏆</div>
                        @endif
                        
                        <!-- Bouton Gérer qui apparaît au survol -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <a href="/joueur/{{ $player->id }}/photo/upload?type=association" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                🏆 Gérer
                            </a>
                        </div>
                    </div>
                    
                    <!-- Drapeau pays de l'association (w-40 h-40) -->
                    <div class="w-40 h-40 bg-orange-100 rounded-lg p-3 flex items-center justify-center relative group">
                        @if($player->association && $player->association->country)
                            @php
                                $associationCountryCode = \App\Helpers\CountryHelper::getCountryCode($player->association->country);
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
                            <a href="/joueur/{{ $player->id }}/photo/upload?type=association_flag" 
                               class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                🏴 Gérer
                            </a>
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
                        <div class="text-lg font-bold text-gray-800 mb-1">{{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : '31' }}</div>
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
                    <div class="text-2xl font-bold text-yellow-800 mb-1">€6M</div>
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
                    <div class="text-2xl font-bold text-purple-800 mb-1">92%</div>
                    <div class="text-sm text-purple-800">Forme</div>
                                        </div>
                                        
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-800 mb-1">95%</div>
                    <div class="text-sm text-purple-800">Moral</div>
                                        </div>
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
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Graphique Performance -->
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4 text-blue-300">
                            <i class="fas fa-chart-line mr-2"></i>Évolution des Performances
                        </h3>
                        <div class="h-80">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>

                    <!-- Graphique Comparaison -->
                    <div class="bg-gray-800 rounded-xl p-6">
                        <h3 class="text-xl font-bold mb-4 text-green-300">
                            <i class="fas fa-radar-chart mr-2"></i>Comparaison des Statistiques
                        </h3>
                        <div class="h-80">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Statistiques Détaillées -->
                <div class="bg-gray-800 rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-6 text-purple-300">
                        <i class="fas fa-tachometer-alt mr-2"></i>Statistiques Détaillées
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-400">{{ $player->overall_rating ?? '88' }}</div>
                            <div class="text-sm text-gray-400">Rating Global</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-400">{{ $player->potential_rating ?? '92' }}</div>
                            <div class="text-sm text-gray-400">Vitesse</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-400">{{ $player->ghs_overall_score ?? '85' }}</div>
                            <div class="text-sm text-gray-400">Technique</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-400">{{ $player->contribution_score ?? '78' }}</div>
                            <div class="text-sm text-gray-400">Mental</div>
                        </div>
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
        
        // Initialisation des graphiques
        function initCharts() {
            // Graphique de performance
            const performanceCtx = document.getElementById('performanceChart');
            if (performanceCtx) {
                new Chart(performanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                        datasets: [{
                            label: 'Performance',
                            data: [85, 87, 89, 88, 90, 92],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#e5e7eb'
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: '#374151'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: '#374151'
                                }
                            }
                        }
                    }
                });
            }
            
            // Graphique de comparaison
            const comparisonCtx = document.getElementById('comparisonChart');
            if (comparisonCtx) {
                new Chart(comparisonCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Vitesse', 'Technique', 'Mental', 'Physique', 'Tactique'],
                        datasets: [{
                            label: '{{ $player->first_name ?? "Joueur" }}',
                            data: [88, 85, 78, 82, 80],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            pointBackgroundColor: '#10b981'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#e5e7eb'
                                }
                            }
                        },
                        scales: {
                            r: {
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: '#374151'
                                }
                            }
                        }
                    }
                });
            }
        }
        
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
        
        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Portail joueur chargé avec succès');
            
            // Mettre à jour le compteur de joueurs
            updatePlayerCounter();
            
            // Afficher l'onglet Performance par défaut
            showTab('performance');
            
            // Initialiser les graphiques après un délai pour s'assurer que les canvas sont visibles
            setTimeout(initCharts, 100);
            
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

