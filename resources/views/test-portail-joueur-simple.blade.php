<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $player->first_name }} {{ $player->last_name }} - Portail Joueur Simple</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    
    <!-- Lien de retour à la liste -->
    <div class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/players/list" 
               class="inline-flex items-center space-x-2 text-blue-400 hover:text-blue-300 transition-colors duration-200">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la Liste</span>
            </a>
            
            <!-- Connection Status Button -->
            @php
                // Vérification globale de l'état de connexion
                $isAuthenticated = Auth::check();
                $isSessionValid = true; // Si Auth::check() est true, la session Laravel est valide
                $isTenantActive = true;
                $user = null;
                
                if ($isAuthenticated) {
                    try {
                        $user = Auth::user();
                        if ($user && $user->exists) {
                            // Vérification du tenant si applicable
                            if (method_exists($user, 'getCurrentTenant') && $user->getCurrentTenant()) {
                                $tenant = $user->getCurrentTenant();
                                $isTenantActive = $tenant && $tenant->is_active;
                            }
                            
                            // Vérifier si la session n'a pas expiré (optionnel)
                            $sessionLifetime = config('session.lifetime') * 60;
                            $lastActivity = session('last_activity', time());
                            if (time() - $lastActivity > $sessionLifetime) {
                                $isSessionValid = false;
                            }
                        } else {
                            $isSessionValid = false;
                        }
                    } catch (Exception $e) {
                        $isSessionValid = false;
                        $isTenantActive = false;
                    }
                }
                
                // Déterminer l'état final avec logique simplifiée
                if (!$isAuthenticated) {
                    $connectionStatus = 'not_connected';
                    $statusColor = 'gray';
                    $statusText = 'Non connecté';
                } elseif ($isAuthenticated && $isSessionValid && $isTenantActive) {
                    $connectionStatus = 'connected';
                    $statusColor = 'green';
                    $statusText = 'Connecté';
                } elseif ($isAuthenticated && (!$isSessionValid || !$isTenantActive)) {
                    $connectionStatus = 'disconnected';
                    $statusColor = 'red';
                    $statusText = 'Session expirée';
                } else {
                    $connectionStatus = 'not_connected';
                    $statusColor = 'gray';
                    $statusText = 'Non connecté';
                }
            @endphp
            
            <div class="flex items-center space-x-2 bg-gray-700 rounded-lg px-3 py-2">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-{{ $statusColor }}-500 rounded-full {{ $connectionStatus === 'connected' ? 'animate-pulse' : '' }}" 
                         title="{{ $statusText }} - Session: {{ $isSessionValid ? 'Valide' : 'Invalide' }} | Tenant: {{ $isTenantActive ? 'Actif' : 'Inactif' }}"></div>
                    <span class="text-xs text-{{ $statusColor }}-400 font-medium">{{ $statusText }}</span>
                </div>
                @if($isAuthenticated && $user)
                    <span class="text-gray-500">|</span>
                    <span class="text-sm font-semibold text-gray-200">{{ $user->name ?? 'User' }}</span>
                    <span class="text-xs text-gray-400">({{ ucfirst($user->role ?? 'user') }})</span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- 🆕 NOUVELLE HERO ZONE SIMPLE EN BLADE (remplace le JavaScript complexe) -->
    <div class="bg-gradient-to-br from-blue-900 to-indigo-900 p-6">
        <div class="max-w-7xl mx-auto">
            
            <!-- En-tête avec photo et infos du joueur -->
            <div class="flex flex-col lg:flex-row items-center lg:items-start gap-6 mb-8">
                
                <!-- Photo du joueur -->
                <div class="w-32 h-32 rounded-full flex items-center justify-center relative group">
                    @if($player->player_picture)
                        <img src="/storage/{{ $player->player_picture }}" 
                             alt="Photo de {{ $player->first_name }} {{ $player->last_name }}" 
                             class="w-full h-full object-cover rounded-full">
                    @elseif($player->player_face_url)
                        <img src="{{ $player->player_face_url }}" 
                             alt="Photo de {{ $player->first_name }} {{ $player->last_name }}" 
                             class="w-full h-full object-cover rounded-full">
                    @else
                        <div class="text-6xl text-gray-400">👤</div>
                    @endif
                    

                </div>
                
                <!-- Informations du joueur -->
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl font-bold text-white mb-2">
                        {{ $player->first_name }} {{ $player->last_name }}
                    </h1>
                    <p class="text-xl text-blue-200 mb-4">
                        {{ $player->position ?? 'Position non définie' }} • 
                        {{ $player->club->name ?? 'Club non défini' }}
                    </p>
                    
                    <!-- Drapeau nationalité -->
                    <div class="inline-block">
                        @if($player->nationality)
                            @php
                                $countryCode = \App\Helpers\CountryHelper::getCountryCode($player->nationality);
                            @endphp
                            @if($countryCode)
                                <img src="https://flagcdn.com/w40/{{ strtolower($countryCode) }}.png" 
                                     alt="Drapeau {{ $player->nationality }}" 
                                     class="h-8 w-12 object-cover rounded border-2 border-white shadow-lg">
                            @else
                                <span class="bg-gray-600 text-white px-3 py-1 rounded text-sm">
                                    {{ $player->nationality }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- 🎯 NOUVELLES CARTES SIMPLES AVEC VRAIES DONNÉES DE LA BASE -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                
                <!-- Carte 1: Club et Association avec LOGOS -->
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4 border-2 border-white/40 shadow-lg">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Club & Association</h3>
                    </div>
                    
                    @if($player->club)
                        <div class="space-y-3">
                            <!-- Club avec logo -->
                            <div class="flex items-center space-x-3 p-2 bg-white/5 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    @if($player->club->logo_path)
                                        <img src="/storage/{{ $player->club->logo_path }}" 
                                             alt="Logo {{ $player->club->name }}" 
                                             class="w-6 h-6 object-contain">
                                    @else
                                        <i class="fas fa-shield-alt text-blue-600 text-sm"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-blue-200 text-xs">🏟️ Club:</span>
                                    <span class="text-white font-medium text-sm">{{ $player->club->name }}</span>
                                </div>
                            </div>
                            
                            @if($player->association)
                                <!-- Association avec logo -->
                                <div class="flex items-center space-x-3 p-2 bg-white/5 rounded-lg">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                        @if($player->association->association_logo_url)
                                            <img src="/storage/{{ $player->association->association_logo_url }}" 
                                                 alt="Logo {{ $player->association->name }}" 
                                                 class="w-6 h-6 object-contain">
                                        @elseif($player->association->logo_path)
                                            <img src="/storage/{{ $player->association->logo_path }}" 
                                                 alt="Logo {{ $player->association->name }}" 
                                                 class="w-6 h-6 object-contain">
                                        @else
                                            <i class="fas fa-flag text-green-600 text-sm"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-green-200 text-xs">🏛️ Association:</span>
                                        <span class="text-white font-medium text-sm">{{ $player->association->name }}</span>
                                    </div>
                                </div>
                                
                                @if($player->association->confederation)
                                    <!-- Confédération -->
                                    <div class="flex items-center space-x-3 p-2 bg-white/5 rounded-lg">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-globe text-purple-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="text-purple-200 text-xs">🌍 Confédération:</span>
                                            <span class="text-white font-medium text-sm">{{ $player->association->confederation->name }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @else
                        <p class="text-gray-300 text-sm">Aucun club assigné</p>
                    @endif
                </div>
                
                <!-- Carte 2: Score FIT -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heartbeat text-white text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Score FIT</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="text-center">
                            @php
                                // Calcul du score FIT basé sur les données de la base
                                $fitScore = 0;
                                $totalFactors = 0;
                                
                                // Facteur 1: Minutes jouées (poids: 30%)
                                $totalMinutes = \DB::table('player_season_stats')
                                    ->where('player_id', $player->id)
                                    ->sum('minutes_played');
                                if ($totalMinutes > 0) {
                                    $minutesScore = min(100, ($totalMinutes / 1000) * 100);
                                    $fitScore += $minutesScore * 0.3;
                                    $totalFactors += 0.3;
                                }
                                
                                // Facteur 2: Âge optimal (poids: 25%)
                                $age = $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : 25;
                                // Calcul dynamique de l'âge optimal basé sur la position du joueur
                                $optimalAge = $player->position === 'Attaquant' ? 26 : 
                                            ($player->position === 'Milieu' ? 27 : 
                                            ($player->position === 'Défenseur' ? 28 : 27));
                                $ageScore = max(0, 100 - abs($age - $optimalAge) * 5);
                                $fitScore += $ageScore * 0.25;
                                $totalFactors += 0.25;
                                
                                // Facteur 3: Performance récente (poids: 25%)
                                $recentStats = \DB::table('player_season_stats')
                                    ->where('player_id', $player->id)
                                    ->first();
                                if ($recentStats) {
                                    $performanceScore = min(100, 
                                        (($recentStats->goals * 10) + 
                                         ($recentStats->assists * 8) + 
                                         ($recentStats->matches_played * 2))
                                    );
                                    $fitScore += $performanceScore * 0.25;
                                    $totalFactors += 0.25;
                                }
                                
                                // Facteur 4: Santé (poids: 20%) - Calculé dynamiquement
                                $healthScore = 0;
                                // Score de base calculé selon l'âge et la position (pas de table santé)
                                $healthScore = max(60, 90 - ($age - 20) * 2);
                                
                                // Ajuster selon la position du joueur
                                if ($player->position === 'Attaquant') {
                                    $healthScore += 5; // Attaquants ont généralement une meilleure forme
                                } elseif ($player->position === 'Défenseur') {
                                    $healthScore -= 3; // Défenseurs plus exposés aux blessures
                                }
                                
                                $fitScore += $healthScore * 0.2;
                                $totalFactors += 0.2;
                                
                                // Normalisation du score final
                                $finalFitScore = $totalFactors > 0 ? round($fitScore / $totalFactors) : 75;
                                
                                // Ajustement du score pour qu'il soit cohérent avec l'indicateur graphique
                                // Calcul dynamique des seuils basé sur les performances du joueur
                                $avgPerformance = \DB::table('player_season_stats')
                                    ->where('player_id', $player->id)
                                    ->avg('goals');
                                $avgPerformance = $avgPerformance ?: 0;
                                
                                // Seuils adaptatifs selon la performance moyenne du joueur
                                $excellentThreshold = min(85, max(75, 80 + ($avgPerformance * 2)));
                                $goodThreshold = min(70, max(55, 60 + ($avgPerformance * 1.5)));
                                $averageThreshold = min(55, max(40, 45 + ($avgPerformance * 1)));
                                
                                if ($finalFitScore > $excellentThreshold) {
                                    // Score très élevé = position vers le bleu (bien-être)
                                    $displayScore = min(100, max($excellentThreshold, $finalFitScore));
                                    $scoreColor = "text-blue-400";
                                    $scoreStatus = "Excellente forme";
                                } elseif ($finalFitScore > $goodThreshold) {
                                    // Score élevé = position vers le vert (transition)
                                    $displayScore = min($excellentThreshold - 1, max($goodThreshold, $finalFitScore));
                                    $scoreColor = "text-green-400";
                                    $scoreStatus = "Bonne forme";
                                } elseif ($finalFitScore > $averageThreshold) {
                                    // Score moyen = position vers le vert-rouge (transition)
                                    $displayScore = min($goodThreshold - 1, max($averageThreshold, $finalFitScore));
                                    $scoreColor = "text-yellow-400";
                                    $scoreStatus = "Forme moyenne";
                                } else {
                                    // Score bas = position vers le rouge (maladie)
                                    $displayScore = max(20, min($averageThreshold - 1, $finalFitScore));
                                    $scoreColor = "text-red-400";
                                    $scoreStatus = "Forme dégradée";
                                }
                                
                                // Calcul de la tendance (comparaison avec la semaine précédente)
                                // Essayer de récupérer des données réelles de tendance
                                $lastWeekScore = $displayScore;
                                $trend = 0;
                                
                                // Vérifier s'il y a des données de performance récentes pour calculer la tendance
                                $recentPerformance = \DB::table('player_season_stats')
                                    ->where('player_id', $player->id)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(2)
                                    ->get();
                                
                                if ($recentPerformance->count() >= 2) {
                                    // Calculer la tendance basée sur les vraies données
                                    $currentPerformance = ($recentPerformance[0]->goals ?? 0) + 
                                                        ($recentPerformance[0]->assists ?? 0) + 
                                                        ($recentPerformance[0]->matches_played ?? 0);
                                    $previousPerformance = ($recentPerformance[1]->goals ?? 0) + 
                                                          ($recentPerformance[1]->assists ?? 0) + 
                                                          ($recentPerformance[1]->matches_played ?? 0);
                                    
                                    if ($previousPerformance > 0) {
                                        $trend = round((($currentPerformance - $previousPerformance) / $previousPerformance) * 100);
                                        $trend = max(-20, min(20, $trend)); // Limiter entre -20% et +20%
                                    }
                                }
                                
                                $lastWeekScore = $displayScore - $trend;
                                $trendDirection = $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable');
                                
                                // Calcul de l'accélération (vitesse de changement par semaine)
                                $acceleration = abs($trend);
                                $accelerationLevel = $acceleration > 10 ? 'rapide' : ($acceleration > 5 ? 'modérée' : 'lente');
                                
                                // Couleur de la flèche selon l'accélération
                                $arrowColor = $acceleration > 10 ? 'text-red-500' : ($acceleration > 5 ? 'text-yellow-500' : 'text-green-500');
                                
                                // Texte explicatif de la tendance
                                if ($trendDirection === 'up') {
                                    $trendText = "Amélioration " . $accelerationLevel;
                                } elseif ($trendDirection === 'down') {
                                    $trendText = "Détérioration " . $accelerationLevel;
                                } else {
                                    $trendText = "Stable";
                                }
                            @endphp
                            
                            <div class="text-4xl font-bold {{ $scoreColor }} mb-2">{{ $displayScore }}%</div>
                            <div class="text-gray-300 text-sm mb-2">{{ $scoreStatus }}</div>
                            <div class="text-green-200 text-sm mb-4">Score de Santé 360</div>
                            
                            <!-- Ligne de tendance colorée -->
                            <div class="relative mb-3">
                                <div class="w-full h-3 bg-gradient-to-r from-blue-500 via-green-500 to-red-500 rounded-full"></div>
                                <!-- Curseur positionné selon le score (inversé : score élevé = gauche) -->
                                <div class="absolute top-0 transform -translate-y-1" style="left: {{ 100 - $displayScore }}%">
                                    <div class="w-4 h-4 bg-white border-2 border-gray-800 rounded-lg"></div>
                                </div>
                            </div>
                            
                            <!-- Indicateur de tendance et accélération -->
                            <div class="flex items-center justify-center space-x-2 mb-2">
                                @if($trendDirection === 'up')
                                    <i class="fas fa-arrow-up {{ $arrowColor }} text-lg"></i>
                                @elseif($trendDirection === 'down')
                                    <i class="fas fa-arrow-down {{ $arrowColor }} text-lg"></i>
                                @else
                                    <i class="fas fa-minus text-gray-400 text-lg"></i>
                                @endif
                                <span class="text-white text-sm font-medium">{{ $trendText }}</span>
                            </div>
                            
                            <!-- Détails de la tendance -->
                            <div class="text-xs text-gray-300">
                                @if($trendDirection !== 'stable')
                                    <span class="{{ $trend > 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $trend > 0 ? '+' : '' }}{{ $trend }}% cette semaine
                                    </span>
                                @else
                                    <span class="text-gray-400">Aucun changement</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Carte 3: Informations personnelles -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Informations</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-purple-200 text-sm">Âge:</span>
                            <span class="text-white font-medium">
                                @if($player->date_of_birth)
                                    {{ \Carbon\Carbon::parse($player->date_of_birth)->age }} ans
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-purple-200 text-sm">Taille:</span>
                            <span class="text-white font-medium">{{ $player->height ?? 'N/A' }} cm</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-purple-200 text-sm">Poids:</span>
                            <span class="text-white font-medium">{{ $player->weight ?? 'N/A' }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-purple-200 text-sm">Pied:</span>
                            <span class="text-white font-medium">{{ $player->preferred_foot ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Carte 4: Informations FIT -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-id-card text-white text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Informations FIT</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-orange-200 text-sm">Licence Club:</span>
                            <span class="text-white font-medium">
                                @php
                                    $activeLicense = \DB::table('player_licenses')
                                        ->where('player_id', $player->id)
                                        ->where('status', 'active')
                                        ->first();
                                @endphp
                                @if($activeLicense)
                                    {{ $activeLicense->license_number }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-orange-200 text-sm">FIT CONNECT ID:</span>
                            <span class="text-white font-medium">
                                @php
                                    $fifaRequest = \DB::table('license_requests')
                                        ->where('fifa_connect_id', 'LIKE', '%' . $player->id . '%')
                                        ->orWhere('current_club_id', $player->club_id ?? 0)
                                        ->first();
                                    
                                    if ($fifaRequest && $fifaRequest->fifa_connect_id) {
                                        echo $fifaRequest->fifa_connect_id;
                                    } else {
                                        // Génération locale si pas de sync FIFA
                                        $countryCode = '';
                                        if ($player->association) {
                                            if (stripos($player->association->country, 'Tunisie') !== false) {
                                                $countryCode = 'TUN';
                                            } elseif (stripos($player->association->country, 'France') !== false) {
                                                $countryCode = 'FRA';
                                            } elseif (stripos($player->association->country, 'Angleterre') !== false) {
                                                $countryCode = 'ENG';
                                            } else {
                                                $countryCode = substr($player->association->country, 0, 3);
                                            }
                                        } else {
                                            $countryCode = 'XXX';
                                        }
                                        echo $countryCode . '-P-' . str_pad($player->id, 9, '0', STR_PAD_LEFT);
                                    }
                                @endphp
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-orange-200 text-sm">Document d'identité:</span>
                            <span class="text-white font-medium">
                                @php
                                    $identityDoc = \DB::table('license_requests')
                                        ->where('fifa_connect_id', 'LIKE', '%' . $player->id . '%')
                                        ->orWhere('current_club_id', $player->club_id ?? 0)
                                        ->first();
                                    
                                    if ($identityDoc) {
                                        // Priorité 1: Passeport
                                        if ($identityDoc->passport_number) {
                                            echo 'Passeport: ' . $identityDoc->passport_number;
                                        }
                                        // Priorité 2: Carte d'identité nationale
                                        elseif ($identityDoc->national_id_number) {
                                            echo 'CNI: ' . $identityDoc->national_id_number;
                                        }
                                        // Priorité 3: Acte de naissance
                                        elseif ($identityDoc->birth_certificate_number) {
                                            echo 'Acte de naissance: ' . $identityDoc->birth_certificate_number;
                                        }
                                        else {
                                            echo 'N/A';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                @endphp
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation rapide vers les modules -->
            <div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
                <h3 class="text-lg font-semibold text-white mb-3">⚽ Statistiques FIT</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        // Récupération des données pour calculer les statistiques
                        $stats = \DB::table('player_season_stats')->where('player_id', $player->id)->get();
                        $age = $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : 25;
                        
                        // Calcul des statistiques FIT
                        $totalMatches = $stats->sum('matches_played');
                        $totalMinutes = $stats->sum('minutes_played');
                        $totalGoals = $stats->sum('goals');
                        $totalAssists = $stats->sum('assists');
                        
                        // Calcul des notes (basé sur les performances réelles ou valeurs par défaut)
                        $vit = min(100, max(50, 70 + ($totalMinutes / 100) + (30 - $age)));
                        $tir = min(100, max(50, 65 + ($totalGoals * 3)));
                        $pas = min(100, max(50, 70 + ($totalAssists * 4)));
                        $dri = min(100, max(50, 75 + ($totalMatches * 2)));
                        $def = min(100, max(50, 60 + ($totalMatches * 1.5)));
                        $phy = min(100, max(50, 80 - ($age - 20) + ($totalMinutes / 200)));
                        
                        // Note générale (moyenne des 6 attributs)
                        $gen = round(($vit + $tir + $pas + $dri + $def + $phy) / 6);
                        
                        // Détermination du rang
                        if ($gen >= 90) $rang = "Élite";
                        elseif ($gen >= 80) $rang = "Top 10";
                        elseif ($gen >= 70) $rang = "Pro";
                        elseif ($gen >= 60) $rang = "Amateur";
                        else $rang = "Débutant";
                    @endphp
                    
                    <!-- RANG -->
                    <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg p-3 text-center">
                        <div class="text-white font-bold text-lg">{{ $rang }}</div>
                        <div class="text-white text-xs opacity-90">RANG</div>
                    </div>
                    
                    <!-- GÉN -->
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg p-3 text-center">
                        <div class="text-white font-bold text-lg">{{ $gen }}</div>
                        <div class="text-white text-xs opacity-90">GÉN</div>
                    </div>
                    
                    <!-- VIT -->
                    <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg p-3 text-center">
                        <div class="text-white font-bold text-lg">{{ round($vit) }}</div>
                        <div class="text-white text-xs opacity-90">VIT</div>
                    </div>
                    
                    <!-- TIR -->
                    <div class="bg-gradient-to-br from-red-500 to-pink-500 rounded-lg p-3 text-center">
                        <div class="text-white font-bold text-lg">{{ round($tir) }}</div>
                        <div class="text-white text-xs opacity-90">TIR</div>
                    </div>
                    
                    <!-- PAS -->
                    <div class="bg-gradient-to-br from-purple-500 to-violet-500 rounded-lg p-3 text-center">
                        <div class="text-white font-bold text-lg">{{ round($pas) }}</div>
                        <div class="text-white text-xs opacity-90">PAS</div>
                    </div>
                    
                    <!-- DRI -->
                    <div class="bg-gradient-to-br from-cyan-500 to-blue-500 rounded-lg p-3 text-center">
                        <div class="text-white font-bold text-lg">{{ round($dri) }}</div>
                        <div class="text-white text-xs opacity-90">DRI</div>
                    </div>
                    
                    <!-- DÉF -->
                    <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-lg p-3 text-center">
                        <div class="text-white font-bold text-lg">{{ round($def) }}</div>
                        <div class="text-white text-xs opacity-90">DÉF</div>
                    </div>
                    
                    <!-- PHY -->
                    <div class="bg-gradient-to-br from-emerald-500 to-green-500 rounded-lg p-3 text-center">
                        <div class="text-white font-bold text-lg">{{ round($phy) }}</div>
                        <div class="text-white text-xs opacity-90">PHY</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 🔄 ONGLETS IDENTIQUES AU PORTAL FIFA (même structure) -->
    <!-- Onglets Principaux -->
    <div class="fifa-tabs">
        <button class="fifa-tab-button active" onclick="showFIFATab('performances')">Performances</button>
        <button class="fifa-tab-button" onclick="showFIFATab('trends')">Tendances</button>
        
        <!-- Nouveaux onglets FIFA -->
        <button class="fifa-tab-button" onclick="showFIFATab('notifications')">
            Notifications <span id="notifications-count">12</span>
        </button>
        <button class="fifa-tab-button" onclick="showFIFATab('health')">
            Santé & Bien-être
        </button>
        <button class="fifa-tab-button" onclick="showFIFATab('medical')">
            Médical <span id="medical-count">4</span>
        </button>
        <button class="fifa-tab-button" onclick="showFIFATab('devices')">
            Devices <span id="medical-count">3</span>
        </button>
        <button class="fifa-tab-button" onclick="showFIFATab('doping')">
            Dopage historique
        </button>
        <button class="fifa-tab-button" onclick="showFIFATab('licenses')">
            Historique licences
        </button>
    </div>

    <!-- Contenu des onglets -->
    <div id="performances-tab" class="fifa-tab-content active">
                            <h2>Centre de Performances FIT</h2>
        
        <!-- Sous-onglets Performances -->
        <div class="fifa-sub-tabs">
            <button class="fifa-sub-tab-button active" onclick="showFIFASubTab('overview')">Vue d'ensemble</button>
            <button class="fifa-sub-tab-button" onclick="showFIFASubTab('advanced-stats')">Statistiques avancées</button>
            <button class="fifa-sub-tab-button" onclick="showFIFASubTab('match-stats')">Statistiques de match</button>
            <button class="fifa-sub-tab-button" onclick="showFIFASubTab('comparison')">Analyse comparative</button>
        </div>
        
        <!-- Contenu des sous-onglets Performances -->
        <div id="overview-sub-tab" class="fifa-sub-tab-content active">
            <h3>Vue d'ensemble des performances</h3>
            <div id="overview-content">
                <!-- 🆕 CONTENU BLADE DIRECT AU LIEU DU CHARGEMENT -->
                <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <!-- Carte Forme Physique -->
                    <div class="fifa-stat-card">
                        <h3>🏃 Forme Physique</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Score de forme:</span>
                                <span style="color: #ffd700; font-weight: bold;">{{ $player->form_percentage ?? 'Données non disponibles' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>État:</span>
                                <span style="color: #87ceeb; font-weight: bold;">Données non disponibles</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Note Globale FIFA -->
                    <div class="fifa-stat-card">
                        <h3>⚽ Note Globale FIT</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Note actuelle:</span>
                                <span style="color: #ffd700; font-weight: bold;">{{ $player->overall_rating ?? 'Données non disponibles' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Potentiel:</span>
                                <span style="color: #51cf66; font-weight: bold;">{{ $player->potential_rating ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Statistiques Détaillées -->
                    <div class="fifa-stat-card">
                        <h3>📊 Statistiques Détaillées</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Vitesse:</span>
                                <span style="color: #ffd700; font-weight: bold;">{{ $player->speed ?? 'Données non disponibles' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Tir:</span>
                                <span style="color: #51cf66; font-weight: bold;">{{ $player->shooting ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graphiques de performance -->
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Graphique Radar des Ratings -->
                    <div class="fifa-stat-card">
                        <h3 class="text-lg font-bold mb-4">📈 Ratings FIT</h3>
                        <div class="h-64">
                            <canvas id="ratingsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Graphique Barres des Statistiques -->
                    <div class="fifa-stat-card">
                        <h3 class="text-lg font-bold mb-4">🎯 Statistiques de Match</h3>
                        <div class="h-64">
                            <canvas id="statsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="advanced-stats-sub-tab" class="fifa-sub-tab-content">
            <h3>Statistiques avancées</h3>
            <div id="advanced-stats-content">
                <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
                <div class="fifa-medical-grid">
                    <!-- Carte Compétences Techniques -->
                    <div class="fifa-medical-card">
                        <h4>📊 Compétences Techniques</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Passe</span>
                                <span class="fifa-stat-value highlight">{{ $player->passing ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dribble</span>
                                <span class="fifa-stat-value">{{ $player->dribbling ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Physique</span>
                                <span class="fifa-stat-value">{{ $player->physical ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Défense</span>
                                <span class="fifa-stat-value">{{ $player->defending ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Réactivité</span>
                                <span class="fifa-stat-value">{{ $player->reactions ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Vision</span>
                                <span class="fifa-stat-value">{{ $player->vision ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Performance Physique -->
                    <div class="fifa-medical-card">
                        <h4>💪 Performance Physique</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Endurance</span>
                                <span class="fifa-stat-value highlight">{{ $player->stamina ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Force</span>
                                <span class="fifa-stat-value">{{ $player->strength ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Agilité</span>
                                <span class="fifa-stat-value">{{ $player->agility ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Équilibre</span>
                                <span class="fifa-stat-value">{{ $player->balance ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Accélération</span>
                                <span class="fifa-stat-value">{{ $player->acceleration ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Vitesse de Sprint</span>
                                <span class="fifa-stat-value">{{ $player->sprint_speed ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Intelligence de Jeu -->
                    <div class="fifa-medical-card">
                        <h4>🧠 Intelligence de Jeu</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Positionnement</span>
                                <span class="fifa-stat-value highlight">{{ $player->positioning ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Anticipation</span>
                                <span class="fifa-stat-value">{{ $player->anticipation ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Composure</span>
                                <span class="fifa-stat-value">{{ $player->composure ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Décisions</span>
                                <span class="fifa-stat-value">{{ $player->decisions ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Leadership</span>
                                <span class="fifa-stat-value">{{ $player->leadership ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Esprit d'équipe</span>
                                <span class="fifa-stat-value">{{ $player->teamwork ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="match-stats-sub-tab" class="fifa-sub-tab-content">
            <h3>Statistiques de match</h3>
            <div id="match-stats-content">
                <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
                <div class="fifa-health-grid">
                    <!-- Carte Performance en Match -->
                    <div class="fifa-health-card">
                        <h4>⚽ Performance en Match</h4>
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Matchs joués</span>
                                <span class="fifa-stat-value highlight">{{ $player->matches_played ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Buts marqués</span>
                                <span class="fifa-stat-value">{{ $player->goals_scored ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Passes décisives</span>
                                <span class="fifa-stat-value">{{ $player->assists ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Minutes jouées</span>
                                <span class="fifa-stat-value">{{ $player->minutes_played ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Cartons jaunes</span>
                                <span class="fifa-stat-value">{{ $player->yellow_cards ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Cartons rouges</span>
                                <span class="fifa-stat-value">{{ $player->red_cards ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Statistiques Défensives -->
                    <div class="fifa-health-card">
                        <h4>🛡️ Statistiques Défensives</h4>
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Tacles réussis</span>
                                <span class="fifa-stat-value highlight">{{ $player->tackles_won ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Interceptions</span>
                                <span class="fifa-stat-value">{{ $player->interceptions ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dégagements</span>
                                <span class="fifa-stat-value">{{ $player->clearances ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Duels gagnés</span>
                                <span class="fifa-stat-value">{{ $player->duels_won ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Statistiques Offensives -->
                    <div class="fifa-health-card">
                        <h4>⚽ Statistiques Offensives</h4>
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Tirs cadrés</span>
                                <span class="fifa-stat-value highlight">{{ $player->shots_on_target ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Précision des tirs</span>
                                <span class="fifa-stat-value">{{ $player->shot_accuracy ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Passes réussies</span>
                                <span class="fifa-stat-value">{{ $player->passes_completed ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Précision des passes</span>
                                <span class="fifa-stat-value">{{ $player->pass_accuracy ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="comparison-sub-tab" class="fifa-sub-tab-content">
            <h3>Analyse comparative</h3>
            <div id="comparison-content">
                <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
                <div class="fifa-medical-grid">
                    <!-- Carte Comparaison avec la Ligue -->
                    <div class="fifa-medical-card">
                        <h4>📈 Comparaison avec la Ligue</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Position dans le classement</span>
                                <span class="fifa-stat-value highlight">{{ $player->league_position ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Moyenne de la ligue</span>
                                <span class="fifa-stat-value">{{ $player->league_average ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Écart avec la moyenne</span>
                                <span class="fifa-stat-value">{{ $player->performance_gap ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Comparaison avec les Joueurs Similaires -->
                    <div class="fifa-medical-card">
                        <h4>👥 Comparaison avec les Joueurs Similaires</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Rang dans la position</span>
                                <span class="fifa-stat-value highlight">{{ $player->position_rank ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Top 10% de la position</span>
                                <span class="fifa-stat-value">{{ $player->top_percentile ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Potentiel d'amélioration</span>
                                <span class="fifa-stat-value">{{ $player->improvement_potential ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Évolution Temporelle -->
                    <div class="fifa-medical-card">
                        <h4>📊 Évolution Temporelle</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Tendance sur 6 mois</span>
                                <span class="fifa-stat-value highlight">{{ $player->trend_6months ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Progression annuelle</span>
                                <span class="fifa-stat-value">{{ $player->annual_progress ?? 'Données non disponibles' }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Objectifs atteints</span>
                                <span class="fifa-stat-value">{{ $player->goals_achieved ?? 'Données non disponibles' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="trends-tab" class="fifa-tab-content">
        <h2>📈 Tendances & Évolution</h2>
        <div id="trends-content">
            <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
            
            <!-- Section Évolution & Tendances -->
            <div class="fifa-medical-card mb-6">
                <h4 class="text-lg font-bold mb-4 flex items-center">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>
                    Évolution & Tendances (30 derniers jours)
                </h4>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Graphique des tendances -->
                    <div>
                        <div class="chart-container mb-4" style="height: 300px;">
                            <canvas id="trendsChart"></canvas>
                        </div>
                    </div>
                    <!-- Indicateurs de tendances -->
                    <div class="space-y-3">
                        <div class="p-3 border-l-4 border-green-500 bg-green-50 rounded-lg">
                            <div class="font-medium text-green-800">📈 Amélioration Notable</div>
                            <div class="text-sm text-green-700">Qualité du sommeil en hausse constante (+12%)</div>
                        </div>
                        <div class="p-3 border-l-4 border-blue-500 bg-blue-50 rounded-lg">
                            <div class="font-medium text-blue-800">💪 Progression Physique</div>
                            <div class="text-sm text-blue-700">VO2 Max et endurance en amélioration</div>
                        </div>
                        <div class="p-3 border-l-4 border-yellow-500 bg-yellow-50 rounded-lg">
                            <div class="font-medium text-yellow-800">⚠️ Attention Nutrition</div>
                            <div class="text-sm text-yellow-700">Apport calorique légèrement insuffisant</div>
                        </div>
                        <div class="p-3 border-l-4 border-purple-500 bg-purple-50 rounded-lg">
                            <div class="font-medium text-purple-800">🧠 Bien-être Mental</div>
                            <div class="text-sm text-purple-700">Niveau de stress maîtrisé, continuer méditation</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section Facteurs SDOH -->
            <div class="fifa-medical-card mb-6">
                <h4 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-users text-teal-600 mr-3"></i>
                    Santé & Bien-être – Facteurs SDOH
                </h4>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Radar Chart SDOH -->
                    <div class="w-full">
                        <div class="chart-container mb-4" style="height: 400px;">
                            <canvas id="sdohRadarChart"></canvas>
                        </div>
                    </div>
                    
                                            <!-- SDOH Details -->
                        <div class="w-full space-y-4">
                            <p class="text-gray-700 mb-4">
                                Cet indicateur <strong>SDOH</strong> (Social Determinants of Health) donne une vision
                                à {{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}° de l'état de bien-être global du joueur, en intégrant les
                                facteurs sociaux, environnementaux et comportementaux.
                            </p>
                            
                            <!-- SDOH Factors breakdown -->
                            <div class="space-y-3">
                                @if($sdohFactors)
                                    <div class="flex items-center justify-between p-3 bg-teal-50 rounded-lg border-l-4 border-teal-500">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-home text-teal-600"></i>
                                            <span class="font-medium text-gray-800">Environnement de vie</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-teal-600">{{ $sdohFactors->environment_score }}/100</div>
                                            <div class="text-xs text-gray-600">Qualité logement, stabilité</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border-l-4 border-green-500">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-heart text-green-600"></i>
                                            <span class="font-medium text-gray-800">Soutien social</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-green-600">{{ $sdohFactors->social_support_score }}/100</div>
                                            <div class="text-xs text-gray-600">Famille, amis, entourage</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-user-md text-blue-600"></i>
                                            <span class="font-medium text-gray-800">Accès aux soins</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-blue-600">{{ $sdohFactors->healthcare_access_score }}/100</div>
                                            <div class="text-xs text-gray-600">Rapidité, disponibilité</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-dollar-sign text-yellow-600"></i>
                                            <span class="font-medium text-gray-800">Situation financière</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-yellow-600">{{ $sdohFactors->financial_status_score }}/100</div>
                                            <div class="text-xs text-gray-600">Stabilité économique</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-graduation-cap text-purple-600"></i>
                                            <span class="font-medium text-gray-800">Éducation & Formation</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-purple-600">{{ $sdohFactors->education_score }}/100</div>
                                            <div class="text-xs text-gray-600">Niveau académique, compétences</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center text-gray-500 py-8">
                                        <i class="fas fa-info-circle text-2xl mb-2"></i>
                                        <p>Données SDOH non disponibles</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                </div>
            </div>
            
            <!-- Section Prédictions et Projections -->
            <div class="fifa-medical-card">
                <h4 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-crystal-ball text-indigo-600 mr-3"></i>
                    Prédictions & Projections
                </h4>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    @if($performancePredictions && $performancePredictions->count() > 0)
                        @foreach($performancePredictions as $prediction)
                            <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600 mb-2">
                                        @if($prediction->prediction_type == 'performance') 📊
                                        @elseif($prediction->prediction_type == 'health') ❤️
                                        @elseif($prediction->prediction_type == 'wellbeing') 🧘
                                        @endif
                                    </div>
                                    <h5 class="font-semibold text-blue-800 mb-2">
                                        @if($prediction->prediction_type == 'performance') Performance
                                        @elseif($prediction->prediction_type == 'health') Santé
                                        @elseif($prediction->prediction_type == 'wellbeing') Bien-être
                                        @endif
                                    </h5>
                                    <div class="text-sm text-blue-700">
                                        <div class="mb-1">Tendance: <span class="font-semibold">
                                            @if($prediction->trend_direction == 'ascending') 📈 Ascendante
                                            @elseif($prediction->trend_direction == 'stable') 🟢 Stable
                                            @elseif($prediction->trend_direction == 'descending') 📉 Descendante
                                            @endif
                                        </span></div>
                                        <div class="mb-1">Prédiction 3 mois: <span class="font-semibold">+{{ $prediction->predicted_score_3months - $prediction->current_score }}%</span></div>
                                        <div>Objectif atteint: <span class="font-semibold">{{ $prediction->current_score }}%</span></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-3 text-center text-gray-500 py-8">
                            <i class="fas fa-info-circle text-2xl mb-2"></i>
                            <p>Aucune prédiction disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Nouveaux onglets FIFA -->
    <div id="notifications-tab" class="fifa-tab-content">
        <h2>🔔 Notifications FIFA</h2>
        <div id="notifications-content">
            <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="fifa-stat-card">
                    <h3>🚨 Alertes Blessures</h3>
                    <div style="text-align: left; margin-top: 15px;">
                        @if($injuryAlerts)
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Risque de blessure:</span>
                                <span style="color: #ffd700; font-weight: bold;">{{ $injuryAlerts->risk_level }}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Type:</span>
                                <span style="color: #87ceeb; font-weight: bold;">{{ $injuryAlerts->injury_type }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Zone:</span>
                                <span style="color: #87ceeb; font-weight: bold;">{{ $injuryAlerts->body_part }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>État:</span>
                                @php
                                    $riskLevel = $injuryAlerts->risk_level;
                                    $riskColor = $riskLevel > 20 ? '#ff6b6b' : ($riskLevel > 10 ? '#ffd700' : '#51cf66');
                                    $riskText = $riskLevel > 20 ? 'ÉLEVÉ' : ($riskLevel > 10 ? 'MODÉRÉ' : 'FAIBLE');
                                @endphp
                                <span style="color: {{ $riskColor }}; font-weight: bold;">{{ $riskText }}</span>
                            </div>
                        @else
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Risque de blessure:</span>
                                <span style="color: #ffd700; font-weight: bold;">Données non disponibles</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>État:</span>
                                <span style="color: #87ceeb; font-weight: bold;">Données non disponibles</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="fifa-stat-card">
                    <h3>💊 Médicaments Actifs</h3>
                    <div style="text-align: left; margin-top: 15px;">
                        @if($playerMedications && $playerMedications->count() > 0)
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Traitements actifs:</span>
                                <span style="color: #ffd700; font-weight: bold;">{{ $playerMedications->count() }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Type principal:</span>
                                <span style="color: #ffd700; font-weight: bold;">{{ ucfirst($playerMedications->first()->medication_type) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Dernier ajout:</span>
                                <span style="color: #87ceeb; font-weight: bold;">{{ \Carbon\Carbon::parse($playerMedications->first()->start_date)->format('d M Y') }}</span>
                            </div>
                        @else
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Traitements:</span>
                                <span style="color: #ffd700; font-weight: bold;">Aucun traitement actif</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Type:</span>
                                <span style="color: #ffd700; font-weight: bold;">Données non disponibles</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="fifa-stat-card">
                    <h3>📊 Alertes Performance</h3>
                    <div style="text-align: left; margin-top: 15px;">
                        @if($playerNotifications && $playerNotifications->count() > 0)
                            @php
                                $performanceAlerts = $playerNotifications->where('notification_type', 'performance_alert');
                                $medicalAlerts = $playerNotifications->where('notification_type', 'medical_alert');
                            @endphp
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Alertes actives:</span>
                                <span style="color: #ffd700; font-weight: bold;">{{ $playerNotifications->count() }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Alertes performance:</span>
                                <span style="color: #87ceeb; font-weight: bold;">{{ $performanceAlerts->count() }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Alertes médicales:</span>
                                <span style="color: #87ceeb; font-weight: bold;">{{ $medicalAlerts->count() }}</span>
                            </div>
                        @else
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme physique:</span>
                                <span style="color: #87ceeb; font-weight: bold;">Aucune alerte active</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Moral:</span>
                                <span style="color: #87ceeb; font-weight: bold;">Données non disponibles</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Liste des Notifications Récentes -->
                <div class="fifa-stat-card mt-6" style="grid-column: 1 / -1;">
                    <h3>📋 Notifications Récentes</h3>
                    <div style="text-align: left; margin-top: 15px;">
                        @if($playerNotifications && $playerNotifications->count() > 0)
                            <div class="space-y-3">
                                @foreach($playerNotifications->take(3) as $notification)
                                    <div class="p-3 bg-gray-50 rounded-lg border-l-4 
                                        @if($notification->severity == 'critical') border-red-500 bg-red-50
                                        @elseif($notification->severity == 'high') border-orange-500 bg-orange-50
                                        @elseif($notification->severity == 'medium') border-yellow-500 bg-yellow-50
                                        @else border-blue-500 bg-blue-50
                                        @endif">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800">{{ $notification->title }}</div>
                                                <div class="text-sm text-gray-600 mt-1">{{ $notification->message }}</div>
                                                <div class="text-xs text-gray-500 mt-2">
                                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($notification->severity == 'critical') bg-red-100 text-red-800
                                                    @elseif($notification->severity == 'high') bg-orange-100 text-orange-800
                                                    @elseif($notification->severity == 'medium') bg-yellow-100 text-yellow-800
                                                    @else bg-blue-100 text-blue-800
                                                    @endif">
                                                    {{ ucfirst($notification->severity) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-4">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucune notification active</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="health-tab" class="fifa-tab-content">
        <h2>Santé & Bien-être</h2>
        <div id="health-content">
            <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
            <div class="fifa-health-grid">
                <!-- Carte État Physique Général -->
                <div class="fifa-health-card">
                    <h4>❤️ État Physique Général</h4>
                    @if($playerHealthWellbeing)
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Score de Forme</span>
                                <span class="fifa-stat-value highlight">{{ $playerHealthWellbeing->fitness_score }}/100</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: {{ $playerHealthWellbeing->fitness_score }}%"></div>
                            </div>
                            <div class="fifa-health-indicator">
                                @php
                                    $fitnessScore = $playerHealthWellbeing->fitness_score;
                                    $fitnessColor = $fitnessScore >= 80 ? '#51cf66' : ($fitnessScore >= 60 ? '#ffd700' : '#ff6b6b');
                                    $fitnessText = $fitnessScore >= 80 ? 'EXCELLENT' : ($fitnessScore >= 60 ? 'BON' : 'À AMÉLIORER');
                                @endphp
                                <span class="fifa-health-status" style="color: {{ $fitnessColor }};">{{ $fitnessText }}</span>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Niveau d'Énergie</span>
                                <span class="fifa-stat-value">{{ $playerHealthWellbeing->energy_level }}/100</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: {{ $playerHealthWellbeing->energy_level }}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Qualité du Sommeil</span>
                                <span class="fifa-stat-value">{{ $playerHealthWellbeing->sleep_quality }}/10</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: {{ $playerHealthWellbeing->sleep_quality * 10 }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Score de Forme</span>
                                <span class="fifa-stat-value highlight">Données non disponibles</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: 0%"></div>
                            </div>
                            <div class="fifa-health-indicator">
                                <span class="fifa-health-status">Données non disponibles</span>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Niveau d'Énergie</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Qualité du Sommeil</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Carte Nutrition et Hydratation -->
                <div class="fifa-health-card">
                    <h4>🥗 Nutrition et Hydratation</h4>
                    @if($playerNutrition)
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Hydratation</span>
                                <span class="fifa-stat-value">{{ $playerNutrition->hydration_score }}/100</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: {{ $playerNutrition->hydration_score }}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Calories Consommées</span>
                                <span class="fifa-stat-value">{{ $playerNutrition->total_calories }} kcal</span>
                            </div>
                            <div class="fifa-progress-bar">
                                @php
                                    $caloriePercentage = min(100, ($playerNutrition->total_calories / 3000) * 100);
                                @endphp
                                <div class="fifa-progress-fill" style="width: {{ $caloriePercentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Protéines</span>
                                <span class="fifa-stat-value">{{ $playerNutrition->protein_grams }}g</span>
                            </div>
                            <div class="fifa-progress-bar">
                                @php
                                    $proteinPercentage = min(100, ($playerNutrition->protein_grams / 200) * 100);
                                @endphp
                                <div class="fifa-progress-fill" style="width: {{ $proteinPercentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Qualité des Repas</span>
                                <span class="fifa-stat-value">{{ $playerNutrition->meal_quality_score }}/10</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: {{ $playerNutrition->meal_quality_score * 10 }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Hydratation</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Calories Consommées</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Protéines</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Carte Récupération -->
                <div class="fifa-health-card">
                    <h4>🔄 Récupération</h4>
                    @if($playerRecovery)
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Heures de Sommeil</span>
                                <span class="fifa-stat-value">{{ $playerRecovery->sleep_hours }}h</span>
                            </div>
                            <div class="fifa-progress-bar">
                                @php
                                    $sleepPercentage = min(100, ($playerRecovery->sleep_hours / 8) * 100);
                                @endphp
                                <div class="fifa-progress-fill" style="width: {{ $sleepPercentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Qualité du Sommeil</span>
                                <span class="fifa-stat-value">{{ $playerRecovery->sleep_quality_score }}/10</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: {{ $playerRecovery->sleep_quality_score * 10 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Douleur Musculaire</span>
                                <span class="fifa-stat-value">{{ $playerRecovery->muscle_soreness }}/100</span>
                            </div>
                            <div class="fifa-progress-bar">
                                @php
                                    $sorenessPercentage = 100 - $playerRecovery->muscle_soreness;
                                @endphp
                                <div class="fifa-progress-fill" style="width: {{ $sorenessPercentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Niveau de Fatigue</span>
                                <span class="fifa-stat-value">{{ $playerRecovery->fatigue_level }}/100</span>
                            </div>
                            <div class="fifa-progress-bar">
                                @php
                                    $fatiguePercentage = 100 - $playerRecovery->fatigue_level;
                                @endphp
                                <div class="fifa-progress-fill" style="width: {{ $fatiguePercentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Activités de Récupération</span>
                                <span class="fifa-stat-value">{{ $playerRecovery->stretching_minutes }}min étirement</span>
                            </div>
                            <div class="fifa-progress-bar">
                                @php
                                    $stretchingPercentage = min(100, ($playerRecovery->stretching_minutes / 30) * 100);
                                @endphp
                                <div class="fifa-progress-fill" style="width: {{ $stretchingPercentage }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Temps de Récupération</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Qualité de Récupération</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="medical-tab" class="fifa-tab-content">
        <h2>Médical</h2>
        <div id="medical-content">
            <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE (5 cartes) -->
            <div class="fifa-medical-grid space-y-4">
                <!-- Carte État Général de Santé -->
                <div class="fifa-medical-card w-full">
                    <h4>🏥 État Général de Santé</h4>
                    <div class="fifa-medical-stat">
                        @if($playerMedicalAptitude)
                            <div class="fifa-stat-header">
                                <span>Score de Santé Global</span>
                                <span class="fifa-stat-value highlight">{{ $playerMedicalAptitude->overall_health_score }}/100</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Statut Médical</span>
                                @php
                                    $statusColor = $playerMedicalAptitude->medical_status == 'fit' ? '#51cf66' : 
                                                 ($playerMedicalAptitude->medical_status == 'temporarily_unfit' ? '#ffd700' : '#ff6b6b');
                                    $statusText = $playerMedicalAptitude->medical_status == 'fit' ? '🟢 APTE AU JEU' : 
                                                 ($playerMedicalAptitude->medical_status == 'temporarily_unfit' ? '🟡 TEMPORAIREMENT INAPTE' : '🔴 INAPTE');
                                @endphp
                                <span class="fifa-stat-value positive" style="color: {{ $statusColor }};">{{ $statusText }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Niveau de Forme</span>
                                <span class="fifa-stat-value">{{ ucfirst($playerMedicalAptitude->fitness_level) }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dernière Évaluation</span>
                                <span class="fifa-stat-value">{{ \Carbon\Carbon::parse($playerMedicalAptitude->assessment_date)->format('d M Y') }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Médecin Traitant</span>
                                <span class="fifa-stat-value">Dr. Martinez (Médecin FIFA)</span>
                            </div>
                        @else
                            <div class="fifa-stat-header">
                                <span>Score de Santé Global</span>
                                <span class="fifa-stat-value highlight">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Statut Médical</span>
                                <span class="fifa-stat-value positive">🟢 APTE AU JEU</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dernière Évaluation</span>
                                <span class="fifa-stat-value">{{ now()->format('d M Y') }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Médecin Traitant</span>
                                <span class="fifa-stat-value">Dr. Martinez (Médecin FIFA)</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Prochaine Consultation</span>
                                <span class="fifa-stat-value">{{ now()->addDays(7)->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Paramètres Vitaux -->
                <div class="fifa-medical-card w-full">
                    <h4>💓 Paramètres Vitaux</h4>
                    <div class="fifa-medical-stat">
                        @if($playerVitalSigns)
                            <div class="fifa-stat-header">
                                <span>Fréquence Cardiaque Repos</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->heart_rate_resting }} bpm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Fréquence Cardiaque Max</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->heart_rate_max }} bpm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Tension Artérielle</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->blood_pressure_systolic }}/{{ $playerVitalSigns->blood_pressure_diastolic }} mmHg</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Température Corporelle</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->temperature }}°C</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>SpO2 (Oxygénation)</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->oxygen_saturation }}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Réserve Cardiaque</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->heart_rate_max - $playerVitalSigns->heart_rate_resting }} bpm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Récupération Cardiaque</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->heart_rate_recovery }} bpm</span>
                            </div>
                        @else
                            <div class="fifa-stat-header">
                                <span>Fréquence Cardiaque Repos</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Fréquence Cardiaque Max</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Tension Artérielle</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Température Corporelle</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>SpO2 (Oxygénation)</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Réserve Cardiaque</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Biométrie Avancée -->
                <div class="fifa-medical-card w-full">
                    <h4>📊 Biométrie Avancée</h4>
                    <div class="fifa-medical-stat">
                        @if($playerVitalSigns)
                            <div class="fifa-stat-header">
                                <span>Poids Corporel</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->body_weight }} kg</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Taille</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->body_height }} cm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>IMC</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->bmi }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Masse Grasse</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->body_fat_percentage }}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Masse Musculaire</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->muscle_mass_percentage }}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Hydratation</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->hydration_percentage }}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Glycémie</span>
                                <span class="fifa-stat-value">{{ $playerVitalSigns->blood_glucose }} mmol/L</span>
                            </div>
                        @else
                            <div class="fifa-stat-header">
                                <span>IMC</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Masse Corporelle</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Masse Grasse</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Masse Musculaire</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Hydratation</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Métabolisme de Base</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte PCMA (Protocoles de Contrôle Médical et d'Aptitude) -->
                <div class="fifa-medical-card w-full">
                    <h4>🏥 PCMA - Contrôle Médical FIFA</h4>
                    <div class="fifa-medical-stat">
                        @if($playerPcma)
                            <div class="fifa-stat-header">
                                <span>Statut PCMA</span>
                                @php
                                    $pcmaColor = $playerPcma->pcma_status == 'approved' ? '#51cf66' : 
                                                ($playerPcma->pcma_status == 'pending' ? '#ffd700' : '#ff6b6b');
                                    $pcmaText = $playerPcma->pcma_status == 'approved' ? '✅ APPROUVÉ' : 
                                                ($playerPcma->pcma_status == 'pending' ? '⏳ EN ATTENTE' : '❌ REJETÉ');
                                @endphp
                                <span class="fifa-stat-value positive" style="color: {{ $pcmaColor }};">{{ $pcmaText }}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Score Global PCMA</span>
                                <span class="fifa-stat-value highlight">{{ $playerPcma->pcma_score }}/100</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Forme Cardiovasculaire</span>
                                <span class="fifa-stat-value">{{ $playerPcma->cardiovascular_fitness }}/100</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Forme Respiratoire</span>
                                <span class="fifa-stat-value">{{ $playerPcma->respiratory_fitness }}/100</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Forme Musculosquelettique</span>
                                <span class="fifa-stat-value">{{ $playerPcma->musculoskeletal_fitness }}/100</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Forme Neurologique</span>
                                <span class="fifa-stat-value">{{ $playerPcma->neurological_fitness }}/100</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Prochaine Évaluation</span>
                                <span class="fifa-stat-value">{{ \Carbon\Carbon::parse($playerPcma->next_assessment_date)->format('d M Y') }}</span>
                            </div>
                        @else
                            <div class="fifa-stat-header">
                                <span>Statut PCMA</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Score Global PCMA</span>
                                <span class="fifa-stat-value highlight">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Forme Cardiovasculaire</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Forme Respiratoire</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Forme Musculosquelettique</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Forme Neurologique</span>
                                <span class="fifa-stat-value">Données non disponibles</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Blessures et Maladies -->
                <div class="fifa-medical-card w-full">
                    <h4>🩹 Blessures et Maladies - Codes ICD-11</h4>
                    <div class="fifa-medical-stat">
                        @if($playerInjuriesDiseases && $playerInjuriesDiseases->count() > 0)
                            <!-- Statistiques générales -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $playerInjuriesDiseases->count() }}</div>
                                    <div class="text-sm text-blue-700">Incidents totaux</div>
                                </div>
                                <div class="text-center p-3 bg-red-50 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">{{ $playerInjuriesDiseases->where('severity', 'severe')->count() + $playerInjuriesDiseases->where('severity', 'critical')->count() }}</div>
                                    <div class="text-sm text-red-700">Blessures graves</div>
                                </div>
                            </div>
                            
                            <!-- Graphique des blessures par type -->
                            <div class="mb-4">
                                <h5 class="font-semibold mb-2">Répartition par type</h5>
                                <div class="chart-container" style="height: 200px;">
                                    <canvas id="injuriesTypeChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Graphique des blessures par gravité -->
                            <div class="mb-4">
                                <h5 class="font-semibold mb-2">Répartition par gravité</h5>
                                <div class="chart-container" style="height: 200px;">
                                    <canvas id="injuriesSeverityChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Liste des incidents récents -->
                            <div class="mt-4">
                                <h5 class="font-semibold mb-2">Incidents récents</h5>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach($playerInjuriesDiseases->take(3) as $incident)
                                        <div class="p-2 bg-gray-50 rounded border-l-4 
                                            @if($incident->severity == 'critical') border-red-500
                                            @elseif($incident->severity == 'severe') border-orange-500
                                            @elseif($incident->severity == 'moderate') border-yellow-500
                                            @else border-green-500
                                            @endif">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <div class="font-medium text-sm">{{ $incident->icd_11_description }}</div>
                                                    <div class="text-xs text-gray-600">Code: {{ $incident->icd_11_code }}</div>
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($incident->incident_date)->format('d M Y') }}</div>
                                                </div>
                                                <div class="ml-2">
                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                        @if($incident->severity == 'critical') bg-red-100 text-red-800
                                                        @elseif($incident->severity == 'severe') bg-orange-100 text-orange-800
                                                        @elseif($incident->severity == 'moderate') bg-yellow-100 text-yellow-800
                                                        @else bg-green-100 text-green-800
                                                        @endif">
                                                        {{ ucfirst($incident->severity) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucun incident médical enregistré</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Tests de Performance -->
                <div class="fifa-medical-card">
                    <h4>🏃 Tests de Performance</h4>
                    <div class="fifa-medical-stat">
                        <div class="fifa-stat-header">
                            <span>Test de Cooper</span>
                            <span class="fifa-stat-value">{{ $player->cooper_test ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Test de Yo-Yo</span>
                            <span class="fifa-stat-value">{{ $player->yoyo_test ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Test de Wingate</span>
                            <span class="fifa-stat-value">{{ $player->wingate_test ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Test de Squat Jump</span>
                            <span class="fifa-stat-value">{{ $player->squat_jump ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Test de Sprint 30m</span>
                            <span class="fifa-stat-value">{{ $player->sprint_30m ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Test de Flexibilité</span>
                            <span class="fifa-stat-value">{{ $player->flexibility_test ?? 'Données non disponibles' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Carte Analyses de Laboratoire -->
                <div class="fifa-medical-card">
                    <h4>🧪 Analyses de Laboratoire</h4>
                    <div class="fifa-medical-stat">
                        <div class="fifa-stat-header">
                            <span>Hémoglobine</span>
                            <span class="fifa-stat-value">{{ $player->hemoglobin ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Hématocrite</span>
                            <span class="fifa-stat-value">{{ $player->hematocrit ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Fer Sérique</span>
                            <span class="fifa-stat-value">{{ $player->serum_iron ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Vitamine D</span>
                            <span class="fifa-stat-value">{{ $player->vitamin_d ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>CRP</span>
                            <span class="fifa-stat-value">{{ $player->crp ?? 'Données non disponibles' }}</span>
                        </div>
                        <div class="fifa-stat-header">
                            <span>Cholestérol Total</span>
                            <span class="fifa-stat-value">{{ $player->total_cholesterol ?? 'Données non disponibles' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="devices-tab" class="fifa-tab-content">
        <h2>🔄 Devices & Systèmes Connectés</h2>
        <div id="devices-content">
            <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
            <div class="fifa-devices-grid space-y-6">
                
                <!-- Carte Devices Sportifs Connectés -->
                <div class="fifa-device-card w-full">
                    <h4>⌚ Devices Sportifs & Wearables</h4>
                    <div class="fifa-device-stat">
                        @if($sportsDevices && $sportsDevices->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                @foreach($sportsDevices as $device)
                                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 
                                        @if($device->connection_status == 'connected') border-green-500
                                        @elseif($device->connection_status == 'syncing') border-blue-500
                                        @elseif($device->connection_status == 'error') border-red-500
                                        @else border-gray-500
                                        @endif">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-semibold text-gray-800">{{ $device->device_name }}</div>
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($device->connection_status == 'connected') bg-green-100 text-green-800
                                                @elseif($device->connection_status == 'syncing') bg-blue-100 text-blue-800
                                                @elseif($device->connection_status == 'error') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($device->connection_status) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 mb-2">{{ $device->brand }} {{ $device->model }}</div>
                                        <div class="text-xs text-gray-500 mb-2">API: {{ $device->api_endpoint }}</div>
                                        <div class="flex justify-between text-xs">
                                            <span>Batterie: {{ $device->battery_level }}%</span>
                                            <span>Sync: {{ \Carbon\Carbon::parse($device->last_sync)->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucun device sportif connecté</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Données Comportementales -->
                <div class="fifa-device-card w-full">
                    <h4>📊 Données Comportementales & Biométriques</h4>
                    <div class="fifa-device-stat">
                        @if($behavioralData)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $behavioralData->sleep_hours }}h</div>
                                    <div class="text-sm text-blue-700">Sommeil</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($behavioralData->steps_count) }}</div>
                                    <div class="text-sm text-green-700">Pas</div>
                                </div>
                                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $behavioralData->active_minutes }}min</div>
                                    <div class="text-sm text-yellow-700">Actif</div>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">{{ $behavioralData->mood_score }}/10</div>
                                    <div class="text-sm text-purple-700">Humeur</div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 text-center">Source: {{ $behavioralData->device_source }} | Dernière mise à jour: {{ \Carbon\Carbon::parse($behavioralData->created_at)->format('d M Y H:i') }}</div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucune donnée comportementale disponible</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Centres de Physiothérapie Connectés -->
                <div class="fifa-device-card w-full">
                    <h4>🏥 Centres de Physiothérapie & Rééducation</h4>
                    <div class="fifa-device-stat">
                        @if($physioCenters && $physioCenters->count() > 0)
                            <div class="space-y-4">
                                @foreach($physioCenters as $center)
                                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <div class="font-semibold text-gray-800">{{ $center->center_name }}</div>
                                                <div class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $center->center_type) }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-gray-600">{{ $center->session_count }} sessions</div>
                                                <div class="text-xs text-gray-500">Dernière: {{ \Carbon\Carbon::parse($center->last_session_date)->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-2">API: {{ $center->api_endpoint }}</div>
                                        <div class="text-sm text-gray-700 mb-2">
                                            <strong>Plan de traitement:</strong> 
                                            @php $plan = json_decode($center->treatment_plan, true); @endphp
                                            {{ $plan['objectif'] ?? 'Non spécifié' }}
                                        </div>
                                        @if($center->next_appointment)
                                            <div class="text-sm text-blue-600">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                Prochain RDV: {{ \Carbon\Carbon::parse($center->next_appointment)->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucun centre de physiothérapie connecté</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Applications de Santé Mentale -->
                <div class="fifa-device-card w-full">
                    <h4>🧠 Applications de Santé Mentale & Bien-être</h4>
                    <div class="fifa-device-stat">
                        @if($mentalHealthApps && $mentalHealthApps->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($mentalHealthApps as $app)
                                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-purple-500">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <div class="font-semibold text-gray-800">{{ $app->app_name }}</div>
                                                <div class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $app->app_type) }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-gray-600">{{ $app->session_duration }}min</div>
                                                <div class="text-xs text-gray-500">Session</div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-2">API: {{ $app->api_endpoint }}</div>
                                                                                    <div class="grid grid-cols-2 gap-2 mb-3">
                                                <div class="text-center p-2 bg-purple-50 rounded">
                                                    <div class="text-lg font-bold text-purple-600">{{ $app->mood_score }}/10</div>
                                                    <div class="text-xs text-purple-700">Humeur</div>
                                                </div>
                                            <div class="text-center p-2 bg-green-50 rounded">
                                                <div class="text-lg font-bold text-green-600">{{ $app->wellness_score }}/10</div>
                                                <div class="text-xs text-green-700">Bien-être</div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            <i class="fas fa-clock mr-1"></i>
                                            Dernière session: {{ \Carbon\Carbon::parse($app->last_session_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucune app de santé mentale connectée</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte APIs & Intégrations -->
                <div class="fifa-device-card w-full">
                    <h4>🔌 APIs & Intégrations Systèmes</h4>
                    <div class="fifa-device-stat">
                        @if($apiIntegrations && $apiIntegrations->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($apiIntegrations as $api)
                                    @php
                                        $colors = [
                                            'health' => 'blue',
                                            'fitness' => 'green', 
                                            'mental_health' => 'purple',
                                            'medical' => 'orange',
                                            'sports' => 'red'
                                        ];
                                        $color = $colors[$api->api_type] ?? 'gray';
                                        $icons = [
                                            'health' => '📱',
                                            'fitness' => '🏃',
                                            'mental_health' => '🧠',
                                            'medical' => '🏥',
                                            'sports' => '⚽'
                                        ];
                                        $icon = $icons[$api->api_type] ?? '🔌';
                                        $dataTypes = json_decode($api->data_types, true);
                                    @endphp
                                    <div class="p-4 bg-{{ $color }}-50 rounded-lg border-l-4 border-{{ $color }}-500">
                                        <h5 class="font-semibold text-{{ $color }}-800 mb-2">{{ $icon }} {{ $api->api_name }}</h5>
                                        <div class="text-sm text-{{ $color }}-700">
                                            <div>Endpoint: {{ $api->api_endpoint }}</div>
                                            <div>Statut: 
                                                @if($api->connection_status == 'connected') ✅ Connecté
                                                @elseif($api->connection_status == 'disconnected') ❌ Déconnecté
                                                @elseif($api->connection_status == 'error') ⚠️ Erreur
                                                @else 🔄 En attente
                                                @endif
                                            </div>
                                            <div>Données: {{ implode(', ', $dataTypes) }}</div>
                                            <div class="text-xs text-{{ $color }}-600 mt-1">
                                                Dernière sync: {{ \Carbon\Carbon::parse($api->last_sync)->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucune intégration API configurée</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="doping-tab" class="fifa-tab-content">
        <h2>🧪 Contrôle Anti-Dopage & Conformité</h2>
        <div id="doping-content">
            <!-- 🆕 CONTENU BLADE DIRECT - STRUCTURE COMPLÈTE -->
            <div class="fifa-doping-grid space-y-6">
                
                <!-- Carte Tests Anti-Dopage -->
                <div class="fifa-doping-card w-full">
                    <h4>🧪 Tests Anti-Dopage</h4>
                    <div class="fifa-doping-info">
                        @if($dopingTests && $dopingTests->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                @foreach($dopingTests->take(3) as $test)
                                    @php
                                        $statusColors = [
                                            'completed' => 'bg-green-50 border-green-500',
                                            'in_progress' => 'bg-yellow-50 border-yellow-500',
                                            'scheduled' => 'bg-blue-50 border-blue-500',
                                            'cancelled' => 'bg-red-50 border-red-500'
                                        ];
                                        $statusColor = $statusColors[$test->test_status] ?? 'bg-gray-50 border-gray-500';
                                        $resultColors = [
                                            'negative' => 'text-green-600 bg-green-100',
                                            'positive' => 'text-red-600 bg-red-100',
                                            'inconclusive' => 'text-yellow-600 bg-yellow-100',
                                            'pending' => 'text-blue-600 bg-blue-100'
                                        ];
                                        $resultColor = $resultColors[$test->test_result] ?? 'text-gray-600 bg-gray-100';
                                    @endphp
                                    <div class="p-4 {{ $statusColor }} rounded-lg border-l-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <div class="font-semibold text-gray-800">{{ ucfirst($test->test_type) }}</div>
                                                <div class="text-sm text-gray-600">{{ $test->test_location }}</div>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $resultColor }}">
                                                @if($test->test_result == 'negative') 🟢 Négatif
                                                @elseif($test->test_result == 'positive') 🔴 Positif
                                                @elseif($test->test_result == 'inconclusive') 🟡 Inconclusif
                                                @else 🔵 En attente
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-2">{{ $test->test_laboratory }}</div>
                                        <div class="text-sm text-gray-700 mb-2">{{ $test->test_notes }}</div>
                                        <div class="text-xs text-gray-600">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($test->test_date)->format('d M Y') }}
                                        </div>
                                        @if($test->next_test_date)
                                            <div class="text-xs text-blue-600 mt-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                Prochain: {{ \Carbon\Carbon::parse($test->next_test_date)->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center">
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-history mr-1"></i>
                                    Voir l'historique complet ({{ $dopingTests->count() }} tests)
                                </a>
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucun test anti-dopage enregistré</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Substances Interdites -->
                <div class="fifa-doping-card w-full">
                    <h4>🚫 Substances Interdites & Contrôles</h4>
                    <div class="fifa-doping-info">
                        @if($bannedSubstances && $bannedSubstances->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                @foreach($bannedSubstances as $substance)
                                    @php
                                        $categoryColors = [
                                            'anabolic_steroids' => 'bg-red-50 border-red-500',
                                            'stimulants' => 'bg-orange-50 border-orange-500',
                                            'diuretics' => 'bg-yellow-50 border-yellow-500',
                                            'peptide_hormones' => 'bg-purple-50 border-purple-500',
                                            'beta_blockers' => 'bg-blue-50 border-blue-500',
                                            'cannabinoids' => 'bg-green-50 border-green-500',
                                            'narcotics' => 'bg-pink-50 border-pink-500',
                                            'glucocorticoids' => 'bg-indigo-50 border-indigo-500'
                                        ];
                                        $categoryColor = $categoryColors[$substance->substance_category] ?? 'bg-gray-50 border-gray-500';
                                        $statusColors = [
                                            'active' => 'text-red-600 bg-red-100',
                                            'cleared' => 'text-green-600 bg-green-100',
                                            'monitoring' => 'text-yellow-600 bg-yellow-100'
                                        ];
                                        $statusColor = $statusColors[$substance->status] ?? 'text-gray-600 bg-gray-100';
                                    @endphp
                                    <div class="p-4 {{ $categoryColor }} rounded-lg border-l-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <div class="font-semibold text-gray-800">{{ $substance->substance_name }}</div>
                                                <div class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $substance->substance_category) }}</div>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColor }}">
                                                @if($substance->status == 'active') 🔴 Actif
                                                @elseif($substance->status == 'cleared') 🟢 Autorisé
                                                @else 🔵 Surveillance
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-2">Code WADA: {{ $substance->wada_code }}</div>
                                        <div class="text-sm text-gray-700 mb-2">{{ $substance->notes }}</div>
                                        @if($substance->detection_count > 0)
                                            <div class="text-xs text-red-600">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ $substance->detection_count }} détection(s)
                                            </div>
                                        @else
                                            <div class="text-xs text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Aucune détection
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucune substance interdite enregistrée</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Autorisations d'Usage Thérapeutique (TUE) -->
                <div class="fifa-doping-card w-full">
                    <h4>💊 Autorisations d'Usage Thérapeutique (TUE)</h4>
                    <div class="fifa-doping-info">
                        @if($therapeuticUseExemptions && $therapeuticUseExemptions->count() > 0)
                            <div class="space-y-4">
                                @foreach($therapeuticUseExemptions as $tue)
                                    <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <div class="font-semibold text-gray-800">{{ $tue->substance_name }}</div>
                                                <div class="text-sm text-gray-600">{{ $tue->medical_condition }}</div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                    ✅ Approuvé
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-700 mb-2">
                                            <strong>Médecin:</strong> {{ $tue->prescribing_doctor }} ({{ $tue->doctor_license }})
                                        </div>
                                        <div class="text-sm text-gray-700 mb-2">
                                            <strong>Période:</strong> 
                                            {{ \Carbon\Carbon::parse($tue->exemption_start_date)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($tue->exemption_end_date)->format('d M Y') }}
                                        </div>
                                        <div class="text-sm text-gray-700 mb-2">
                                            <strong>Approbations:</strong>
                                            @if($tue->wada_approval) <span class="text-green-600">WADA ✅</span> @endif
                                            @if($tue->fifa_approval) <span class="text-blue-600">FIFA ✅</span> @endif
                                        </div>
                                        <div class="text-xs text-gray-600">{{ $tue->approval_notes }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucune autorisation TUE enregistrée</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Alertes & Notifications -->
                <div class="fifa-doping-card w-full">
                    <h4>⚠️ Alertes & Notifications Anti-Dopage</h4>
                    <div class="fifa-doping-info">
                        @if($dopingAlerts && $dopingAlerts->count() > 0)
                            <div class="space-y-3">
                                @foreach($dopingAlerts as $alert)
                                    @php
                                        $severityColors = [
                                            'low' => 'bg-blue-50 border-blue-500',
                                            'medium' => 'bg-yellow-50 border-yellow-500',
                                            'high' => 'bg-orange-50 border-orange-500',
                                            'critical' => 'bg-red-50 border-red-500'
                                        ];
                                        $severityColor = $severityColors[$alert->alert_severity] ?? 'bg-gray-50 border-gray-500';
                                        $severityIcons = [
                                            'low' => 'ℹ️',
                                            'medium' => '⚠️',
                                            'high' => '🚨',
                                            'critical' => '🚨'
                                        ];
                                        $severityIcon = $severityIcons[$alert->alert_severity] ?? 'ℹ️';
                                    @endphp
                                    <div class="p-3 {{ $severityColor }} rounded-lg border-l-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-start">
                                                <span class="text-lg mr-2">{{ $severityIcon }}</span>
                                                <div>
                                                    <div class="font-semibold text-gray-800">{{ $alert->alert_message }}</div>
                                                    <div class="text-xs text-gray-600 capitalize">
                                                        {{ str_replace('_', ' ', $alert->alert_type) }} - 
                                                        {{ ucfirst($alert->alert_severity) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($alert->alert_status == 'active') bg-red-100 text-red-800
                                                @elseif($alert->alert_status == 'acknowledged') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                {{ ucfirst($alert->alert_status) }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($alert->alert_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>Aucune alerte anti-dopage active</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Carte Conformité & Réglementation -->
                <div class="fifa-doping-card w-full">
                    <h4>📋 Conformité & Réglementation FIFA/WADA</h4>
                    <div class="fifa-doping-info">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                                <h5 class="font-semibold text-green-800 mb-2">✅ Statut de Conformité</h5>
                                <div class="text-sm text-green-700">
                                    @if($complianceStatus && $complianceStatus->count() > 0)
                                        @foreach($complianceStatus as $compliance)
                                            @php
                                                $statusColors = [
                                                    'compliant' => 'text-green-600',
                                                    'non_compliant' => 'text-red-600',
                                                    'under_review' => 'text-yellow-600',
                                                    'warning' => 'text-orange-600'
                                                ];
                                                $statusColor = $statusColors[$compliance->status] ?? 'text-gray-600';
                                                $statusIcons = [
                                                    'compliant' => '✅',
                                                    'non_compliant' => '❌',
                                                    'under_review' => '🔄',
                                                    'warning' => '⚠️'
                                                ];
                                                $statusIcon = $statusIcons[$compliance->status] ?? 'ℹ️';
                                            @endphp
                                            <div class="mb-2">
                                                <div class="font-medium {{ $statusColor }}">
                                                    {{ $statusIcon }} {{ ucfirst(str_replace('_', ' ', $compliance->compliance_type)) }}
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    Score: {{ $compliance->compliance_score }}% | 
                                                    @if($compliance->violations_count > 0)
                                                        <span class="text-red-600">{{ $compliance->violations_count }} violation(s)</span>
                                                    @else
                                                        <span class="text-green-600">Aucune violation</span>
                                                    @endif
                                                    @if($compliance->warnings_count > 0)
                                                        | <span class="text-yellow-600">{{ $compliance->warnings_count }} avertissement(s)</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Dernière évaluation: {{ \Carbon\Carbon::parse($compliance->last_assessment_date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-gray-500">Aucun statut de conformité disponible</div>
                                    @endif
                                </div>
                            </div>
                            <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                <h5 class="font-semibold text-blue-800 mb-2">📚 Ressources & Informations</h5>
                                <div class="text-sm text-blue-700">
                                    @if($complianceResources && $complianceResources->count() > 0)
                                        @foreach($complianceResources->take(4) as $resource)
                                            @php
                                                $typeIcons = [
                                                    'guide' => '📖',
                                                    'procedure' => '📋',
                                                    'contact' => '📞',
                                                    'document' => '📄',
                                                    'training' => '🎓'
                                                ];
                                                $typeIcon = $typeIcons[$resource->resource_type] ?? '📚';
                                            @endphp
                                            <div class="mb-2">
                                                <div class="font-medium">
                                                    {{ $typeIcon }} {{ $resource->resource_name }}
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    {{ $resource->resource_description }}
                                                </div>
                                                @if($resource->resource_url)
                                                    <div class="text-xs text-blue-600">
                                                        <a href="{{ $resource->resource_url }}" target="_blank" class="hover:underline">
                                                            Accéder à la ressource
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($complianceResources->count() > 4)
                                            <div class="text-xs text-blue-600 mt-2">
                                                <a href="#" class="hover:underline">
                                                    Voir toutes les ressources ({{ $complianceResources->count() }})
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-gray-500">Aucune ressource disponible</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="licenses-tab" class="fifa-tab-content">
        <h2>🏆 Gestion des Licences & Validation FIFA</h2>
        <div id="licenses-content">
            <!-- Filtres -->
            <div class="fifa-license-filters">
                <div class="filter-group">
                    <label for="year-filter">Année:</label>
                    <select id="year-filter" class="fifa-filter-select">
                        <option value="toutes">Toutes</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="club-filter">Club:</label>
                    <select id="club-filter" class="fifa-filter-select">
                        <option value="tous">Tous</option>
                        @if($player->club)
                            <option value="{{ $player->club->id }}">{{ $player->club->name }}</option>
                        @endif
                    </select>
                </div>
                <div class="filter-group">
                    <label for="type-filter">Type:</label>
                    <select id="type-filter" class="fifa-filter-select">
                        <option value="tous">Tous</option>
                        <option value="Pro">Professionnel</option>
                        <option value="Amateur">Amateur</option>
                        <option value="Jeunes">Jeunes</option>
                    </select>
                </div>
            </div>

            <!-- Tableau des licences -->
            <div class="fifa-license-table-container">
                <table class="fifa-license-table">
                    <thead>
                        <tr>
                            <th>Saison</th>
                            <th>Club</th>
                            <th>Association</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody class="licenses-table-body">
                        <!-- 🆕 CONTENU BLADE DIRECT -->
                        @if($playerLicenses && $playerLicenses->count() > 0)
                            @foreach($playerLicenses as $license)
                                @php
                                    $club = \DB::table('clubs')->where('id', $license->club_id)->first();
                                    $clubAssociation = null;
                                    if ($club) {
                                        $clubAssociation = \DB::table('associations')->where('id', $club->association_id)->first();
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        @php
                                            $startYear = \Carbon\Carbon::parse($license->start_date)->format('Y');
                                            $endYear = \Carbon\Carbon::parse($license->end_date)->format('Y');
                                        @endphp
                                        Saison {{ $startYear }}-{{ $endYear }}
                                    </td>
                                    <td>{{ $club->name ?? 'N/A' }}</td>
                                    <td>{{ $clubAssociation ? $clubAssociation->name : 'N/A' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $license->license_type)) }}</td>
                                    <td>FIFA</td>
                                    <td>
                                        <span class="fifa-status 
                                            @if($license->status == 'active') active
                                            @elseif($license->status == 'expired') expired
                                            @elseif($license->status == 'suspended') suspended
                                            @else revoked
                                            @endif">
                                            @if($license->status == 'active') 🟢 Active
                                            @elseif($license->status == 'expired') 🔴 Expirée
                                            @elseif($license->status == 'suspended') 🟡 Suspendue
                                            @else 🔘 Révoquée
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-4">
                                    Aucune licence enregistrée
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Résumé des primes de formation -->
            <div class="fifa-training-compensation">
                <h3>Primes de Formation FIFA</h3>
                <div class="training-compensation-content">
                    <!-- 🆕 CONTENU BLADE DIRECT -->
                    <div class="fifa-compensation-card">
                        <h4>💰 Calcul des Primes</h4>
                        <div class="fifa-compensation-info">
                            <div class="fifa-compensation-stat">
                                <span>Club de formation:</span>
                                <span>{{ $player->club->name ?? 'N/A' }}</span>
                            </div>
                            <div class="fifa-compensation-stat">
                                <span>Période de formation:</span>
                                <span>
                                    @if($playerLicenses && $playerLicenses->count() > 0)
                                        {{ $playerLicenses->count() }} saison(s)
                                    @else
                                        Non définie
                                    @endif
                                </span>
                            </div>
                            <div class="fifa-compensation-stat">
                                <span>Prime estimée:</span>
                                <span class="fifa-compensation-amount">
                                    @if($playerLicenses && $playerLicenses->where('status', 'active')->count() > 0)
                                        €{{ number_format($playerLicenses->where('status', 'active')->count() * 50000) }}
                                    @else
                                        €0
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton de rafraîchissement -->
    <div style="text-align: center; margin-top: 30px;">
        <button class="fifa-refresh-btn" onclick="location.reload()">Rafraîchir les données</button>
    </div>

    <!-- 🎨 Styles CSS FIFA (identiques au portail FIFA) -->
    <style>
        .fifa-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 60px 0;
            margin: 0;
            width: 100%;
            max-width: 100%;
        }
        
        .fifa-container {
            max-width: 100%;
            margin: 0;
            padding: 0 20px;
            width: 100%;
        }
        
        .fifa-tabs {
            display: flex;
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 8px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            flex-wrap: wrap;
            width: 100%;
            max-width: 100%;
        }
        
        .fifa-tab-button {
            flex: 1;
            min-width: 150px;
            padding: 15px 20px;
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .fifa-tab-button.active {
            background: #ffd700;
            color: #1e3c72;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.4);
        }
        
        .fifa-tab-button:hover:not(.active) {
            background: rgba(255,255,255,0.1);
        }
        
        .fifa-tab-content {
            display: none;
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            min-height: 400px;
        }
        
        .fifa-tab-content.active {
            display: block;
        }
        
        /* Styles pour les sous-onglets Performances */
        .fifa-sub-tabs {
            display: flex;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 20px;
            gap: 5px;
        }
        
        .fifa-sub-tab-button {
            flex: 1;
            padding: 10px 15px;
            background: transparent;
            border: none;
            color: #87ceeb;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }
        
        .fifa-sub-tab-button.active {
            background: #ffd700;
            color: #1e3c72;
        }
        
        .fifa-sub-tab-content {
            display: none;
        }
        
        .fifa-sub-tab-content.active {
            display: block;
        }
        
        /* Styles pour les cartes */
        .fifa-stat-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }
        
        .fifa-medical-grid, .fifa-health-grid, .fifa-devices-grid, .fifa-doping-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .fifa-medical-card, .fifa-health-card, .fifa-device-card, .fifa-doping-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            width: 100%;
        }
        
        .fifa-medical-stat, .fifa-health-stat, .fifa-device-stat, .fifa-doping-stat {
            margin-bottom: 15px;
        }
        
        .fifa-stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .fifa-stat-value {
            font-weight: 600;
            color: #ffd700;
        }
        
        .fifa-stat-value.highlight {
            font-size: 1.2em;
            color: #ffd700;
        }
        
        .fifa-stat-value.positive {
            color: #51cf66;
        }
        
        .fifa-progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .fifa-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #51cf66, #ffd700);
            transition: width 0.3s ease;
        }
        
        .fifa-health-status {
            font-size: 0.9em;
            font-weight: 600;
        }
        
        .fifa-health-status.excellent {
            color: #51cf66;
        }
        
        .fifa-health-status.good {
            color: #ffd700;
        }
        
        .fifa-health-status.poor {
            color: #ff6b6b;
        }
        
        /* Styles pour les filtres et tableaux */
        .fifa-license-filters {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .fifa-filter-select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .fifa-license-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .fifa-license-table th,
        .fifa-license-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .fifa-license-table th {
            background: rgba(255,255,255,0.1);
            font-weight: 600;
        }
        
        .fifa-status.active {
            color: #51cf66;
            font-weight: 600;
        }
        
        .fifa-refresh-btn {
            background: #ffd700;
            color: #1e3c72;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .fifa-refresh-btn:hover {
            background: #ffed4e;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .fifa-tabs {
                flex-direction: column;
            }
            
            .fifa-tab-button {
                min-width: auto;
            }
        }
    </style>

    <!-- 🔧 JavaScript FIFA (identique au portail FIFA) -->
    <script>
        // Fonction pour changer d'onglet FIFA
        function showFIFATab(tabName) {
            console.log('Changement vers l\'onglet:', tabName);
            
            // Masquer tous les onglets
            document.querySelectorAll('.fifa-tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Désactiver tous les boutons d'onglets
            document.querySelectorAll('.fifa-tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Afficher l'onglet sélectionné
            const selectedTab = document.getElementById(tabName + '-tab');
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
            
            // Activer le bouton correspondant
            const activeButton = document.querySelector(`button[onclick="showFIFATab('${tabName}')"]`);
            if (activeButton) {
                activeButton.classList.add('active');
            }
        }
        
        // Fonction pour changer de sous-onglet Performances
        function showFIFASubTab(subTabName) {
            console.log('Changement vers le sous-onglet:', subTabName);
            
            // Masquer tous les sous-onglets
            document.querySelectorAll('.fifa-sub-tab-content').forEach(subTab => {
                subTab.classList.remove('active');
            });
            
            // Désactiver tous les boutons de sous-onglets
            document.querySelectorAll('.fifa-sub-tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Afficher le sous-onglet sélectionné
            const selectedSubTab = document.getElementById(subTabName + '-sub-tab');
            if (selectedSubTab) {
                selectedSubTab.classList.add('active');
            }
            
            // Activer le bouton correspondant
            const activeButton = document.querySelector(`button[onclick="showFIFASubTab('${subTabName}')"]`);
            if (activeButton) {
                activeButton.classList.add('active');
            }
        }
        
                             // Initialisation au chargement de la page
                     document.addEventListener('DOMContentLoaded', function() {
                         console.log('🚀 Portail FIFA Simple initialisé !');
                         
                         // Afficher l'onglet Performances par défaut
                         showFIFATab('performances');
                         
                         // Afficher le sous-onglet Overview par défaut
                         showFIFASubTab('overview');
                         
                         // Initialiser les graphiques Chart.js
                         initializeCharts();
                     });
                     
                     // Fonction pour initialiser les graphiques
                     function initializeCharts() {
                         console.log('📊 Initialisation des graphiques Chart.js...');
                         
                         // Graphique Radar des Ratings FIFA
                         const ratingsCtx = document.getElementById('ratingsChart');
                         if (ratingsCtx) {
                             new Chart(ratingsCtx, {
                                 type: 'radar',
                                 data: {
                                     labels: ['Attaque', 'Défense', 'Physique', 'Technique', 'Mental'],
                                     datasets: [{
                                         label: 'Ratings FIFA',
                                         data: [85, 78, 82, 88, 80],
                                         borderColor: '#ffd700',
                                         backgroundColor: 'rgba(255, 215, 0, 0.2)',
                                         pointBackgroundColor: '#ffd700',
                                         pointBorderColor: '#fff',
                                         pointBorderWidth: 2
                                     }]
                                 },
                                 options: {
                                     responsive: true,
                                     maintainAspectRatio: false,
                                     scales: {
                                         r: {
                                             beginAtZero: true,
                                             max: 100,
                                             ticks: { 
                                                 stepSize: 20, 
                                                 color: '#87ceeb',
                                                 font: { size: 12 }
                                             },
                                             pointLabels: { 
                                                 color: '#87ceeb',
                                                 font: { size: 14, weight: 'bold' }
                                             },
                                             grid: { 
                                                 color: 'rgba(135, 206, 235, 0.3)' 
                                             }
                                         }
                                     },
                                     plugins: {
                                         legend: { 
                                             labels: { 
                                                 color: '#87ceeb',
                                                 font: { size: 14, weight: 'bold' }
                                             } 
                                         }
                                     }
                                 }
                             });
                         }
                         
                         // Graphique Barres des Statistiques de Match
                         const statsCtx = document.getElementById('statsChart');
                         if (statsCtx) {
                             new Chart(statsCtx, {
                                 type: 'bar',
                                 data: {
                                     labels: ['Buts', 'Passes', 'Tirs Cadrés', 'Passes Réussies'],
                                     datasets: [{
                                         label: 'Performance',
                                         data: [12, 8, 25, 89],
                                         backgroundColor: [
                                             'rgba(255, 215, 0, 0.8)',
                                             'rgba(135, 206, 235, 0.8)',
                                             'rgba(81, 207, 102, 0.8)',
                                             'rgba(255, 107, 107, 0.8)'
                                         ],
                                         borderColor: [
                                             '#ffd700',
                                             '#87ceeb',
                                             '#51cf66',
                                             '#ff6b6b'
                                         ],
                                         borderWidth: 2
                                     }]
                                 },
                                 options: {
                                     responsive: true,
                                     maintainAspectRatio: false,
                                     scales: {
                                         y: { 
                                             beginAtZero: true, 
                                             grid: { color: 'rgba(135, 206, 235, 0.3)' }, 
                                             ticks: { color: '#87ceeb' } 
                                         },
                                         x: { 
                                             grid: { color: 'rgba(135, 206, 235, 0.3)' }, 
                                             ticks: { color: '#87ceeb' } 
                                         }
                                     },
                                     plugins: {
                                         legend: { 
                                             labels: { 
                                                 color: '#87ceeb',
                                                 font: { size: 14, weight: 'bold' }
                                             } 
                                         }
                                     }
                                 }
                             });
                         }
                         
                         // Graphique des Tendances (ligne temporelle) - Données dynamiques
                         const trendsCtx = document.getElementById('trendsChart');
                         if (trendsCtx) {
                             // Récupérer les données de tendances depuis la base
                             const trendsData = @json($performanceTrends);
                             
                             if (trendsData && trendsData.length > 0) {
                                 const labels = trendsData.map(record => {
                                     const date = new Date(record.date);
                                     return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
                                 }).reverse();
                                 
                                 const performanceData = trendsData.map(record => record.performance_score).reverse();
                                 const healthData = trendsData.map(record => record.health_score).reverse();
                                 const wellbeingData = trendsData.map(record => record.wellbeing_score).reverse();
                                 
                                 new Chart(trendsCtx, {
                                     type: 'line',
                                     data: {
                                         labels: labels,
                                         datasets: [{
                                             label: 'Performance',
                                             data: performanceData,
                                             borderColor: '#3b82f6',
                                             backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                             tension: 0.4,
                                             fill: true
                                         }, {
                                             label: 'Santé',
                                             data: healthData,
                                             borderColor: '#10b981',
                                             backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                             tension: 0.4,
                                             fill: true
                                         }, {
                                             label: 'Bien-être',
                                             data: wellbeingData,
                                             borderColor: '#8b5cf6',
                                             backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                             tension: 0.4,
                                             fill: true
                                         }]
                                     },
                                     options: {
                                         responsive: true,
                                         maintainAspectRatio: false,
                                         scales: {
                                             y: { 
                                                 beginAtZero: true, 
                                                 max: 100,
                                                 grid: { color: 'rgba(135, 206, 235, 0.3)' }, 
                                                 ticks: { color: '#87ceeb' } 
                                             },
                                             x: { 
                                                 grid: { color: 'rgba(135, 206, 235, 0.3)' }, 
                                                 ticks: { color: '#87ceeb' } 
                                             }
                                         },
                                         plugins: {
                                             legend: { 
                                                 labels: { 
                                                     color: '#87ceeb',
                                                     font: { size: 14, weight: 'bold' }
                                             } 
                                         }
                                     }
                                 }
                             });
                             } else {
                                 // Graphique vide si pas de données
                                 new Chart(trendsCtx, {
                                     type: 'line',
                                     data: { labels: [], datasets: [] },
                                     options: { responsive: true, maintainAspectRatio: false }
                                 });
                             }
                         }
                         
                         // Graphique Radar SDOH - Données dynamiques
                         const sdohCtx = document.getElementById('sdohRadarChart');
                         if (sdohCtx) {
                             // Récupérer les données SDOH depuis la base
                             const sdohData = @json($sdohFactors);
                             
                             if (sdohData) {
                                 new Chart(sdohCtx, {
                                     type: 'radar',
                                     data: {
                                         labels: ['Environnement', 'Soutien Social', 'Accès Soins', 'Situation Financière', 'Éducation'],
                                         datasets: [{
                                             label: 'Score SDOH',
                                             data: [
                                                 sdohData.environment_score,
                                                 sdohData.social_support_score,
                                                 sdohData.healthcare_access_score,
                                                 sdohData.financial_status_score,
                                                 sdohData.education_score
                                             ],
                                             borderColor: '#14b8a6',
                                             backgroundColor: 'rgba(20, 184, 166, 0.2)',
                                             pointBackgroundColor: '#14b8a6',
                                             pointBorderColor: '#fff',
                                             pointBorderWidth: 2
                                         }]
                                     },
                                     options: {
                                         responsive: true,
                                         maintainAspectRatio: false,
                                         scales: {
                                             r: {
                                                 beginAtZero: true,
                                                 max: 100,
                                                 ticks: { 
                                                     stepSize: 20, 
                                                     color: '#87ceeb',
                                                     font: { size: 12 }
                                                 },
                                                 pointLabels: { 
                                                     color: '#87ceeb',
                                                     font: { size: 14, weight: 'bold' }
                                                 },
                                                 grid: { 
                                                     color: 'rgba(135, 206, 235, 0.3)' 
                                                 }
                                             }
                                         },
                                         plugins: {
                                             legend: { 
                                                 labels: { 
                                                     color: '#87ceeb',
                                                     font: { size: 14, weight: 'bold' }
                                                 } 
                                             }
                                         }
                                     }
                                 });
                             } else {
                                 // Graphique vide si pas de données
                                 new Chart(sdohCtx, {
                                     type: 'radar',
                                     data: { labels: [], datasets: [] },
                                     options: { responsive: true, maintainAspectRatio: false }
                                 });
                             }
                         }
                         
                         // Graphiques des Blessures et Maladies
                         const injuriesTypeCtx = document.getElementById('injuriesTypeChart');
                         if (injuriesTypeCtx) {
                             // Récupérer les données de blessures depuis la base
                             const injuriesData = @json($playerInjuriesDiseases);
                             
                             if (injuriesData && injuriesData.length > 0) {
                                 // Compter par type
                                 const typeCounts = {};
                                 injuriesData.forEach(incident => {
                                     typeCounts[incident.type] = (typeCounts[incident.type] || 0) + 1;
                                 });
                                 
                                 const typeLabels = Object.keys(typeCounts).map(type => {
                                     const labels = { 'injury': 'Blessures', 'disease': 'Maladies', 'surgery': 'Chirurgies', 'rehabilitation': 'Rééducation' };
                                     return labels[type] || type;
                                 });
                                 const typeData = Object.values(typeCounts);
                                 
                                 new Chart(injuriesTypeCtx, {
                                     type: 'doughnut',
                                     data: {
                                         labels: typeLabels,
                                         datasets: [{
                                             data: typeData,
                                             backgroundColor: [
                                                 'rgba(255, 107, 107, 0.8)',
                                                 'rgba(81, 207, 102, 0.8)',
                                                 'rgba(255, 215, 0, 0.8)',
                                                 'rgba(135, 206, 235, 0.8)'
                                             ],
                                             borderColor: [
                                                 '#ff6b6b',
                                                 '#51cf66',
                                                 '#ffd700',
                                                 '#87ceeb'
                                             ],
                                             borderWidth: 2
                                         }]
                                     },
                                     options: {
                                         responsive: true,
                                         maintainAspectRatio: false,
                                         plugins: {
                                             legend: { 
                                                 position: 'bottom',
                                                 labels: { 
                                                     color: '#87ceeb',
                                                     font: { size: 12 }
                                                 } 
                                             }
                                         }
                                     }
                                 });
                             }
                         }
                         
                         const injuriesSeverityCtx = document.getElementById('injuriesSeverityChart');
                         if (injuriesSeverityCtx) {
                             // Récupérer les données de blessures depuis la base
                             const injuriesData = @json($playerInjuriesDiseases);
                             
                             if (injuriesData && injuriesData.length > 0) {
                                 // Compter par gravité
                                 const severityCounts = {};
                                 injuriesData.forEach(incident => {
                                     severityCounts[incident.severity] = (severityCounts[incident.severity] || 0) + 1;
                                 });
                                 
                                 const severityLabels = Object.keys(severityCounts).map(severity => {
                                     const labels = { 'mild': 'Légère', 'moderate': 'Modérée', 'severe': 'Grave', 'critical': 'Critique' };
                                     return labels[severity] || severity;
                                 });
                                 const severityData = Object.values(severityCounts);
                                 
                                 new Chart(injuriesSeverityCtx, {
                                     type: 'bar',
                                     data: {
                                         labels: severityLabels,
                                         datasets: [{
                                             data: severityData,
                                             backgroundColor: [
                                                 'rgba(81, 207, 102, 0.8)',
                                                 'rgba(255, 215, 0, 0.8)',
                                                 'rgba(255, 165, 0, 0.8)',
                                                 'rgba(255, 107, 107, 0.8)'
                                             ],
                                             borderColor: [
                                                 '#51cf66',
                                                 '#ffd700',
                                                 '#ffa500',
                                                 '#ff6b6b'
                                             ],
                                             borderWidth: 2
                                         }]
                                     },
                                     options: {
                                         responsive: true,
                                         maintainAspectRatio: false,
                                         scales: {
                                             y: { 
                                                 beginAtZero: true, 
                                                 grid: { color: 'rgba(135, 206, 235, 0.3)' }, 
                                                 ticks: { color: '#87ceeb' } 
                                             },
                                             x: { 
                                                 grid: { color: 'rgba(135, 206, 235, 0.3)' }, 
                                                 ticks: { color: '#87ceeb' } 
                                             }
                                         },
                                         plugins: {
                                             legend: { display: false }
                                         }
                                     }
                                 });
                             }
                         }
                         
                         console.log('✅ Graphiques Chart.js initialisés !');
                     }
                 </script>
             </body>
             </html>
