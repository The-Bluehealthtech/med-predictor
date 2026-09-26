<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FIT - Football Intelligence & Tracking') }} - {{ ucfirst($footballType) }} Medical</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('/css/fifa-design-system.css') }}">
    <script src="{{ mix('/js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">FIT</span>
                                </div>
                                <div class="ml-3">
                                    <h1 class="text-2xl font-bold text-gray-900">
                                        {{ ucfirst($footballType) }} Medical Management
                                    </h1>
                                    <p class="text-sm text-gray-600">Health records and medical clearances</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/{{ $footballType }}/dashboard" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Back to Dashboard</a>
                        <a href="/" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Change Format</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Medical Module</h2>
                <p class="text-gray-600 mb-4">
                    This is the medical management module for {{ ucfirst($footballType) }} football.
                    Here you can manage health records, medical clearances, and fitness assessments.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="font-medium text-green-900">Medical Clearances</h3>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['activeClearances'] ?? 0 }}</p>
                        <p class="text-sm text-green-700">Valid clearances</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <h3 class="font-medium text-yellow-900">Pending Assessments</h3>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pendingAssessments'] ?? 0 }}</p>
                        <p class="text-sm text-yellow-700">Awaiting medical review</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <h3 class="font-medium text-red-900">Medical Suspensions</h3>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['medicalSuspensions'] ?? 0 }}</p>
                        <p class="text-sm text-red-700">Temporarily suspended</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="flex space-x-4">
                        <a href="{{ route('health-records.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            New Medical Record
                        </a>
                        <a href="{{ route('medical-predictions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Medical Prediction
                        </a>
                        <a href="{{ route('health-records.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Health Report
                        </a>
                        <button onclick="showAthleteSelector()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            View Athlete Profile
                        </button>
                    </div>
                </div>

                <!-- Player Records List -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Player Medical Records</h3>
                    
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h4 class="text-lg font-medium text-gray-900">All Players</h4>
                                <div class="flex space-x-2">
                                    <input type="text" id="playerSearch" placeholder="Search players..." 
                                           class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <div id="playerRecordsList" class="divide-y divide-gray-200">
                            @forelse($players as $player)
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <span class="text-blue-600 font-bold text-lg">
                                                        {{ $player->name ? substr($player->name, 0, 1) : ($player->first_name ? substr($player->first_name, 0, 1) : 'P') }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">
                                                        {{ $player->name ?? ($player->first_name . ' ' . $player->last_name) ?? 'Unknown Player' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $player->team->name ?? $player->club->name ?? 'No Team' }} • 
                                                        FIFA ID: {{ $player->fifa_id ?? $player->fifa_connect_id ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        Last updated: {{ $player->updated_at ? $player->updated_at->format('Y-m-d') : 'Unknown' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a href="/modules/medical/athlete/{{ $player->id }}" 
                                                   class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-md text-sm font-medium hover:bg-blue-50 transition-colors">
                                                    👁️ Voir
                                                </a>
                                                <a href="/modules/medical/athlete/{{ $player->id }}/edit" 
                                                   class="text-indigo-600 hover:text-indigo-900 px-3 py-1 rounded-md text-sm font-medium hover:bg-indigo-50 transition-colors">
                                                    ✏️ Modifier
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            @empty
                            <div class="text-center py-8">
                                    <div class="text-gray-400 mb-4">
                                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No players found</h3>
                                    <p class="text-gray-600 mb-4">No player records are available at the moment.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if($players->hasPages())
                <div class="mt-4">
                    {{ $players->links() }}
                </div>
                @endif

                <!-- Athlete Selector Modal -->
                <div id="athleteSelectorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                    <div class="flex items-center justify-center min-h-screen">
                        <div class="bg-white rounded-lg p-6 w-96 max-w-md">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Select Athlete</h3>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Athlete</label>
                                <input type="text" id="athleteSearch" placeholder="Enter athlete name..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div id="athleteList" class="max-h-60 overflow-y-auto">
                                <!-- Athlete list will be populated here -->
                            </div>
                            <div class="flex justify-end space-x-3 mt-4">
                                <button onclick="hideAthleteSelector()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                // Search functionality
                document.getElementById('playerSearch').addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const playerItems = document.querySelectorAll('#playerRecordsList > div');
                    
                    playerItems.forEach(item => {
                        const playerName = item.textContent.toLowerCase();
                        if (playerName.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });

                function showAthleteSelector() {
                    document.getElementById('athleteSelectorModal').classList.remove('hidden');
                    loadAthletes();
                }

                function hideAthleteSelector() {
                    document.getElementById('athleteSelectorModal').classList.add('hidden');
                }

                function loadAthletes() {
                    const athleteList = document.getElementById('athleteList');
                    const searchInput = document.getElementById('athleteSearch');

                    athleteList.innerHTML = '<p class="text-sm text-gray-500 p-2">Chargement...</p>';

                    fetch('/api/players', { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(json => {
                            const players = (json && json.success && Array.isArray(json.data)) ? json.data : [];
                            renderAthletes(players);

                            searchInput.oninput = function(e) {
                                const term = e.target.value.toLowerCase();
                                const filtered = players.filter(p => {
                                    const name = (p.name || `${p.first_name || ''} ${p.last_name || ''}`).toLowerCase();
                                    return name.includes(term);
                                });
                                renderAthletes(filtered);
                            };
                        })
                        .catch(() => {
                            athleteList.innerHTML = '<p class="text-sm text-red-500 p-2">Impossible de charger la liste des joueurs.</p>';
                        });

                    function renderAthletes(players) {
                        athleteList.innerHTML = '';
                        if (players.length === 0) {
                            athleteList.innerHTML = '<p class="text-sm text-gray-500 p-2">Aucun joueur trouvé.</p>';
                            return;
                        }
                        players.forEach(athlete => {
                            const name = athlete.name || `${athlete.first_name || ''} ${athlete.last_name || ''}`.trim();
                            const team = (athlete.club && athlete.club.name) ? athlete.club.name : '';
                            const div = document.createElement('div');
                            div.className = 'p-2 hover:bg-gray-100 cursor-pointer rounded';
                            div.innerHTML = `
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">${name}</span>
                                    <span class="text-sm text-gray-500">${team}</span>
                                </div>
                            `;
                            div.onclick = () => {
                                window.location.href = `/modules/medical/athlete/${athlete.id}`;
                            };
                            athleteList.appendChild(div);
                        });
                    }
                }
                </script>

                <div class="mt-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Recent Medical Activities</h3>
                    <div class="space-y-3">
                        @forelse($recentActivities as $activity)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 rounded-full {{ $activity->status === 'verified' ? 'bg-green-500' : ($activity->status === 'false_positive' ? 'bg-red-500' : 'bg-yellow-500') }}"></div>
                            <span class="text-sm text-gray-700">
                                Prédiction {{ $activity->status }} ({{ $activity->predicted_condition ?? 'évaluation' }}) pour {{ $activity->player?->full_name ?? 'joueur inconnu' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $activity->prediction_date?->diffForHumans() ?? '' }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">Aucune activité médicale récente.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 