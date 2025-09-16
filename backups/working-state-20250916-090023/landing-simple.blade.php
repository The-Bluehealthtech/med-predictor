<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FIT Platform - Accueil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">FIT Platform</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/login" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Connexion
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h2 class="text-4xl font-bold text-gray-900 mb-6">
                    Plateforme de Gestion Médicale FIT
                </h2>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Système intégré de gestion médicale pour les athlètes, incluant dossiers de santé, 
                    vaccinations, évaluations posturales et suivi des performances.
                </p>
                
                <div class="flex justify-center space-x-4">
                    <a href="/login" 
                       class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105">
                        Se Connecter
                    </a>
                    <a href="/profile-selector" 
                       class="bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-all duration-200">
                        Sélecteur de Profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-3xl mb-4">🏥</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Gestion Médicale</h3>
                    <p class="text-gray-600">
                        Dossiers de santé complets, vaccinations, évaluations posturales et suivi médical des athlètes.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-3xl mb-4">📋</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Licences & Autorisations</h3>
                    <p class="text-gray-600">
                        Gestion des licences sportives, validation des demandes et suivi des autorisations.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-3xl mb-4">🏆</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Compétitions</h3>
                    <p class="text-gray-600">
                        Organisation des compétitions, gestion des équipes et suivi des performances.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-800 text-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; 2024 FIT Platform. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</body>
</html> 