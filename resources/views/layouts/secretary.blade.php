<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('secretary.page_title')) - FIT</title>
    <link rel="icon" type="image/png" href="{{ asset('images/fit-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fit-logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100" style="padding-top: 8rem;">
        {{-- Même haut de page que les autres modules (layouts.app). --}}
        @include('partials.top-bar')

        <main>
            @include('partials.back-links')

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col lg:flex-row gap-6">
                <!-- Menu du secrétariat médical -->
                <aside class="lg:w-60 shrink-0">
                    <nav class="bg-white rounded-2xl border border-slate-200 shadow-sm p-2 lg:sticky lg:top-24" aria-label="Secrétariat médical">
                        <a href="{{ route('secretary.dashboard') }}"
                           @if(request()->routeIs('secretary.dashboard')) aria-current="page" @endif
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('secretary.dashboard') ? 'bg-blue-100 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fas fa-tachometer-alt mr-3" aria-hidden="true"></i>
                            {{ __('secretary.nav_dashboard') }}
                        </a>
                        <div class="mt-4 px-3 text-xs uppercase tracking-wider text-gray-400 font-semibold">Flux médical</div>
                        <a href="{{ route('secretary.dashboard') }}#new-appointment-panel"
                           class="group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-calendar-plus mr-3" aria-hidden="true"></i>
                            Nouveau rendez-vous
                        </a>
                        <a href="{{ route('secretary.dashboard') }}#patient-flow"
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-users mr-3" aria-hidden="true"></i>
                            Parcours patients
                        </a>
                    </nav>
                </aside>

                <!-- Contenu principal -->
                <div class="flex-1 min-w-0">
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
                </div>
            </div>
        </main>
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
                throw new Error(error.message || @json(__('secretary.generic_error')));
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