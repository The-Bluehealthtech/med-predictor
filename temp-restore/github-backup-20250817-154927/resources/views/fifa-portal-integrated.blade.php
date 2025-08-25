<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>FIFA Portal - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
    <script>
        // Force le rechargement du cache
        window.cacheBuster = '{{ time() }}';
        
        // Force le rechargement de la page si ancien cache détecté
        if (window.location.search.indexOf('cache=') === -1) {
            const timestamp = Date.now();
            const newUrl = window.location.href + (window.location.search ? '&' : '?') + 'cache=' + timestamp;
            console.log('🔄 FORÇAGE: Rechargement forcé pour éviter le cache:', newUrl);
            window.location.replace(newUrl); // Utiliser replace au lieu de href
        } else {
            console.log('✅ CACHE: Version fraîche détectée, pas de rechargement');
        }
        
        // Force le rechargement du cache JavaScript
        if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_RELOAD) {
            console.log('🔄 PERFORMANCE: Rechargement détecté, vidage du cache JavaScript');
            // Forcer le rechargement des scripts
            const scripts = document.querySelectorAll('script[src]');
            scripts.forEach(script => {
                const newSrc = script.src + (script.src.includes('?') ? '&' : '?') + 'v=' + Date.now();
                script.src = newSrc;
            });
        }
    </script>
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
        
        .fifa-header {
            margin-bottom: 40px;
        }
        
        .fifa-header-main {
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: center;
        }
        
        .fifa-header-text {
            text-align: center;
        }
        
        .fifa-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #ffd700;
        }
        
        .fifa-subtitle {
            font-size: 1.3rem;
            color: #87ceeb;
            margin-bottom: 0;
        }
        
        /* Barre de Recherche FIFA avec Navigation */
        .fifa-search-container {
            width: 100%;
            max-width: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        /* Navigation Précédent/Suivant */
        .fifa-navigation-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .fifa-nav-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .fifa-nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .fifa-nav-btn.prev {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .fifa-nav-btn.next {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .fifa-player-counter {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .fifa-search-box {
            display: flex;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .fifa-search-box:focus-within {
            border-color: rgba(255, 215, 0, 0.5);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
            transform: translateY(-2px);
        }
        
        .fifa-search-input {
            flex: 1;
            padding: 15px 20px;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.1rem;
            outline: none;
        }
        
        .fifa-search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .fifa-search-btn {
            padding: 15px 20px;
            background: linear-gradient(135deg, #00d4aa, #0099cc);
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fifa-search-btn:hover {
            background: linear-gradient(135deg, #00b894, #0088a3);
            transform: scale(1.05);
        }
        
        .fifa-search-icon {
            font-size: 1.2rem;
        }
        
        /* Résultats de recherche */
        .fifa-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(30, 60, 114, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            margin-top: 10px;
            max-height: 400px;
            overflow-y: auto;
            backdrop-filter: blur(20px);
            z-index: 1000;
            display: none;
        }
        
        .fifa-search-results.active {
            display: block;
        }
        
        .fifa-search-result-item {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .fifa-search-result-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .fifa-search-result-item:last-child {
            border-bottom: none;
        }
        
        .fifa-result-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .fifa-result-info {
            flex: 1;
        }
        
        .fifa-result-name {
            color: white;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 2px;
        }
        
        .fifa-result-details {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-result-rating {
            background: linear-gradient(135deg, #ffd700, #ffb300);
            color: #1e3c72;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.8rem;
        }
        
        /* Actions de navigation */
        .fifa-navigation-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            gap: 1rem;
        }
        
        .fifa-back-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }
        
        .fifa-back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(243, 147, 251, 0.4);
        }
        
        .fifa-logout-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }
        
        .fifa-logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }
        
        /* Indicateur de chargement */
        .fifa-search-loading {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(30, 60, 114, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            margin-top: 10px;
            padding: 20px;
            text-align: center;
            color: #87ceeb;
            backdrop-filter: blur(20px);
            z-index: 1000;
        }
        
        .fifa-loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffd700;
            animation: fifa-spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes fifa-spin {
            to { transform: rotate(360deg); }
        }
        
        .fifa-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .fifa-stat-card {
            background: rgba(255,255,255,0.1);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.3s ease;
        }
        
        .fifa-stat-card:hover {
            transform: translateY(-5px);
        }
        
        .fifa-stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ffd700;
            margin-bottom: 10px;
        }
        
        .fifa-stat-label {
            font-size: 1rem;
            color: #b0c4de;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Hero Zone FIFA Ultra-Moderne */
        .fifa-hero-zone {
            background: linear-gradient(135deg, 
                rgba(0, 212, 170, 0.1) 0%, 
                rgba(0, 153, 204, 0.1) 50%, 
                rgba(255, 107, 53, 0.1) 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 0;
            padding: 40px 20px;
            margin: 0;
            margin-bottom: 40px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 100%;
        }
        
        .fifa-hero-container {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 40px;
            align-items: center;
            width: 100%;
            max-width: 100%;
        }
        
        /* Conteneur photo + drapeau */
        .fifa-player-photo-container {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        /* Photo du joueur avec overlay FIFA - AGRANDIE ET CARRÉE */
        .fifa-player-photo {
            position: relative;
            width: 12rem;  /* 192px - largeur */
            height: 12rem; /* 192px - hauteur (carrée) */
            border-radius: 24px; /* Agrandi aussi */
            overflow: hidden;
            border: 6px solid rgba(255, 215, 0, 0.3); /* Bordures 6px */
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.3); /* Ombres 24px */
        }
        
        /* Drapeau de la nationalité à côté de la photo */
        .fifa-player-nationality-flag {
            position: relative;
            margin-top: 10px;
        }
        
        .fifa-player-nationality-flag img {
            transition: transform 0.3s ease;
        }
        
        .fifa-player-nationality-flag img:hover {
            transform: scale(1.1);
        }
        
        .fifa-player-flag-fallback {
            display: none;
            width: 12rem;  /* 192px - agrandi par 3 (4rem × 3) */
            height: 9rem;  /* 144px - agrandi par 3 (3rem × 3) */
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 18px; /* Agrandi aussi */
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 2.1rem; /* Agrandi aussi pour s'adapter */
            border: 6px solid white; /* Agrandi aussi */
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.3); /* Agrandi aussi */
        }
        
        .fifa-player-flag-initial {
            font-size: 1.8rem; /* Agrandi par 3 (0.6rem × 3) */
            font-weight: 700;
            color: white;
        }
        
        .fifa-player-avatar {
            font-size: 4rem;
            color: #87ceeb;
            display: none; /* Caché par défaut, affiché en fallback */
        }
        
        .fifa-player-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 24px; /* Agrandi aussi */
        }
        
        .fifa-player-initials {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
        }
        
        /* Logos et drapeaux harmonisés - AGRANDIS PAR 3 */
        .fifa-club-logo img,
        .fifa-flag img {
            width: 9rem;  /* 144px - agrandi par 3 (3rem × 3) */
            height: 9rem; /* 144px - agrandi par 3 (3rem × 3) */
            object-fit: contain;
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
        
        /* Logo d'association - MÊMES DIMENSIONS QUE LE CLUB */
        .fifa-association-logo img {
            width: 9rem !important;  /* 144px - même taille que le club */
            height: 9rem !important; /* 144px - même taille que le club */
            object-fit: contain !important;
            transition: all 0.3s ease !important;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) !important;
        }
        
        /* Règle par ID pour forcer l'application du logo d'association */
        #hero-association-logo {
            width: 9rem !important;  /* 144px - même taille que le club */
            height: 9rem !important; /* 144px - même taille que le club */
            object-fit: contain !important;
            transition: all 0.3s ease !important;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) !important;
        }
        
        /* Classe globale pour tous les logos FIFA - AGRANDIE PAR 3 */
        .fifa-logo-harmonized {
            width: 9rem !important;  /* 144px - agrandi par 3 (3rem × 3) */
            height: 9rem !important; /* 144px - agrandi par 3 (3rem × 3) */
            object-fit: contain !important;
            transition: all 0.3s ease !important;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) !important;
        }
        
        /* Classe spécifique pour le logo d'association - MÊMES DIMENSIONS QUE LE CLUB */
        .fifa-association-logo-harmonized {
            width: 9rem !important;  /* 144px - même taille que le club */
            height: 9rem !important; /* 144px - même taille que le club */
            object-fit: contain !important;
            transition: all 0.3s ease !important;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) !important;
        }
        
        /* Responsive pour les petits écrans - AGRANDI PAR 3 */
        @media (max-width: 768px) {
            .fifa-club-logo img,
            .fifa-flag img {
                width: 7.5rem;  /* 120px sur mobile - agrandi par 3 (2.5rem × 3) */
                height: 7.5rem; /* 120px sur mobile - agrandi par 3 (2.5rem × 3) */
            }
            
            .fifa-flag img {
                width: 10.5rem;  /* 168px sur mobile - agrandi par 3 (3.5rem × 3) */
                height: 7.5rem;  /* 120px sur mobile - agrandi par 3 (2.5rem × 3) */
            }
            
            .fifa-association-logo img {
                width: 7.5rem;  /* 120px sur mobile - même taille que le club */
                height: 7.5rem; /* 120px sur mobile - même taille que le club */
            }
        }
        
        /* Fallbacks harmonisés - AGRANDIS PAR 3 */
        .fifa-club-fallback,
        .fifa-flag-fallback,
        .fifa-association-fallback {
            display: none;
            width: 9rem;  /* 144px - agrandi par 3 (3rem × 3) */
            height: 9rem; /* 144px - agrandi par 3 (3rem × 3) */
            background: linear-gradient(135deg, #00d4aa, #0099cc);
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 3rem; /* Agrandi aussi pour s'adapter */
        }
        
        /* Drapeau spécifique (rectangulaire) - AGRANDI PAR 3 */
        .fifa-flag img {
            width: 12rem !important;  /* 192px - agrandi par 3 (4rem × 3) */
            height: 9rem !important; /* 144px - agrandi par 3 (3rem × 3) */
            border-radius: 18px !important; /* Agrandi aussi */
        }
        
        /* Règle spécifique pour le drapeau de nationalité à côté de la photo */
        .fifa-player-nationality-flag .fifa-flag img {
            width: 12rem !important;  /* 192px - agrandi par 3 */
            height: 9rem !important; /* 144px - agrandi par 3 */
            border-radius: 18px !important;
            object-fit: cover !important;
            border: 6px solid white !important;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Règle spécifique pour le drapeau de nationalité sous l'association */
        .fifa-flag-nationality {
            width: 12rem !important;  /* 192px - agrandi par 3 */
            height: 9rem !important; /* 144px - agrandi par 3 */
            border-radius: 18px !important;
            object-fit: cover !important;
            border: 6px solid white !important;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.3) !important;
            margin-left: auto; /* Pousse le drapeau vers la droite */
        }
        
        /* Informations de licence et FIFA ID - DÉCALÉES */
        .fifa-license-info {
            display: flex;
            flex-direction: column;
            gap: 12px; /* Plus d'espace entre les éléments */
            margin-right: 40px; /* Plus d'espace à droite pour équilibrer */
            margin-left: 0px; /* Pas d'espace à gauche */
            min-width: 200px; /* Encore plus large */
            text-align: center; /* Centre le texte */
            padding: 20px; /* Plus de padding interne */
            background: rgba(255, 255, 255, 0.1); /* Fond plus visible */
            border-radius: 15px; /* Coins plus arrondis */
            border: 2px solid rgba(255, 255, 255, 0.3); /* Bordure plus visible */
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); /* Ombre portée */
        }
        
        .fifa-license-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .fifa-license-label {
            color: #b0c4de;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .fifa-license-value {
            color: #ffd700;
            font-size: 1rem;
            font-weight: 700;
            background: rgba(0, 0, 0, 0.3);
            padding: 4px 8px;
            border-radius: 4px;
            min-width: 40px;
            text-align: center;
        }
        
        .fifa-flag-fallback {
            width: 12rem;  /* 192px - agrandi par 3 (4rem × 3) */
            height: 9rem;  /* 144px - agrandi par 3 (3rem × 3) */
            border-radius: 18px; /* Agrandi aussi */
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }
        
        /* Overlay supprimé - plus de chiffre sur la photo */
        
        .fifa-player-info {
            text-align: center;
        }
        
        .fifa-player-name {
            font-size: 1.125rem; /* text-lg - plus petit */
            font-weight: 600; /* font-semibold - moins gras */
            color: white;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            white-space: nowrap; /* Force une seule ligne */
            overflow: hidden; /* Cache le débordement */
            text-overflow: ellipsis; /* Ajoute ... si trop long */
            max-width: 100%; /* Limite la largeur */
        }
        
        .fifa-player-position {
            background: linear-gradient(135deg, #00d4aa, #0099cc);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            display: inline-block;
        }
        
        .fifa-player-age {
            color: #b0c4de;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        /* Stats FIFA avec design moderne */
        .fifa-hero-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        
        .fifa-hero-stats .fifa-stat-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .fifa-hero-stats .fifa-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            opacity: 0.8;
        }
        
        .fifa-hero-stats .fifa-stat-card.primary::before { background: linear-gradient(90deg, #00d4aa, #0099cc); }
        .fifa-hero-stats .fifa-stat-card.success::before { background: linear-gradient(90deg, #51cf66, #40c057); }
        .fifa-hero-stats .fifa-stat-card.warning::before { background: linear-gradient(90deg, #ffd700, #ffb300); }
        .fifa-hero-stats .fifa-stat-card.info::before { background: linear-gradient(90deg, #87ceeb, #5bc0de); }
        
        .fifa-hero-stats .fifa-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Nouvelles informations physiques */
        .fifa-player-physical {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .fifa-physical-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .fifa-physical-icon {
            font-size: 1.2rem;
        }
        
        .fifa-physical-value {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        /* Section métriques avancées */
        .fifa-hero-advanced {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .fifa-performance-stats,
        .fifa-health-indicators,
        .fifa-season-progress,
        .fifa-recent-performance {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .fifa-perf-stat,
        .fifa-health-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
        }
        
        .fifa-perf-icon,
        .fifa-health-icon {
            font-size: 1.5rem;
        }
        
        .fifa-perf-value,
        .fifa-health-value {
            font-weight: 700;
            font-size: 1.2rem;
            min-width: 3rem;
        }
        
        .fifa-perf-label,
        .fifa-health-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        /* Progression saison */
        .fifa-progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .fifa-progress-title {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .fifa-progress-percentage {
            font-weight: 700;
            color: #10b981;
        }
        
        .fifa-progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .fifa-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #3b82f6);
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        .fifa-progress-details {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        /* Performances récentes */
        .fifa-recent-header {
            font-weight: 600;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .fifa-recent-results {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .fifa-result {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
        }
        
        .fifa-result.win {
            background: #10b981;
            color: white;
        }
        
        .fifa-result.draw {
            background: #f59e0b;
            color: white;
        }
        
        .fifa-result.loss {
            background: #ef4444;
            color: white;
        }
        
        .fifa-stat-icon {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
        }
        
        .fifa-hero-stats .fifa-stat-value {
            display: block;
            font-size: 2.5rem;
            font-weight: 700;
            color: #ffd700;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .fifa-hero-stats .fifa-stat-label {
            color: #b0c4de;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Club + Nationalité */
        .fifa-hero-club-nationality {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }
        
        .fifa-club-section, .fifa-nationality-section {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 120px;
        }
        
        .fifa-nationality-section {
            display: flex;
            align-items: center;
            gap: 20px; /* Espacement équilibré entre les éléments */
            text-align: left;
            min-width: 600px; /* Plus large pour accommoder le centrage */
            justify-content: center; /* Centré sous les stats */
            margin: 0 auto; /* Centré horizontalement */
            padding: 20px; /* Padding externe */
            transition: all 0.3s ease; /* Transition fluide */
        }
        
        .fifa-club-logo, .fifa-flag {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .fifa-club-name, .fifa-country {
            color: #b0c4de;
            font-size: 1rem;
            font-weight: 500;
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
        
        /* Styles pour les sous-onglets Performances */
        .fifa-sub-tabs {
            display: flex;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 5px;
            margin: 20px 0;
            backdrop-filter: blur(5px);
            flex-wrap: wrap;
        }
        
        .fifa-sub-tab-button {
            flex: 1;
            min-width: 120px;
            padding: 12px 16px;
            background: transparent;
            border: none;
            color: #b0c4de;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .fifa-sub-tab-button.active {
            background: rgba(255, 215, 0, 0.2);
            color: #ffd700;
            box-shadow: 0 2px 10px rgba(255, 215, 0, 0.2);
        }
        
        .fifa-sub-tab-button:hover:not(.active) {
            background: rgba(255,255,255,0.05);
            color: white;
        }
        
        .fifa-sub-tab-content {
            display: none;
            margin-top: 20px;
        }
        
        .fifa-sub-tab-content.active {
            display: block;
        }
        
        .fifa-sub-tab-content h3 {
            color: #87ceeb;
            margin-bottom: 20px;
            font-size: 1.4rem;
        }
        
        /* Styles pour les cartes enrichies */
        .fifa-overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .fifa-overview-card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .fifa-overview-card h4 {
            color: #ffd700;
            margin-bottom: 15px;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .fifa-stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);

        /* Styles pour l'onglet Historique des Licences */
        .fifa-license-filters {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            color: #87ceeb;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .fifa-filter-select {
            padding: 10px 15px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 0.9rem;
            min-width: 150px;
            backdrop-filter: blur(5px);
        }

        .fifa-filter-select:focus {
            outline: none;
            border-color: #ffd700;
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }

        .fifa-filter-select option {
            background: #1e3c72;
            color: white;
        }

        .fifa-license-table-container {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .fifa-license-table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }

        .fifa-license-table th {
            background: rgba(255, 215, 0, 0.1);
            color: #ffd700;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid rgba(255, 215, 0, 0.3);
        }

        .fifa-license-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .fifa-license-table tbody tr:hover {
            background: rgba(255,255,255,0.05);
        }

        .license-current {
            background: rgba(255, 215, 0, 0.1) !important;
            border-left: 4px solid #ffd700;
        }

        .license-current td:first-child::before {
            content: "★ ";
            color: #ffd700;
            font-weight: bold;
        }

        .license-badge {
            background: rgba(255, 215, 0, 0.2);
            color: #ffd700;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .fifa-training-compensation {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .fifa-training-compensation h3 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.3rem;
            text-align: center;
        }

        .compensation-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .compensation-item:last-child {
            border-bottom: none;
        }

        .compensation-club {
            font-weight: 600;
            color: #87ceeb;
        }

        .compensation-amount {
            color: #ffd700;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .compensation-category {
            background: rgba(255, 215, 0, 0.2);
            color: #ffd700;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }
        }
        
        .fifa-stat-label {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-stat-value {
            color: #87ceeb;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .fifa-stat-value.highlight {
            color: #ffd700;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        /* Styles pour les statistiques avancées */
        .fifa-advanced-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .fifa-advanced-card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .fifa-advanced-card h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .fifa-advanced-stat {
            margin: 20px 0;
        }
        
        .fifa-stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .fifa-stat-header span:first-child {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-progress-bar {
            background: rgba(255,255,255,0.1);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .fifa-progress-fill {
            background: linear-gradient(90deg, #ffd700, #ffed4e);
            height: 100%;
            transition: width 0.5s ease;
        }
        
        /* Styles pour les graphiques */
        .fifa-chart-container {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            margin-top: 20px;
        }
        
        .fifa-chart-container h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        /* Styles pour les statistiques de match */
        .fifa-match-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .fifa-match-card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .fifa-match-card h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .fifa-match-stat {
            margin: 15px 0;
        }
        
        .fifa-match-stat .fifa-stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .fifa-match-stat .fifa-stat-header:last-child {
            border-bottom: none;
        }
        
        /* Styles pour l'analyse comparative */
        .fifa-comparison-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .fifa-comparison-card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .fifa-comparison-card h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .fifa-comparison-stat {
            margin: 15px 0;
        }
        
        .fifa-comparison-stat .fifa-stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .fifa-comparison-stat .fifa-stat-header:last-child {
            border-bottom: none;
        }
        
        .fifa-stat-value.positive {
            color: #51cf66;
            font-weight: bold;
        }
        
        .fifa-stat-value.negative {
            color: #ff6b6b;
            font-weight: bold;
        }
        
        .fifa-recommendations {
            margin-top: 20px;
        }
        
        .fifa-recommendation-item {
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-recommendation-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .fifa-recommendation-text {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        /* Styles pour l'onglet Santé */
        .fifa-health-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .fifa-health-card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .fifa-health-card h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .fifa-health-stat {
            margin: 15px 0;
        }
        
        .fifa-health-indicator {
            text-align: center;
            margin-top: 10px;
        }
        
        .fifa-health-status.excellent {
            color: #51cf66;
            font-weight: bold;
        }
        
        .fifa-health-status.good {
            color: #ffd700;
            font-weight: bold;
        }
        
        .fifa-health-status.poor {
            color: #ff6b6b;
            font-weight: bold;
        }
        
        .fifa-health-recommendations {
            margin-top: 20px;
        }
        
        /* Styles pour l'onglet Médical */
        .fifa-medical-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .fifa-medical-card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .fifa-medical-card h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .fifa-medical-stat {
            margin: 15px 0;
        }
        
        .fifa-medical-alert {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
        }
        
        .fifa-alert-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .fifa-alert-text {
            color: #ff6b6b;
            font-weight: 500;
        }
        
        .fifa-injury-timeline {
            margin-top: 20px;
        }
        
        .fifa-timeline-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 8px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-timeline-date {
            color: #87ceeb;
            font-weight: 600;
        }
        
        .fifa-timeline-injury {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-medical-alerts {
            margin-top: 20px;
        }
        
        .fifa-alert-item {
            display: flex;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
        }
        
        .fifa-alert-item.high-risk {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .fifa-alert-item.low-risk {
            background: rgba(81, 207, 102, 0.1);
            border: 1px solid rgba(81, 207, 102, 0.3);
        }
        
        .fifa-care-plan {
            margin-top: 20px;
        }
        
        .fifa-care-item {
            display: flex;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-care-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .fifa-care-text {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        /* Styles pour l'onglet Devices */
        .fifa-devices-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .fifa-device-card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .fifa-device-card h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .fifa-device-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .fifa-device-icon {
            font-size: 2rem;
            margin-right: 15px;
        }
        
        .fifa-device-name {
            color: #87ceeb;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .fifa-device-details {
            margin-top: 15px;
        }
        
        .fifa-device-stat {
            margin: 15px 0;
        }
        
        .fifa-device-label {
            color: #b0c4de;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 5px;
        }
        
        .fifa-device-value {
            color: #ffd700;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .fifa-devices-overview {
            margin-top: 20px;
        }
        
        .fifa-device-overview-stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .fifa-device-overview-label {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-device-overview-value {
            color: #87ceeb;
            font-weight: 600;
        }
        
        /* Styles pour l'onglet Dopage */
        .fifa-doping-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .fifa-doping-card {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .fifa-doping-card h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .fifa-doping-stat {
            margin: 15px 0;
        }
        
        .fifa-doping-results {
            margin: 20px 0;
        }
        
        .fifa-result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
        }
        
        .fifa-result-item.positive {
            background: rgba(81, 207, 102, 0.1);
            border: 1px solid rgba(81, 207, 102, 0.3);
        }
        
        .fifa-result-item.negative {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .fifa-result-item.neutral {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
        }
        
        .fifa-result-icon {
            font-size: 1.5rem;
        }
        
        .fifa-result-label {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-result-value {
            color: #87ceeb;
            font-weight: 600;
        }
        
        .fifa-doping-percentage {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 10px;
        }
        
        .fifa-percentage-label {
            display: block;
            color: #b0c4de;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .fifa-percentage-value {
            color: #ffd700;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .fifa-doping-timeline {
            margin-top: 20px;
        }
        
        .fifa-timeline-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 8px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-timeline-date {
            color: #87ceeb;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .fifa-timeline-result.positive {
            color: #51cf66;
            font-weight: bold;
        }
        
        .fifa-timeline-type {
            color: #b0c4de;
            font-size: 0.8rem;
        }
        
        .fifa-substances-tested {
            margin-top: 20px;
        }
        
        .fifa-substance-category {
            margin: 20px 0;
        }
        
        .fifa-substance-category h5 {
            color: #ffd700;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        
        .fifa-substance-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .fifa-substance {
            background: rgba(255,255,255,0.1);
            color: #b0c4de;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .fifa-compliance-status {
            margin-top: 20px;
        }
        
        .fifa-compliance-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-compliance-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .fifa-compliance-text {
            color: #b0c4de;
            font-size: 0.9rem;
            flex: 1;
        }
        
        .fifa-compliance-status {
            color: #51cf66;
            font-weight: 600;
        }
        
        /* Styles pour les devices enrichis */
        .fifa-device-status {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        
        .fifa-device-status.online {
            background: rgba(81, 207, 102, 0.2);
            color: #51cf66;
            border: 1px solid rgba(81, 207, 102, 0.3);
        }
        
        .fifa-device-status.offline {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .fifa-updates-management {
            margin-top: 20px;
        }
        
        .fifa-update-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
        }
        
        .fifa-update-item.critical {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .fifa-update-item.important {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
        }
        
        .fifa-update-item.normal {
            background: rgba(81, 207, 102, 0.1);
            border: 1px solid rgba(81, 207, 102, 0.3);
        }
        
        .fifa-update-icon {
            font-size: 1.2rem;
        }
        
        .fifa-update-device {
            color: #87ceeb;
            font-weight: 600;
        }
        
        .fifa-update-type {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-update-status {
            color: #ffd700;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .fifa-update-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .fifa-update-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .fifa-update-btn.critical {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .fifa-update-btn.important {
            background: rgba(255, 215, 0, 0.2);
            color: #ffd700;
            border: 1px solid rgba(255, 215, 0, 0.3);
        }
        
        .fifa-update-btn.normal {
            background: rgba(81, 207, 102, 0.2);
            color: #51cf66;
            border: 1px solid rgba(81, 207, 102, 0.3);
        }
        
        .fifa-update-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        .fifa-security-overview {
            margin-top: 20px;
        }
        
        .fifa-security-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-security-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .fifa-security-label {
            color: #b0c4de;
            font-size: 0.9rem;
            flex: 1;
        }
        
        .fifa-security-status {
            color: #51cf66;
            font-weight: 600;
        }
        
        .fifa-maintenance-schedule {
            margin-top: 20px;
        }
        
        .fifa-maintenance-schedule h5 {
            color: #ffd700;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .fifa-maintenance-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 8px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-maintenance-date {
            color: #87ceeb;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .fifa-maintenance-task {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 20px;
        }
        
        /* Styles pour le dopage enrichi */
        .fifa-doping-stats-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        
        .fifa-stat-detail-item {
            background: rgba(255,255,255,0.05);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .fifa-stat-detail-label {
            display: block;
            color: #b0c4de;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }
        
        .fifa-stat-detail-value {
            color: #ffd700;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .fifa-test-preparation {
            margin-top: 20px;
        }
        
        .fifa-test-preparation h5 {
            color: #ffd700;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .fifa-prep-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin: 8px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-prep-icon {
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .fifa-prep-text {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-timeline-lab {
            color: #87ceeb;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .fifa-timeline-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .fifa-summary-item {
            background: rgba(255,255,255,0.05);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .fifa-summary-label {
            display: block;
            color: #b0c4de;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }
        
        .fifa-summary-value {
            color: #87ceeb;
            font-size: 1.1rem;
            font-weight: bold;
        }
        
        .fifa-summary-value.positive {
            color: #51cf66;
        }
        
        .fifa-substance-info {
            margin-top: 20px;
        }
        
        .fifa-substance-info h5 {
            color: #ffd700;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .fifa-info-item {
            display: flex;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(255, 215, 0, 0.2);
        }
        
        .fifa-info-icon {
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .fifa-info-text {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-certification-details {
            margin-top: 20px;
        }
        
        .fifa-certification-details h5 {
            color: #ffd700;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .fifa-cert-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 8px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-cert-label {
            color: #b0c4de;
            font-size: 0.9rem;
        }
        
        .fifa-cert-value {
            color: #87ceeb;
            font-weight: 600;
        }
        
        .fifa-education-overview {
            margin-top: 20px;
        }
        
        .fifa-education-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-education-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .fifa-education-text {
            color: #b0c4de;
            font-size: 0.9rem;
            flex: 1;
        }
        
        .fifa-education-status {
            color: #51cf66;
            font-weight: 600;
            margin: 0 10px;
        }
        
        .fifa-education-date {
            color: #87ceeb;
            font-size: 0.8rem;
        }
        
        .fifa-education-resources {
            margin-top: 20px;
        }
        
        .fifa-education-resources h5 {
            color: #ffd700;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .fifa-resource-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        
        .fifa-resource-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .fifa-resource-text {
            color: #b0c4de;
            font-size: 0.9rem;
            flex: 1;
        }
        
        .fifa-resource-status {
            color: #51cf66;
            font-weight: 600;
        }
        
        /* Styles pour l'onglet Médical ultra-professionnel */
        .fifa-medical-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .fifa-medical-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .fifa-medical-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #00d4aa, #0099cc, #ff6b35);
            opacity: 0.8;
        }
        
        .fifa-medical-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .fifa-medical-card h4 {
            color: #ffd700;
            font-size: 1.3rem;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .fifa-medical-stat {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .fifa-stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        .fifa-stat-header:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .fifa-stat-header span:first-child {
            color: #b0c4de;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .fifa-stat-value {
            color: #87ceeb;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .fifa-stat-value.highlight {
            color: #ffd700;
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .fifa-stat-value.positive {
            color: #51cf66;
            font-weight: 700;
        }
        
        /* Styles pour les alertes médicales */
        .fifa-medical-alerts {
            margin-bottom: 25px;
        }
        
        .fifa-alert-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin: 12px 0;
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 10px;
            border-left: 4px solid #ffd700;
        }
        
        .fifa-alert-item.low-risk {
            background: rgba(255, 215, 0, 0.1);
            border-color: rgba(255, 215, 0, 0.2);
            border-left-color: #ffd700;
        }
        
        .fifa-alert-item.medium-risk {
            background: rgba(255, 107, 107, 0.1);
            border-color: rgba(255, 107, 107, 0.2);
            border-left-color: #ff6b6b;
        }
        
        .fifa-alert-item.high-risk {
            background: rgba(255, 0, 0, 0.1);
            border-color: rgba(255, 0, 0, 0.2);
            border-left-color: #ff0000;
        }
        
        .fifa-alert-icon {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .fifa-alert-text {
            color: #b0c4de;
            font-size: 0.9rem;
            flex: 1;
            line-height: 1.4;
        }
        
        .fifa-alert-priority {
            color: #ffd700;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 8px;
            background: rgba(255, 215, 0, 0.2);
            border-radius: 6px;
            white-space: nowrap;
        }
        
        /* Styles pour les recommandations */
        .fifa-recommendations h5 {
            color: #ffd700;
            margin-bottom: 15px;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .fifa-recommendation-item {
            display: flex;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: rgba(81, 207, 102, 0.1);
            border: 1px solid rgba(81, 207, 102, 0.2);
            border-radius: 10px;
        }
        
        .fifa-recommendation-icon {
            font-size: 1.3rem;
            margin-right: 15px;
            color: #51cf66;
        }
        
        .fifa-recommendation-text {
            color: #b0c4de;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        /* Styles pour la timeline médicale */
        .fifa-medical-timeline {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .fifa-timeline-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .fifa-timeline-date {
            color: #87ceeb;
            font-weight: 600;
            font-size: 0.85rem;
            min-width: 80px;
        }
        
        .fifa-timeline-event {
            color: #b0c4de;
            font-size: 0.9rem;
            flex: 1;
            margin: 0 15px;
        }
        
        .fifa-timeline-status {
            color: #51cf66;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 4px 8px;
            background: rgba(81, 207, 102, 0.2);
            border-radius: 6px;
        }
        
        /* Styles pour les nouveaux devices professionnels */
        .fifa-device-card .fifa-device-details .fifa-device-stat:nth-child(even) {
            background: rgba(255,255,255,0.02);
            padding: 8px;
            border-radius: 6px;
            margin: 5px 0;
        }
        
        .fifa-device-card .fifa-device-details .fifa-device-stat:nth-child(odd) {
            background: rgba(255,255,255,0.01);
            padding: 8px;
            border-radius: 6px;
            margin: 5px 0;
        }
        
        /* Styles spécifiques pour Garmin */
        .fifa-device-card h4:contains("Garmin") {
            background: linear-gradient(135deg, #00d4aa, #0099cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Styles spécifiques pour Catapult */
        .fifa-device-card h4:contains("Catapult") {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Styles pour les médicaments */
        .fifa-device-card h4:contains("Médicaments") {
            background: linear-gradient(135deg, #e91e63, #9c27b0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Styles pour les ATU */
        .fifa-device-card h4:contains("ATU") {
            background: linear-gradient(135deg, #2196f3, #00bcd4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .fifa-tab-content.active {
            display: block;
        }
        
        .fifa-chart-container {
            height: 400px;
            margin: 20px 0;
        }
        
        .fifa-loading {
            text-align: center;
            padding: 60px;
            color: #b0c4de;
            font-size: 1.2rem;
        }
        
        .fifa-error {
            text-align: center;
            padding: 60px;
            color: #ff6b6b;
            font-size: 1.2rem;
        }
        
        .fifa-refresh-btn {
            background: #ffd700;
            color: #1e3c72;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 20px;
            transition: all 0.3s ease;
        }
        
        .fifa-refresh-btn:hover {
            background: #ffed4e;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.3);
        }
        
        .fifa-section h2 {
            color: #ffd700;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        
        .fifa-section h3 {
            color: #87ceeb;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        @media (max-width: 768px) {
            .fifa-tabs {
                flex-direction: column;
            }
            
            .fifa-tab-button {
                min-width: auto;
            }
            
            .fifa-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body class="antialiased">
    <!-- Header FIFA ultra-compact -->
    <div class="relative bg-gray-100 dark:bg-gray-900 py-4">
        @if (Route::has('login'))
            <div class="absolute top-0 right-0 p-4 text-right z-10">
                @auth
                    <a href="{{ url('/home') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Home</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-4xl mx-auto px-4">
                            <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        FIT Portal
                    </h1>
                    <p class="text-base text-gray-600 dark:text-gray-400">
                        Bienvenue dans votre portail FIT personalisé
                    </p>
                </div>
        </div>
    </div>

    <!-- Section FIFA Portal intégrée -->
    <section class="fifa-section">
        <div class="fifa-container">
            <!-- Header FIFA simplifié -->
            <div class="fifa-header">
                <div class="fifa-header-main">
                    <div class="fifa-header-text">
                        <!-- Titre et sous-titre supprimés -->
                    </div>
                    
                    <!-- Barre de Recherche FIFA avec Navigation -->
                    <div class="fifa-search-container">
                        <!-- Navigation Précédent/Suivant -->
                        <div class="fifa-navigation-controls">
                            <button class="fifa-nav-btn prev" id="search-prev-btn">
                                ← Précédent
                            </button>
                            <span class="fifa-player-counter" id="fifa-player-counter">
                                17 / 56
                            </span>
                            <button class="fifa-nav-btn next" id="search-next-btn">
                                Suivant →
                            </button>
                        </div>
                        
                        <!-- Barre de Recherche -->
                        <div class="fifa-search-box">
                            <input type="text" 
                                   id="fifa-player-search" 
                                   class="fifa-search-input" 
                                   placeholder="Rechercher par nom, club ou association..."
                                   autocomplete="off">
                            <button class="fifa-search-btn" onclick="performFIFASearch()">
                                <span class="fifa-search-icon">Rechercher</span>
                            </button>
                        </div>
                        
                        <!-- Résultats de recherche en temps réel -->
                        <div id="fifa-search-results" class="fifa-search-results">
                            <!-- Les résultats s'afficheront ici -->
                        </div>
                        
                        <!-- Indicateur de chargement -->
                        <div id="fifa-search-loading" class="fifa-search-loading" style="display: none;">
                            <div class="fifa-loading-spinner"></div>
                            <span>Recherche en cours...</span>
                        </div>
                        
                        <!-- Actions de Navigation -->
                        <div class="fifa-navigation-actions">
                            <a href="/players/list" class="fifa-back-btn">
                                ← Retour à la liste
                            </a>
                            <a href="/logout" class="fifa-logout-btn">
                                Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Zone FIFA Ultra-Moderne Enrichie -->
            <div class="fifa-hero-zone">
                <div class="fifa-hero-container">
                    <!-- Section Photo + Infos de Base -->
                    <div class="fifa-hero-main">
                        <div class="fifa-player-photo-container">
                            <div class="fifa-player-photo">
                                <!-- Photo du joueur avec fallbacks locaux robustes - DYNAMIQUE -->
                                <img src="{{ $player->player_face_url ?: $player->player_picture ?: $player->profile_image ?: $player->photo_url ?: $player->photo_path ?: "/images/players/default_player.svg" }}" 
                                     alt="Photo de {{ $player->first_name }} {{ $player->last_name }}"
                                     id="hero-player-photo"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                
                                <!-- Fallback avec initiales (MÉTHODE PORTAL JOUEURS) -->
                                <div class="fifa-player-avatar" id="hero-player-avatar" style="display: none;">
                                    <span class="fifa-player-initials">LM</span>
                                </div>
                                
                                <!-- Overlay supprimé - plus de chiffre sur la photo -->
                            </div>
                            
                            <!-- Drapeau de la nationalité supprimé - plus de drapeau à côté de la photo -->
                        </div>
                        
                        <div class="fifa-player-info">
                            <h1 class="fifa-player-name text-lg font-semibold" id="hero-player-name">Nom du Joueur</h1>
                            <div class="fifa-player-position" id="hero-position">LW</div>
                            <div class="fifa-player-age" id="hero-age">25 ans</div>
                            
                                        <!-- Boutons de navigation des joueurs -->
            <div class="fifa-player-navigation flex gap-2 mt-4">
                                <button id="previous-player-btn" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    ← Précédent
                                </button>
                                <button id="next-player-btn" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Suivant →
                                </button>
                            </div>
                            
                            <!-- Nouvelles informations physiques -->
                            <div class="fifa-player-physical">
                                <div class="fifa-physical-item">
                                    <span class="fifa-physical-icon">Taille</span>
                                    <span class="fifa-physical-value" id="hero-height">170cm</span>
                                </div>
                                <div class="fifa-physical-item">
                                    <span class="fifa-physical-icon">Poids</span>
                                    <span class="fifa-physical-value" id="hero-weight">72kg</span>
                                </div>
                                <div class="fifa-physical-item">
                                    <span class="fifa-physical-icon">Pied</span>
                                    <span class="fifa-physical-value" id="hero-preferred-foot">Droit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Stats FIFA Dynamiques - EN DESSOUS DE LA PHOTO -->
                    <div class="fifa-hero-stats">
                        <div class="fifa-stat-card primary">
                            <span class="fifa-stat-icon">OVR</span>
                            <span class="fifa-stat-value" id="hero-overall-rating-main">84</span>
                            <span class="fifa-stat-label">OVR</span>
                        </div>
                        
                        <div class="fifa-stat-card success">
                            <span class="fifa-stat-icon">POT</span>
                            <span class="fifa-stat-value" id="hero-potential-rating">88</span>
                            <span class="fifa-stat-label">POT</span>
                        </div>
                        
                        <div class="fifa-stat-card warning">
                            <span class="fifa-stat-icon">FIT</span>
                            <span class="fifa-stat-value" id="hero-fitness-score">92%</span>
                            <span class="fifa-stat-label">FIT</span>
                        </div>
                        
                        <div class="fifa-stat-card info">
                            <span class="fifa-stat-icon">FORME</span>
                            <span class="fifa-stat-value" id="hero-form-percentage">85%</span>
                            <span class="fifa-stat-label">FORME</span>
                        </div>
                    </div>
                    
                    <!-- Section Club + Nationalité -->
                    <div class="fifa-hero-club-nationality">
                        <div class="fifa-club-section">
                            <!-- Logo du club avec fallback local robuste -->
                            <img src="{{ $player->club && $player->club->logo_url ? $player->club->logo_url : ($player->club && $player->club->logo_path ? $player->club->logo_path : ($player->club && $player->club->logo_image ? $player->club->logo_image : "/images/clubs/default_club.svg")) }}" 
                                 alt="Logo de {{ $player->club ? $player->club->name : 'Club' }}"
                                 id="hero-club-logo"
                                 class="fifa-club-logo"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            
                            <!-- Fallback club icon (MÉTHODE PORTAL JOUEURS) -->
                            <div class="fifa-club-fallback" id="hero-club-fallback" style="display: none;">
                                <span class="fifa-club-initial">C</span>
                            </div>
                            
                            <div class="fifa-club-name" id="hero-club-name">{{ $player->club ? $player->club->name : "Club" }}</div>
                        </div>
                        
                        
                        <!-- Logo de l'association avec fallback -->
                        <div class="fifa-association-section">
                            <img src="{{ $player->association && $player->association->logo_url ? $player->association->logo_url : ($player->association && $player->association->logo_path ? $player->association->logo_path : "/images/associations/default_association.svg") }}" 
                                 alt="Logo de {{ $player->association ? $player->association->name : "Association" }}"
                                 id="hero-association-logo"
                                 class="fifa-association-logo"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            
                            <!-- Fallback association icon -->
                            <div class="fifa-association-fallback" id="hero-association-fallback" style="display: none;">
                                <span class="fifa-association-initial">FA</span>
                            </div>
                            
                            <div class="fifa-association-name" id="hero-association-name">{{ $player->association ? $player->association->name : ($player->nationality ?? "Association") }}</div>
                        </div>
                        
                        <div class="fifa-nationality-section">
                            <!-- Informations de licence et FIFA ID à gauche -->
                            <div class="fifa-license-info">
                                <div class="fifa-license-item">
                                    <span class="fifa-license-label">Licence Club:</span>
                                    <span class="fifa-license-value" id="hero-license-number">{{ $player->license_number ?? 'N/A' }}</span>
                                </div>
                                <div class="fifa-license-item">
                                    <span class="fifa-license-label">FIFA ID:</span>
                                    <span class="fifa-license-value" id="hero-fifa-id">{{ $player->id ?? 'N/A' }}</span>
                                </div>
                            </div>
                            
                            <!-- Drapeau de la nation avec fallback (MÉTHODE PORTAL JOUEURS) -->
                            <img src="{{ $player->flag_image ?: "/images/flags/default_flag.svg" }}" 
                                 alt="Drapeau de {{ $player->nationality }}"
                                 id="hero-flag"
                                 class="fifa-flag-nationality"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            
                            <!-- Fallback nation (MÉTHODE PORTAL JOUEURS) -->
                            <div class="fifa-flag-fallback" id="hero-flag-fallback" style="display: none;">
                                <span class="fifa-flag-initial">TF</span>
                            </div>
                            
                            <div class="fifa-country" id="hero-nationality">{{ $player->nationality ?? "Nation" }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Nouvelle Section Métriques Avancées -->
                <div class="fifa-hero-advanced">
                    <!-- Statistiques de Performance -->
                    <div class="fifa-performance-stats">
                        <div class="fifa-perf-stat">
                            <span class="fifa-perf-icon">Buts</span>
                            <span class="fifa-perf-value" id="hero-goals-scored">0</span>
                            <span class="fifa-perf-label">Buts Saison</span>
                        </div>
                        <div class="fifa-perf-stat">
                            <span class="fifa-perf-icon">Passes</span>
                            <span class="fifa-perf-value" id="hero-assists">0</span>
                            <span class="fifa-perf-label">Assists</span>
                        </div>
                        <div class="fifa-perf-stat">
                            <span class="fifa-perf-icon">Matchs</span>
                            <span class="fifa-perf-value" id="hero-matches-played">0</span>
                            <span class="fifa-perf-label">Matchs</span>
                        </div>
                    </div>
                    
                    <!-- Indicateurs de Santé -->
                    <div class="fifa-health-indicators">
                        <div class="fifa-health-item">
                            <span class="fifa-health-icon">Risque</span>
                            <span class="fifa-health-value" id="hero-injury-risk">15%</span>
                            <span class="fifa-health-label">Risque Blessure</span>
                        </div>
                        <div class="fifa-health-item">
                            <span class="fifa-health-icon">Moral</span>
                            <span class="fifa-health-value" id="hero-morale">85%</span>
                            <span class="fifa-health-label">Moral</span>
                        </div>
                        <div class="fifa-health-item">
                            <span class="fifa-health-icon">Médic</span>
                            <span class="fifa-health-value" id="hero-medications-count">0</span>
                            <span class="fifa-health-label">Médicaments</span>
                        </div>
                    </div>
                    
                    <!-- Progression Saison -->
                    <div class="fifa-season-progress">
                        <div class="fifa-progress-header">
                            <span class="fifa-progress-title">Saison 2024-25</span>
                            <span class="fifa-progress-percentage" id="hero-season-progress">75%</span>
                        </div>
                        <div class="fifa-progress-bar">
                            <div class="fifa-progress-fill" id="hero-progress-fill" style="width: 75%"></div>
                        </div>
                        <div class="fifa-progress-details">
                            <span id="hero-matches-completed">25 matchs joués</span>
                            <span id="hero-matches-remaining">10 matchs restants</span>
                        </div>
                    </div>
                    
                    <!-- Performances Récentes -->
                    <div class="fifa-recent-performance">
                        <div class="fifa-recent-header">5 derniers matchs</div>
                        <div class="fifa-recent-results" id="hero-recent-results">
                            <div class="fifa-result win">W</div>
                            <div class="fifa-result win">W</div>
                            <div class="fifa-result draw">D</div>
                            <div class="fifa-result win">W</div>
                            <div class="fifa-result win">W</div>
                        </div>
                    </div>
                </div>
            </div>



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
                    Devices <span id="devices-count">3</span>
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
                <h2>Centre de Performances FIFA</h2>
                
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
                        <div class="fifa-loading">Chargement des données FIFA...</div>
                    </div>
                </div>

                <div id="advanced-stats-sub-tab" class="fifa-sub-tab-content">
                    <h3>Statistiques avancées</h3>
                    <div id="advanced-stats-content">
                        <div class="fifa-loading">Chargement des statistiques avancées...</div>
                    </div>
                </div>

                <div id="match-stats-sub-tab" class="fifa-sub-tab-content">
                    <h3>Statistiques de match</h3>
                    <div id="match-stats-content">
                        <div class="fifa-loading">Chargement des statistiques de match...</div>
                    </div>
                </div>

                <div id="comparison-sub-tab" class="fifa-sub-tab-content">
                    <h3>Analyse comparative</h3>
                    <div id="comparison-content">
                        <div class="fifa-loading">Chargement de l'analyse comparative...</div>
                    </div>
                </div>
            </div>

            <div id="trends-tab" class="fifa-tab-content">
                <h2>Tendances</h2>
                <div id="trends-content">
                    <div class="fifa-loading">Chargement des tendances...</div>
                </div>
            </div>

            <!-- Nouveaux onglets FIFA -->
            <div id="notifications-tab" class="fifa-tab-content">
                <h2>🔔 Notifications FIFA</h2>
                <div id="notifications-content">
                    <div class="fifa-loading">Chargement des notifications...</div>
                </div>
            </div>

            <div id="health-tab" class="fifa-tab-content">
                <h2>Santé & Bien-être</h2>
                <div id="health-content">
                    <div class="fifa-loading">Chargement des données de santé...</div>
                </div>
            </div>

            <div id="medical-tab" class="fifa-tab-content">
                <h2>Médical</h2>
                <div id="medical-content">
                    <div class="fifa-loading">Chargement des données médicales...</div>
                </div>
            </div>

            <div id="devices-tab" class="fifa-tab-content">
                <h2>Devices</h2>
                <div id="devices-content">
                    <div class="fifa-loading">Chargement des devices...</div>
                </div>
            </div>

            <div id="doping-tab" class="fifa-tab-content">
                <h2>Dopage historique</h2>
                <div id="doping-content">
                    <div class="fifa-loading">Chargement de l'historique dopage...</div>
                </div>
            </div>

            <div id="licenses-tab" class="fifa-tab-content">
                <h2>Historique des Licences</h2>
                <div id="licenses-content">
                    <!-- Filtres -->
                    <div class="fifa-license-filters">
                        <div class="filter-group">
                            <label for="year-filter">Année:</label>
                            <select id="year-filter" class="fifa-filter-select">
                                <option value="toutes">Toutes</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="club-filter">Club:</label>
                            <select id="club-filter" class="fifa-filter-select">
                                <option value="tous">Tous</option>
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
                                    <th>Période</th>
                                    <th>Club</th>
                                    <th>Association</th>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody class="licenses-table-body">
                                <tr>
                                    <td colspan="6" class="fifa-loading">Chargement de l'historique des licences...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Résumé des primes de formation -->
                    <div class="fifa-training-compensation">
                        <h3>Primes de Formation FIFA</h3>
                        <div class="training-compensation-content">
                            <div class="fifa-loading">Calcul des primes de formation...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton de rafraîchissement -->
            <div style="text-align: center; margin-top: 30px;">
                <button class="fifa-refresh-btn" onclick="loadFIFAData()">Rafraîchir les données FIFA</button>
            </div>
        </div>
    </section>

    <script>
        // Variables globales FIFA
        let currentFIFATab = 'performances';
        let fifaData = null;
        let isLoadingLicenses = false; // Verrou pour éviter les appels multiples
        
        // Fonction pour changer d'onglet FIFA
        function showFIFATab(tabName) {
            // Masquer tous les onglets
            document.querySelectorAll('.fifa-tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Désactiver tous les boutons
            document.querySelectorAll('.fifa-tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Afficher l'onglet sélectionné
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Activer le bouton correspondant
            const activeButton = document.querySelector(`.fifa-tab-button[onclick*="${tabName}"]`);
            if (activeButton) {
                activeButton.classList.add('active');
            }
            
            currentFIFATab = tabName;
            
            // Charger le contenu de l'onglet
            loadFIFATabContent(tabName);
        }
        
        // Fonction pour changer de sous-onglet Performances
        function showFIFASubTab(subTabName) {
            // Masquer tous les sous-onglets
            document.querySelectorAll('.fifa-sub-tab-content').forEach(subTab => {
                subTab.classList.remove('active');
            });
            
            // Désactiver tous les boutons de sous-onglets
            document.querySelectorAll('.fifa-sub-tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Afficher le sous-onglet sélectionné
            document.getElementById(subTabName + '-sub-tab').classList.add('active');
            
            // Activer le bouton correspondant (sans dépendre de event)
            const activeButton = document.querySelector(`.fifa-sub-tab-button[onclick*="${subTabName}"]`);
            if (activeButton) {
                activeButton.classList.add('active');
            }
            
            // Charger le contenu du sous-onglet
            loadFIFASubTabContent(subTabName);
        }
        
        // Charger le contenu d'un sous-onglet Performances
        function loadFIFASubTabContent(subTabName) {
            console.log('INFO: loadFIFASubTabContent appelée avec:', subTabName);
            console.log('INFO: fifaData disponible:', !!fifaData);
            console.log('INFO: fifaData complet:', fifaData);
            
            if (!fifaData) {
                console.log('ERREUR: Pas de fifaData, chargement en cours...');
                loadFIFAData();
                return;
            }
            
            console.log('SUCCES: fifaData disponible, chargement du contenu...');
            
            switch(subTabName) {
                case 'overview':
                    console.log('CIBLE: Chargement du contenu Overview...');
                    try {
                        loadFIFAOverviewContent();
                        console.log('SUCCES: loadFIFAOverviewContent exécutée avec succès');
                    } catch (error) {
                        console.error('ERREUR: Erreur dans loadFIFAOverviewContent:', error);
                    }
                    break;
                case 'advanced-stats':
                    console.log('CIBLE: Chargement du contenu Advanced Stats...');
                    loadFIFAAdvancedStatsContent();
                    break;
                case 'match-stats':
                    console.log('CIBLE: Chargement du contenu Match Stats...');
                    loadFIFAMatchStatsContent();
                    break;
                case 'comparison':
                    console.log('CIBLE: Chargement du contenu Comparison...');
                    loadFIFAComparisonContent();
                    break;
            }
        }
        
        // Charger le contenu d'un onglet FIFA
        function loadFIFATabContent(tabName) {
            if (!fifaData) {
                loadFIFAData();
                return;
            }
            
            switch(tabName) {
                case 'performances':
                    // L'onglet performances affiche le premier sous-onglet par défaut
                    console.log('CIBLE: Activation de l\'onglet Performances avec sous-onglet Overview...');
                    showFIFASubTab('overview');
                    break;
                case 'trends':
                    loadFIFATrendsContent();
                    break;
                case 'notifications':
                    loadFIFANotificationsContent();
                    break;
                case 'health':
                    loadFIFAHealthContent();
                    break;
                case 'medical':
                    loadFIFAMedicalContent();
                    break;
                case 'devices':
                    loadFIFADevicesContent();
                    break;
                case 'doping':
                    loadFIFADopingContent();
                    break;
                case 'licenses':
                    loadFIFALicensesContent();
                    break;
            }
        }
        
        // Charger les données FIFA
        async function loadFIFAData() {
            try {
                console.log('DEMARRAGE: loadFIFAData démarré...');
                
                // Afficher le loading pour les onglets principaux (avec vérification d'existence)
                const elementsToUpdate = [
                    'trends-content', 'notifications-content', 'health-content', 
                    'medical-content', 'devices-content', 'doping-content'
                ];
                
                elementsToUpdate.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.innerHTML = '<div class="fifa-loading">Chargement...</div>';
                    } else {
                        console.log(`Attention Élément ${id} non trouvé dans le DOM`);
                    }
                });
                
                // Afficher le loading pour les sous-onglets Performances (avec vérification d'existence)
                const performanceElements = [
                    'overview-content', 'advanced-stats-content', 
                    'match-stats-content', 'comparison-content'
                ];
                
                performanceElements.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.innerHTML = '<div class="fifa-loading">Chargement des données FIFA...</div>';
                    } else {
                        console.log(`Attention Élément ${id} non trouvé dans le DOM`);
                    }
                });
                
                // Récupérer l'ID du joueur depuis l'URL
                const urlParams = new URLSearchParams(window.location.search);
                const playerId = urlParams.get('player_id') || '7'; // Fallback sur l'ID 7 si aucun paramètre
                
                console.log('INFO: loadFIFAData - player_id trouvé:', playerId);
                
                // Appel à l'API FIFA avec l'ID dynamique
                const response = await fetch(`/api/fifa/player/${playerId}`);
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const responseData = await response.json();
                fifaData = responseData.data;
                
                console.log('SUCCES: Données FIFA récupérées:', fifaData);
                console.log('INFO: Structure de fifaData:', JSON.stringify(fifaData, null, 2));
                
                // Vérifier que fifaData est bien structuré
                if (!fifaData || typeof fifaData !== 'object') {
                    console.error('ERREUR: fifaData invalide:', fifaData);
                    throw new Error('Données FIFA invalides');
                }
                
                // Mettre à jour l'interface
                updateFIFAInterface();
                
                // Charger le contenu de l'onglet actuel
                loadFIFATabContent(currentFIFATab);
                
                // Charger le contenu des sous-onglets Performances
                console.log('CIBLE: Chargement automatique de l\'onglet Overview...');
                loadFIFASubTabContent('overview');
                
            } catch (error) {
                console.error('ERREUR: Erreur lors du chargement FIFA:', error);
                
                // Erreurs pour les onglets principaux (avec vérification d'existence)
                const errorElements = [
                    'trends-content', 'notifications-content', 'health-content', 
                    'medical-content', 'devices-content', 'doping-content'
                ];
                
                errorElements.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.innerHTML = '<div class="fifa-error">Erreur de chargement</div>';
                    }
                });
                
                // Erreurs pour les sous-onglets Performances (avec vérification d'existence)
                const performanceErrorElements = [
                    'overview-content', 'advanced-stats-content', 
                    'match-stats-content', 'comparison-content'
                ];
                
                performanceErrorElements.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.innerHTML = '<div class="fifa-error">Erreur de chargement des données FIFA</div>';
                    }
                });
            }
        }
        
        // Mettre à jour l'interface FIFA
        function updateFIFAInterface() {
            if (!fifaData) return;
            
            console.log('CIBLE: updateFIFAInterface appelée avec:', fifaData);
            
            // Mettre à jour les compteurs des nouveaux onglets
            updateFIFACounters();
        }
        
        // Mettre à jour les compteurs FIFA
        function updateFIFACounters() {
            if (!fifaData) return;
            
            // Compteur notifications basé sur les alertes FIFA
            const notificationsCount = calculateNotificationsCount(fifaData);
            updateCounter('notifications-count', notificationsCount);
            
            // Compteur médical basé sur les traitements
            const medicalCount = calculateMedicalCount(fifaData);
            updateCounter('medical-count', medicalCount);
            
            // Compteur devices basé sur l'équipement
            const devicesCount = calculateDevicesCount(fifaData);
            updateCounter('devices-count', devicesCount);
        }
        
        // Calculer le nombre de notifications
        function calculateNotificationsCount(fifaData) {
            let count = 0;
            if (fifaData.injury_risk > 20) count++; // Alerte blessure
            if (fifaData.fitness_score < 70) count++; // Alerte forme
            if (fifaData.morale_percentage < 60) count++; // Alerte moral
            if (fifaData.medications_count > 0) count++; // Alerte médicaments
            return count;
        }
        
        // Calculer le nombre d'éléments médicaux
        function calculateMedicalCount(fifaData) {
            let count = 0;
            if (fifaData.medications_count > 0) count++;
            if (fifaData.injuries_count > 0) count++;
            if (fifaData.last_medical_check) count++;
            if (fifaData.injury_risk > 15) count++;
            return count;
        }
        
        // Fonctions de chargement du contenu des sous-onglets Performances
        // (Les fonctions complètes sont définies plus bas)
        
        // Calculer le nombre de devices
        function calculateDevicesCount(fifaData) {
            return 3; // iPhone, Apple Watch, AirPods
        }
        
        // Mettre à jour un compteur
        function updateCounter(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = value;
            }
        }
        
        // Charger le contenu de l'onglet Vue d'ensemble
        function loadFIFAOverviewContent() {
            console.log('DEMARRAGE: loadFIFAOverviewContent démarrée');
            console.log('INFO: fifaData reçu:', fifaData);
            
            if (!fifaData) {
                console.log('ERREUR: fifaData est null/undefined, sortie de la fonction');
                return;
            }
            
            const content = `
                <div class="fifa-overview-grid">
                    <!-- Carte Statistiques Générales -->
                    <div class="fifa-overview-card">
                        <h4>Statistiques Statistiques Générales</h4>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Note Globale:</span>
                            <span class="fifa-stat-value highlight">${fifaData.overall_rating || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Potentiel:</span>
                            <span class="fifa-stat-value">${fifaData.potential || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Forme Actuelle:</span>
                            <span class="fifa-stat-value">${fifaData.form || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Fitness:</span>
                            <span class="fifa-stat-value">${fifaData.fitness_score || 'N/A'}%</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Morale:</span>
                            <span class="fifa-stat-value">${fifaData.morale_percentage || 'N/A'}%</span>
                        </div>
                    </div>
                    
                    <!-- Carte Statistiques Offensives -->
                    <div class="fifa-overview-card">
                        <h4>Football Statistiques Offensives</h4>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Buts Marqués:</span>
                            <span class="fifa-stat-value highlight">${fifaData.goals_scored || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Passes Décisives:</span>
                            <span class="fifa-stat-value highlight">${fifaData.assists || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Tirs Cadrés:</span>
                            <span class="fifa-stat-value">${fifaData.shots_on_target || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Précision Tirs:</span>
                            <span class="fifa-stat-value">${fifaData.shot_accuracy || 'N/A'}%</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Dribbles Réussis:</span>
                            <span class="fifa-stat-value">${fifaData.dribbles_won || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <!-- Carte Statistiques Défensives -->
                    <div class="fifa-overview-card">
                        <h4>🛡️ Statistiques Défensives</h4>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Tacles Réussis:</span>
                            <span class="fifa-stat-value highlight">${fifaData.tackles_won || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Interceptions:</span>
                            <span class="fifa-stat-value">${fifaData.interceptions || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Duel Aérien:</span>
                            <span class="fifa-stat-value">${fifaData.aerial_duels_won || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Fautes Commises:</span>
                            <span class="fifa-stat-value">${fifaData.fouls_committed || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Cartons Jaunes:</span>
                            <span class="fifa-stat-value">${fifaData.yellow_cards || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <!-- Carte Statistiques Physiques -->
                    <div class="fifa-overview-card">
                        <h4>Physique Statistiques Physiques</h4>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Vitesse:</span>
                            <span class="fifa-stat-value">${fifaData.max_speed || 'N/A'} km/h</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Endurance:</span>
                            <span class="fifa-stat-value">${fifaData.stamina || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Force:</span>
                            <span class="fifa-stat-value">${fifaData.strength || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Agilité:</span>
                            <span class="fifa-stat-value">${fifaData.agility || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Équilibre:</span>
                            <span class="fifa-stat-value">${fifaData.balance || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <!-- Carte Statistiques Techniques -->
                    <div class="fifa-overview-card">
                        <h4>CIBLE: Statistiques Techniques</h4>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Précision Passe:</span>
                            <span class="fifa-stat-value highlight">${fifaData.passing_accuracy || 'N/A'}%</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Contrôle:</span>
                            <span class="fifa-stat-value">${fifaData.ball_control || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Vision:</span>
                            <span class="fifa-stat-value">${fifaData.vision || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Composure:</span>
                            <span class="fifa-stat-value">${fifaData.composure || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Technique:</span>
                            <span class="fifa-stat-value">${fifaData.technique || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <!-- Carte Performance Saison -->
                    <div class="fifa-overview-card">
                        <h4>Croissance Performance Saison</h4>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Matchs Joués:</span>
                            <span class="fifa-stat-value highlight">${fifaData.matches_played || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Minutes Jouées:</span>
                            <span class="fifa-stat-value">${fifaData.minutes_played || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Note Moyenne:</span>
                            <span class="fifa-stat-value">${fifaData.average_rating || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Man of the Match:</span>
                            <span class="fifa-stat-value">${fifaData.man_of_match || 'N/A'}</span>
                        </div>
                        <div class="fifa-stat-item">
                            <span class="fifa-stat-label">Forme Tendancielle:</span>
                            <span class="fifa-stat-value">${fifaData.form_trend || 'Stable'}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Graphique de Performance -->
                <div class="fifa-chart-container">
                    <h4>Statistiques Évolution des Performances</h4>
                    <canvas id="fifa-overview-chart" width="400" height="200"></canvas>
                </div>
            `;
            
            console.log('INFO: Tentative de mise à jour du DOM avec overview-content...');
            const overviewElement = document.getElementById('overview-content');
            console.log('INFO: Élément overview-content trouvé:', !!overviewElement);
            
            if (overviewElement) {
                overviewElement.innerHTML = content;
                console.log('SUCCES: Contenu Overview mis à jour avec succès !');
            } else {
                console.error('ERREUR: Élément overview-content non trouvé !');
            }
            
            // Créer le graphique de performance
            try {
                createFIFAOverviewChart();
                console.log('SUCCES: Graphique Overview créé avec succès !');
            } catch (error) {
                console.error('ERREUR: Erreur lors de la création du graphique:', error);
            }
        }
        
        // Charger le contenu de l'onglet Statistiques avancées
        function loadFIFAAdvancedStatsContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="fifa-stat-card">
                        <h3>Statistiques Offensives</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Buts marqués:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.goals_scored || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Passes décisives:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.assists || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Tirs cadrés:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.shots_on_target || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Précision des tirs:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.shot_accuracy || 0}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Statistiques Défensives</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Tacles réussis:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.tackles_won || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Interceptions:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.interceptions || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Dégagements:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.clearances || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Duels gagnés:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.duels_won || 0}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Statistiques Physiques</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Distance parcourue:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.distance_covered || 0}km</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Vitesse max:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.max_speed || 0}km/h</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Sprints:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.sprints_count || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme physique:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.fitness_score || 0}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('advanced-stats-content').innerHTML = content;
        }
        
        // Charger le contenu de l'onglet Statistiques de match
        function loadFIFAMatchStatsContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="fifa-stat-card">
                        <h3>Performance en Match</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Matchs joués:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.matches_played || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Minutes jouées:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.minutes_played || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Passes réussies:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.passes_completed || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Précision des passes:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.pass_accuracy || 0}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Activité en Match</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Distance par match:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.distance_covered ? Math.round(fifaData.distance_covered / (fifaData.matches_played || 1)) : 0}km</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Sprints par match:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.sprints_count ? Math.round(fifaData.sprints_count / (fifaData.matches_played || 1)) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Vitesse moyenne:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.max_speed ? Math.round(fifaData.max_speed * 0.7) : 0}km/h</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Intensité:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.fitness_score ? Math.round(fifaData.fitness_score * 0.9) : 0}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Efficacité</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Buts/match:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.goals_scored && fifaData.matches_played ? (fifaData.goals_scored / fifaData.matches_played).toFixed(2) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Passes/match:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.assists && fifaData.matches_played ? (fifaData.assists / fifaData.matches_played).toFixed(2) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Rating moyen:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.overall_rating || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme actuelle:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.form_percentage || 0}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('match-stats-content').innerHTML = content;
        }
        
        // Charger le contenu de l'onglet Analyse comparative
        function loadFIFAComparisonContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="fifa-stat-card">
                        <h3>Comparaison avec la Position</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Rating FIFA:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.overall_rating || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Moyenne position:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.overall_rating ? Math.round(fifaData.overall_rating * 0.95) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Différence:</span>
                                <span style="color: #ffd700; font-weight: bold;">+${fifaData.overall_rating ? Math.round(fifaData.overall_rating * 0.05) : 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Classement:</span>
                                <span style="color: #ffd700; font-weight: bold;">Top ${fifaData.overall_rating ? Math.round(100 - (fifaData.overall_rating / 100) * 100) : 0}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Comparaison avec l'Âge</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Âge actuel:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.age || 25}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Pic de forme:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.age ? (fifaData.age >= 25 && fifaData.age <= 30 ? 'SUCCES: Oui' : 'ERREUR: Non') : '❓'}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Potentiel:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.age ? (fifaData.age < 25 ? 'Croissance Croissant' : fifaData.age > 30 ? 'Déclin Déclinant' : 'CIBLE: Optimal') : '❓'}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Valeur marchande:</span>
                                <span style="color: #ffd700; font-weight: bold;">€${fifaData.market_value || '6M'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Comparaison avec la Forme</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme actuelle:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.form_percentage || 0}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme physique:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.fitness_score || 0}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Moral:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.morale_percentage || 0}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>État général:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.form_percentage && fifaData.fitness_score ? Math.round((fifaData.form_percentage + fifaData.fitness_score) / 2) : 0}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('comparison-content').innerHTML = content;
        }
        
        // Charger le contenu de l'onglet Tendances
        function loadFIFATrendsContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="fifa-stat-card">
                        <h3>Évolution des Performances</h3>
                        <div class="fifa-chart-container">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Radar des Compétences</h3>
                        <div class="fifa-chart-container">
                            <canvas id="skillsRadar"></canvas>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <div class="fifa-stat-card">
                        <h3>Analyse des Tendances</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="font-size: 1.5rem; color: #ffd700; margin-bottom: 5px;">Croissance</div>
                                <div style="font-weight: bold; margin-bottom: 5px;">Progression</div>
                                <div style="color: #87ceeb;">Rating FIFA en hausse</div>
                            </div>
                            <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="font-size: 1.5rem; color: #ffd700; margin-bottom: 5px;">Football</div>
                                <div style="font-weight: bold; margin-bottom: 5px;">Efficacité</div>
                                <div style="color: #87ceeb;">Buts et passes stables</div>
                            </div>
                            <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="font-size: 1.5rem; color: #ffd700; margin-bottom: 5px;">🏃</div>
                                <div style="font-weight: bold; margin-bottom: 5px;">Physique</div>
                                <div style="color: #87ceeb;">Forme physique excellente</div>
                            </div>
                            <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <div style="font-size: 1.5rem; color: #ffd700; margin-bottom: 5px;">CIBLE:</div>
                                <div style="font-weight: bold; margin-bottom: 5px;">Objectifs</div>
                                <div style="color: #87ceeb;">Rating 90+ cette saison</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('trends-content').innerHTML = content;
            
            // Créer les graphiques après un délai pour s'assurer que les canvas sont prêts
            setTimeout(() => {
                createFIFAPerformanceChart();
                createFIFASkillsRadar();
            }, 100);
        }
        
        // Créer le graphique de performance FIFA
        function createFIFAPerformanceChart() {
            const ctx = document.getElementById('performanceChart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Match 1', 'Match 2', 'Match 3', 'Match 4', 'Match 5'],
                    datasets: [{
                        label: 'Rating FIFA',
                        data: [fifaData.overall_rating - 2, fifaData.overall_rating - 1, fifaData.overall_rating, fifaData.overall_rating + 1, fifaData.overall_rating + 2],
                        borderColor: '#ffd700',
                        backgroundColor: 'rgba(255, 215, 0, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le radar des compétences FIFA
        function createFIFASkillsRadar() {
            const ctx = document.getElementById('skillsRadar');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Vitesse', 'Tir', 'Passe', 'Dribble', 'Défense', 'Physique'],
                    datasets: [{
                        label: 'Compétences',
                        data: [
                            fifaData.max_speed || 50,
                            fifaData.shot_accuracy || 70,
                            fifaData.passes_completed || 80,
                            fifaData.duels_won || 75,
                            fifaData.tackles_won || 65,
                            fifaData.fitness_score || 85
                        ],
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        borderColor: '#ffd700',
                        borderWidth: 2,
                        pointBackgroundColor: '#ffd700'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        r: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            pointLabels: { color: 'white' }
                        }
                    }
                }
            });
        }
        
        // Charger le contenu de l'onglet Notifications
        function loadFIFANotificationsContent() {
            if (!fifaData) return;
            
            const content = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="fifa-stat-card">
                        <h3>🚨 Alertes Blessures</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Risque de blessure:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.injury_risk || 15}%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>État:</span>
                                <span style="color: ${fifaData.injury_risk > 20 ? '#ff6b6b' : '#51cf66'}; font-weight: bold;">
                                    ${fifaData.injury_risk > 20 ? 'Attention ÉLEVÉ' : 'SUCCES: FAIBLE'}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Médicament Médicaments Actifs</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Traitements:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.medications_count || 0}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Type:</span>
                                <span style="color: #ffd700; font-weight: bold;">${fifaData.medication_type || 'Aucun'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fifa-stat-card">
                        <h3>Statistiques Alertes Performance</h3>
                        <div style="text-align: left; margin-top: 15px;">
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Forme physique:</span>
                                <span style="color: ${fifaData.fitness_score < 70 ? '#ff6b6b' : '#51cf66'}; font-weight: bold;">
                                    ${fifaData.fitness_score || 0}%
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin: 10px 0;">
                                <span>Moral:</span>
                                <span style="color: ${fifaData.morale_percentage < 60 ? '#ff6b6b' : '#51cf66'}; font-weight: bold;">
                                    ${fifaData.morale_percentage || 0}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('notifications-content').innerHTML = content;
        }
        
        // Charger le contenu de l'onglet Santé
        function loadFIFAHealthContent() {
            if (!fifaData) return;
            
            const content = `
                <div class="fifa-health-grid">
                    <!-- Carte État Physique Général -->
                    <div class="fifa-health-card">
                        <h4>Coeur État Physique Général</h4>
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Score de Forme</span>
                                <span class="fifa-stat-value highlight">${fifaData.fitness_score || 'N/A'}%</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: ${fifaData.fitness_score || 0}%"></div>
                            </div>
                            <div class="fifa-health-indicator">
                                <span class="fifa-health-status ${fifaData.fitness_score >= 80 ? 'excellent' : fifaData.fitness_score >= 60 ? 'good' : 'poor'}">
                                    ${fifaData.fitness_score >= 80 ? '🟢 Excellent' : fifaData.fitness_score >= 60 ? '🟡 Bon' : '🔴 Faible'}
                                </span>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Niveau d'Énergie</span>
                                <span class="fifa-stat-value">${fifaData.energy_level || 'N/A'}%</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: ${fifaData.energy_level || 0}%"></div>
                            </div>
                        </div>
                        
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Qualité du Sommeil</span>
                                <span class="fifa-stat-value">${fifaData.sleep_quality || 'N/A'}/10</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: ${(fifaData.sleep_quality || 0) * 10}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Endurance et Performance -->
                    <div class="fifa-health-card">
                        <h4>🏃 Endurance et Performance</h4>
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Distance par Match</span>
                                <span class="fifa-stat-value highlight">${fifaData.distance_covered ? (fifaData.distance_covered / (fifaData.matches_played || 1)).toFixed(1) : 'N/A'} km</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Sprints par Match</span>
                                <span class="fifa-stat-value">${fifaData.sprints_count ? Math.round(fifaData.sprints_count / (fifaData.matches_played || 1)) : 'N/A'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Vitesse Moyenne</span>
                                <span class="fifa-stat-value">${fifaData.average_speed || 'N/A'} km/h</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Vitesse Maximale</span>
                                <span class="fifa-stat-value">${fifaData.max_speed || 'N/A'} km/h</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Récupération et Régénération -->
                    <div class="fifa-health-card">
                        <h4>🧘 Récupération et Régénération</h4>
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Temps de Récupération</span>
                                <span class="fifa-stat-value">${fifaData.recovery_time || 'N/A'} heures</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Qualité de Récupération</span>
                                <span class="fifa-stat-value">${fifaData.recovery_quality || 'N/A'}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Hydratation</span>
                                <span class="fifa-stat-value">${fifaData.hydration_level || 'N/A'}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Nutrition</span>
                                <span class="fifa-stat-value">${fifaData.nutrition_score || 'N/A'}/10</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Métriques Cardiovasculaires -->
                    <div class="fifa-health-card">
                        <h4>💓 Métriques Cardiovasculaires</h4>
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Fréquence Cardiaque Repos</span>
                                <span class="fifa-stat-value">${fifaData.resting_heart_rate || 'N/A'} bpm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Fréquence Cardiaque Max</span>
                                <span class="fifa-stat-value">${fifaData.max_heart_rate || 'N/A'} bpm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Variabilité Cardiaque</span>
                                <span class="fifa-stat-value">${fifaData.heart_rate_variability || 'N/A'} ms</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Pression Artérielle</span>
                                <span class="fifa-stat-value">${fifaData.blood_pressure || 'N/A'} mmHg</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Bien-être Mental -->
                    <div class="fifa-health-card">
                        <h4>Cerveau Bien-être Mental</h4>
                        <div class="fifa-health-stat">
                            <div class="fifa-stat-header">
                                <span>Niveau de Stress</span>
                                <span class="fifa-stat-value">${fifaData.stress_level || 'N/A'}/10</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: ${(fifaData.stress_level || 0) * 10}%"></div>
                            </div>
                            
                            <div class="fifa-stat-header">
                                <span>Qualité de Concentration</span>
                                <span class="fifa-stat-value">${fifaData.concentration_quality || 'N/A'}%</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: ${fifaData.concentration_quality || 0}%"></div>
                            </div>
                            
                            <div class="fifa-stat-header">
                                <span>Niveau de Motivation</span>
                                <span class="fifa-stat-value">${fifaData.motivation_level || 'N/A'}%</span>
                            </div>
                            <div class="fifa-progress-bar">
                                <div class="fifa-progress-fill" style="width: ${fifaData.motivation_level || 0}%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Recommandations Santé -->
                    <div class="fifa-health-card">
                        <h4>📋 Recommandations Santé</h4>
                        <div class="fifa-health-recommendations">
                            <div class="fifa-recommendation-item">
                                <span class="fifa-recommendation-icon">💧</span>
                                <span class="fifa-recommendation-text">Augmenter l'hydratation de 20%</span>
                            </div>
                            <div class="fifa-recommendation-item">
                                <span class="fifa-recommendation-icon">😴</span>
                                <span class="fifa-recommendation-text">Améliorer la qualité du sommeil</span>
                            </div>
                            <div class="fifa-recommendation-item">
                                <span class="fifa-recommendation-icon">🥗</span>
                                <span class="fifa-recommendation-text">Optimiser l'apport nutritionnel</span>
                            </div>
                            <div class="fifa-recommendation-item">
                                <span class="fifa-recommendation-icon">🧘</span>
                                <span class="fifa-recommendation-text">Pratiquer la méditation 15min/jour</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graphique Évolution Santé -->
                <div class="fifa-chart-container">
                    <h4>Statistiques Évolution de la Santé sur 30 Jours</h4>
                    <canvas id="fifa-health-chart" width="400" height="200"></canvas>
                </div>
            `;
            
            document.getElementById('health-content').innerHTML = content;
            
            // Créer le graphique de santé
            createFIFAHealthChart();
        }
        
        // Charger le contenu de l'onglet Médical
        function loadFIFAMedicalContent() {
            if (!fifaData) return;
            
            const content = `
                <div class="fifa-medical-grid">
                    <!-- Carte État Général de Santé -->
                    <div class="fifa-medical-card">
                        <h4>🏥 État Général de Santé</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Score de Santé Global</span>
                                <span class="fifa-stat-value highlight">${fifaData.health_score || '92'}/100</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Statut Médical</span>
                                <span class="fifa-stat-value positive">🟢 APTE AU JEU</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dernière Évaluation</span>
                                <span class="fifa-stat-value">15 Jan 2024</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Médecin Traitant</span>
                                <span class="fifa-stat-value">Dr. Martinez (Médecin FIFA)</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Prochaine Consultation</span>
                                <span class="fifa-stat-value">22 Jan 2024</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Paramètres Vitaux -->
                    <div class="fifa-medical-card">
                        <h4>💓 Paramètres Vitaux</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Fréquence Cardiaque Repos</span>
                                <span class="fifa-stat-value">${fifaData.resting_hr || '58'} bpm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Fréquence Cardiaque Max</span>
                                <span class="fifa-stat-value">${fifaData.max_hr || '195'} bpm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Tension Artérielle</span>
                                <span class="fifa-stat-value">${fifaData.blood_pressure || '118/75'} mmHg</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Température Corporelle</span>
                                <span class="fifa-stat-value">${fifaData.body_temp || '36.8'}°C</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>SpO2 (Oxygénation)</span>
                                <span class="fifa-stat-value">${fifaData.spo2 || '98'}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Réserve Cardiaque</span>
                                <span class="fifa-stat-value">${fifaData.cardiac_reserve || 'Excellent'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Biométrie Avancée -->
                    <div class="fifa-medical-card">
                        <h4>Statistiques Biométrie Avancée</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>IMC</span>
                                <span class="fifa-stat-value">${fifaData.bmi || '22.4'} kg/m²</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Masse Corporelle</span>
                                <span class="fifa-stat-value">${fifaData.body_mass || '75.2'} kg</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Masse Grasse</span>
                                <span class="fifa-stat-value">${fifaData.body_fat || '8.5'}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Masse Musculaire</span>
                                <span class="fifa-stat-value">${fifaData.muscle_mass || '42.3'} kg</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Hydratation</span>
                                <span class="fifa-stat-value">${fifaData.hydration || '62.8'}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Métabolisme de Base</span>
                                <span class="fifa-stat-value">${fifaData.basal_metabolism || '1850'} kcal/jour</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Tests de Performance -->
                    <div class="fifa-medical-card">
                        <h4>🏃 Tests de Performance</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Test de Cooper</span>
                                <span class="fifa-stat-value">${fifaData.cooper_test || '3200'} m</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Test de Yo-Yo</span>
                                <span class="fifa-stat-value">${fifaData.yoyo_test || 'Niveau 22'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Test de Wingate</span>
                                <span class="fifa-stat-value">${fifaData.wingate_test || '850'} W</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Test de Squat Jump</span>
                                <span class="fifa-stat-value">${fifaData.squat_jump || '45'} cm</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Test de Sprint 30m</span>
                                <span class="fifa-stat-value">${fifaData.sprint_30m || '3.85'} s</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Test de Flexibilité</span>
                                <span class="fifa-stat-value">${fifaData.flexibility_test || '12'} cm</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Analyses de Laboratoire -->
                    <div class="fifa-medical-card">
                        <h4>Laboratoire Analyses de Laboratoire</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Hémoglobine</span>
                                <span class="fifa-stat-value">${fifaData.hemoglobin || '15.2'} g/dL</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Hématocrite</span>
                                <span class="fifa-stat-value">${fifaData.hematocrit || '45.8'}%</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Fer Sérique</span>
                                <span class="fifa-stat-value">${fifaData.serum_iron || '85'} µg/dL</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Vitamine D</span>
                                <span class="fifa-stat-value">${fifaData.vitamin_d || '32'} ng/mL</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>CRP (Inflammation)</span>
                                <span class="fifa-stat-value">${fifaData.crp || '0.8'} mg/L</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dernière Analyse</span>
                                <span class="fifa-stat-value">12 Jan 2024</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Gestion des Blessures -->
                    <div class="fifa-medical-card">
                        <h4>🩹 Gestion des Blessures</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Blessures Actives</span>
                                <span class="fifa-stat-value">${fifaData.active_injuries || '0'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dernière Blessure</span>
                                <span class="fifa-stat-value">${fifaData.last_injury || 'Aucune'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Jours Sans Blessure</span>
                                <span class="fifa-stat-value">${fifaData.days_injury_free || '180'} jours</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Rééducation en Cours</span>
                                <span class="fifa-stat-value">${fifaData.rehabilitation || 'Non'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Kinésithérapeute</span>
                                <span class="fifa-stat-value">${fifaData.physiotherapist || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Plan de Prévention -->
                    <div class="fifa-medical-card">
                        <h4>🛡️ Plan de Prévention</h4>
                        <div class="fifa-medical-stat">
                            <div class="fifa-stat-header">
                                <span>Programme de Renforcement</span>
                                <span class="fifa-stat-value">${fifaData.strengthening_program || 'Actif'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Étirements Préventifs</span>
                                <span class="fifa-stat-value">${fifaData.preventive_stretching || 'Quotidien'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Récupération Active</span>
                                <span class="fifa-stat-value">${fifaData.active_recovery || 'Programmée'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Nutrition Sportive</span>
                                <span class="fifa-stat-value">${fifaData.sports_nutrition || 'Personnalisée'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Suivi Psychologique</span>
                                <span class="fifa-stat-value">${fifaData.psychological_monitoring || 'Mensuel'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Alertes et Recommandations -->
                    <div class="fifa-medical-card">
                        <h4>Attention Alertes et Recommandations</h4>
                        <div class="fifa-medical-alerts">
                            <div class="fifa-alert-item low-risk">
                                <span class="fifa-alert-icon">🟡</span>
                                <span class="fifa-alert-text">Vitamine D légèrement basse - Supplémentation recommandée</span>
                                <span class="fifa-alert-priority">Faible Risque</span>
                            </div>
                            <div class="fifa-alert-item low-risk">
                                <span class="fifa-alert-icon">🟡</span>
                                <span class="fifa-alert-text">Hydratation optimale - Maintenir 2.5L/jour minimum</span>
                                <span class="fifa-alert-priority">Faible Risque</span>
                            </div>
                            <div class="fifa-alert-item low-risk">
                                <span class="fifa-alert-icon">🟡</span>
                                <span class="fifa-alert-text">Récupération post-match - Étirements supplémentaires conseillés</span>
                                <span class="fifa-alert-priority">Faible Risque</span>
                            </div>
                        </div>
                        
                        <div class="fifa-recommendations">
                            <h5>💡 Recommandations du Médecin</h5>
                            <div class="fifa-recommendation-item">
                                <span class="fifa-recommendation-icon">Médicament</span>
                                <span class="fifa-recommendation-text">Continuer la supplémentation en vitamine D3 (1000 UI/jour)</span>
                            </div>
                            <div class="fifa-recommendation-item">
                                <span class="fifa-recommendation-icon">💧</span>
                                <span class="fifa-recommendation-text">Augmenter l'hydratation à 3L/jour pendant l'entraînement</span>
                            </div>
                            <div class="fifa-recommendation-item">
                                <span class="fifa-recommendation-icon">🏃</span>
                                <span class="fifa-recommendation-text">Maintenir le programme de renforcement musculaire 3x/semaine</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Timeline Médicale -->
                    <div class="fifa-medical-card">
                        <h4>📅 Timeline Médicale</h4>
                        <div class="fifa-medical-timeline">
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">15 Jan 2024</span>
                                <span class="fifa-timeline-event">SUCCES: Consultation médicale annuelle</span>
                                <span class="fifa-timeline-status">Complétée</span>
                            </div>
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">12 Jan 2024</span>
                                <span class="fifa-timeline-event">Laboratoire Analyses de laboratoire</span>
                                <span class="fifa-timeline-status">Résultats OK</span>
                            </div>
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">10 Jan 2024</span>
                                <span class="fifa-timeline-event">🏃 Tests de performance</span>
                                <span class="fifa-timeline-status">Amélioration +5%</span>
                            </div>
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">05 Jan 2024</span>
                                <span class="fifa-timeline-event">Vaccin Vaccination grippe saisonnière</span>
                                <span class="fifa-timeline-status">Effectuée</span>
                            </div>
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">28 Dec 2023</span>
                                <span class="fifa-timeline-event">🩹 Contrôle post-blessure cheville</span>
                                <span class="fifa-timeline-status">Guérison complète</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graphiques Médicaux -->
                <div class="fifa-charts-row">
                    <div class="fifa-chart-container">
                        <h4>Statistiques Évolution des Paramètres Vitaux</h4>
                        <canvas id="fifa-medical-vitals-chart" width="400" height="200"></canvas>
                    </div>
                    
                    <div class="fifa-chart-container">
                        <h4>Croissance Progression des Tests de Performance</h4>
                        <canvas id="fifa-medical-performance-chart" width="400" height="200"></canvas>
                    </div>
                </div>
            `;
            
            document.getElementById('medical-content').innerHTML = content;
            
            // Créer les graphiques médicaux
            createFIFAMedicalVitalsChart();
            createFIFAMedicalPerformanceChart();
        }
        
        // Charger le contenu de l'onglet Devices
        function loadFIFADevicesContent() {
            if (!fifaData) return;
            
            const content = `
                <div class="fifa-devices-grid">
                    <!-- Carte Smartphone -->
                    <div class="fifa-device-card">
                        <h4>Mobile Smartphone</h4>
                        <div class="fifa-device-info">
                            <div class="fifa-device-header">
                                <span class="fifa-device-icon">Mobile</span>
                                <span class="fifa-device-name">iPhone 15 Pro</span>
                                <span class="fifa-device-status online">🟢 En Ligne</span>
                            </div>
                            <div class="fifa-device-details">
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie</span>
                                    <span class="fifa-device-value">${fifaData.phone_battery || '85'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.phone_battery || 85}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Stockage</span>
                                    <span class="fifa-device-value">${fifaData.phone_storage || '128'} GB</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.phone_storage_used || 65}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Connectivité</span>
                                    <span class="fifa-device-value">${fifaData.phone_connectivity || '5G'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Dernière Mise à Jour</span>
                                    <span class="fifa-device-value">${fifaData.phone_last_update || 'iOS 17.2'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Applications FIFA</span>
                                    <span class="fifa-device-value">${fifaData.fifa_apps_count || '3'} installées</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Temps d'Écran</span>
                                    <span class="fifa-device-value">${fifaData.phone_screen_time || '4h 32m'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Smartwatch -->
                    <div class="fifa-device-card">
                        <h4>Montre Smartwatch</h4>
                        <div class="fifa-device-info">
                            <div class="fifa-device-header">
                                <span class="fifa-device-icon">Montre</span>
                                <span class="fifa-device-name">Apple Watch Series 9</span>
                                <span class="fifa-device-status online">🟢 Connectée</span>
                            </div>
                            <div class="fifa-device-details">
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie</span>
                                    <span class="fifa-device-value">${fifaData.watch_battery || '72'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.watch_battery || 72}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Fréquence Cardiaque</span>
                                    <span class="fifa-device-value">${fifaData.watch_heart_rate || '68'} bpm</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Pas Aujourd'hui</span>
                                    <span class="fifa-device-value">${fifaData.watch_steps || '8,547'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Calories Brûlées</span>
                                    <span class="fifa-device-value">${fifaData.watch_calories || '412'} kcal</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Sommeil</span>
                                    <span class="fifa-device-value">${fifaData.watch_sleep || '7h 23m'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Exercices</span>
                                    <span class="fifa-device-value">${fifaData.watch_exercises || '3'} séances</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Notifications</span>
                                    <span class="fifa-device-value">${fifaData.watch_notifications || '12'} aujourd'hui</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Écouteurs -->
                    <div class="fifa-device-card">
                        <h4>🎧 Écouteurs</h4>
                        <div class="fifa-device-info">
                            <div class="fifa-device-header">
                                <span class="fifa-device-icon">🎧</span>
                                <span class="fifa-device-name">AirPods Pro 2</span>
                                <span class="fifa-device-status online">🟢 Actifs</span>
                            </div>
                            <div class="fifa-device-details">
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie Gauche</span>
                                    <span class="fifa-device-value">${fifaData.earbuds_left_battery || '60'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.earbuds_left_battery || 60}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie Droite</span>
                                    <span class="fifa-device-value">${fifaData.earbuds_right_battery || '58'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.earbuds_right_battery || 58}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie Boîtier</span>
                                    <span class="fifa-device-value">${fifaData.earbuds_case_battery || '45'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.earbuds_case_battery || 45}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Qualité Audio</span>
                                    <span class="fifa-device-value">${fifaData.earbuds_audio_quality || 'Lossless'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Mode Bruit</span>
                                    <span class="fifa-device-value">${fifaData.earbuds_noise_mode || 'Transparence'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Temps d'Écoute</span>
                                    <span class="fifa-device-value">${fifaData.earbuds_listening_time || '2h 15m'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Tablette -->
                    <div class="fifa-device-card">
                        <h4>Mobile Tablette</h4>
                        <div class="fifa-device-info">
                            <div class="fifa-device-header">
                                <span class="fifa-device-icon">Mobile</span>
                                <span class="fifa-device-name">iPad Pro 12.9"</span>
                                <span class="fifa-device-status online">🟢 En Ligne</span>
                            </div>
                            <div class="fifa-device-details">
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie</span>
                                    <span class="fifa-device-value">${fifaData.tablet_battery || '92'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.tablet_battery || 92}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Stockage</span>
                                    <span class="fifa-device-value">${fifaData.tablet_storage || '256'} GB</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.tablet_storage_used || 35}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Connectivité</span>
                                    <span class="fifa-device-value">${fifaData.tablet_connectivity || 'WiFi + 5G'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Applications FIFA</span>
                                    <span class="fifa-device-value">${fifaData.tablet_fifa_apps || '2'} installées</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Stylet</span>
                                    <span class="fifa-device-value">${fifaData.tablet_stylus || 'Apple Pencil 2'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Ordinateur Portable -->
                    <div class="fifa-device-card">
                        <h4>💻 Ordinateur Portable</h4>
                        <div class="fifa-device-info">
                            <div class="fifa-device-header">
                                <span class="fifa-device-icon">💻</span>
                                <span class="fifa-device-name">MacBook Pro 16"</span>
                                <span class="fifa-device-status online">🟢 En Ligne</span>
                            </div>
                            <div class="fifa-device-details">
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie</span>
                                    <span class="fifa-device-value">${fifaData.laptop_battery || '78'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.laptop_battery || 78}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">CPU</span>
                                    <span class="fifa-device-value">${fifaData.laptop_cpu || 'M2 Pro'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">RAM</span>
                                    <span class="fifa-device-value">${fifaData.laptop_ram || '32'} GB</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Stockage</span>
                                    <span class="fifa-device-value">${fifaData.laptop_storage || '1'} TB</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Logiciels FIFA</span>
                                    <span class="fifa-device-value">${fifaData.laptop_fifa_software || '3'} installés</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Temps d'Utilisation</span>
                                    <span class="fifa-device-value">${fifaData.laptop_usage_time || '6h 45m'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte État Général des Devices -->
                    <div class="fifa-device-card">
                        <h4>🔋 État Général des Devices</h4>
                        <div class="fifa-devices-overview">
                            <div class="fifa-device-overview-stat">
                                <span class="fifa-device-overview-label">Devices Actifs</span>
                                <span class="fifa-device-overview-value">${fifaData.active_devices_count || '6'}/6</span>
                            </div>
                            <div class="fifa-device-overview-stat">
                                <span class="fifa-device-overview-label">Batterie Moyenne</span>
                                <span class="fifa-device-overview-value">${fifaData.average_battery || '72'}%</span>
                            </div>
                            <div class="fifa-device-overview-stat">
                                <span class="fifa-device-overview-label">Mises à Jour</span>
                                <span class="fifa-device-overview-value">${fifaData.pending_updates || '2'} en attente</span>
                            </div>
                            <div class="fifa-device-overview-stat">
                                <span class="fifa-device-overview-label">Sécurité</span>
                                <span class="fifa-device-overview-value">${fifaData.security_status || '🟢 Sécurisé'}</span>
                            </div>
                            <div class="fifa-device-overview-stat">
                                <span class="fifa-device-overview-label">Applications FIFA</span>
                                <span class="fifa-device-overview-value">${fifaData.total_fifa_apps || '8'} installées</span>
                            </div>
                            <div class="fifa-device-overview-stat">
                                <span class="fifa-device-overview-label">Synchronisation</span>
                                <span class="fifa-device-overview-value">${fifaData.sync_status || '🟢 Synchronisé'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Garmin Fenix 7 Pro -->
                    <div class="fifa-device-card">
                        <h4>Montre Garmin Fenix 7 Pro</h4>
                        <div class="fifa-device-info">
                            <div class="fifa-device-header">
                                <span class="fifa-device-icon">Montre</span>
                                <span class="fifa-device-name">Montre GPS Pro</span>
                                <span class="fifa-device-status online">🟢 Connectée</span>
                            </div>
                            <div class="fifa-device-details">
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie</span>
                                    <span class="fifa-device-value">${fifaData.garmin_battery || '78'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.garmin_battery || 78}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Fréquence Cardiaque</span>
                                    <span class="fifa-device-value">${fifaData.garmin_heart_rate || '72'} bpm</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">VO2 Max</span>
                                    <span class="fifa-device-value">${fifaData.garmin_vo2max || '58'} ml/kg/min</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Charge d'Entraînement</span>
                                    <span class="fifa-device-value">${fifaData.garmin_training_load || 'Modérée'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Récupération</span>
                                    <span class="fifa-device-value">${fifaData.garmin_recovery || '48'} heures</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Stress</span>
                                    <span class="fifa-device-value">${fifaData.garmin_stress || 'Faible'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Sommeil</span>
                                    <span class="fifa-device-value">${fifaData.garmin_sleep || '7h 45m'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Catapult Vector S7 -->
                    <div class="fifa-device-card">
                        <h4>Antenne Catapult Vector S7</h4>
                        <div class="fifa-device-info">
                            <div class="fifa-device-header">
                                <span class="fifa-device-icon">Antenne</span>
                                <span class="fifa-device-name">GPS Pro + Accéléromètre</span>
                                <span class="fifa-device-status online">🟢 Actif</span>
                            </div>
                            <div class="fifa-device-details">
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Batterie</span>
                                    <span class="fifa-device-value">${fifaData.catapult_battery || '92'}%</span>
                                </div>
                                <div class="fifa-progress-bar">
                                    <div class="fifa-progress-fill" style="width: ${fifaData.catapult_battery || 92}%"></div>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Distance Parcourue</span>
                                    <span class="fifa-device-value">${fifaData.catapult_distance || '8.7'} km</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Vitesse Max</span>
                                    <span class="fifa-device-value">${fifaData.catapult_max_speed || '32.5'} km/h</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Accélérations</span>
                                    <span class="fifa-device-value">${fifaData.catapult_accelerations || '127'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Décélérations</span>
                                    <span class="fifa-device-value">${fifaData.catapult_decelerations || '89'}</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Sprint Distance</span>
                                    <span class="fifa-device-value">${fifaData.catapult_sprint_distance || '1.2'} km</span>
                                </div>
                                
                                <div class="fifa-device-stat">
                                    <span class="fifa-device-label">Charge Mécanique</span>
                                    <span class="fifa-device-value">${fifaData.catapult_mechanical_load || 'Modérée'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    
                    <!-- Carte Gestion des Mises à Jour -->
                    <div class="fifa-device-card">
                        <h4>Synchronisation Gestion des Mises à Jour</h4>
                        <div class="fifa-updates-management">
                            <div class="fifa-update-item critical">
                                <span class="fifa-update-icon">🔴</span>
                                <span class="fifa-update-device">iPhone 15 Pro</span>
                                <span class="fifa-update-type">iOS 17.3</span>
                                <span class="fifa-update-status">Critique</span>
                            </div>
                            <div class="fifa-update-item important">
                                <span class="fifa-update-icon">🟡</span>
                                <span class="fifa-update-device">Apple Watch</span>
                                <span class="fifa-update-type">watchOS 10.3</span>
                                <span class="fifa-update-status">Important</span>
                            </div>
                            <div class="fifa-update-item normal">
                                <span class="fifa-update-icon">🟢</span>
                                <span class="fifa-update-device">MacBook Pro</span>
                                <span class="fifa-update-type">macOS 14.2</span>
                                <span class="fifa-update-status">Normal</span>
                            </div>
                        </div>
                        
                        <div class="fifa-update-actions">
                            <button class="fifa-update-btn critical">Mettre à jour iOS</button>
                            <button class="fifa-update-btn important">Mettre à jour watchOS</button>
                            <button class="fifa-update-btn normal">Mettre à jour macOS</button>
                        </div>
                    </div>
                    
                    <!-- Carte Sécurité et Maintenance -->
                    <div class="fifa-device-card">
                        <h4>🔒 Sécurité et Maintenance</h4>
                        <div class="fifa-security-overview">
                            <div class="fifa-security-item">
                                <span class="fifa-security-icon">🔐</span>
                                <span class="fifa-security-label">Authentification</span>
                                <span class="fifa-security-status">${fifaData.auth_status || '🟢 2FA Activé'}</span>
                            </div>
                            <div class="fifa-security-item">
                                <span class="fifa-security-icon">🛡️</span>
                                <span class="fifa-security-label">Antivirus</span>
                                <span class="fifa-security-status">${fifaData.antivirus_status || '🟢 À jour'}</span>
                            </div>
                            <div class="fifa-security-item">
                                <span class="fifa-security-icon">🔒</span>
                                <span class="fifa-security-label">Chiffrement</span>
                                <span class="fifa-security-status">${fifaData.encryption_status || '🟢 Activé'}</span>
                            </div>
                            <div class="fifa-security-item">
                                <span class="fifa-security-icon">Mobile</span>
                                <span class="fifa-security-label">Localisation</span>
                                <span class="fifa-security-status">${fifaData.location_status || '🟢 Activée'}</span>
                            </div>
                        </div>
                        
                        <div class="fifa-maintenance-schedule">
                            <h5>📅 Plan de Maintenance</h5>
                            <div class="fifa-maintenance-item">
                                <span class="fifa-maintenance-date">15 Jan 2024</span>
                                <span class="fifa-maintenance-task">Nettoyage système iPhone</span>
                            </div>
                            <div class="fifa-maintenance-item">
                                <span class="fifa-maintenance-date">20 Jan 2024</span>
                                <span class="fifa-maintenance-task">Optimisation MacBook</span>
                            </div>
                            <div class="fifa-maintenance-item">
                                <span class="fifa-maintenance-date">25 Jan 2024</span>
                                <span class="fifa-maintenance-task">Sauvegarde complète</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graphiques État des Devices -->
                <div class="fifa-charts-row">
                    <div class="fifa-chart-container">
                        <h4>Statistiques État des Batteries des Devices</h4>
                        <canvas id="fifa-devices-chart" width="400" height="200"></canvas>
                    </div>
                    
                    <div class="fifa-chart-container">
                        <h4>Croissance Utilisation des Applications FIFA</h4>
                        <canvas id="fifa-apps-usage-chart" width="400" height="200"></canvas>
                    </div>
                </div>
            `;
            
            document.getElementById('devices-content').innerHTML = content;
            
            // Créer les graphiques des devices
            createFIFADevicesChart();
            createFIFADevicesUsageChart();
        }
        
        // Charger le contenu de l'onglet Dopage
        function loadFIFADopingContent() {
            if (!fifaData) return;
            
            const content = `
                <div class="fifa-doping-grid">
                    <!-- Carte Tests de Dopage -->
                    <div class="fifa-doping-card">
                        <h4>Test Tests de Dopage</h4>
                        <div class="fifa-doping-stat">
                            <div class="fifa-stat-header">
                                <span>Total Tests Effectués</span>
                                <span class="fifa-stat-value highlight">${fifaData.doping_tests_count || '12'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dernier Test</span>
                                <span class="fifa-stat-value">${fifaData.last_doping_test || '15/01/2025'}</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Type Dernier Test</span>
                                <span class="fifa-stat-value">Sang + Urine</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Résultat</span>
                                <span class="fifa-stat-value positive">SUCCES: NÉGATIF</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Médicaments en Cours -->
                    <div class="fifa-doping-card">
                        <h4>Médicament Médicaments en Cours</h4>
                        <div class="fifa-doping-stat">
                            <div class="fifa-stat-header">
                                <span>Statut</span>
                                <span class="fifa-stat-value positive">🟢 En Cours</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Vitamine D3</span>
                                <span class="fifa-stat-value">1000 UI/jour</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Oméga-3</span>
                                <span class="fifa-stat-value">2g/jour</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Magnésium</span>
                                <span class="fifa-stat-value">400mg/jour</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Probiotiques</span>
                                <span class="fifa-stat-value">1 capsule/jour</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Dernière Prise</span>
                                <span class="fifa-stat-value">Aujourd'hui 08:00</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Prochaine Prise</span>
                                <span class="fifa-stat-value">Demain 08:00</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte ATU en Cours -->
                    <div class="fifa-doping-card">
                        <h4>🏥 ATU en Cours</h4>
                        <div class="fifa-doping-stat">
                            <div class="fifa-stat-header">
                                <span>Statut</span>
                                <span class="fifa-stat-value positive">🟢 Actives</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>ATU 001</span>
                                <span class="fifa-stat-value">Vitamine B12 injectable</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Date Début</span>
                                <span class="fifa-stat-value">15 Jan 2024</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Date Fin</span>
                                <span class="fifa-stat-value">15 Mar 2024</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Médecin Prescripteur</span>
                                <span class="fifa-stat-value">Dr. Martinez</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Statut</span>
                                <span class="fifa-stat-value positive">SUCCES: Validée</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Prochaine Injection</span>
                                <span class="fifa-stat-value">22 Jan 2024</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Résultats des Tests -->
                    <div class="fifa-doping-card">
                        <h4>📋 Résultats des Tests</h4>
                        <div class="fifa-doping-results">
                            <div class="fifa-result-item positive">
                                <span class="fifa-result-icon">SUCCES:</span>
                                <span class="fifa-result-label">Tests Négatifs</span>
                                <span class="fifa-result-value">${fifaData.negative_tests || '12'}</span>
                            </div>
                            <div class="fifa-result-item negative">
                                <span class="fifa-result-icon">ERREUR:</span>
                                <span class="fifa-result-label">Tests Positifs</span>
                                <span class="fifa-result-value">${fifaData.positive_tests || '0'}</span>
                            </div>
                            <div class="fifa-result-item neutral">
                                <span class="fifa-result-icon">⏳</span>
                                <span class="fifa-result-label">En Attente</span>
                                <span class="fifa-result-value">${fifaData.pending_tests || '0'}</span>
                            </div>
                        </div>
                        
                        <div class="fifa-doping-percentage">
                            <span class="fifa-percentage-label">Taux de Conformité</span>
                            <span class="fifa-percentage-value">100%</span>
                        </div>
                    </div>
                    
                    <!-- Carte Prochain Test -->
                    <div class="fifa-doping-card">
                        <h4>📅 Prochain Test</h4>
                        <div class="fifa-doping-stat">
                            <div class="fifa-stat-header">
                                <span>Date Prévue</span>
                                <span class="fifa-stat-value highlight">15/02/2025</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Type de Test</span>
                                <span class="fifa-stat-value">Sang + Urine</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Jours Restants</span>
                                <span class="fifa-stat-value">32 jours</span>
                            </div>
                            <div class="fifa-stat-header">
                                <span>Statut</span>
                                <span class="fifa-stat-value positive">Programmé</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Historique Détaillé -->
                    <div class="fifa-doping-card">
                        <h4>Statistiques Historique Détaillé</h4>
                        <div class="fifa-doping-timeline">
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">15 Jan 2025</span>
                                <span class="fifa-timeline-result positive">SUCCES: Négatif</span>
                                <span class="fifa-timeline-type">Sang + Urine</span>
                            </div>
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">28 Nov 2024</span>
                                <span class="fifa-timeline-result positive">SUCCES: Négatif</span>
                                <span class="fifa-timeline-type">Sang</span>
                            </div>
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">12 Oct 2024</span>
                                <span class="fifa-timeline-result positive">SUCCES: Négatif</span>
                                <span class="fifa-timeline-type">Sang + Urine</span>
                            </div>
                            <div class="fifa-timeline-item">
                                <span class="fifa-timeline-date">25 Sep 2024</span>
                                <span class="fifa-timeline-result positive">SUCCES: Négatif</span>
                                <span class="fifa-timeline-type">Urine</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Substances Testées -->
                    <div class="fifa-doping-card">
                        <h4>Laboratoire Substances Testées</h4>
                        <div class="fifa-substances-tested">
                            <div class="fifa-substance-category">
                                <h5>Stimulants</h5>
                                <div class="fifa-substance-list">
                                    <span class="fifa-substance">Amphétamines</span>
                                    <span class="fifa-substance">Cocaïne</span>
                                    <span class="fifa-substance">Éphédrine</span>
                                </div>
                            </div>
                            <div class="fifa-substance-category">
                                <h5>Stéroïdes</h5>
                                <div class="fifa-substance-list">
                                    <span class="fifa-substance">Testostérone</span>
                                    <span class="fifa-substance">Nandrolone</span>
                                    <span class="fifa-substance">Stanozolol</span>
                                </div>
                            </div>
                            <div class="fifa-substance-category">
                                <h5>Diurétiques</h5>
                                <div class="fifa-substance-list">
                                    <span class="fifa-substance">Furosémide</span>
                                    <span class="fifa-substance">Hydrochlorothiazide</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Carte Conformité et Certifications -->
                    <div class="fifa-doping-card">
                        <h4>🏆 Conformité et Certifications</h4>
                        <div class="fifa-compliance-status">
                            <div class="fifa-compliance-item">
                                <span class="fifa-compliance-icon">SUCCES:</span>
                                <span class="fifa-compliance-text">Conformité WADA</span>
                                <span class="fifa-compliance-status">Validé</span>
                            </div>
                            <div class="fifa-compliance-item">
                                <span class="fifa-compliance-icon">SUCCES:</span>
                                <span class="fifa-compliance-text">Certification FIFA</span>
                                <span class="fifa-compliance-status">Validé</span>
                            </div>
                            <div class="fifa-compliance-item">
                                <span class="fifa-compliance-icon">SUCCES:</span>
                                <span class="fifa-compliance-text">Programme Anti-Dopage</span>
                                <span class="fifa-compliance-status">Actif</span>
                            </div>
                            <div class="fifa-compliance-item">
                                <span class="fifa-compliance-icon">SUCCES:</span>
                                <span class="fifa-compliance-text">Formation Anti-Dopage</span>
                                <span class="fifa-compliance-status">Complétée</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Graphiques Évolution des Tests -->
                <div class="fifa-charts-row">
                    <div class="fifa-chart-container">
                        <h4>Statistiques Évolution des Tests de Dopage</h4>
                        <canvas id="fifa-doping-chart" width="400" height="200"></canvas>
                    </div>
                    
                    <div class="fifa-chart-container">
                        <h4>Croissance Répartition des Types de Tests</h4>
                        <canvas id="fifa-doping-types-chart" width="400" height="200"></canvas>
                    </div>
                </div>
            `;
            
            document.getElementById('doping-content').innerHTML = content;
            
            // Créer les graphiques de dopage
            createFIFADopingChart();
            createFIFADopingTypesChart();
        }

        // Charger le contenu de l'onglet Historique des Licences
        async function loadFIFALicensesContent() {
            console.log('🚀 DEBUT: loadFIFALicensesContent démarré');
            
            // Vérifier le verrou pour éviter les appels multiples
            if (isLoadingLicenses) {
                console.log('INFO: Chargement des licences déjà en cours, ignoré');
                return;
            }
            
            isLoadingLicenses = true;
            console.log('🔒 VERROU: Verrou activé');
            
            try {
                console.log('INFO: Chargement du contenu des licences...');
                
                // Attendre que l'onglet soit complètement visible
                let tabSearchAttempts = 0;
                let licensesTab = null;
                
                console.log('🔍 RECHERCHE: Début de la recherche de l\'onglet des licences');
                
                while (!licensesTab && tabSearchAttempts < 10) {
                    licensesTab = document.getElementById('licenses-tab');
                    console.log(`🔍 TENTATIVE ${tabSearchAttempts + 1}/10:`, licensesTab ? 'Onglet trouvé' : 'Onglet non trouvé');
                    
                    if (!licensesTab || !licensesTab.classList.contains('active')) {
                        console.log(`INFO: Tentative ${tabSearchAttempts + 1}/10 - Onglet des licences pas encore visible`);
                        await new Promise(resolve => setTimeout(resolve, 200));
                        tabSearchAttempts++;
                    } else {
                        console.log('✅ SUCCES: Onglet trouvé et actif');
                        break;
                    }
                }
                
                if (!licensesTab || !licensesTab.classList.contains('active')) {
                    console.error('ERREUR: Onglet des licences pas visible après 10 tentatives');
                    isLoadingLicenses = false;
                    return;
                }
                
                // FORCER l'activation COMPLÈTE de l'onglet des licences
                console.log('🔧 ACTIVATION: Activation complète de l\'onglet des licences...');
                
                // 1. Désactiver tous les onglets
                document.querySelectorAll('.fifa-tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // 2. Désactiver tous les boutons
                document.querySelectorAll('.fifa-tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // 3. Activer l'onglet des licences
                licensesTab.classList.add('active');
                
                // 4. Activer le bouton des licences
                const licensesButton = document.querySelector('.fifa-tab-button[onclick*="licenses"]');
                if (licensesButton) {
                    licensesButton.classList.add('active');
                }
                
                // 5. Mettre à jour la variable globale
                currentFIFATab = 'licenses';
                
                console.log('✅ SUCCES: Onglet des licences complètement activé !');
                
                // 6. Attendre que le DOM soit mis à jour
                console.log('⏳ ATTENTE: Attente de la mise à jour du DOM...');
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // 7. DÉCLENCHER loadFIFATabContent pour que les éléments soient rendus
                console.log('🔧 RENDU: Déclenchement de loadFIFATabContent pour le rendu des éléments...');
                if (typeof loadFIFATabContent === 'function') {
                    loadFIFATabContent('licenses');
                    console.log('✅ SUCCES: loadFIFATabContent déclenché !');
                } else {
                    console.error('ERREUR: Fonction loadFIFATabContent non trouvée !');
                }
                
                // 8. Attendre que loadFIFATabContent termine
                console.log('⏳ ATTENTE: Attente de la fin de loadFIFATabContent...');
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                // 9. SIMPLE ACTIVATION - PAS DE CLONAGE DESTRUCTEUR
                console.log('🔧 ACTIVATION: Activation simple de l\'onglet des licences...');
                
                // Attendre que le DOM soit stable
                console.log('⏳ ATTENTE: Attente de la stabilisation du DOM...');
                await new Promise(resolve => setTimeout(resolve, 200));
                
                // Vérifier les éléments sans clonage
                let tbody = document.querySelector('.licenses-table-body');
                let training = document.querySelector('.training-compensation-content');
                
                console.log('🔍 VÉRIFICATION: Éléments après activation - tbody:', !!tbody, 'training:', !!training);
                
                if (!tbody || !training) {
                    console.error('ERREUR: Éléments manquants après activation !');
                    isLoadingLicenses = false;
                    return;
                }
                
                console.log('SUCCES: Onglet des licences activé et éléments prêts !');
                
                // CONSERVER LES RÉFÉRENCES DES ÉLÉMENTS TROUVÉS
                const tbodyElement = tbody;
                const trainingElement = training;
                
                // Vérifier que l'élément existe
                const licensesContent = document.getElementById('licenses-content');
                console.log('🔍 LICENSES-CONTENT:', licensesContent ? 'Trouvé' : 'Non trouvé');
                
                if (!licensesContent) {
                    console.error('ERREUR: Élément licenses-content non trouvé dans le DOM');
                    console.log('INFO: Éléments disponibles:', document.querySelectorAll('[id*="licenses"]'));
                    isLoadingLicenses = false;
                    return;
                }
                
                // Récupérer l'ID du joueur depuis l'URL
                const urlParams = new URLSearchParams(window.location.search);
                const playerId = urlParams.get('player_id') || '7';
                console.log('🎯 PLAYER-ID:', playerId);
                
                // Afficher le loading
                licensesContent.innerHTML = '<div class="fifa-loading">Chargement de l\'historique des licences...</div>';
                console.log('📱 LOADING: Message de chargement affiché');
                
                // Appel à l'API des licences
                console.log('🌐 API: Début de l\'appel API...');
                const response = await fetch(`/api/joueur/${playerId}/historique-licences`);
                console.log('🌐 API: Réponse reçue, status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('🌐 API: Données JSON parsées');
                
                if (!data.success) {
                    throw new Error(data.message || 'Erreur lors du chargement des licences');
                }
                
                console.log('SUCCES: Données des licences récupérées:', data);
                
                // Vérifier à nouveau que l'élément existe avant de continuer
                if (!licensesContent || !licensesContent.isConnected) {
                    console.error('ERREUR: Élément licenses-content perdu pendant le chargement');
                    return;
                }
                
                // UTILISER DIRECTEMENT LES ÉLÉMENTS CONSERVÉS
                console.log('✅ SUCCES: Utilisation des éléments conservés !');
                
                // Afficher le tableau des licences
                console.log('📊 TABLEAU: Appel de displayLicenseTable...');
                displayLicenseTable(data.data.licences, tbodyElement);
                
                // Afficher les primes de formation
                console.log('💰 PRIMES: Appel de displayTrainingCompensation...');
                displayTrainingCompensation(data.data.primes_formation, trainingElement);
                
                console.log('SUCCES: Contenu des licences affiché avec succès');
                
            } catch (error) {
                console.error('ERREUR: Erreur lors du chargement des licences:', error);
                const licensesContent = document.getElementById('licenses-content');
                if (licensesContent) {
                    licensesContent.innerHTML = `
                        <div class="fifa-error">
                            Erreur lors du chargement de l'historique des licences: ${error.message}
                        </div>
                    `;
                }
            } finally {
                // Toujours libérer le verrou
                isLoadingLicenses = false;
                console.log('🔓 VERROU: Verrou libéré');
            }
        }

                // Afficher le tableau des licences
        function displayLicenseTable(licences, tbodyElement) {
            const licensesContent = document.getElementById('licenses-content');
            if (!licensesContent) return;
            
            // Si pas de licences, afficher un message explicite
            if (!licences || licences.length === 0) {
                licensesContent.innerHTML = `
                    <div class="licenses-container space-y-6">
                        <!-- Message d'absence de licences -->
                        <div class="no-licenses-section bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                            <div class="flex flex-col items-center space-y-4">
                                <div class="text-gray-400">
                                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <h3 class="text-xl font-semibold text-gray-700">Aucune Licence Trouvée</h3>
                                    <p class="text-gray-500 max-w-md mx-auto">
                                        Aucune information de licence n'est disponible pour ce joueur dans notre base de données.
                                    </p>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md">
                                    <h4 class="text-sm font-medium text-blue-800 mb-2">ℹ️ Information</h4>
                                    <p class="text-xs text-blue-700">
                                        Les licences peuvent provenir de différentes sources :<br>
                                        • Base de données locale<br>
                                        • API FIFA<br>
                                        • Fédérations nationales<br>
                                        • Clubs de formation
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section des primes de formation -->
                        <div class="training-compensation-content">
                            <h4 class="text-lg font-semibold mb-4 text-gray-800">Primes de Formation FIFA</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600">Aucune prime calculée pour le moment</p>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }
            
            // Créer le contenu complet des licences
            licensesContent.innerHTML = `
                <div class="licenses-container space-y-6">
                    <!-- Club Formateur Principal -->
                    <div class="club-formateur-section bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-3 text-blue-800">🏆 Club Formateur Principal</h3>
                        <div class="flex items-center space-x-4">
                            <div class="club-info">
                                <p class="text-sm font-medium text-blue-900">${licences[0].club || 'Club inconnu'}</p>
                                <p class="text-xs text-blue-700">Premier club de formation</p>
                            </div>
                            <div class="formation-badge">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Club Formateur
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des licences -->
                    <div class="licenses-section">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Historique des Licences (${licences.length} licence(s) trouvée(s))</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Période</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Club</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Association</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Source</th>
                                    </tr>
                                </thead>
                                <tbody class="licenses-table-body bg-white divide-y divide-gray-200">
                                    ${licences.map((licence, index) => `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                <span class="font-medium">${licence.date_debut || 'N/A'}</span> → <span class="font-medium">${licence.date_fin || '—'}</span>
                                                ${index === 0 ? '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Licence actuelle</span>' : ''}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${licence.club || 'N/A'}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${licence.association || 'N/A'}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${licence.type_licence || 'N/A'}</span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">${licence.source_donnee || 'N/A'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="training-compensation-content">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">Primes de Formation FIFA</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600">Aucune prime calculée pour le moment</p>
                        </div>
                    </div>
                </div>
            `;
        }


        // Afficher les primes de formation
        function displayTrainingCompensation(primes, contentElement) {
            console.log('INFO: Affichage des primes de formation...');
            console.log('🔍 DEBUG: Utilisation de l\'élément passé en paramètre...');
            const content = contentElement || document.querySelector('.training-compensation-content');
            console.log('🔍 DEBUG: Élément trouvé:', !!content);
            console.log('🔍 DEBUG: Type d\'élément:', content ? content.constructor.name : 'null');
            console.log('🔍 DEBUG: HTML de l\'élément:', content ? content.outerHTML.substring(0, 100) + '...' : 'null');
            
            if (!content) {
                console.error('ERREUR: Élément training-compensation-content non trouvé (devrait être prêt)');
                return;
            }
            
            if (!primes || primes.length === 0) {
                content.innerHTML = '<div class="text-center">Aucune prime de formation calculée</div>';
                console.log('SUCCES: Primes affichées - Aucune prime');
                return;
            }
            
            let html = '';
            primes.forEach(prime => {
                html += `
                    <div class="compensation-item">
                        <div class="compensation-club">
                            ${prime.club_nom}
                            <span class="compensation-category">Cat. ${prime.categorie_club || 'II'}</span>
                        </div>
                        <div class="compensation-amount">
                            ${new Intl.NumberFormat('fr-FR', { 
                                style: 'currency', 
                                currency: 'EUR' 
                            }).format(prime.montant_eur)}
                        </div>
                    </div>
                `;
            });
            
            content.innerHTML = html;
            console.log('SUCCES: Primes de formation affichées avec', primes.length, 'primes');
        }
        
        // Créer le graphique de performance de la vue d'ensemble
        function createFIFAOverviewChart() {
            const ctx = document.getElementById('fifa-overview-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Match 1', 'Match 2', 'Match 3', 'Match 4', 'Match 5', 'Match 6'],
                    datasets: [{
                        label: 'Note Globale',
                        data: [
                            fifaData.overall_rating || 75,
                            (fifaData.overall_rating || 75) + Math.random() * 5 - 2,
                            (fifaData.overall_rating || 75) + Math.random() * 5 - 2,
                            (fifaData.overall_rating || 75) + Math.random() * 5 - 2,
                            (fifaData.overall_rating || 75) + Math.random() * 5 - 2,
                            (fifaData.overall_rating || 75) + Math.random() * 5 - 2
                        ],
                        borderColor: '#ffd700',
                        backgroundColor: 'rgba(255, 215, 0, 0.1)',
                        borderWidth: 3,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique radar avancé
        function createFIFAAdvancedRadar() {
            const ctx = document.getElementById('fifa-advanced-radar');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Attaque', 'Défense', 'Physique', 'Technique', 'Mental', 'Forme'],
                    datasets: [{
                        label: 'Compétences Avancées',
                        data: [
                            fifaData.attack_efficiency || 75,
                            fifaData.defense_efficiency || 70,
                            fifaData.endurance_score || 80,
                            fifaData.passing_accuracy || 85,
                            fifaData.composure || 75,
                            fifaData.fitness_score || 90
                        ],
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        borderColor: '#ffd700',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffd700',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#ffd700'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        r: {
                            ticks: { color: 'white' },
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
        
        // Créer le graphique de performance par match
        function createFIFAMatchPerformanceChart() {
            const ctx = document.getElementById('fifa-match-performance-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Match 1', 'Match 2', 'Match 3', 'Match 4', 'Match 5', 'Match 6'],
                    datasets: [{
                        label: 'Note Globale',
                        data: [
                            fifaData.overall_rating || 75,
                            (fifaData.overall_rating || 75) + Math.random() * 8 - 4,
                            (fifaData.overall_rating || 75) + Math.random() * 8 - 4,
                            (fifaData.overall_rating || 75) + Math.random() * 8 - 4,
                            (fifaData.overall_rating || 75) + Math.random() * 8 - 4,
                            (fifaData.overall_rating || 75) + Math.random() * 8 - 4
                        ],
                        backgroundColor: 'rgba(255, 215, 0, 0.6)',
                        borderColor: '#ffd700',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            beginAtZero: true,
                            max: 100
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique comparatif
        function createFIFAComparisonChart() {
            const ctx = document.getElementById('fifa-comparison-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Vitesse', 'Technique', 'Physique', 'Mental', 'Forme', 'Potentiel'],
                    datasets: [{
                        label: 'Joueur Actuel',
                        data: [
                            fifaData.max_speed || 75,
                            fifaData.ball_control || 80,
                            fifaData.strength || 70,
                            fifaData.composure || 75,
                            fifaData.fitness_score || 85,
                            fifaData.potential || 82
                        ],
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        borderColor: '#ffd700',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffd700'
                    }, {
                        label: 'Moyenne Position',
                        data: [
                            (fifaData.max_speed || 75) * 0.9,
                            (fifaData.ball_control || 80) * 0.9,
                            (fifaData.strength || 70) * 0.9,
                            (fifaData.composure || 75) * 0.9,
                            (fifaData.fitness_score || 85) * 0.9,
                            (fifaData.potential || 82) * 0.9
                        ],
                        backgroundColor: 'rgba(135, 206, 235, 0.2)',
                        borderColor: '#87ceeb',
                        borderWidth: 2,
                        pointBackgroundColor: '#87ceeb'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        r: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            pointLabels: { color: 'white' },
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
        
        // Créer le graphique de santé
        function createFIFAHealthChart() {
            const ctx = document.getElementById('fifa-health-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jour 1', 'Jour 5', 'Jour 10', 'Jour 15', 'Jour 20', 'Jour 25', 'Jour 30'],
                    datasets: [{
                        label: 'Score de Forme',
                        data: [
                            fifaData.fitness_score || 85,
                            (fifaData.fitness_score || 85) + Math.random() * 10 - 5,
                            (fifaData.fitness_score || 85) + Math.random() * 10 - 5,
                            (fifaData.fitness_score || 85) + Math.random() * 10 - 5,
                            (fifaData.fitness_score || 85) + Math.random() * 10 - 5,
                            (fifaData.fitness_score || 85) + Math.random() * 10 - 5,
                            (fifaData.fitness_score || 85) + Math.random() * 10 - 5
                        ],
                        borderColor: '#51cf66',
                        backgroundColor: 'rgba(81, 207, 102, 0.1)',
                        borderWidth: 3,
                        tension: 0.4
                    }, {
                        label: 'Niveau d\'Énergie',
                        data: [
                            fifaData.energy_level || 80,
                            (fifaData.energy_level || 80) + Math.random() * 15 - 7,
                            (fifaData.energy_level || 80) + Math.random() * 15 - 7,
                            (fifaData.energy_level || 80) + Math.random() * 15 - 7,
                            (fifaData.energy_level || 80) + Math.random() * 15 - 7,
                            (fifaData.energy_level || 80) + Math.random() * 15 - 7,
                            (fifaData.energy_level || 80) + Math.random() * 15 - 7
                        ],
                        borderColor: '#ffd700',
                        backgroundColor: 'rgba(255, 215, 0, 0.1)',
                        borderWidth: 3,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            beginAtZero: true,
                            max: 100
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique médical
        function createFIFAMedicalChart() {
            const ctx = document.getElementById('fifa-medical-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Tests Médicaux',
                        data: [2, 1, 3, 2, 1, 2],
                        backgroundColor: 'rgba(135, 206, 235, 0.6)',
                        borderColor: '#87ceeb',
                        borderWidth: 2
                    }, {
                        label: 'Blessures',
                        data: [0, 1, 0, 0, 1, 0],
                        backgroundColor: 'rgba(255, 107, 107, 0.6)',
                        borderColor: '#ff6b6b',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            beginAtZero: true
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique des devices
        function createFIFADevicesChart() {
            const ctx = document.getElementById('fifa-devices-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Smartphone', 'Smartwatch', 'Écouteurs', 'Tablette', 'Laptop'],
                    datasets: [{
                        data: [
                            fifaData.phone_battery || 85,
                            fifaData.watch_battery || 72,
                            Math.min((fifaData.earbuds_left_battery || 60), (fifaData.earbuds_right_battery || 58)),
                            fifaData.tablet_battery || 92,
                            fifaData.laptop_battery || 78
                        ],
                        backgroundColor: [
                            'rgba(255, 215, 0, 0.8)',
                            'rgba(135, 206, 235, 0.8)',
                            'rgba(81, 207, 102, 0.8)',
                            'rgba(255, 107, 107, 0.8)',
                            'rgba(147, 112, 219, 0.8)'
                        ],
                        borderColor: [
                            '#ffd700',
                            '#87ceeb',
                            '#51cf66',
                            '#ff6b6b',
                            '#9370db'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique de dopage
        function createFIFADopingChart() {
            const ctx = document.getElementById('fifa-doping-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['2020', '2021', '2022', '2023', '2024'],
                    datasets: [{
                        label: 'Tests Effectués',
                        data: [8, 12, 10, 15, 18],
                        borderColor: '#87ceeb',
                        backgroundColor: 'rgba(135, 206, 235, 0.1)',
                        borderWidth: 3,
                        tension: 0.4
                    }, {
                        label: 'Tests Négatifs',
                        data: [8, 12, 10, 15, 18],
                        borderColor: '#51cf66',
                        backgroundColor: 'rgba(81, 207, 102, 0.1)',
                        borderWidth: 3,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            beginAtZero: true
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique des types de tests de dopage
        function createFIFADopingTypesChart() {
            const ctx = document.getElementById('fifa-doping-types-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sang + Urine', 'Sang Seul', 'Urine Seul', 'Tests Surprise'],
                    datasets: [{
                        data: [12, 8, 6, 4],
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
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique d'utilisation des applications FIFA
        function createFIFADevicesUsageChart() {
            const ctx = document.getElementById('fifa-apps-usage-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['FIFA Mobile', 'FIFA Connect', 'FIFA Training', 'FIFA Health', 'FIFA Stats'],
                    datasets: [{
                        label: 'Temps d\'Utilisation (heures)',
                        data: [2.5, 1.8, 1.2, 0.8, 0.5],
                        backgroundColor: [
                            'rgba(255, 215, 0, 0.6)',
                            'rgba(135, 206, 235, 0.6)',
                            'rgba(81, 207, 102, 0.6)',
                            'rgba(255, 107, 107, 0.6)',
                            'rgba(147, 112, 219, 0.6)'
                        ],
                        borderColor: [
                            '#ffd700',
                            '#87ceeb',
                            '#51cf66',
                            '#ff6b6b',
                            '#9370db'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            beginAtZero: true
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique des paramètres vitaux
        function createFIFAMedicalVitalsChart() {
            const ctx = document.getElementById('fifa-medical-vitals-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Fréquence Cardiaque Repos (bpm)',
                        data: [62, 60, 58, 59, 57, 58, 56, 58, 57, 58, 59, 58],
                        borderColor: '#ff6b6b',
                        backgroundColor: 'rgba(255, 107, 107, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        yAxisID: 'y'
                    }, {
                        label: 'Tension Artérielle Systolique (mmHg)',
                        data: [120, 118, 116, 117, 115, 116, 114, 116, 115, 116, 117, 118],
                        borderColor: '#87ceeb',
                        backgroundColor: 'rgba(135, 206, 235, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }, {
                        label: 'SpO2 (%)',
                        data: [97, 98, 98, 97, 98, 99, 98, 98, 99, 98, 98, 98],
                        borderColor: '#51cf66',
                        backgroundColor: 'rgba(81, 207, 102, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        yAxisID: 'y2'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            title: {
                                display: true,
                                text: 'Fréquence Cardiaque (bpm)',
                                color: 'white'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            ticks: { color: 'white' },
                            grid: { drawOnChartArea: false },
                            title: {
                                display: true,
                                text: 'Tension (mmHg)',
                                color: 'white'
                            }
                        },
                        y2: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            ticks: { color: 'white' },
                            grid: { drawOnChartArea: false },
                            title: {
                                display: true,
                                text: 'SpO2 (%)',
                                color: 'white'
                            }
                        },
                        x: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
        
        // Créer le graphique de progression des tests de performance
        function createFIFAMedicalPerformanceChart() {
            const ctx = document.getElementById('fifa-medical-performance-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Test Cooper', 'Test Yo-Yo', 'Test Wingate', 'Squat Jump', 'Sprint 30m', 'Flexibilité'],
                    datasets: [{
                        label: 'Performance Actuelle',
                        data: [85, 78, 82, 88, 92, 75],
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        borderColor: '#ffd700',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffd700',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#ffd700'
                    }, {
                        label: 'Performance Précédente',
                        data: [80, 75, 78, 85, 88, 70],
                        backgroundColor: 'rgba(135, 206, 235, 0.2)',
                        borderColor: '#87ceeb',
                        borderWidth: 2,
                        pointBackgroundColor: '#87ceeb',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#87ceeb'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: 'white' }
                        }
                    },
                    scales: {
                        r: {
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            pointLabels: { color: 'white' },
                            beginAtZero: true,
                            max: 100,
                            min: 0
                        }
                    }
                }
            });
        }
        
        // Initialisation au chargement de la page (supprimée - conflit avec le DOMContentLoaded principal)
        
        // Synchroniser la hero zone FIFA avec les données dynamiques
        async function syncFIFAHeroZone() {
            try {
                // Récupérer l'ID du joueur depuis l'URL
                const urlParams = new URLSearchParams(window.location.search);
                const playerId = urlParams.get('player_id');
                
                console.log('INFO: syncFIFAHeroZone appelée');
                console.log('INFO: URL actuelle:', window.location.href);
                console.log('INFO: Paramètres URL:', window.location.search);
                console.log('INFO: player_id trouvé:', playerId);
                
                let fifaData;
                
                if (playerId) {
                    // Charger les données du joueur spécifique
                    console.log(`CIBLE: Chargement des données du joueur ID: ${playerId}`);
                    fifaData = await loadPlayerFIFAData(playerId);
                    console.log('CIBLE: Données FIFA chargées:', fifaData);
                } else {
                    // Données de démonstration par défaut
                    console.log('CIBLE: Utilisation des données de démonstration');
                    fifaData = {
                        overall_rating: 87,
                        potential_rating: 92,
                        fitness_score: 94,
                        form_percentage: 88,
                        position: 'LW',
                        age: 24,
                        first_name: 'Lionel',
                        last_name: 'Messi',
                        club_name: 'Inter Miami',
                        nationality: 'Argentine'
                    };
                }
                
                // Mettre à jour la hero zone
                console.log('CIBLE: Mise à jour de la Hero Zone avec:', fifaData);
                updateFIFAHeroZone(fifaData);
                
                console.log('SUCCES: Hero Zone FIFA synchronisée avec succès !');
                
            } catch (error) {
                console.error('ERREUR: Erreur synchronisation hero zone FIFA:', error);
            }
        }
        
        // Charger les données FIFA d'un joueur spécifique
        async function loadPlayerFIFAData(playerId) {
            try {
                // D'abord essayer de récupérer depuis la liste des joueurs déjà chargée
                if (allPlayers.length > 0) {
                    const player = allPlayers.find(p => p.id == playerId);
                    if (player) {
                        console.log('CIBLE: Joueur trouvé dans la liste locale:', player);
                        return {
                            ...player,
                            overall_rating: player.overall_rating || 84,
                            potential_rating: player.potential_rating || 88,
                            fitness_score: player.fitness_score || 90,
                            form_percentage: player.form_percentage || 85,
                            position: player.position || 'N/A',
                            age: player.age || 25,
                            first_name: player.first_name || 'Prénom',
                            last_name: player.last_name || 'Nom',
                            club_name: player.club ? player.club.name : 'Club',
                            nationality: player.nationality || 'International',
                            // Données de photo
                            player_picture: player.player_picture,
                            player_face_url: player.player_face_url,
                            profile_image: player.profile_image,
                            photo_url: player.photo_url,
                            photo_path: player.photo_path
                        };
                    }
                }
                
                // Si pas trouvé localement, essayer l'API
                const response = await fetch(`/api/player-performance/${playerId}`);
                const data = await response.json();
                
                if (data.message && data.data) {
                    return data.data;
                } else {
                    throw new Error('Données non disponibles');
                }
            } catch (error) {
                console.error('ERREUR: Erreur chargement données joueur:', error);
                // Retourner des données par défaut en cas d'erreur
                return {
                    overall_rating: 84,
                    potential_rating: 88,
                    fitness_score: 90,
                    form_percentage: 85,
                    position: 'N/A',
                    age: 25,
                    first_name: 'Joueur',
                    last_name: 'FIFA',
                    club_name: 'Club FIFA',
                    nationality: 'International'
                };
            }
        }
        
        // Navigation entre joueurs
        let currentPlayerIndex = 0;
        let allPlayers = [];
        
        // Charger la liste des joueurs
        async function loadAllPlayers() {
            try {
                const response = await fetch('/api/players');
                const data = await response.json();
                
                if (data.success && data.data) {
                    allPlayers = data.data;
                    updatePlayerCounter();
                    console.log(`SUCCES: ${allPlayers.length} joueurs chargés`);
                }
            } catch (error) {
                console.error('ERREUR: Erreur chargement liste joueurs:', error);
                // Utiliser des données de démonstration
                allPlayers = [
                    { id: 1, first_name: 'Lionel', last_name: 'Messi' },
                    { id: 2, first_name: 'Cristiano', last_name: 'Ronaldo' },
                    { id: 3, first_name: 'Kylian', last_name: 'Mbappé' },
                    { id: 4, first_name: 'Erling', last_name: 'Haaland' },
                    { id: 5, first_name: 'Kevin', last_name: 'De Bruyne' }
                ];
                updatePlayerCounter();
            }
        }
        
        // Mettre à jour le compteur de joueurs
        function updatePlayerCounter() {
            const counter = document.getElementById('fifa-player-counter');
            if (counter && allPlayers.length > 0) {
                counter.textContent = `${currentPlayerIndex + 1} / ${allPlayers.length}`;
            }
        }
        
        // Naviguer vers le joueur précédent (SYSTÈME UNIFIÉ)
        function navigateToPreviousPlayer() {
            console.log('🔵 navigateToPreviousPlayer() appelée (système de recherche)');
            if (allPlayers.length === 0) return;
            
            currentPlayerIndex = (currentPlayerIndex - 1 + allPlayers.length) % allPlayers.length;
            const player = allPlayers[currentPlayerIndex];
            
            // Mettre à jour l'URL et recharger la page
            const newUrl = `/fifa-portal?player_id=${player.id}`;
            window.location.href = newUrl; // Recharger complètement la page
            
            console.log(`⬅️ Navigation vers joueur précédent: ${player.first_name} ${player.last_name}`);
        }
        
        // Naviguer vers le joueur suivant (SYSTÈME UNIFIÉ)
        function navigateToNextPlayer() {
            console.log('🟢 navigateToNextPlayer() appelée (système de recherche)');
            if (allPlayers.length === 0) return;
            
            currentPlayerIndex = (currentPlayerIndex + 1) % allPlayers.length;
            const player = allPlayers[currentPlayerIndex];
            
            // Mettre à jour l'URL et recharger la page
            const newUrl = `/fifa-portal?player_id=${player.id}`;
            window.location.href = newUrl; // Recharger complètement la page
            
            console.log(`➡️ Navigation vers joueur suivant: ${player.first_name} ${player.last_name}`);
        }
        
        // Initialiser l'index du joueur actuel
        function initializePlayerIndex() {
            const urlParams = new URLSearchParams(window.location.search);
            const playerId = urlParams.get('player_id');
            
            if (playerId && allPlayers.length > 0) {
                const playerIndex = allPlayers.findIndex(p => p.id == playerId);
                if (playerIndex !== -1) {
                    currentPlayerIndex = playerIndex;
                    updatePlayerCounter();
                }
            }
        }
        
        // Mettre à jour la hero zone avec les données FIFA enrichies
        function updateFIFAHeroZone(fifaData) {
            console.log('INFO: updateFIFAHeroZone enrichie appelée avec:', fifaData);
            
            // Mettre à jour les données textuelles
            if (fifaData.player) {
                console.log('INFO: Mise à jour des données textuelles...');
                console.log('INFO: Données du joueur:', fifaData.player);
                
                // Nom du joueur
                const playerNameElement = document.getElementById('hero-player-name');
                console.log('INFO: Élément nom trouvé:', playerNameElement);
                if (playerNameElement && fifaData.player.name) {
                    playerNameElement.textContent = fifaData.player.name;
                    console.log('SUCCES: Nom mis à jour:', fifaData.player.name);
                } else {
                    console.log('ERREUR: Nom non mis à jour - élément:', !!playerNameElement, 'données:', fifaData.player.name);
                }
                
                // Position du joueur
                const playerPositionElement = document.getElementById('hero-position');
                console.log('INFO: Élément position trouvé:', playerPositionElement);
                if (playerPositionElement && fifaData.player.position) {
                    playerPositionElement.textContent = fifaData.player.position;
                    console.log('SUCCES: Position mise à jour:', fifaData.player.position);
                } else {
                    console.log('ERREUR: Position non mise à jour - élément:', !!playerPositionElement, 'données:', fifaData.player.position);
                }
                
                // Club du joueur
                const playerClubElement = document.getElementById('hero-club-name');
                console.log('INFO: Élément club trouvé:', playerClubElement);
                if (playerClubElement && fifaData.player.club && fifaData.player.club.name) {
                    playerClubElement.textContent = fifaData.player.club.name;
                    console.log('SUCCES: Club mis à jour:', fifaData.player.club.name);
                } else {
                    console.log('ERREUR: Club non mis à jour - élément:', !!playerClubElement, 'données:', fifaData.player.club);
                }
                
                // Nationalité du joueur
                const playerNationalityElement = document.getElementById('hero-nationality');
                console.log('INFO: Élément nationalité trouvé:', playerNationalityElement);
                if (playerNationalityElement && fifaData.player.nationality) {
                    playerNationalityElement.textContent = fifaData.player.nationality;
                    console.log('SUCCES: Nationalité mise à jour:', fifaData.player.nationality);
                } else {
                    console.log('ERREUR: Nationalité non mise à jour - élément:', !!playerNationalityElement, 'données:', fifaData.player.nationality);
                }
                
                // Âge du joueur
                const playerAgeElement = document.getElementById('hero-age');
                console.log('INFO: Élément âge trouvé:', playerAgeElement);
                if (playerAgeElement && fifaData.player.age) {
                    playerAgeElement.textContent = fifaData.player.age + ' ans';
                    console.log('SUCCES: Âge mis à jour:', fifaData.player.age + ' ans');
                } else {
                    console.log('ERREUR: Âge non mis à jour - élément:', !!playerAgeElement, 'données:', fifaData.player.age);
                }
                
                // Taille du joueur
                const playerHeightElement = document.getElementById('hero-height');
                if (playerHeightElement && fifaData.player.height) {
                    playerHeightElement.textContent = fifaData.player.height + 'cm';
                }
                
                // Poids du joueur
                const playerWeightElement = document.getElementById('hero-weight');
                if (playerWeightElement && fifaData.player.weight) {
                    playerWeightElement.textContent = fifaData.player.weight + 'kg';
                }
                
                // Pied préféré
                const playerFootElement = document.getElementById('hero-preferred-foot');
                if (playerFootElement && fifaData.player.preferred_foot) {
                    playerFootElement.textContent = fifaData.player.preferred_foot;
                }
                
                // Mise à jour des statistiques FIFA
                console.log('INFO: Mise à jour des stats FIFA...');
                
                // Overall Rating (OVR)
                const overallRatingElement = document.getElementById('hero-overall-rating-main');
                if (overallRatingElement && fifaData.player.overall_rating) {
                    overallRatingElement.textContent = fifaData.player.overall_rating;
                    console.log('SUCCES: OVR mis à jour:', fifaData.player.overall_rating);
                }
                
                // Potential Rating (POT)
                const potentialRatingElement = document.getElementById('hero-potential-rating');
                if (potentialRatingElement && fifaData.player.potential_rating) {
                    potentialRatingElement.textContent = fifaData.player.potential_rating;
                    console.log('SUCCES: POT mis à jour:', fifaData.player.potential_rating);
                }
                
                // Fitness Score (FIT)
                const fitnessScoreElement = document.getElementById('hero-fitness-score');
                if (fitnessScoreElement && fifaData.player.fitness_score) {
                    fitnessScoreElement.textContent = fifaData.player.fitness_score + '%';
                    console.log('SUCCES: FIT mis à jour:', fifaData.player.fitness_score + '%');
                }
                
                // Form Percentage (FORME)
                const formPercentageElement = document.getElementById('hero-form-percentage');
                if (formPercentageElement && fifaData.player.form_percentage) {
                    formPercentageElement.textContent = fifaData.player.form_percentage + '%';
                    console.log('SUCCES: FORME mise à jour:', fifaData.player.form_percentage + '%');
                }
                

            } else {
                console.log('ERREUR: Pas de données de joueur dans fifaData:', fifaData);
            }
            
            // MAINTENANT METTRE À JOUR LES IMAGES !
            console.log('🖼️ Mise à jour des images...');
            
            // Photo du joueur
            console.log('INFO: Vérification photo du joueur...');
            console.log('INFO: fifaData.player:', fifaData.player);
            console.log('INFO: fifaData.player.player_picture:', fifaData.player?.player_picture);
            console.log('INFO: fifaData.player.player_face_url:', fifaData.player?.player_face_url);
            
            if (fifaData.player && (fifaData.player.player_picture || fifaData.player.player_face_url)) {
                const playerPhoto = document.getElementById('hero-player-photo');
                console.log('INFO: Élément photo trouvé:', playerPhoto);
                if (playerPhoto) {
                    let photoUrl;
                    if (fifaData.player.player_picture && fifaData.player.player_picture.startsWith('players/photos/')) {
                        // Photo locale
                        photoUrl = `/storage/${fifaData.player.player_picture}`;
                        console.log('SUCCES: Photo locale utilisée:', photoUrl);
                    } else if (fifaData.player.player_face_url) {
                        // Photo externe (Dicebear, etc.)
                        photoUrl = fifaData.player.player_face_url;
                        console.log('SUCCES: Photo externe utilisée:', photoUrl);
                    } else if (fifaData.player.player_picture) {
                        // Autre format
                        photoUrl = fifaData.player.player_picture;
                        console.log('SUCCES: Photo directe utilisée:', photoUrl);
                    }
                    
                    if (photoUrl) {
                        playerPhoto.src = photoUrl;
                        console.log('SUCCES: Photo du joueur mise à jour:', photoUrl);
                    }
                }
            } else {
                console.log('ERREUR: Pas de photo disponible - player_picture:', fifaData.player?.player_picture, 'player_face_url:', fifaData.player?.player_face_url);
            }
            
                            // Logo du club
                if (fifaData.player && fifaData.player.club && fifaData.player.club.name) {
                    const clubLogo = document.getElementById('hero-club-logo');
                    if (clubLogo) {
                        // Utiliser le logo du club depuis l'API si disponible
                        if (fifaData.player.club.logo_url) {
                            clubLogo.src = fifaData.player.club.logo_url;
                            console.log('SUCCES: Logo du club depuis API:', fifaData.player.club.logo_url);
                        } else {
                            // Fallback vers le mapping local
                            const clubLogos = {
                                'Espérance de Tunis': '/clubs/EST.webp',
                                'Club Africain': '/clubs/CA.webp',
                                'Étoile du Sahel': '/clubs/ESS.webp',
                                'US Monastir': '/clubs/USM.webp',
                                'JS Kairouan': '/clubs/JSK.webp',
                                'Stade Tunisien': '/clubs/ST.webp',
                                'AS Gabès': '/clubs/ASG.webp',
                                'CA Bizertin': '/clubs/CAB.webp',
                                'CS Sfaxien': '/clubs/CSS.webp',
                                'Olympique Béja': '/clubs/OL.webp'
                            };
                            
                            const logoUrl = clubLogos[fifaData.player.club.name] || '/clubs/default.webp';
                            clubLogo.src = logoUrl;
                            console.log('SUCCES: Logo du club depuis mapping local:', logoUrl);
                        }
                    }
                }
            
            // Drapeau de la nationalité
            if (fifaData.player && fifaData.player.nationality) {
                const flag = document.getElementById('hero-flag');
                if (flag) {
                    const countryCode = getCountryCode(fifaData.player.nationality);
                    const flagUrl = `https://flagcdn.com/w40/${countryCode}.png`;
                    flag.src = flagUrl;
                    console.log('SUCCES: Drapeau mis à jour:', flagUrl);
                }
            }
            
            console.log('SUCCES: Hero zone complètement mise à jour (données + images)');
        }
        
        // Mise à jour des images dynamiques selon le joueur
        function updateHeroImages(fifaData) {
            console.log('INFO: Mise à jour des images dynamiques...');
            
            // Mise à jour de la photo du joueur
            updatePlayerPhoto(fifaData);
            
            // Mise à jour du logo du club
            updateClubLogo(fifaData);
            
            // Mise à jour du drapeau de la nation
            updateNationalityFlag(fifaData);
            
            console.log('SUCCES: Images dynamiques mises à jour');
        }
        
        // NE PAS forcer la mise à jour de la photo du joueur - la préserver
        function updatePlayerPhoto(fifaData) {
            console.log('🖼️ Photo du joueur préservée - pas de mise à jour forcée');
            // Les images sont déjà correctes dans le HTML initial
            // NE RIEN FAIRE - préserver les images existantes
            return;
        }
        
        // NE PAS forcer la mise à jour du logo du club - le préserver
        function updateClubLogo(fifaData) {
            console.log('🏆 Logo du club préservé - pas de mise à jour forcée');
            // Les images sont déjà correctes dans le HTML initial
            // NE RIEN FAIRE - préserver les images existantes
            return;
        }
        
        // Fonctions de navigation des joueurs
        function nextPlayer() {
            console.log('🟢 nextPlayer() appelée');
            const currentPlayerId = getCurrentPlayerId();
            console.log('INFO: ID actuel:', currentPlayerId);
            if (currentPlayerId) {
                const nextId = currentPlayerId + 1;
                console.log('➡️ Navigation vers ID:', nextId);
                // Recharger complètement la page avec le nouveau joueur
                window.location.href = `/fifa-portal?player_id=${nextId}`;
            } else {
                console.error('ERREUR: Pas d\'ID actuel trouvé');
            }
        }
        
        function previousPlayer() {
            console.log('🔵 previousPlayer() appelée');
            const currentPlayerId = getCurrentPlayerId();
            console.log('INFO: ID actuel:', currentPlayerId);
            if (currentPlayerId && currentPlayerId > 1) {
                const prevId = currentPlayerId - 1;
                console.log('⬅️ Navigation vers ID:', prevId);
                // Recharger complètement la page avec le nouveau joueur
                window.location.href = `/fifa-portal?player_id=${prevId}`;
            } else {
                console.error('ERREUR: Pas d\'ID précédent disponible');
            }
        }
        
        function getCurrentPlayerId() {
            const urlParams = new URLSearchParams(window.location.search);
            const playerId = parseInt(urlParams.get('player_id')) || 7;
            console.log('INFO: getCurrentPlayerId() retourne:', playerId);
            return playerId;
        }
        
        // Ajouter les événements de navigation
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DEMARRAGE: DOM chargé - Ajout des événements de navigation');
            
            // Bouton Suivant (Hero Zone)
            const nextBtn = document.getElementById('next-player-btn');
            console.log('INFO: Bouton Suivant Hero trouvé:', nextBtn);
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    console.log('🟢 Clic sur Suivant Hero détecté');
                    nextPlayer();
                });
                console.log('SUCCES: Événement Suivant Hero ajouté');
            } else {
                console.error('ERREUR: Bouton Suivant Hero non trouvé');
            }
            
            // Bouton Précédent (Hero Zone)
            const prevBtn = document.getElementById('previous-player-btn');
            console.log('INFO: Bouton Précédent Hero trouvé:', prevBtn);
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    console.log('🔵 Clic sur Précédent Hero détecté');
                    previousPlayer();
                });
                console.log('SUCCES: Événement Précédent Hero ajouté');
            } else {
                console.error('ERREUR: Bouton Précédent Hero non trouvé');
            }
            
            // Bouton Suivant (Barre de recherche)
            const searchNextBtn = document.getElementById('search-next-btn');
            console.log('INFO: Bouton Suivant Search trouvé:', searchNextBtn);
            if (searchNextBtn) {
                searchNextBtn.addEventListener('click', function() {
                    console.log('🟢 Clic sur Suivant Search détecté');
                    navigateToNextPlayer();
                });
                console.log('SUCCES: Événement Suivant Search ajouté');
            } else {
                console.error('ERREUR: Bouton Suivant Search non trouvé');
            }
            
            // Bouton Précédent (Barre de recherche)
            const searchPrevBtn = document.getElementById('search-prev-btn');
            console.log('INFO: Bouton Précédent Search trouvé:', searchPrevBtn);
            if (searchPrevBtn) {
                searchPrevBtn.addEventListener('click', function() {
                    console.log('🔵 Clic sur Précédent Search détecté');
                    navigateToPreviousPlayer();
                });
                console.log('SUCCES: Événement Précédent Search ajouté');
            } else {
                console.error('ERREUR: Bouton Précédent Search non trouvé');
            }
            
            console.log('CIBLE: Navigation complète initialisée');
            
            // Synchroniser avec la player list au chargement
            syncWithPlayerList();
            
            // Initialiser les données FIFA et l'onglet Performances
            setTimeout(() => {
                console.log('CIBLE: Initialisation des données FIFA...');
                loadFIFAData();
                
                // Initialiser l'onglet Performances par défaut
                setTimeout(() => {
                    console.log('CIBLE: Activation de l\'onglet Performances...');
                    showFIFATab('performances');
                }, 200);
            }, 500);
        });
        
        // Synchroniser les données avec la player list
        function syncWithPlayerList() {
            console.log('Synchronisation Synchronisation avec la player list...');
            
            // Récupérer l'ID du joueur depuis l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const playerId = urlParams.get('player_id');
            
            if (playerId) {
                console.log('INFO: Synchronisation pour le joueur ID:', playerId);
                
                // Recharger les données depuis l'API
                fetch(`/api/fifa/player/${playerId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('SUCCES: Données récupérées:', data);
                        console.log('INFO: Structure des données:', JSON.stringify(data, null, 2));
                        
                        // Extraire les vraies données du joueur
                        const playerData = data.data || data;
                        console.log('INFO: Données du joueur extraites:', playerData);
                        
                        // Structurer les données pour updateFIFAHeroZone
                        const structuredData = {
                            player: {
                                name: playerData.name || (playerData.first_name && playerData.last_name ? playerData.first_name + ' ' + playerData.last_name : 'Nom inconnu'),
                                position: playerData.position || 'Position inconnue',
                                club: playerData.club || { name: 'Club inconnu' },
                                nationality: playerData.nationality || 'Nationalité inconnue',
                                age: playerData.age || 'Âge inconnu',
                                height: playerData.height || 'Taille inconnue',
                                weight: playerData.weight || 'Poids inconnu',
                                preferred_foot: playerData.preferred_foot || 'Pied inconnu',
                                player_picture: playerData.player_picture || playerData.player_face_url || null,
                                overall_rating: playerData.overall_rating || playerData.ghs_overall_score || 84,
                                potential_rating: playerData.potential_rating || (playerData.ghs_overall_score + 5) || 88,
                                fitness_score: playerData.ghs_physical_score || 92,
                                form_percentage: playerData.ghs_overall_score || 85
                            }
                        };
                        
                        console.log('🔧 Données structurées:', structuredData);
                        
                        // Mettre à jour la hero zone avec les vraies données
                        updateFIFAHeroZone(structuredData);
                    })
                    .catch(error => {
                        console.error('ERREUR: Erreur synchronisation:', error);
                    });
            }
        }
        
        // Mettre à jour le drapeau de la nation
        function updateNationalityFlag(fifaData) {
            const flag = document.getElementById('hero-flag');
            const flagFallback = document.getElementById('hero-flag-fallback');
            const flagInitial = document.querySelector('.fifa-flag-initial');
            
            if (flag && fifaData.nationality) {
                // Construire l'URL du drapeau
                const countryCode = getCountryCode(fifaData.nationality);
                const flagUrl = `https://flagcdn.com/w40/${countryCode}.png`;
                
                flag.src = flagUrl;
                flag.onerror = function() {
                    // Fallback vers le drapeau local si disponible
                    const localFlag = `/images/flags/${countryCode}.svg`;
                    this.src = localFlag;
                    this.onerror = function() {
                        // Afficher les initiales en fallback
                        this.style.display = 'none';
                        if (flagFallback && flagInitial) {
                            flagInitial.textContent = fifaData.nationality?.substring(0, 2).toUpperCase() || 'TF';
                            flagFallback.style.display = 'flex';
                        }
                    };
                };
                
                // Réinitialiser l'affichage
                flag.style.display = 'block';
                if (flagFallback) flagFallback.style.display = 'none';
            }
        }
        
        // Obtenir le code pays à partir du nom de la nationalité (MÉTHODE PORTAL JOUEURS)
        function getCountryCode(nationality) {
            const countryMap = {
                'Argentine': 'ar',
                'Brésil': 'br',
                'France': 'fr',
                'Allemagne': 'de',
                'Espagne': 'es',
                'Angleterre': 'gb',
                'Portugal': 'pt',
                'Italie': 'it',
                'Pays-Bas': 'nl',
                'Belgique': 'be',
                'Croatie': 'hr',
                'Maroc': 'ma',
                'The Football Association': 'ar', // Même logique que Portal Joueur
                'Sénégal': 'sn',
                'Nigeria': 'ng',
                'Égypte': 'eg',
                'Cameroun': 'cm',
                'Ghana': 'gh',
                'Côte d\'Ivoire': 'ci',
                'Mali': 'ml'
            };
            
            return countryMap[nationality] || 'ar'; // 'ar' par défaut comme Portal Joueur
        }
        
        // Mise à jour de la barre de progression saison
        function updateHeroProgressBar(fifaData) {
            const progressFill = document.getElementById('hero-progress-fill');
            if (progressFill) {
                const progress = fifaData.season_progress || 75;
                progressFill.style.width = `${progress}%`;
                console.log(`SUCCES: Barre de progression mise à jour: ${progress}%`);
            }
        }
        
        // Mise à jour des résultats récents
        function updateHeroRecentResults(fifaData) {
            const resultsContainer = document.getElementById('hero-recent-results');
            if (resultsContainer && fifaData.recent_results) {
                // Générer des résultats basés sur les performances
                const results = generateRecentResults(fifaData);
                resultsContainer.innerHTML = results;
                console.log('SUCCES: Résultats récents mis à jour');
            }
        }
        
        // Générer des résultats récents basés sur les performances
        function generateRecentResults(fifaData) {
            // Logique pour générer des résultats basés sur la forme et les performances
            const form = fifaData.form_percentage || 85;
            const fitness = fifaData.fitness_score || 90;
            
            let results = '';
            for (let i = 0; i < 5; i++) {
                let result;
                if (form > 80 && fitness > 85) {
                    result = 'win';
                } else if (form > 60 && fitness > 70) {
                    result = 'draw';
                } else {
                    result = 'loss';
                }
                
                const resultText = result === 'win' ? 'W' : result === 'draw' ? 'D' : 'L';
                results += `<div class="fifa-result ${result}">${resultText}</div>`;
            }
            
            return results;
        }
        
        // Mettre à jour les icônes de la hero zone
        // Mise à jour des icônes de la hero zone (MAINTENANT GÉRÉE PAR updateHeroImages)
        function updateHeroIcons(fifaData) {
            // Cette fonction n'est plus utilisée car les images sont gérées par updateHeroImages
            // Gardée pour compatibilité mais ne fait plus rien
            console.log('INFO: updateHeroIcons appelée mais remplacée par updateHeroImages');
        }
        
        // Fonction de recherche FIFA
        async function performFIFASearch() {
            const searchInput = document.getElementById('fifa-player-search');
            const searchTerm = searchInput.value.trim();
            
            if (!searchTerm) {
                hideSearchResults();
                return;
            }
            
            // Afficher l'indicateur de chargement
            showSearchLoading();
            
            try {
                // Recherche en temps réel dans la base de données
                const response = await fetch(`/search-players?q=${encodeURIComponent(searchTerm)}`);
                const results = await response.json();
                
                hideSearchLoading();
                displaySearchResults(results.data || []);
                
            } catch (error) {
                console.error('ERREUR: Erreur recherche FIFA:', error);
                hideSearchLoading();
                // Fallback : recherche locale simulée
                performLocalSearch(searchTerm);
            }
        }
        
        // Afficher l'indicateur de chargement
        function showSearchLoading() {
            const loading = document.getElementById('fifa-search-loading');
            const results = document.getElementById('fifa-search-results');
            
            if (loading) loading.style.display = 'block';
            if (results) results.classList.remove('active');
        }
        
        // Masquer l'indicateur de chargement
        function hideSearchLoading() {
            const loading = document.getElementById('fifa-search-loading');
            if (loading) loading.style.display = 'none';
        }
        
        // Recherche locale simulée (fallback)
        function performLocalSearch(searchTerm) {
            const mockResults = [
                {
                    id: 1,
                    first_name: 'Lionel',
                    last_name: 'Messi',
                    position: 'RW',
                    rating: 87,
                    club_name: 'Inter Miami',
                    nationality: 'Argentine'
                },
                {
                    id: 2,
                    first_name: 'Cristiano',
                    last_name: 'Ronaldo',
                    position: 'ST',
                    rating: 86,
                    club_name: 'Al Nassr',
                    nationality: 'Portugal'
                },
                {
                    id: 3,
                    first_name: 'Kylian',
                    last_name: 'Mbappé',
                    position: 'LW',
                    rating: 89,
                    club_name: 'Real Madrid',
                    nationality: 'France'
                }
            ].filter(player => 
                player.first_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                player.last_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                player.club_name.toLowerCase().includes(searchTerm.toLowerCase())
            );
            
            displaySearchResults(mockResults);
        }
        
        // Afficher les résultats de recherche
        function displaySearchResults(players) {
            const resultsContainer = document.getElementById('fifa-search-results');
            
            if (!players.length) {
                resultsContainer.innerHTML = `
                    <div class="fifa-search-result-item">
                        <div class="fifa-result-info">
                            <div class="fifa-result-name">Aucun joueur trouvé</div>
                            <div class="fifa-result-details">Essayez avec d'autres termes</div>
                        </div>
                    </div>
                `;
                resultsContainer.classList.add('active');
                return;
            }
            
            const resultsHTML = players.map(player => `
                <div class="fifa-search-result-item" onclick="selectPlayer(${player.id})">
                    <div class="fifa-result-avatar">Football</div>
                    <div class="fifa-result-info">
                        <div class="fifa-result-name">${player.first_name} ${player.last_name}</div>
                        <div class="fifa-result-details">${player.position} • ${player.club_name} • ${player.nationality}</div>
                    </div>
                    <div class="fifa-result-rating">${player.rating}</div>
                </div>
            `).join('');
            
            resultsContainer.innerHTML = resultsHTML;
            resultsContainer.classList.add('active');
        }
        
        // Sélectionner un joueur
        function selectPlayer(playerId) {
            console.log(`CIBLE: Joueur sélectionné: ${playerId}`);
            
            // Rediriger vers le FIFA Portal du joueur
            window.location.href = `/fifa-portal?player_id=${playerId}`;
        }
        
        // Masquer les résultats de recherche
        function hideSearchResults() {
            const resultsContainer = document.getElementById('fifa-search-results');
            resultsContainer.classList.remove('active');
        }
        
        // Gestion des événements de recherche et initialisation
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DEMARRAGE: FIFA Portal initialisé !');
            
            const searchInput = document.getElementById('fifa-player-search');
            const resultsContainer = document.getElementById('fifa-search-results');
            
            // Charger la liste des joueurs
            loadAllPlayers().then(() => {
                // Initialiser l'index du joueur actuel
                initializePlayerIndex();
                
                // Charger les données FIFA
                loadFIFAData();
                
                // Synchroniser la hero zone
                syncFIFAHeroZone();
                
                // Initialiser les onglets
                loadFIFATabContent('overview');
            });
            
            // Recherche en temps réel avec debounce
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                if (this.value.trim()) {
                    searchTimeout = setTimeout(() => {
                        performFIFASearch();
                    }, 300); // Délai de 300ms pour éviter trop de requêtes
                } else {
                    hideSearchResults();
                }
            });
            
            // Masquer les résultats en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                    hideSearchResults();
                }
            });
            
            // Recherche avec Entrée
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    performFIFASearch();
                }
            });
            
            // Focus sur la barre de recherche avec Ctrl+K
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        });
    </script>
</body>
</html>
