<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Joueur - Dashboard Simple</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">⚽ Portail Joueur</h1>
                    @if(Auth::user() && Auth::user()->player)
                        <p class="text-gray-600 mt-2">Bienvenue, {{ Auth::user()->player->first_name }} {{ Auth::user()->player->last_name }}</p>
                    @else
                        <p class="text-gray-600 mt-2">Bienvenue, {{ Auth::user()->name ?? 'Utilisateur' }}</p>
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    @if(Auth::user() && Auth::user()->player && Auth::user()->player->club)
                        <div class="text-right">
                            <p class="text-sm text-gray-600">{{ Auth::user()->player->club->name }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->player->position ?? 'Position non définie' }}</p>
                        </div>
                        @if(Auth::user()->player->club->logo_url)
                            <img src="{{ Auth::user()->player->club->logo_url }}" alt="Logo club" class="w-12 h-12 object-contain">
                        @endif
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Informations de debug -->
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <h3 class="font-semibold">Informations de debug :</h3>
            <p><strong>Utilisateur connecté :</strong> {{ Auth::user()->name ?? 'Non connecté' }}</p>
            <p><strong>Email :</strong> {{ Auth::user()->email ?? 'Non connecté' }}</p>
            <p><strong>Rôle :</strong> {{ Auth::user()->role ?? 'Non connecté' }}</p>
            @if(Auth::user() && Auth::user()->player)
                <p><strong>Joueur associé :</strong> {{ Auth::user()->player->first_name }} {{ Auth::user()->player->last_name }}</p>
                <p><strong>ID Joueur :</strong> {{ Auth::user()->player->id }}</p>
                <p><strong>Dossiers de santé :</strong> {{ Auth::user()->player->healthRecords->count() }}</p>
                <p><strong>Performances :</strong> {{ Auth::user()->player->performances->count() }}</p>
                <p><strong>PCMA :</strong> {{ Auth::user()->player->pcmas->count() }}</p>
                <p><strong>Prédictions :</strong> {{ Auth::user()->player->medicalPredictions->count() }}</p>
            @else
                <p><strong>Joueur associé :</strong> Aucun joueur associé</p>
            @endif
        </div>

        <!-- Statistiques -->
        @if(Auth::user() && Auth::user()->player)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-blue-600">📋</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Dossiers Médicaux</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ Auth::user()->player->healthRecords->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-green-600">📊</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">PCMA</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ Auth::user()->player->pcmas->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="text-purple-600">🔮</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Prédictions</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ Auth::user()->player->medicalPredictions->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <span class="text-orange-600">⚽</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Performances</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ Auth::user()->player->performances->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">⚡ Actions Rapides</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('player-portal.medical-records') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-green-600">📋</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Dossiers Médicaux</h4>
                            <p class="text-sm text-gray-500">Consulter mes dossiers médicaux</p>
                        </div>
                    </a>

                    <a href="{{ route('player-portal.performances') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-orange-600">📈</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Performances</h4>
                            <p class="text-sm text-gray-500">Suivre mes performances</p>
                        </div>
                    </a>

                    <a href="{{ route('player-portal.test') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-blue-600">🧪</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Test</h4>
                            <p class="text-sm text-gray-500">Page de test</p>
                        </div>
                    </a>

                    <a href="/" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-gray-600">🏠</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Accueil</h4>
                            <p class="text-sm text-gray-500">Retour à l'accueil</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong>Erreur :</strong> Aucun joueur associé à votre compte. Contactez l'administrateur.
        </div>
        @endif
    </div>
</div>
</body>
</html>
