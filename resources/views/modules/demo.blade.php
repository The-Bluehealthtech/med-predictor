<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Démo Modules - Système de Gestion FIFA Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Démo des Modules</h1>
                        <p class="mt-1 text-sm text-gray-500">Démonstration des fonctionnalités FIFA Connect</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($footballType) }}
                        </span>
                        <a href="/modules" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            ← Retour aux Modules
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu Principal -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Fonctionnalités de Démonstration</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Démo Compétitions -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">🏆 Compétitions FIFA Connect</h3>
                        <ul class="space-y-2 text-sm text-gray-600 mb-4">
                            <li>• Création de compétitions avec validation</li>
                            <li>• Workflow Draft → Submit → Validate → Publish</li>
                            <li>• Gestion des phases et groupes</li>
                            <li>• Actions en lot et gestion des erreurs</li>
                        </ul>
                        <a href="/competitions/dashboard" class="inline-block bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Tester les Compétitions
                        </a>
                    </div>

                    <!-- Démo Associations -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">🏢 Gestion des Associations</h3>
                        <ul class="space-y-2 text-sm text-gray-600 mb-4">
                            <li>• Création et gestion des associations</li>
                            <li>• Navigation hiérarchique</li>
                            <li>• Intégration avec les compétitions</li>
                            <li>• Gestion des clubs et équipes</li>
                        </ul>
                        <a href="/associations" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Tester les Associations
                        </a>
                    </div>

                    <!-- Démo FIFA Dashboard -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">⚡ FIFA Dashboard</h3>
                        <ul class="space-y-2 text-sm text-gray-600 mb-4">
                            <li>• Interface principale FIFA</li>
                            <li>• Connectivité et synchronisation</li>
                            <li>• Analytics et rapports</li>
                            <li>• Gestion des contrats</li>
                        </ul>
                        <a href="/fifa/dashboard" class="inline-block bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                            Tester le Dashboard FIFA
                        </a>
                    </div>

                    <!-- Démo Système Complet -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">🔗 Système Intégré</h3>
                        <ul class="space-y-2 text-sm text-gray-600 mb-4">
                            <li>• Navigation entre tous les modules</li>
                            <li>• Workflow complet end-to-end</li>
                            <li>• Gestion des autorisations</li>
                            <li>• Logs et audit complets</li>
                        </ul>
                        <a href="/modules" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Explorer le Système
                        </a>
                    </div>
                </div>

                <!-- Instructions de Test -->
                <div class="mt-8 p-6 bg-blue-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">📋 Instructions de Test</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                        <div>
                            <h4 class="font-semibold mb-2">1. Test des Compétitions :</h4>
                            <ul class="space-y-1">
                                <li>• Créer une nouvelle compétition</li>
                                <li>• Tester le workflow de validation</li>
                                <li>• Vérifier les actions en lot</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2">2. Test des Associations :</h4>
                            <ul class="space-y-1">
                                <li>• Naviguer dans la hiérarchie</li>
                                <li>• Créer des associations</li>
                                <li>• Tester les liens vers les compétitions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>




