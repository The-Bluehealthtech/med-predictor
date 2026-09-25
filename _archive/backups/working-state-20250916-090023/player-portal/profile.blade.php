<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Portail Joueur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Mon Profil</h1>
            <p class="text-gray-600">Informations personnelles et médicales</p>
        </div>

        @if(Auth::user() && Auth::user()->player)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informations personnelles -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Informations Personnelles</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->first_name }} {{ Auth::user()->player->last_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->date_of_birth ? Auth::user()->player->date_of_birth->format('d/m/Y') : 'Non spécifiée' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nationalité</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->nationality ?? 'Non spécifiée' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Position</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->position ?? 'Non spécifiée' }}</p>
                        </div>
                        
                        @if(Auth::user()->player->club)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Club</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->club->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations médicales -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Informations Médicales</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Taille</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->height ?? 'Non spécifiée' }} cm</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Poids</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->weight ?? 'Non spécifié' }} kg</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">IMC</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->bmi ?? 'Non calculé' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pied préféré</label>
                            <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->player->preferred_foot ?? 'Non spécifié' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Statistiques</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ Auth::user()->player->healthRecords->count() }}</div>
                        <div class="text-sm text-gray-600">Dossiers médicaux</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ Auth::user()->player->pcmas->count() }}</div>
                        <div class="text-sm text-gray-600">PCMA</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ Auth::user()->player->medicalPredictions->count() }}</div>
                        <div class="text-sm text-gray-600">Prédictions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ Auth::user()->player->performances->count() }}</div>
                        <div class="text-sm text-gray-600">Performances</div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong>Erreur :</strong> Aucun joueur associé à votre compte.
        </div>
        @endif
    </div>
</div>
</body>
</html>
