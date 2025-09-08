<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard FIT - Test Simple</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">Dashboard FIT</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/login" class="text-gray-700 hover:text-gray-900">Connexion</a>
                        <a href="/register" class="text-gray-700 hover:text-gray-900">Inscription</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-bold mb-4">Dashboard FIT</h1>
                <p class="text-xl mb-6">Outil Holistique de Suivi pour Clubs & Associations</p>
                <div class="flex justify-center space-x-4">
                    <span class="bg-green-500 px-3 py-1 rounded-full text-sm">Système Opérationnel</span>
                    <span class="bg-blue-500 px-3 py-1 rounded-full text-sm">Données en Temps Réel</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Carte 1 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Joueurs</h3>
                    <div class="text-3xl font-bold text-blue-600">5</div>
                    <div class="text-sm text-gray-600">Total des joueurs</div>
                </div>

                <!-- Carte 2 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Clubs & Associations</h3>
                    <div class="text-3xl font-bold text-green-600">3</div>
                    <div class="text-sm text-gray-600">Clubs actifs</div>
                </div>

                <!-- Carte 3 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Performances</h3>
                    <div class="text-3xl font-bold text-purple-600">85%</div>
                    <div class="text-sm text-gray-600">Score moyen</div>
                </div>

                <!-- Carte 4 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Système FIT</h3>
                    <div class="text-3xl font-bold text-orange-600">100%</div>
                    <div class="text-sm text-gray-600">Opérationnel</div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <p class="text-gray-600">Version de test simplifiée - Base de données non accessible</p>
            </div>
        </div>
    </div>
</body>
</html>




