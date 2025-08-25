@php
    $user = auth()->user();
@endphp

<!-- Main Navigation Menu - Fixed at top -->
<nav class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 px-4 py-3 shadow-sm z-50 nav-bar" style="z-index: 1000 !important; position: fixed !important;">
    <div class="flex flex-col sm:flex-row items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center space-x-3 mb-2 sm:mb-0">
            <img src="{{ asset('images/logos/fit.png') }}" alt="FIT Logo" style="height:60px;width:auto;margin-right:0.75rem;" class="inline-block align-middle">
        </div>
        <!-- Menus principaux -->
        <div class="flex flex-wrap gap-2 sm:gap-6 items-center justify-center">
            <!-- Admin -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="px-3 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 hover:text-blue-700 transition-colors">{{ __('navigation.admin') }}</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                    <a href="{{ route('user-management.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.user_management') }}</a>
                    @if($user && in_array($user->role, ['system_admin', 'association_admin', 'association_registrar']))
                        <a href="{{ route('admin.account-requests.index') }}" class="block px-4 py-2 hover:bg-blue-50 font-semibold text-blue-700">{{ __('navigation.account_requests') }}</a>
                    @endif
                    <a href="{{ route('role-management.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.role_management') }}</a>
                    <a href="{{ route('audit-trail.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.audit_trail') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('logs.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.logs') }}</a>
                    <a href="{{ route('system-status.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.system_status') }}</a>
                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.settings') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('license-types.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.license_types') }}</a>
                    <a href="{{ route('content.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.content_management') }}</a>
                    <a href="{{ route('stakeholder-gallery.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.gallery') }}</a>
                </div>
            </div>
            <!-- Club Management -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="px-3 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 hover:text-blue-700 transition-colors">{{ __('navigation.club_management') }}</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                    <a href="{{ route('players.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.players') }}</a>
                    <a href="{{ route('player-registration.create') }}" class="block px-4 py-2 hover:bg-blue-50 font-semibold text-blue-700">{{ __('navigation.register_player') }}</a>
                    <a href="{{ route('club.player-licenses.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.player_license_status') }}</a>
                    <a href="{{ route('player-passports.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.player_passports') }}</a>
                    <a href="{{ route('health-records.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.player_health') }}</a>
                    <a href="{{ route('performances.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.performances') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('teams.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.teams') }}</a>
                    <a href="{{ route('club-player-assignments.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.player_assignments') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('match-sheet.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.match_sheets') }}</a>
                    <a href="{{ route('transfers.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.transfers') }}</a>
                    <a href="{{ route('performance-recommendations.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.recommendations') }}</a>
                </div>
            </div>
            <!-- Association Management -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="px-3 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 hover:text-blue-700 transition-colors">{{ __('navigation.association_management') }}</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                    <a href="{{ route('competitions.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.competitions') }}</a>
                    <a href="{{ route('fixtures.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.fixtures') }}</a>
                    <a href="{{ route('rankings.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.rankings') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('seasons.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.seasons') }}</a>
                    <a href="{{ route('federations.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.federations') }}</a>
                    <a href="{{ route('registration-requests.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.registration_requests') }}</a>
                    <a href="{{ route('licenses.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.licenses') }}</a>
                    <a href="{{ route('player-licenses.index') }}" class="block px-4 py-2 hover:bg-blue-50 font-semibold text-blue-700">{{ __('navigation.player_license_requests') }}</a>
                    <a href="{{ route('contracts.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.contracts') }}</a>
                </div>
            </div>
            <!-- FIFA -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="px-3 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 hover:text-blue-700 transition-colors">{{ __('navigation.fifa') }}</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                    <a href="{{ route('fifa.dashboard') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.fifa_dashboard') }}</a>
                    <a href="{{ route('fifa.connectivity') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.fifa_connect') }}</a>
                    <a href="{{ route('fifa.sync-dashboard') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.sync_dashboard') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('fifa.contracts') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.fifa_contracts') }}</a>
                    <a href="{{ route('fifa.analytics') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.fifa_analytics') }}</a>
                    <a href="{{ route('fifa.statistics') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.fifa_statistics') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('daily-passport.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.daily_passports') }}</a>
                    <a href="{{ route('data-sync.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.data_sync') }}</a>
                    <a href="{{ route('fifa.players.search') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.player_search') }}</a>
                </div>
            </div>
            <!-- Device Connections -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="px-3 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 hover:text-blue-700 transition-colors">{{ __('navigation.device_connections') }}</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                    <a href="{{ route('device-connections.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.device_dashboard') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('apple-health-kit.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.apple_health_kit') }}</a>
                    <a href="{{ route('catapult-connect.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.catapult_connect') }}</a>
                    <a href="{{ route('garmin-connect.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.garmin_connect') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('device-connections.oauth2.tokens') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.oauth2_tokens') }}</a>
                </div>
            </div>
            <!-- Healthcare -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="px-3 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 hover:text-blue-700 transition-colors">{{ __('navigation.healthcare') }}</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                    <a href="{{ route('healthcare.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.healthcare_dashboard') }}</a>
                    <a href="{{ route('healthcare.predictions') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.medical_predictions') }}</a>
                    <a href="{{ route('healthcare.export') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.export_data') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('health-records.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.health_records') }}</a>
                    <a href="{{ route('pcma.dashboard') }}" class="block px-4 py-2 hover:bg-blue-50">📋 PCMA Management</a>
                    <a href="{{ route('medical-predictions.dashboard') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.prediction_models') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('appointments.index') }}" class="block px-4 py-2 hover:bg-blue-50 font-semibold text-blue-700">📅 {{ __('navigation.appointments') }}</a>
                    <a href="{{ route('visits.index') }}" class="block px-4 py-2 hover:bg-blue-50 font-semibold text-blue-700">🏥 {{ __('navigation.visits') }}</a>
                    <a href="{{ route('documents.index') }}" class="block px-4 py-2 hover:bg-blue-50 font-semibold text-blue-700">📄 {{ __('navigation.documents') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('portal.dashboard') }}" class="block px-4 py-2 hover:bg-blue-50 font-semibold text-green-700">🏃‍♂️ Portail Athlète</a>
                    <a href="{{ route('secretary.dashboard') }}" class="block px-4 py-2 hover:bg-blue-50 font-semibold text-purple-700">🏥 Secrétariat Médical</a>
                </div>
            </div>
            <!-- Referee -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="px-3 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 hover:text-blue-700 transition-colors">{{ __('navigation.referee') }}</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                    <a href="{{ route('referee.dashboard') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.dashboard') }}</a>
                    <a href="{{ route('referee.match-assignments') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.match_assignments') }}</a>
                    <a href="{{ route('referee.competition-schedule') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.competition_schedule') }}</a>
                    <div class="border-t my-1"></div>
                    <a href="{{ route('referee.create-match-report') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.create_match_report') }}</a>
                    <a href="{{ route('referee.performance-stats') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.performance_stats') }}</a>
                    <a href="{{ route('referee.settings') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.settings') }}</a>
                </div>
            </div>
            <!-- Performance Analytics -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="px-3 py-2 rounded hover:bg-blue-100 font-semibold text-gray-700 hover:text-blue-700 transition-colors">{{ __('navigation.performance_analytics') }}</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                    <a href="{{ route('performance-recommendations.index') }}" class="block px-4 py-2 hover:bg-blue-50">{{ __('navigation.recommendations') }}</a>
                    <a href="{{ route('performances.analytics') }}" class="block px-4 py-2 hover:bg-blue-50">📊 {{ __('navigation.performance_analytics') }}</a>
                    <a href="{{ route('performances.trends') }}" class="block px-4 py-2 hover:bg-blue-50">📈 {{ __('navigation.performance_trends') }}</a>
                    <a href="{{ route('alerts.performance') }}" class="block px-4 py-2 hover:bg-blue-50">⚠️ {{ __('navigation.performance_alerts') }}</a>
                </div>
            </div>
        </div>
        
        <!-- User Menu -->
        <div class="flex items-center space-x-4 mt-2 sm:mt-0">
            @auth
                <!-- Notifications -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-blue-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 1 6 6v3.25l-1.586 1.586A2 2 0 0 1 13.414 16H4.586a2 2 0 0 1-1.414-1.414L1.5 13.25V9.75a6 6 0 0 1 6-6z" />
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 z-20 bg-white border rounded shadow-lg mt-2 min-w-[300px] max-h-[400px] overflow-y-auto">
                        <div class="p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                        </div>
                        <div class="p-2">
                            <!-- Sample notifications -->
                            <div class="p-3 hover:bg-gray-50 rounded">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                    <div class="flex-1">
                                        <div class="text-sm">Licence validée - <a href="#" class="text-blue-600 hover:underline">Voir la demande</a></div>
                                        <div class="text-xs text-gray-500 mt-1">Il y a 2 heures</div>
                                    </div>
                                    <button class="text-xs text-gray-400 hover:text-gray-600">×</button>
                                </div>
                            </div>
                            
                            <div class="p-3 hover:bg-gray-50 rounded">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                    <div class="flex-1">
                                        <div class="text-sm">Nouveau dossier médical créé pour Athlète #1234</div>
                                        <div class="text-xs text-gray-500 mt-1">Il y a 4 heures</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-3 hover:bg-gray-50 rounded">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                    <div class="flex-1">
                                        <div class="text-sm">Licence en attente de validation</div>
                                        <div class="text-xs text-gray-500 mt-1">Il y a 6 heures</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-t">
                            <a href="{{ route('profile.show') }}#notifications" class="text-blue-600 hover:underline text-xs">{{ __('navigation.view_all_notifications') }}</a>
                        </div>
                    </div>
                </div>
                
                <!-- User Profile -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center space-x-2 px-3 py-2 rounded hover:bg-blue-100 text-gray-700 hover:text-blue-700 transition-colors">
                        <span>{{ $user->name ?? 'User' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 z-20 bg-white border rounded shadow-lg mt-2 min-w-[200px]">
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 hover:bg-blue-50">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-blue-50">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                    Login
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- Spacer for fixed navigation -->
<div class="h-20"></div> 