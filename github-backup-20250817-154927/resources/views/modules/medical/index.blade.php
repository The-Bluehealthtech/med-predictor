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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                        <p class="text-2xl font-bold text-green-600">1,156</p>
                        <p class="text-sm text-green-700">Valid clearances</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <h3 class="font-medium text-yellow-900">Pending Assessments</h3>
                        <p class="text-2xl font-bold text-yellow-600">34</p>
                        <p class="text-sm text-yellow-700">Awaiting medical review</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <h3 class="font-medium text-red-900">Medical Suspensions</h3>
                        <p class="text-2xl font-bold text-red-600">12</p>
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
                            @if(isset($players) && $players->count() > 0)
                                @foreach($players as $player)
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
                                @endforeach
                            @else
                            <div class="text-center py-8">
                                    <div class="text-gray-400 mb-4">
                                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No players found</h3>
                                    <p class="text-gray-600 mb-4">No player records are available at the moment.</p>
                                    <button onclick="loadDemoPlayers()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                        Load Demo Players
                                    </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

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

                function loadDemoPlayers() {
                    const playerList = document.getElementById('playerRecordsList');
                    const demoPlayers = [
                        { id: 1, name: 'John Smith', team: { name: 'Team Alpha' }, fifa_id: 'FIFA001', updated_at: '2024-08-04' },
                        { id: 2, name: 'Sarah Johnson', team: { name: 'Team Beta' }, fifa_id: 'FIFA002', updated_at: '2024-08-03' },
                        { id: 3, name: 'Mike Wilson', team: { name: 'Team Gamma' }, fifa_id: 'FIFA003', updated_at: '2024-08-02' },
                        { id: 4, name: 'Emma Davis', team: { name: 'Team Delta' }, fifa_id: 'FIFA004', updated_at: '2024-08-01' },
                        { id: 5, name: 'Alex Brown', team: { name: 'Team Echo' }, fifa_id: 'FIFA005', updated_at: '2024-07-31' }
                    ];
                    
                    playerList.innerHTML = '';
                    
                    demoPlayers.forEach(player => {
                        const div = document.createElement('div');
                        div.className = 'p-4 hover:bg-gray-50 transition-colors';
                        div.innerHTML = `
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-lg">${player.name.charAt(0)}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">${player.name}</div>
                                        <div class="text-sm text-gray-500">${player.team.name} • FIFA ID: ${player.fifa_id}</div>
                                        <div class="text-xs text-gray-400">Last updated: ${player.updated_at}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="/modules/medical/athlete/${player.id}" 
                                       class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-md text-sm font-medium hover:bg-blue-50 transition-colors">
                                        👁️ Voir
                                    </a>
                                    <a href="/modules/medical/athlete/${player.id}/edit" 
                                       class="text-indigo-600 hover:text-indigo-900 px-3 py-1 rounded-md text-sm font-medium hover:bg-indigo-50 transition-colors">
                                        ✏️ Modifier
                                    </a>
                                </div>
                            </div>
                        `;
                        playerList.appendChild(div);
                    });
                }

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
                    
                    // Demo athletes for selector
                    const demoAthletes = [
                        { id: 1, name: 'John Smith', team: 'Team Alpha' },
                        { id: 2, name: 'Sarah Johnson', team: 'Team Beta' },
                        { id: 3, name: 'Mike Wilson', team: 'Team Gamma' },
                        { id: 4, name: 'Emma Davis', team: 'Team Delta' },
                        { id: 5, name: 'Alex Brown', team: 'Team Echo' }
                    ];
                    
                    athleteList.innerHTML = '';
                    
                    demoAthletes.forEach(athlete => {
                        const div = document.createElement('div');
                        div.className = 'p-2 hover:bg-gray-100 cursor-pointer rounded';
                        div.innerHTML = `
                            <div class="flex justify-between items-center">
                                <span class="font-medium">${athlete.name}</span>
                                <span class="text-sm text-gray-500">${athlete.team}</span>
                            </div>
                        `;
                        div.onclick = () => {
                            window.location.href = `/modules/medical/athlete/${athlete.id}`;
                        };
                        athleteList.appendChild(div);
                    });
                    
                    // Search functionality
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        const athleteItems = athleteList.querySelectorAll('div');
                        
                        athleteItems.forEach(item => {
                            const athleteName = item.textContent.toLowerCase();
                            if (athleteName.includes(searchTerm)) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    });
                }
                </script>

                <div class="mt-8">
                    <h3 class="text-md font-medium text-gray-900 mb-4">Recent Medical Activities</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Medical clearance approved for John Smith</span>
                            <span class="text-xs text-gray-500">2 hours ago</span>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Fitness assessment scheduled for Sarah Johnson</span>
                            <span class="text-xs text-gray-500">4 hours ago</span>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Medical suspension issued for Mike Wilson</span>
                            <span class="text-xs text-gray-500">1 day ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 