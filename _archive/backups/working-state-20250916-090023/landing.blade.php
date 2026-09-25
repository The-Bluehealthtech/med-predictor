<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FIT Platform - Suivi Holistique des Joueurs</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #000000 0%, #0f172a 20%, #1e293b 50%, #0f172a 80%, #000000 100%);
        }
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.01'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        .floating-delayed {
            animation: floating 3s ease-in-out infinite 1.5s;
        }
        .pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .text-shadow {
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.8), 0 2px 4px rgba(0, 0, 0, 0.6);
        }
        .text-shadow-strong {
            text-shadow: 0 8px 16px rgba(0, 0, 0, 1), 0 4px 8px rgba(0, 0, 0, 0.8), 0 2px 4px rgba(0, 0, 0, 0.6);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="font-inter antialiased">
    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img src="{{ asset('images/the-fit-logo.png') }}" alt="The FIT Logo" class="w-8 h-8 mr-3">
                        <h1 class="text-2xl font-bold text-gray-900">FIT Platform</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Language Switcher -->
                    <div class="relative">
                        <select class="bg-gray-100 text-gray-700 px-3 py-2 rounded-md text-sm font-medium border-0 focus:ring-2 focus:ring-blue-500">
                            <option value="fr">🇫🇷 FR</option>
                            <option value="en">🇬🇧 EN</option>
                            <option value="es">🇪🇸 ES</option>
                        </select>
                    </div>
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                Connexion
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Connexion
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    Inscription
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient hero-pattern relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0">
            <!-- Football Animation -->
            <div class="absolute top-20 left-10 floating">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>
            
            <!-- AI Brain Animation -->
            <div class="absolute top-32 right-20 floating-delayed">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center pulse-slow">
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Performance Chart Animation -->
            <div class="absolute bottom-20 left-1/4 floating">
                <div class="w-24 h-24 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Trophy Animation -->
            <div class="absolute bottom-32 right-1/4 floating-delayed">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 4V2c0-1.1.9-2 2-2h6c1.1 0 2 .9 2 2v2h4c1.1 0 2 .9 2 2v2c0 1.1-.9 2-2 2h-1v8c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2v-8H3c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2h4zm2-2v2h6V2H9z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-black text-white mb-6 text-shadow-strong">
                    FIT Platform
                    </h1>
                <p class="text-2xl md:text-3xl text-white mb-8 max-w-4xl mx-auto font-semibold text-shadow-strong">
                    Suivi holistique des joueurs de football avec analyse complète des performances, santé et développement
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center mb-12">
                    @if (Route::has('login'))
                        @auth
                            <a href="/account-request" class="bg-white text-blue-600 px-10 py-4 rounded-xl text-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105">
                                📝 Demander un Compte
                            </a>
                        @else
                            <a href="/account-request" class="glass-effect text-white px-10 py-4 rounded-xl text-xl font-bold hover:bg-white/20 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105">
                                📝 Demander un Compte
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-10 py-4 rounded-xl text-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105">
                                    ⚡ S'inscrire
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
                
                <!-- Stats Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="bg-black/40 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20">
                        <div class="text-3xl font-bold text-white mb-2 text-shadow-strong">500+</div>
                        <div class="text-white font-bold text-shadow-strong">Joueurs suivis</div>
                        </div>
                    <div class="bg-black/40 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20">
                        <div class="text-3xl font-bold text-white mb-2 text-shadow-strong">50+</div>
                        <div class="text-white font-bold text-shadow-strong">Clubs partenaires</div>
                        </div>
                    <div class="bg-black/40 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20">
                        <div class="text-3xl font-bold text-white mb-2 text-shadow-strong">99.9%</div>
                        <div class="text-white font-bold text-shadow-strong">Disponibilité</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Fonctionnalités principales
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Une plateforme complète pour le suivi et l'analyse des joueurs de football
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Module 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Analytics Avancées</h3>
                    <p class="text-gray-600 text-lg">
                        Suivi détaillé des performances avec métriques personnalisées et tableaux de bord interactifs.
                    </p>
                </div>

                <!-- Module 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Santé & Bien-être</h3>
                    <p class="text-gray-600 text-lg">
                        Monitoring de la condition physique, récupération et prévention des blessures.
                    </p>
                </div>

                <!-- Module 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Gestion d'Équipe</h3>
                    <p class="text-gray-600 text-lg">
                        Coordination des équipes, planning des entraînements et suivi des objectifs.
                    </p>
                </div>

                <!-- Module 4 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Conformité FIFA</h3>
                    <p class="text-gray-600 text-lg">
                        Intégration avec les standards FIFA Connect pour la gestion des licences et transferts.
                    </p>
                </div>

                <!-- Module 5 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Performance Temps Réel</h3>
                    <p class="text-gray-600 text-lg">
                        Données en temps réel pendant les matchs et entraînements avec alertes automatiques.
                    </p>
                </div>

                <!-- Module 6 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Rapports Intelligents</h3>
                    <p class="text-gray-600 text-lg">
                        Génération automatique de rapports détaillés avec insights et recommandations.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Prêt à optimiser les performances de vos joueurs ?
            </h2>
            <p class="text-2xl text-blue-100 mb-12 max-w-3xl mx-auto">
                Rejoignez les clubs qui font confiance à FIT Platform pour le suivi holistique de leurs joueurs.
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                @if (Route::has('login'))
                    @auth
                        <a href="/account-request" class="bg-white text-blue-600 px-10 py-4 rounded-xl text-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105">
                            📝 Demander un Compte
                        </a>
                    @else
                        <a href="/account-request" class="bg-white text-blue-600 px-10 py-4 rounded-xl text-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105">
                            📝 Demander un Compte
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-500 text-white px-10 py-4 rounded-xl text-xl font-bold hover:bg-blue-400 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105 border-2 border-white">
                                ⚡ S'inscrire
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                        <div>
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/the-fit-logo.png') }}" alt="The FIT Logo" class="w-8 h-8 mr-3">
                        <h3 class="text-2xl font-bold">FIT Platform</h3>
                    </div>
                    <p class="text-gray-400 text-lg">
                        La solution complète pour le suivi holistique des joueurs de football.
                    </p>
                </div>
                        <div>
                    <h4 class="text-lg font-semibold text-gray-300 uppercase tracking-wider mb-6">Produit</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">Fonctionnalités</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">Tarifs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">API</a></li>
                            </ul>
                        </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-300 uppercase tracking-wider mb-6">Support</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">Aide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">Contact</a></li>
                            </ul>
                        </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-300 uppercase tracking-wider mb-6">Légal</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">Confidentialité</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">Conditions</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors text-lg">Cookies</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <img src="{{ asset('images/logos/the-blue-healthtech-logo.png') }}" alt="The Blue Healthtech" class="h-8 mr-3">
                        <span class="text-gray-400 text-lg">Développé par</span>
                    </div>
                    <p class="text-gray-400 text-lg">&copy; 2025 The Blue Healthtech Ltd. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html> 