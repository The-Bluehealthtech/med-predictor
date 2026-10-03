{{-- Boutons « Retour au tableau de bord » et « Modules » du haut de page (sauf sur les tableaux de bord). --}}
<!-- Back to Dashboard Button -->
@auth
    @php
        $dashboardRoute = match(auth()->user()->role) {
            'system_admin' => 'dashboard',
            'association_admin' => 'dashboard',
            'association_registrar' => 'dashboard',
            'association_medical' => 'dashboard',
            'club_admin' => 'dashboard',
            'club_manager' => 'dashboard',
            'club_medical' => 'dashboard',
            'player' => 'player-dashboard',
            'referee' => 'referee.dashboard',
            'secretary' => 'secretary.dashboard',
            'admin' => 'dashboard',
            default => 'dashboard'
        };
        $dashboardRoute = Route::has($dashboardRoute) ? $dashboardRoute : 'dashboard';
        
        $isDashboardPage = request()->routeIs([
            'dashboard',
            'back-office.dashboard',
            'club-management.dashboard',
            'player-dashboard',
            'referee.dashboard',
            'healthcare.dashboard',
            'medical-predictions.dashboard',
            'player-registration.dashboard',
            'user-management.dashboard',
            'secretary.dashboard'
        ]);
    @endphp
    
    @if(!$isDashboardPage)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            <div class="flex space-x-4">
                <a href="{{ route($dashboardRoute) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('dashboard.back_to_dashboard') }}
                </a>
                <a href="{{ route('modules.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    {{ __('competition_management.back_modules') }}
                </a>
            </div>
        </div>
    @endif
@endauth
