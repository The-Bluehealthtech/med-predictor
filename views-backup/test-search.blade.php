<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Système de Recherche et Pagination</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-900">Test Système de Recherche et Pagination</h1>
                    <p class="text-gray-600">Composant de recherche avec 6 champs et pagination de 20 éléments</p>
                </div>
            </div>

            <!-- Composant de recherche et pagination -->
            <x-search-pagination 
                :search-fields="$searchFields" 
                :current-page="$currentPage"
                :total-pages="$totalPages"
                :per-page="$perPage"
                :total-items="$totalItems"
                :search-params="request()->query()" />

            <!-- Contenu de démonstration -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Résultats de la recherche</h2>
                    
                    <!-- Tableau de démonstration -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationalité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @for($i = 1; $i <= 20; $i++)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Joueur {{ (($currentPage - 1) * 20) + $i }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ['Gardien', 'Défenseur', 'Milieu', 'Attaquant'][rand(0, 3)] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Club {{ rand(1, 10) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ['Tunisie', 'France', 'Algérie', 'Maroc'][rand(0, 3)] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Instructions d'utilisation -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">Instructions d'utilisation :</h3>
                <ul class="text-blue-800 space-y-2">
                    <li>• <strong>Champs de recherche :</strong> Utilisez les différents champs pour filtrer les résultats</li>
                    <li>• <strong>Recherche textuelle :</strong> Nom, Club, Nationalité (recherche partielle)</li>
                    <li>• <strong>Sélection :</strong> Position et Statut (sélection exacte)</li>
                    <li>• <strong>Plage de dates :</strong> Date de naissance (recherche entre deux dates)</li>
                    <li>• <strong>Pagination :</strong> 20 éléments par page avec navigation complète</li>
                    <li>• <strong>Réinitialisation :</strong> Bouton pour effacer tous les filtres</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
