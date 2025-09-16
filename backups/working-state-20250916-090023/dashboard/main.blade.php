<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FIT - Football Intelligence & Tracking') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">FIT Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Bienvenue, {{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Bienvenue -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Dashboard Principal - FIT
                </h2>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Bienvenue, {{ $user->name }} !
                </h3>
                <p class="text-gray-600">
                    Accédez aux différents modules de la plateforme FIT pour gérer vos équipes et performances.
                </p>
            </div>
        </div>

        <!-- Modules disponibles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($modules as $key => $module)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-{{ $module['color'] }}-100 rounded-lg flex items-center justify-center">
                                @if($module['icon'] === 'flag')
                                <svg class="w-6 h-6 text-{{ $module['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                                </svg>
                                @elseif($module['icon'] === 'calendar')
                                <svg class="w-6 h-6 text-{{ $module['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $module['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $module['description'] }}</p>
                        </div>
                    </div>
                    
                    <a href="{{ $module['route'] }}" 
                       class="inline-flex items-center px-4 py-2 bg-{{ $module['color'] }}-600 hover:bg-{{ $module['color'] }}-700 text-white text-sm font-medium rounded-md transition-colors"
                       data-auto-key="auto.key1">
                        <span>Accéder au module</span>
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Statistiques rapides -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques Rapides</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Player::count() }}</div>
                        <div class="text-sm text-gray-500">Joueurs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ \App\Models\Club::count() }}</div>
                        <div class="text-sm text-gray-500">Clubs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\Competition::count() }}</div>
                        <div class="text-sm text-gray-500">Compétitions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ \App\Models\HealthRecord::count() }}</div>
                        <div class="text-sm text-gray-500">Dossiers médicaux</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('players.index') }}" class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" data-auto-key="auto.key2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-medium text-blue-900">Gérer les joueurs</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('performances.index') }}" class="block p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors" data-auto-key="auto.key1">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="font-medium text-green-900">Performances</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('competitions.index') }}" class="block p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors" data-auto-key="auto.key2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="font-medium text-yellow-900">Compétitions</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Gestion des clés automatiques sur le dashboard principal
        document.addEventListener('DOMContentLoaded', function() {
            const actionButtons = document.querySelectorAll('[data-auto-key]');
            actionButtons.forEach(button => {
                const autoKey = button.getAttribute('data-auto-key');
                
                // Ajouter un indicateur visuel pour les clés automatiques
                const keyIndicator = document.createElement('span');
                keyIndicator.className = 'ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded';
                keyIndicator.textContent = autoKey;
                
                // Ajouter l'indicateur au bouton
                const buttonContent = button.querySelector('.flex.items-center');
                if (buttonContent) {
                    buttonContent.appendChild(keyIndicator);
                }
                
                // Gestionnaire d'événements pour les clés automatiques
                button.addEventListener('click', function(e) {
                    console.log('Action dashboard principal avec clé automatique:', autoKey);
                });
            });
        });
    </script>
</body>
</html> 