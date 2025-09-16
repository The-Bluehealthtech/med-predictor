<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FIT Platform - Suivi Holistique des Athlètes</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .logo {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        .nav {
            position: fixed;
            top: 0;
            right: 0;
            padding: 1.5rem;
            z-index: 10;
        }
        .nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .nav a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        .feature {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 1rem;
            backdrop-filter: blur(10px);
        }
        .feature h3 {
            margin-top: 0;
            font-size: 1.5rem;
        }
        .cta {
            text-align: center;
            margin-top: 3rem;
        }
        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    @if (Route::has('login'))
        <div class="nav">
            @auth
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Connexion</a>
            @endauth
        </div>
    @endif

    <div class="container">
        <div class="header">
            <div class="logo">FIT Platform</div>
            <div class="subtitle">Suivi Holistique des Athlètes - Plateforme Intégrée de Performance</div>
        </div>

        <div class="features">
            <div class="feature">
                <h3>🏥 Suivi Médical</h3>
                <p>Gestion complète des dossiers médicaux, examens, et prévention des blessures avec intelligence artificielle.</p>
            </div>
            
            <div class="feature">
                <h3>⚡ Performance</h3>
                <p>Analyse des performances sportives, statistiques avancées et recommandations personnalisées.</p>
            </div>
            
            <div class="feature">
                <h3>📊 Analytics</h3>
                <p>Tableaux de bord interactifs, rapports détaillés et prédictions basées sur les données.</p>
            </div>
            
            <div class="feature">
                <h3>🔗 Intégration FIFA</h3>
                <p>Connexion directe avec FIFA Connect pour la gestion des licences et transferts internationaux.</p>
            </div>
            
            <div class="feature">
                <h3>👥 Gestion d'Équipe</h3>
                <p>Outils complets pour la gestion des clubs, associations et compétitions.</p>
            </div>
            
            <div class="feature">
                <h3>🔒 Sécurité</h3>
                <p>Protection des données sensibles avec chiffrement de bout en bout et conformité RGPD.</p>
            </div>
        </div>

        <div class="cta">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn">Accéder au Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn">Se Connecter</a>
            @endauth
        </div>
    </div>
</body>
</html>