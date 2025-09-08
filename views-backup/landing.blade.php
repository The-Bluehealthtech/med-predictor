de forreàé<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIT - Football Intelligence & Tracking</title>
    <meta name="description" content="Plateforme FIT (Football Intelligence & Tracking) innovante de prédiction médicale IA pour le football professionnel">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body class="bg-gradient-to-br from-green-50 via-blue-50 to-indigo-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- Logo FIT -->
                        <div class="flex items-center">
                            <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-12 h-12 mr-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">FIT</h1>
                                <p class="text-sm text-gray-600 -mt-1">Football Intelligence & Tracking</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-300 ease-in-out transform hover:scale-105">
                        Connexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section - Football & IA -->
    <div class="relative overflow-hidden bg-gradient-to-br from-green-600 via-blue-600 to-indigo-700">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="text-white">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        <span class="block">Football</span>
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-blue-400">Intelligence</span>
                        <span class="block">& Tracking</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-blue-100 mb-8 leading-relaxed">
                        La première plateforme IA qui prédit les risques médicaux et optimise les performances des joueurs de football professionnel.
                    </p>
                                            <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('login') }}" class="bg-white text-green-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                                🚀 Accéder à FIT Platform
                            </a>
                            <a href="{{ route('account-request.create') }}" class="border-2 border-white text-white hover:bg-white hover:text-green-600 px-8 py-4 rounded-lg font-bold text-lg transition duration-300 ease-in-out">
                                📋 Demander un Compte
                            </a>
                            <a href="#features" class="border-2 border-white text-white hover:bg-white hover:text-green-600 px-8 py-4 rounded-lg font-bold text-lg transition duration-300 ease-in-out">
                                📊 Découvrir
                            </a>
                        </div>
                </div>
                
                <!-- Visual Elements -->
                <div class="relative">
                    <!-- Football Field Background -->
                    <div class="relative bg-green-800 rounded-2xl p-8 shadow-2xl">
                        <div class="grid grid-cols-5 gap-2 mb-4">
                            <!-- Field Lines -->
                            <div class="col-span-5 h-1 bg-white rounded"></div>
                            <div class="col-span-5 h-1 bg-white rounded"></div>
                            <div class="col-span-1 h-8 bg-white rounded"></div>
                            <div class="col-span-3 h-8 bg-transparent"></div>
                            <div class="col-span-1 h-8 bg-white rounded"></div>
                            <div class="col-span-5 h-1 bg-white rounded"></div>
                        </div>
                        
                        <!-- AI Brain Icon -->
                        <div class="absolute top-4 right-4 w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-white">99%</div>
                                <div class="text-blue-100 text-sm">Précision IA</div>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-white">24/7</div>
                                <div class="text-blue-100 text-sm">Monitoring</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Football Intelligence & Tracking
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    FIT combine l'expertise médicale avec les dernières avancées en IA pour révolutionner la gestion de la santé des joueurs de football.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Prédiction IA Avancée</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Algorithmes de machine learning qui analysent les données biométriques pour prédire les risques de blessures avec une précision exceptionnelle.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Suivi Médical Intelligent</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Dossiers médicaux numériques avec suivi en temps réel, alertes automatiques et recommandations personnalisées pour chaque joueur.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-8 shadow-lg hover:shadow-xl transition duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Optimisation Performance</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Analyse des performances et recommandations d'entraînement personnalisées pour maximiser le potentiel de chaque athlète.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-green-600 to-blue-600">
        <div class="max-w-4xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-white mb-6">
                Prêt à Révolutionner la Gestion Médicale du Football ?
            </h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Rejoignez les clubs et fédérations qui font déjà confiance à FIT pour la santé de leurs joueurs.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="bg-white text-green-600 hover:bg-gray-100 px-10 py-4 rounded-lg font-bold text-xl transition duration-300 ease-in-out transform hover:scale-105 shadow-lg inline-block">
                    🚀 Commencer Maintenant
                </a>
                <a href="{{ route('account-request.create') }}" class="border-2 border-white text-white hover:bg-white hover:text-green-600 px-10 py-4 rounded-lg font-bold text-xl transition duration-300 ease-in-out inline-block">
                    📋 Demander un Compte
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <div class="space-y-8 xl:col-span-1">
                    <div class="flex items-center">
                        <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-10 h-10 mr-3">
                        <div>
                            <h3 class="text-2xl font-bold text-white">FIT</h3>
                            <p class="text-sm text-gray-300 -mt-1">Football Intelligence & Tracking</p>
                        </div>
                    </div>
                    <p class="text-gray-300 text-base">
                        Plateforme FIT (Football Intelligence & Tracking) innovante de prédiction médicale IA pour le football professionnel.
                    </p>
                </div>
                <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Plateforme
                            </h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white">
                                        Fonctionnalités
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white">
                                        Sécurité
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Support
                            </h3>
                            <ul class="mt-4 space-y-4">
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white">
                                        Documentation
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-300 hover:text-white">
                                        Contact
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-700 pt-8">
                <p class="text-base text-gray-400 xl:text-center">
                    &copy; 2025 FIT - Football Intelligence & Tracking. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>
</body>
</html> 