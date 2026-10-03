{{-- Haut de page commun (layouts app et secrétariat) : langue, état de connexion, déconnexion, notifications. --}}
<!-- Connection Status & Notification Bell -->
<div class="fixed top-4 right-8 z-50 flex items-center space-x-4">
    <x-language-switcher />
    <!-- Connection Status Button -->
    @php
        // Vérification globale de l'état de connexion
        $isAuthenticated = Auth::check();
        $isSessionValid = true; // Si Auth::check() est true, la session Laravel est valide
        $isTenantActive = true;
        $user = null;
        
        if ($isAuthenticated) {
            try {
                $user = Auth::user();
                if ($user && $user->exists) {
                    // Vérification du tenant si applicable
                    if (method_exists($user, 'getCurrentTenant') && $user->getCurrentTenant()) {
                        $tenant = $user->getCurrentTenant();
                        $isTenantActive = $tenant && $tenant->is_active;
                    }
                    
                    // Vérifier si la session n'a pas expiré (optionnel)
                    $sessionLifetime = config('session.lifetime') * 60;
                    $lastActivity = session('last_activity', time());
                    if (time() - $lastActivity > $sessionLifetime) {
                        $isSessionValid = false;
                    }
                } else {
                    $isSessionValid = false;
                }
            } catch (Exception $e) {
                $isSessionValid = false;
                $isTenantActive = false;
            }
        }
        
        // Déterminer l'état final avec logique simplifiée
        if (!$isAuthenticated) {
            $connectionStatus = 'not_connected';
            $statusColor = 'gray';
            $statusText = 'Non connecté';
        } elseif ($isAuthenticated && $isSessionValid && $isTenantActive) {
            $connectionStatus = 'connected';
            $statusColor = 'green';
            $statusText = 'Connecté';
        } elseif ($isAuthenticated && (!$isSessionValid || !$isTenantActive)) {
            $connectionStatus = 'disconnected';
            $statusColor = 'red';
            $statusText = 'Session expirée';
        } else {
            $connectionStatus = 'not_connected';
            $statusColor = 'gray';
            $statusText = 'Non connecté';
        }
    @endphp
    
    <div class="flex items-center space-x-2 bg-white rounded-lg shadow-sm border border-gray-200 px-3 py-2">
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-{{ $statusColor }}-500 rounded-full {{ $connectionStatus === 'connected' ? 'animate-pulse' : '' }}" 
                 title="{{ $statusText }} - Session: {{ $isSessionValid ? 'Valide' : 'Invalide' }} | Tenant: {{ $isTenantActive ? 'Actif' : 'Inactif' }}"></div>
            <span class="text-xs text-{{ $statusColor }}-600 font-medium">{{ $statusText }}</span>
        </div>
        @if($isAuthenticated && $user)
            <span class="text-gray-400">|</span>
            <span class="text-sm font-semibold text-gray-700">{{ $user->name ?? 'User' }}</span>
            <span class="text-xs text-gray-500">({{ ucfirst($user->role ?? 'user') }})</span>
        @endif
    </div>

    @include('partials.logout-button', ['variant' => 'chip'])

    <!-- Notification Bell -->
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="relative focus:outline-none hover:bg-blue-50 rounded-full p-2 transition-colors duration-200">
            <svg class="w-7 h-7 text-blue-700 hover:text-blue-800 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @auth
                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                @if($unread > 0)
                    <span class="absolute top-0 right-0 inline-block w-4 h-4 bg-red-600 text-white text-xs font-bold rounded-full text-center animate-pulse">{{ $unread }}</span>
                @endif
            @else
                <!-- Show notification icon for non-authenticated users with pulse animation -->
                <span class="absolute top-0 right-0 inline-block w-3 h-3 bg-blue-500 rounded-full animate-pulse"></span>
            @endauth
        </button>
        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50" style="display: none;">
            <div class="p-4 border-b font-semibold text-blue-700">
                @auth
                    {{ __('dashboard.notifications') }}
                @else
                    Notifications
                @endauth
            </div>
            <ul class="max-h-80 overflow-y-auto">
                @auth
                    @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notification)
                        <li class="border-b {{ $notification->read_at ? 'text-gray-500' : 'text-blue-900 font-semibold' }}">
                            <a href="{{ Route::has('notifications.open') ? route('notifications.open', $notification->id) : '#' }}" class="block px-4 py-2 hover:bg-blue-50">
                                {{ $notification->data['message'] ?? 'Notification' }}
                                <span class="block text-xs font-normal text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-2 text-gray-400">{{ __('dashboard.no_notifications') }}</li>
                    @endforelse
                @else
                    <li class="px-4 py-2 text-gray-400">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-2">{{ __('Connectez-vous pour voir vos notifications') }}</p>
                            <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                                Se connecter
                            </a>
                        </div>
                    </li>
                @endauth
            </ul>
            @auth
            <div class="p-2 text-center">
                <a href="{{ Route::has('notifications.index') ? route('notifications.index') : '#' }}" class="text-blue-600 hover:underline text-sm">{{ __('dashboard.view_all_notifications') }}</a>
            </div>
            @endauth
        </div>
    </div>
</div>
