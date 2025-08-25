<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Secrétariat Médical') - Med Predictor</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-user-md text-blue-600 mr-2"></i>
                            Secrétariat Médical
                        </h1>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-700">
                        <i class="fas fa-user-circle mr-1"></i>
                        {{ auth()->user()->name }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar et Contenu Principal -->
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <nav class="mt-5 px-2">
                <a href="{{ route('secretary.dashboard') }}" 
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('secretary.dashboard') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                
                <a href="{{ route('secretary.appointments.index') }}" 
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('secretary.appointments.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    Rendez-vous
                </a>
                
                <a href="{{ route('secretary.documents.index') }}" 
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('secretary.documents.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-file-medical mr-3"></i>
                    Documents
                </a>
                
                <a href="{{ route('secretary.athletes.search') }}" 
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('secretary.athletes.*') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-users mr-3"></i>
                    Athlètes
                </a>
                
                <a href="{{ route('secretary.stats') }}" 
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('secretary.stats') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    Statistiques
                </a>
            </nav>
        </div>

        <!-- Contenu Principal -->
        <div class="flex-1 overflow-auto">
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Configuration globale pour les requêtes AJAX
        window.axios = {
            defaults: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }
        };

        // Fonction utilitaire pour les requêtes AJAX
        window.apiRequest = async function(url, options = {}) {
            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...options.headers
                },
                ...options
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Une erreur est survenue');
            }

            return response.json();
        };

        // Fonction pour afficher les notifications
        window.showNotification = function(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        };
    </script>

    @stack('scripts')
</body>
</html> 