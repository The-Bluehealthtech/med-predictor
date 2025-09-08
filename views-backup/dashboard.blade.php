
<x-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('dashboard.title') }}
        </h2>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- FIT BI Card (Business Intelligence) -->
            <div id="fit-bi-card" class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">{{ __('dashboard.fit_intelligence') }}</h3>
                    <span class="text-gray-400 text-sm">{{ __('dashboard.live') }}</span>
                </div>
                <div class="mb-2 text-green-700 font-semibold">[FIT BI card is rendered]</div>
                <div id="fit-kpis" class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-3xl font-bold" id="active-players">-</div>
                        <div class="text-gray-600">{{ __('dashboard.active_players') }}</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold" id="injury-rate">-</div>
                        <div class="text-gray-600">{{ __('dashboard.injury_rate') }}</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold" id="avg-fitness-score">-</div>
                        <div class="text-gray-600">{{ __('dashboard.avg_fitness_score') }}</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold" id="players-at-risk">-</div>
                        <div class="text-gray-600">{{ __('dashboard.players_at_risk') }}</div>
                    </div>
                </div>
                <div class="mt-4 text-right">
                    <button class="text-blue-600 hover:underline" onclick="toggleFitDetails()">{{ __('dashboard.view_details') }}</button>
                </div>
                <div id="fit-details" class="hidden mt-6">
                    <div class="mb-2">
                        <span class="font-semibold">{{ __('dashboard.top_clubs') }}:</span>
                        <span id="top-clubs">-</span>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">{{ __('dashboard.recent_health_alerts') }}:</span>
                        <span id="recent-alerts">-</span>
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">{{ __('dashboard.health_record_compliance') }}:</span>
                        <span id="compliance">-</span>
                    </div>
                </div>
            </div>
            <script>
                function toggleFitDetails() {
                    const details = document.getElementById('fit-details');
                    details.classList.toggle('hidden');
                }
                function fetchFitKpis() {
                    fetch('/api/fit/kpis')
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('active-players').textContent = data.active_players;
                            document.getElementById('injury-rate').textContent = data.injury_rate + '%';
                            document.getElementById('avg-fitness-score').textContent = data.avg_fitness_score;
                            document.getElementById('players-at-risk').textContent = data.players_at_risk;
                            document.getElementById('top-clubs').textContent = data.top_clubs.map(c => c.name + ' (' + c.active_players_count + ')').join(', ');
                            document.getElementById('recent-alerts').textContent = data.recent_alerts;
                            document.getElementById('compliance').textContent = data.compliance + '%';
                        });
                }
                document.addEventListener('DOMContentLoaded', fetchFitKpis);
            </script>
            <!-- End FIT BI Card -->

            <!-- User Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Photo de profil de l'utilisateur -->
                            @if(auth()->user()->hasProfilePicture())
                                <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                                     alt="{{ auth()->user()->getProfilePictureAlt() }}" 
                                     class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 shadow-lg">
                            @else
                                <div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center text-white text-2xl font-semibold border-4 border-gray-200 shadow-lg">
                                    {{ auth()->user()->getInitials() }}
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('dashboard.welcome') }}, {{ auth()->user() ? auth()->user()->name : __('dashboard.guest') }}!</h3>
                                <p class="text-gray-600">{{ auth()->user() ? auth()->user()->getRoleDisplay() : __('dashboard.guest') }} at {{ auth()->user() ? auth()->user()->getEntityName() : __('dashboard.system') }}</p>
                                <p class="text-sm text-gray-500">{{ __('common.fifa_connect_id') }}: {{ auth()->user() ? auth()->user()->fifa_connect_id : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ __('dashboard.last_login') }}</div>
                            <div class="text-sm font-medium">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : __('dashboard.first_time') }}</div>
                        </div>
                    </div>
                    
                    <!-- Bouton Commencez pour System Administrator -->
                    @if(auth()->user() && auth()->user()->role === 'system_admin')
                    <div class="mt-6 flex justify-center">
                        <a href="{{ route('modules.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                            Commencez
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Module KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Player Registration KPI Card -->
                @if(auth()->user() && auth()->user()->canAccessModule('player_registration'))
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Player Registration</h3>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <p class="text-blue-100 mb-4">Player registration statistics and FIFA Connect integration</p>
                        
                        <!-- KPI Metrics -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $stats['total_players'] ?? 0 }}</div>
                                <div class="text-xs text-blue-200">Total Players</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $licenseStatsByClub->sum('total') ?? 0 }}</div>
                                <div class="text-xs text-blue-200">Total Licenses</div>
                            </div>
                        </div>
                        
                        <!-- License Status Chart -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-sm font-semibold mb-2">License Status</div>
                            <div class="space-y-2">
                                @php
                                    $totalLicenses = $licenseStatsByClub->sum('total') ?: 1;
                                    $pendingPercent = round(($licenseStatsByClub->sum('pending') / $totalLicenses) * 100);
                                    $activePercent = round(($licenseStatsByClub->sum('active') / $totalLicenses) * 100);
                                    $rejectedPercent = round(($licenseStatsByClub->sum('revoked') / $totalLicenses) * 100);
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Pending</span>
                                        <span class="font-bold text-blue-800">{{ $licenseStatsByClub->sum('pending') ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-yellow-400 h-1 rounded-full" style="width: {{ $pendingPercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Active</span>
                                        <span class="font-bold text-blue-800">{{ $licenseStatsByClub->sum('active') ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-green-400 h-1 rounded-full" style="width: {{ $activePercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Rejected</span>
                                        <span class="font-bold text-blue-800">{{ $licenseStatsByClub->sum('revoked') ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-red-400 h-1 rounded-full" style="width: {{ $rejectedPercent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Competition Management KPI Card -->
                @if(auth()->user() && auth()->user()->canAccessModule('competition_management'))
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Competition Management</h3>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <p class="text-green-100 mb-4">Competition and match statistics</p>
                        
                        <!-- KPI Metrics -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $stats['active_competitions'] ?? 0 }}</div>
                                <div class="text-xs text-green-200">Active Competitions</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $stats['total_competitions'] ?? 0 }}</div>
                                <div class="text-xs text-green-200">Total Competitions</div>
                            </div>
                        </div>
                        
                        <!-- Competition Status Chart -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-sm font-semibold mb-2">Competition Status</div>
                            <div class="space-y-2">
                                @php
                                    $totalCompetitions = $stats['total_competitions'] ?: 1;
                                    $activePercent = round(($stats['active_competitions'] / $totalCompetitions) * 100);
                                    $upcomingPercent = round(($stats['upcoming_competitions'] / $totalCompetitions) * 100);
                                    $completedPercent = round(($stats['completed_competitions'] / $totalCompetitions) * 100);
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Active</span>
                                        <span class="font-bold text-blue-800">{{ $stats['active_competitions'] ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-green-400 h-1 rounded-full" style="width: {{ $activePercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Upcoming</span>
                                        <span class="font-bold text-blue-800">{{ $stats['upcoming_competitions'] ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-blue-400 h-1 rounded-full" style="width: {{ $upcomingPercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Completed</span>
                                        <span class="font-bold text-blue-800">{{ $stats['completed_competitions'] ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-gray-400 h-1 rounded-full" style="width: {{ $completedPercent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Healthcare KPI Card -->
                @if(auth()->user() && auth()->user()->canAccessModule('healthcare'))
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Healthcare</h3>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <p class="text-red-100 mb-4">Health monitoring and medical predictions</p>
                        
                        <!-- KPI Metrics -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $stats['total_health_records'] ?? 0 }}</div>
                                <div class="text-xs text-red-200">Health Records</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $medicalAlerts->count() ?? 0 }}</div>
                                <div class="text-xs text-red-200">Medical Alerts</div>
                            </div>
                        </div>
                        
                        <!-- Health Status Chart -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-sm font-semibold mb-2">Health Status</div>
                            <div class="space-y-2">
                                @php
                                    $totalHealthRecords = $stats['total_health_records'] ?: 1;
                                    $activePercent = round(($healthRecordsByStatus['active'] / $totalHealthRecords) * 100);
                                    $pendingPercent = round(($healthRecordsByStatus['pending'] / $totalHealthRecords) * 100);
                                    $archivedPercent = round(($healthRecordsByStatus['archived'] / $totalHealthRecords) * 100);
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Active Records</span>
                                        <span class="font-bold text-blue-800">{{ $healthRecordsByStatus['active'] ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-green-400 h-1 rounded-full" style="width: {{ $activePercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Pending</span>
                                        <span class="font-bold text-blue-800">{{ $healthRecordsByStatus['pending'] ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-yellow-400 h-1 rounded-full" style="width: {{ $pendingPercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Archived</span>
                                        <span class="font-bold text-blue-800">{{ $healthRecordsByStatus['archived'] ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-gray-400 h-1 rounded-full" style="width: {{ $archivedPercent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Dataset Analytics KPI Card -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Dataset Analytics</h3>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14"></path>
                            </svg>
                        </div>
                        <p class="text-purple-100 mb-4">Évaluation de la valeur et qualité des données FIFA</p>
                        
                        <!-- KPI Metrics -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">100%</div>
                                <div class="text-xs text-purple-200">Couverture</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">8.7/10</div>
                                <div class="text-xs text-purple-200">Score Valeur</div>
                            </div>
                        </div>
                        
                        <!-- Dataset Quality Chart -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-sm font-semibold mb-2">Qualité des Données</div>
                            <div class="space-y-2">
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-purple-800 font-semibold">Complétude</span>
                                        <span class="font-bold text-purple-800">92.1%</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-green-400 h-1 rounded-full" style="width: 92.1%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-purple-800 font-semibold">Précision</span>
                                        <span class="font-bold text-purple-800">89.7%</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-blue-400 h-1 rounded-full" style="width: 89.7%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-purple-800 font-semibold">Cohérence</span>
                                        <span class="font-bold text-purple-800">80.1%</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-yellow-400 h-1 rounded-full" style="width: 80.1%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="mt-4">
                            <a href="{{ route('dataset.analytics') }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-medium rounded-md transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                Accéder aux Analytics
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Audit Logs KPI Card -->
                @if(auth()->user() && (auth()->user()->isSystemAdmin() || auth()->user()->canAccessModule('audit_logs')))
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Audit Logs</h3>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v8"></path>
                            </svg>
                        </div>
                        <p class="text-purple-100 mb-4">System activity monitoring and security tracking</p>
                        
                        <!-- KPI Metrics -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $auditLogStats['total_logs'] ?? 0 }}</div>
                                <div class="text-xs text-purple-200">Total Logs</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $auditLogStats['today_logs'] ?? 0 }}</div>
                                <div class="text-xs text-purple-200">Today</div>
                            </div>
                        </div>
                        
                        <!-- Event Type Chart -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-sm font-semibold mb-2">Event Types</div>
                            <div class="space-y-2">
                                @php
                                    $userAction = $auditLogsByEventType['user_action'] ?? 0;
                                    $systemEvent = $auditLogsByEventType['system_event'] ?? 0;
                                    $securityEvent = $auditLogsByEventType['security_event'] ?? 0;
                                    $dataAccess = $auditLogsByEventType['data_access'] ?? 0;
                                    $totalEvents = $userAction + $systemEvent + $securityEvent + $dataAccess;
                                    $userActionPercent = $totalEvents > 0 ? round(($userAction / $totalEvents) * 100) : 0;
                                    $systemEventPercent = $totalEvents > 0 ? round(($systemEvent / $totalEvents) * 100) : 0;
                                    $securityEventPercent = $totalEvents > 0 ? round(($securityEvent / $totalEvents) * 100) : 0;
                                    $dataAccessPercent = $totalEvents > 0 ? round(($dataAccess / $totalEvents) * 100) : 0;
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">User Actions</span>
                                        <span class="font-bold text-blue-800">{{ $userAction }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-blue-400 h-1 rounded-full" style="width: {{ $userActionPercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">System Events</span>
                                        <span class="font-bold text-blue-800">{{ $systemEvent }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-green-400 h-1 rounded-full" style="width: {{ $systemEventPercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Security Events</span>
                                        <span class="font-bold text-blue-800">{{ $securityEvent }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-yellow-400 h-1 rounded-full" style="width: {{ $securityEventPercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Data Access</span>
                                        <span class="font-bold text-blue-800">{{ $dataAccess }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-purple-400 h-1 rounded-full" style="width: {{ $dataAccessPercent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Top Clubs By Players KPI Card -->
                @if(auth()->user() && (auth()->user()->isSystemAdmin() || auth()->user()->canAccessModule('club_management')))
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Top Clubs By Players</h3>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <p class="text-emerald-100 mb-4">Club performance rankings and player statistics</p>
                        
                        <!-- KPI Metrics -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $topClubsByPlayers->sum('total_players') ?? 0 }}</div>
                                <div class="text-xs text-emerald-200">Total Players</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ number_format($topClubsByPlayers->avg('average_rating') ?? 0, 1) }}</div>
                                <div class="text-xs text-emerald-200">Avg Rating</div>
                            </div>
                        </div>
                        
                        <!-- Top Clubs List -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-sm font-semibold mb-2">Top Performing Clubs</div>
                            <div class="space-y-2">
                                @forelse($topClubsByPlayers->take(3) as $club)
                                <div class="flex items-center justify-between p-2 bg-white bg-opacity-5 rounded">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold">{{ $loop->iteration }}</span>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-blue-800">{{ $club->name ?? 'Unknown Club' }}</div>
                                            <div class="text-xs text-emerald-200">{{ $club->players_count ?? 0 }} players</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-bold text-blue-800">{{ $club->players_count ?? 0 }}</div>
                                        <div class="text-xs text-emerald-200">Players</div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center text-emerald-200 text-xs py-2">
                                    No club data available
                                </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <!-- Performance Metrics -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3 mt-3">
                            <div class="text-sm font-semibold mb-2">Performance Metrics</div>
                            <div class="space-y-2">
                                @php
                                    $maxValue = $topClubsByPlayers->max('total_value') ?: 1;
                                    $maxRating = $topClubsByPlayers->max('average_rating') ?: 1;
                                    $avgValuePercent = round((($topClubsByPlayers->avg('total_value') ?: 0) / $maxValue) * 100);
                                    $avgRatingPercent = round((($topClubsByPlayers->avg('average_rating') ?: 0) / $maxRating) * 100);
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Avg Squad Value</span>
                                        <span class="font-bold text-blue-800">€{{ number_format($topClubsByPlayers->avg('total_value') / 1000000, 1) }}M</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-emerald-400 h-1 rounded-full" style="width: {{ $avgValuePercent }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold">Avg Player Rating</span>
                                        <span class="font-bold text-blue-800">{{ number_format($topClubsByPlayers->avg('average_rating'), 1) }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
                                        <div class="bg-blue-400 h-1 rounded-full" style="width: {{ $avgRatingPercent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Performance Analytics KPI Card -->
                @if(auth()->user() && (auth()->user()->isSystemAdmin() || auth()->user()->canAccessModule('performance')))
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Performance Analytics</h3>
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-indigo-100 mb-4">Advanced performance tracking and analytics</p>
                        
                        <!-- KPI Metrics -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ number_format($performanceData['total_performances'] ?? 0) }}</div>
                                <div class="text-xs text-indigo-200">Total Records</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $performanceData['avg_overall_score'] ?? 0 }}</div>
                                <div class="text-xs text-indigo-200">Avg Score</div>
                            </div>
                        </div>
                        
                        <!-- Performance by Type Chart (Area Chart Style) -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3 mb-3">
                            <div class="text-sm font-semibold mb-2">Performance by Category</div>
                            <div class="space-y-2">
                                @php
                                    $performanceTypes = $performanceData['performance_by_type'] ?? [];
                                    $maxScore = max($performanceTypes) ?: 1;
                                @endphp
                                @foreach($performanceTypes as $type => $score)
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-blue-800 font-semibold capitalize">{{ $type }}</span>
                                        <span class="font-bold text-blue-800">{{ round($score, 1) }}</span>
                                    </div>
                                    <div class="w-full bg-white bg-opacity-20 rounded-full h-2 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-400 to-purple-400 rounded-full transition-all duration-500" 
                                             style="width: {{ ($score / $maxScore) * 100 }}%"></div>
                                        <div class="absolute inset-0 bg-white bg-opacity-30 rounded-full" 
                                             style="width: {{ ($score / $maxScore) * 100 }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Top Performers Matrix -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3 mb-3">
                            <div class="text-sm font-semibold mb-2">Top Performers</div>
                            <div class="space-y-1">
                                @forelse($performanceData['top_performers'] ?? [] as $performer)
                                <div class="flex items-center justify-between p-1 bg-white bg-opacity-5 rounded text-xs">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-4 h-4 bg-indigo-400 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-white">{{ $loop->iteration }}</span>
                                        </div>
                                        <div>
                                            <div class="text-blue-800 font-semibold">{{ $performer['player_name'] }}</div>
                                            <div class="text-indigo-200 text-xs">{{ $performer['type'] }} • {{ $performer['date'] }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-blue-800 font-bold">{{ $performer['score'] }}</div>
                                        <div class="text-indigo-200 text-xs capitalize">{{ $performer['trend'] }}</div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center text-indigo-200 text-xs py-2">
                                    No performance data available
                                </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <!-- Performance Metrics Grid -->
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <div class="text-sm font-semibold mb-2">Key Metrics</div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="text-center p-2 bg-white bg-opacity-5 rounded">
                                    <div class="text-blue-800 font-bold">{{ number_format($performanceData['performance_metrics']['total_goals'] ?? 0) }}</div>
                                    <div class="text-indigo-200">Goals</div>
                                </div>
                                <div class="text-center p-2 bg-white bg-opacity-5 rounded">
                                    <div class="text-blue-800 font-bold">{{ number_format($performanceData['performance_metrics']['total_assists'] ?? 0) }}</div>
                                    <div class="text-indigo-200">Assists</div>
                                </div>
                                <div class="text-center p-2 bg-white bg-opacity-5 rounded">
                                    <div class="text-blue-800 font-bold">{{ $performanceData['performance_metrics']['avg_pass_accuracy'] ?? 0 }}%</div>
                                    <div class="text-indigo-200">Pass Accuracy</div>
                                </div>
                                <div class="text-center p-2 bg-white bg-opacity-5 rounded">
                                    <div class="text-blue-800 font-bold">{{ number_format(($performanceData['performance_metrics']['total_distance'] ?? 0) / 1000, 1) }}km</div>
                                    <div class="text-indigo-200">Distance</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Total Players</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $stats['total_players'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Active Competitions</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $stats['active_competitions'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Health Records</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $stats['total_health_records'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">FIFA Connect</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $stats['fifa_connect_status'] ?? 'Connected' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            <span>System initialized with FIFA Connect integration</span>
                            <span class="ml-auto text-gray-400">{{ now()->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <span>Role-based access control activated</span>
                            <span class="ml-auto text-gray-400">{{ now()->subMinutes(5)->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                            <span>Modular system architecture implemented</span>
                            <span class="ml-auto text-gray-400">{{ now()->subMinutes(10)->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Logs Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-blue-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v8" /></svg>
                        Audit Logs
                    </h3>
                    <div class="divide-y divide-gray-100">
                        @forelse(($auditLogs ?? []) as $log)
                            <div class="flex items-center py-3">
                                <!-- User Avatar/Initials -->
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg mr-4">
                                    @if(isset($log->user) && $log->user->profile_picture)
                                        <img src="{{ $log->user->profile_picture }}" alt="{{ $log->user->name }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        {{ strtoupper(substr($log->user->name ?? 'U', 0, 2)) }}
                                    @endif
                                </div>
                                <!-- Action Icon -->
                                <div class="mr-3">
                                    @php
                                        $icon = match($log->action ?? '') {
                                            'created' => 'M12 4v16m8-8H4',
                                            'updated' => 'M5 13l4 4L19 7',
                                            'deleted' => 'M6 18L18 6M6 6l12 12',
                                            default => 'M12 4v16m8-8H4',
                                        };
                                        $color = match($log->action ?? '') {
                                            'created' => 'text-green-600',
                                            'updated' => 'text-yellow-600',
                                            'deleted' => 'text-red-600',
                                            default => 'text-blue-600',
                                        };
                                    @endphp
                                    <svg class="w-6 h-6 {{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                                    </svg>
                                </div>
                                <!-- Log Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-800">{{ $log->user->name ?? 'Unknown User' }}</span>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($log->action ?? 'action') }}</span>
                                        <span class="text-gray-500">on</span>
                                        <span class="font-medium text-blue-700">{{ $log->entity ?? 'Entity' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-400 py-4 text-center">No audit logs found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Clubs By Players Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-green-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17v-2a4 4 0 014-4h10a4 4 0 014 4v2" /></svg>
                        Top Clubs By Players
                    </h3>
                    <div class="space-y-4">
                        @php
                            $maxPlayers = isset($topClubs) && count($topClubs) > 0 ? $topClubs[0]->players_count : 1;
                        @endphp
                        @forelse(($topClubs ?? []) as $club)
                            <div class="flex items-center">
                                <!-- Club Logo/Initials -->
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-lg mr-4 overflow-hidden">
                                    @if(isset($club->logo_url) && $club->logo_url)
                                        <img src="{{ $club->logo_url }}" alt="{{ $club->name }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        {{ strtoupper(substr($club->name ?? 'C', 0, 2)) }}
                                    @endif
                                </div>
                                <!-- Club Name and Bar -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-gray-800">{{ $club->name ?? 'Club' }}</span>
                                        <span class="text-sm font-bold text-green-700 ml-2">{{ $club->players_count ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-green-50 rounded h-3 mt-2 relative">
                                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded" style="width: {{ $maxPlayers > 0 ? (($club->players_count ?? 0) / $maxPlayers * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-400 py-4 text-center">No club data available.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- License Requests Per Club Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-yellow-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v8" /></svg>
                        Club License Status Dashboard
                    </h3>
                    
                    <!-- Summary Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        @php
                            $totalPending = $licenseStatsByClub ? $licenseStatsByClub->sum('pending') : 0;
                            $totalActive = $licenseStatsByClub ? $licenseStatsByClub->sum('active') : 0;
                            $totalRejected = $licenseStatsByClub ? $licenseStatsByClub->sum('revoked') : 0;
                            $totalExplanation = $licenseStatsByClub ? $licenseStatsByClub->sum('justification_requested') : 0;
                            $grandTotal = $licenseStatsByClub ? $licenseStatsByClub->sum('total') : 0;
                        @endphp
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">Pending</p>
                                    <p class="text-2xl font-bold text-yellow-900">{{ $totalPending }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 border-l-4 border-green-400 p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">Approved</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $totalActive }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">Rejected</p>
                                    <p class="text-2xl font-bold text-red-900">{{ $totalRejected }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-orange-50 border-l-4 border-orange-400 p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-orange-800">Need Info</p>
                                    <p class="text-2xl font-bold text-orange-900">{{ $totalExplanation }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Club Cards -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @forelse(($licenseStatsByClub ?? collect()) as $row)
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="text-lg font-bold text-gray-900">{{ $row['club_name'] }}</h4>
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $row['total'] }} Total
                                    </span>
                                </div>
                                
                                <!-- Status Progress Bars -->
                                <div class="space-y-3 mb-4">
                                    @if($row['pending'] > 0)
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-yellow-700 font-medium">Pending</span>
                                                <span class="text-yellow-700">{{ $row['pending'] }}</span>
                                            </div>
                                            <div class="w-full bg-yellow-200 rounded-full h-2">
                                                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ ($row['pending'] / max($row['total'], 1)) * 100 }}%"></div>
                                            </div>
                                            @if($row['avg_pending_time'])
                                                <p class="text-xs text-yellow-600 mt-1">Avg: {{ $row['avg_pending_time'] }} days</p>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($row['active'] > 0)
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-green-700 font-medium">Approved</span>
                                                <span class="text-green-700">{{ $row['active'] }}</span>
                                            </div>
                                            <div class="w-full bg-green-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($row['active'] / max($row['total'], 1)) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($row['revoked'] > 0)
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-red-700 font-medium">Rejected</span>
                                                <span class="text-red-700">{{ $row['revoked'] }}</span>
                                            </div>
                                            <div class="w-full bg-red-200 rounded-full h-2">
                                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ ($row['revoked'] / max($row['total'], 1)) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($row['justification_requested'] > 0)
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-orange-700 font-medium">Need Info</span>
                                                <span class="text-orange-700">{{ $row['justification_requested'] }}</span>
                                            </div>
                                            <div class="w-full bg-orange-200 rounded-full h-2">
                                                <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($row['justification_requested'] / max($row['total'], 1)) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Status Icons -->
                                <div class="flex space-x-2">
                                    @if($row['pending'] > 0)
                                        <div class="flex items-center text-yellow-600">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs">{{ $row['pending'] }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($row['active'] > 0)
                                        <div class="flex items-center text-green-600">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs">{{ $row['active'] }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($row['revoked'] > 0)
                                        <div class="flex items-center text-red-600">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs">{{ $row['revoked'] }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($row['justification_requested'] > 0)
                                        <div class="flex items-center text-orange-600">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs">{{ $row['justification_requested'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No license data</h3>
                                <p class="mt-1 text-sm text-gray-500">No club license data available.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-dashboard-layout>
