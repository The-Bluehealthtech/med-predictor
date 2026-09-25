@extends('layouts.app')

@section('title', __('Import Players') . ' - ' . ($match->homeTeam->club->name ?? $match->homeTeam->name) . ' vs ' . ($match->awayTeam->club->name ?? $match->awayTeam->name))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                {{ __('Import Players') }} - {{ $match->homeTeam->club->name ?? $match->homeTeam->name }} vs {{ $match->awayTeam->club->name ?? $match->awayTeam->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('competition-management.matches.match-sheet', $match) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to Match Sheet') }}
                </a>
            </div>
        </div>

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

        <!-- Match Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Match Information') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">{{ __('Teams') }}</h4>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $match->homeTeam->club->name ?? $match->homeTeam->name }} vs {{ $match->awayTeam->club->name ?? $match->awayTeam->name }}
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">{{ __('Competition') }}</h4>
                        <div class="text-gray-900">{{ $match->competition->name }}</div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">{{ __('Current Rosters') }}</h4>
                        <div class="text-sm text-gray-600">
                            <div>Home Team: {{ $homeTeamRoster ? $homeTeamRoster->players->count() : 0 }} players</div>
                            <div>Away Team: {{ $awayTeamRoster ? $awayTeamRoster->players->count() : 0 }} players</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Import Players from Club Management') }}</h3>
                
                @if($clubs->isNotEmpty())
                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <strong>{{ __('Available Clubs:') }}</strong> 
                            {{ $clubs->pluck('name')->implode(', ') }}
                        </p>
                        <p class="text-xs text-blue-600 mt-1">
                            {{ __('Only clubs associated with the match teams are shown.') }}
                        </p>
                    </div>
                @else
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800">
                            <strong>{{ __('No Clubs Available') }}</strong>
                        </p>
                        <p class="text-xs text-red-600 mt-1">
                            {{ __('No clubs are associated with the match teams. Please ensure the match teams have associated clubs with players.') }}
                        </p>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('competition-management.matches.match-sheet.import-players.process', $match) }}" id="importForm" {{ $clubs->isEmpty() ? 'style="opacity: 0.5; pointer-events: none;"' : '' }}>
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Team Selection -->
                        <div>
                            <label for="team_type" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Select Team') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="flex space-x-2">
                                <select name="team_type" id="team_type" required class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Choose team...') }}</option>
                                <option value="home">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }} (Home)</option>
                                <option value="away">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }} (Away)</option>
                            </select>
                                <button type="button" onclick="switchTeam()" class="px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition-colors">
                                    Switch Team
                                </button>
                            </div>
                        </div>

                        <!-- Club Selection -->
                        <div>
                            <label for="club_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Select Club') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="club_id" id="club_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Choose club...') }}</option>
                                @foreach($clubs as $club)
                                    @php
                                        $teamType = '';
                                        if ($match->homeTeam && $match->homeTeam->club && $match->homeTeam->club->id == $club->id) {
                                            $teamType = ' (Home Team)';
                                        } elseif ($match->awayTeam && $match->awayTeam->club && $match->awayTeam->club->id == $club->id) {
                                            $teamType = ' (Away Team)';
                                        }
                                    @endphp
                                    <option value="{{ $club->id }}" data-players-count="{{ $club->players->count() }}">
                                        {{ $club->name }}{{ $teamType }} ({{ $club->players->count() }} players)
                                    </option>
                                @endforeach
                            </select>
                            @if($clubs->isEmpty())
                                <p class="mt-2 text-sm text-red-600">
                                    {{ __('No clubs found for this match. Please ensure the match teams have associated clubs.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Players Selection -->
                    <div class="mt-6" id="playersSection" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Select Players') }} <span class="text-red-500">*</span>
                            <span class="text-sm text-gray-500">(First 11 will be starters, rest will be substitutes)</span>
                        </label>
                        
                        <div class="bg-gray-50 p-6 rounded-lg w-full">
                            <div class="w-full" id="playersGrid">
                                <!-- Players will be loaded here via JavaScript -->
                            </div>
                        </div>
                    </div>



                    <!-- Quick Load Button -->
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Quick Load</h4>
                                <p class="text-xs text-blue-600">Load all available players for both teams</p>
                            </div>
                            <button type="button" onclick="displayAllClubsPlayers()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                Load All Players
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button type="submit" id="submitBtn" disabled class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            {{ __('Import Selected Players') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let clubs = @json($clubs);
let matchData = @json([
    'home_club_id' => $match->homeTeam->club->id ?? null,
    'away_club_id' => $match->awayTeam->club->id ?? null
]);
let isAutoSelecting = false; // Flag to prevent infinite loops during auto-selection

// Function to auto-select team type based on club
function autoSelectTeamType(clubId) {
    const teamTypeSelect = document.getElementById('team_type');
    
    if (teamTypeSelect.value === '') {
        if (matchData.home_club_id == clubId) {
            teamTypeSelect.value = 'home';
            // Add visual feedback
            teamTypeSelect.style.backgroundColor = '#f0f9ff';
            setTimeout(() => {
                teamTypeSelect.style.backgroundColor = '';
            }, 2000);
        } else if (matchData.away_club_id == clubId) {
            teamTypeSelect.value = 'away';
            // Add visual feedback
            teamTypeSelect.style.backgroundColor = '#f0f9ff';
            setTimeout(() => {
                teamTypeSelect.style.backgroundColor = '';
            }, 2000);
        }
    }
    
    // Trigger change event to update submit button
    teamTypeSelect.dispatchEvent(new Event('change'));
}

// Function to display all clubs' players in two columns
function displayAllClubsPlayers() {
    const playersGrid = document.getElementById('playersGrid');
    const playersSection = document.getElementById('playersSection');
    
    if (!playersGrid || !playersSection) {
        return;
    }
    
    // Show the players section
                playersSection.style.display = 'block';
    playersSection.style.backgroundColor = '#f0f9ff';
    playersSection.style.border = '2px solid #3b82f6';
    
    // Clear existing content
    playersGrid.innerHTML = '';
    
    // Create a two-column layout with larger columns
    const container = document.createElement('div');
    container.className = 'flex flex-col lg:flex-row gap-8 w-full h-full';
    
    // Order clubs correctly: Home team first, then Away team
    const orderedClubs = [];
    
    // Add home team first
    const homeClub = clubs.find(club => club.id == matchData.home_club_id);
    if (homeClub) {
        orderedClubs.push({...homeClub, teamType: 'Home Team'});
    }
    
    // Add away team second
    const awayClub = clubs.find(club => club.id == matchData.away_club_id);
    if (awayClub) {
        orderedClubs.push({...awayClub, teamType: 'Away Team'});
    }
    
    // If clubs not found by ID, use original order
    if (orderedClubs.length === 0) {
        clubs.forEach((club, index) => {
            orderedClubs.push({...club, teamType: index === 0 ? 'Home Team' : 'Away Team'});
        });
    }
    
    console.log('Ordered clubs for display:', orderedClubs.map(c => ({name: c.name, teamType: c.teamType})));
    
    orderedClubs.forEach((club, index) => {
        if (club.players && club.players.length > 0) {
            // Create club column
            const clubColumn = document.createElement('div');
            clubColumn.className = 'bg-white border-2 border-gray-300 rounded-xl p-8 min-h-[600px] flex-1 w-full shadow-lg';
            
            // Club header with team type indicator
            const clubHeader = document.createElement('div');
            const headerColor = club.teamType === 'Home Team' ? 'from-blue-50 to-purple-50' : 'from-red-50 to-orange-50';
            const borderColor = club.teamType === 'Home Team' ? 'border-blue-300' : 'border-red-300';
            clubHeader.className = `text-center mb-8 pb-6 border-b-2 ${borderColor} bg-gradient-to-r ${headerColor} rounded-lg p-4`;
            clubHeader.innerHTML = `
                <h3 class="text-3xl font-bold text-gray-800 mb-2">${club.name}</h3>
                <p class="text-lg font-semibold text-gray-700 mb-1">${club.teamType}</p>
                <p class="text-xl text-gray-600 font-medium">${club.players.length} players available</p>
            `;
            clubColumn.appendChild(clubHeader);
            
            // Players list for this club
            const playersList = document.createElement('div');
            playersList.className = 'space-y-3 max-h-[500px] overflow-y-auto pr-2';
            
            club.players.forEach((player, playerIndex) => {
                const playerCard = document.createElement('div');
                playerCard.className = 'flex items-center space-x-4 p-4 hover:bg-purple-50 rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-md';
                
                playerCard.innerHTML = `
                    <input type="checkbox" name="players[]" value="${player.id}" id="player_${player.id}" 
                           data-club-id="${club.id}" 
                           class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <div class="flex-1">
                        <label for="player_${player.id}" class="text-base font-semibold text-gray-900 cursor-pointer hover:text-purple-600 transition-colors">
                            ${player.name}
                        </label>
                        <div class="text-sm text-gray-600 mt-1">
                            ${player.position || 'N/A'} • ${player.age || 'N/A'}y • Rating: ${player.overall_rating || 'N/A'}
                            • ${club.teamType}
                        </div>
                    </div>
                `;
                
                playersList.appendChild(playerCard);
            });
            
            clubColumn.appendChild(playersList);
            container.appendChild(clubColumn);
        }
    });
    
    playersGrid.appendChild(container);
    
    // Add event listeners to checkboxes
    const checkboxes = playersGrid.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', window.updateSubmitButton);
    });
    
    // Update submit button after displaying players
    window.updateSubmitButton();
}









// Fallback function to display players directly
function displayPlayersDirectly(players) {
    console.log('displayPlayersDirectly called with:', players.length, 'players');
    
    const playersGrid = document.getElementById('playersGrid');
    const playersSection = document.getElementById('playersSection');
    
    if (!playersGrid || !playersSection) {
        console.error('Required elements not found!');
        return;
    }
    
    // Show the players section
    playersSection.style.display = 'block';
    console.log('Players section displayed');
    
    // Clear existing content
        playersGrid.innerHTML = '';
        
    if (!players || players.length === 0) {
        playersGrid.innerHTML = '<div class="col-span-full text-center py-8"><p class="text-gray-500">No players found</p></div>';
        return;
    }
    
        players.forEach((player, index) => {
        console.log('Creating card for player:', player.name);
        
            const playerCard = document.createElement('div');
            playerCard.className = 'bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow';
            
            playerCard.innerHTML = `
                <div class="flex items-center space-x-3">
                    <input type="checkbox" name="players[]" value="${player.id}" id="player_${player.id}" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <div class="flex-1">
                        <label for="player_${player.id}" class="text-sm font-medium text-gray-900 cursor-pointer">
                            ${player.name}
                        </label>
                        <div class="text-xs text-gray-500">
                            ${player.position || 'N/A'} • ${player.age || 'N/A'}y • Rating: ${player.overall_rating || 'N/A'}
                        </div>
                    </div>
                </div>
            `;
            
            playersGrid.appendChild(playerCard);
        console.log('Added player card for:', player.name);
    });
    
    // Add event listeners to checkboxes
    const checkboxes = playersGrid.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSubmitButton);
    });
    
    // Update submit button after displaying players
    updateSubmitButton();
}

document.addEventListener('DOMContentLoaded', function() {
    const clubSelect = document.getElementById('club_id');
    const playersSection = document.getElementById('playersSection');
    const playersGrid = document.getElementById('playersGrid');
    const submitBtn = document.getElementById('submitBtn');
    
    clubSelect.addEventListener('change', function() {
        // Skip if we're in auto-selection mode to prevent infinite loops
        if (isAutoSelecting) {
            console.log('Skipping club change event during auto-selection');
            return;
        }
        
        const clubId = this.value;
        console.log('Club selection changed to:', clubId);
        
        if (clubId) {
            // Auto-select team type immediately when club is selected
            autoSelectTeamType(clubId);
            
            // Show loading state
            playersSection.style.display = 'block';
            playersGrid.innerHTML = '<div class="col-span-full text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div><p class="mt-2 text-sm text-gray-600">Loading players...</p></div>';
            
            // Try to load players from JSON data first (faster and more reliable)
            const club = clubs.find(c => c.id == clubId);
            
            if (club && club.players && club.players.length > 0) {
                console.log('Using JSON data for club:', club.name, 'with', club.players.length, 'players');
                
                // Add club information to each player
                const playersWithClub = club.players.map(player => ({
                    ...player,
                    club_id: club.id,
                    club_name: club.name
                }));
                
                if (window.displayPlayers) {
                    window.displayPlayers(playersWithClub);
                } else {
                    console.error('displayPlayers function not found!');
                    // Fallback: manually display players
                    displayPlayersDirectly(playersWithClub);
                }
            } else {
                console.log('No players found for club, falling back to API');
                // Fallback to API if JSON data is not available
                const apiUrl = `{{ route('competition-management.matches.match-sheet.get-club-players', $match) }}?club_id=${clubId}`;
                
                fetch(apiUrl, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.players.length > 0) {
                        // Add club information to each player from API
                        const playersWithClub = data.players.map(player => ({
                            ...player,
                            club_id: clubId,
                            club_name: clubSelect.options[clubSelect.selectedIndex]?.text || 'Unknown Club'
                        }));
                        
                        if (window.displayPlayers) {
                            window.displayPlayers(playersWithClub);
                        }
                    } else {
                        playersSection.style.display = 'none';
                        alert('No players found for this club.');
                    }
                })
                .catch(error => {
                    playersSection.style.display = 'none';
                    alert('Failed to load players.');
                })
                .finally(() => {
                    window.updateSubmitButton();
                });
            }
        } else {
            playersSection.style.display = 'none';
            window.updateSubmitButton();
        }
    });
    
    // Global displayPlayers function
    window.displayPlayers = function(players) {
        console.log('displayPlayers called with:', players.length, 'players');
        console.log('First player:', players[0]);
        
        const playersGrid = document.getElementById('playersGrid');
        const playersSection = document.getElementById('playersSection');
        
        console.log('playersGrid:', playersGrid);
        console.log('playersSection:', playersSection);
        
        if (!playersGrid || !playersSection) {
            console.error('Required elements not found!');
            return;
        }
        
        // Show the players section
        playersSection.style.display = 'block';
        playersSection.style.backgroundColor = '#f0f9ff'; // Light blue background
        playersSection.style.border = '2px solid #3b82f6'; // Blue border
        console.log('Players section displayed with visual indicator');
        
        // Clear existing content
        playersGrid.innerHTML = '';
        
        if (!players || players.length === 0) {
            playersGrid.innerHTML = '<div class="col-span-full text-center py-8"><p class="text-gray-500">No players found</p></div>';
            return;
        }
        
        players.forEach((player, index) => {
            console.log('Creating card for player:', player.name);
            
            const playerCard = document.createElement('div');
            
            // Check if this is a header
            if (player.isHeader) {
                playerCard.className = 'col-span-full bg-gray-100 border border-gray-300 rounded-lg p-3';
                playerCard.innerHTML = `
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-800">${player.name}</h3>
                    </div>
                `;
            } else {
                playerCard.className = 'bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow';
                
                playerCard.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="players[]" value="${player.id}" id="player_${player.id}" 
                               data-club-id="${player.club_id || ''}" 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <div class="flex-1">
                            <label for="player_${player.id}" class="text-sm font-medium text-gray-900 cursor-pointer">
                                ${player.name}
                            </label>
                            <div class="text-xs text-gray-500">
                                ${player.position || 'N/A'} • ${player.age || 'N/A'}y • Rating: ${player.overall_rating || 'N/A'}
                                ${player.club_name ? `• ${player.club_name}` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }
            
            playersGrid.appendChild(playerCard);
            console.log('Added player card for:', player.name);
        });
        
        // Add event listeners to checkboxes
        const checkboxes = playersGrid.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', window.updateSubmitButton);
        });
        
        // Update submit button after displaying players
        window.updateSubmitButton();
    }
    
    // Global updateSubmitButton function
    window.updateSubmitButton = function() {
        const playersGrid = document.getElementById('playersGrid');
        const submitBtn = document.getElementById('submitBtn');
        const clubSelect = document.getElementById('club_id');
        
        if (!playersGrid || !submitBtn) {
            console.error('Required elements not found for updateSubmitButton');
            return;
        }
        
        const selectedPlayers = playersGrid.querySelectorAll('input[type="checkbox"]:checked');
        const teamSelected = document.getElementById('team_type').value;
        const clubSelected = clubSelect ? clubSelect.value : '';
        
        // Only count players from the selected club
        let validSelectedPlayers = 0;
        selectedPlayers.forEach(checkbox => {
            const playerClubId = checkbox.getAttribute('data-club-id');
            if (playerClubId === clubSelected) {
                validSelectedPlayers++;
            }
        });
        
        console.log('Update submit button check:', {
            totalSelectedPlayers: selectedPlayers.length,
            validSelectedPlayers: validSelectedPlayers,
            teamSelected: teamSelected,
            clubSelected: clubSelected,
            isDisabled: validSelectedPlayers === 0 || !teamSelected || !clubSelected
        });
        
        const isDisabled = validSelectedPlayers === 0 || !teamSelected || !clubSelected;
        submitBtn.disabled = isDisabled;
        
        // Visual feedback
        if (isDisabled) {
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        // Show warning if players from wrong club are selected
        if (selectedPlayers.length > validSelectedPlayers) {
            console.warn(`${selectedPlayers.length - validSelectedPlayers} players from wrong club selected!`);
            // Optionally show a visual warning to the user
            const warningDiv = document.getElementById('clubWarning') || createWarningElement();
            warningDiv.style.display = 'block';
            warningDiv.textContent = `Warning: ${selectedPlayers.length - validSelectedPlayers} selected players do not belong to the selected club. Only players from ${clubSelect.options[clubSelect.selectedIndex]?.text} will be imported.`;
        } else {
            const warningDiv = document.getElementById('clubWarning');
            if (warningDiv) warningDiv.style.display = 'none';
        }
    }
    
    // Helper function to create warning element
    function createWarningElement() {
        const warningDiv = document.createElement('div');
        warningDiv.id = 'clubWarning';
        warningDiv.className = 'col-span-full bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4';
        warningDiv.style.display = 'none';
        
        const playersSection = document.getElementById('playersSection');
        if (playersSection) {
            playersSection.insertBefore(warningDiv, playersSection.firstChild);
        }
        
        return warningDiv;
    }
    
    // Also listen for team type changes
    const teamTypeSelect = document.getElementById('team_type');
    if (teamTypeSelect) {
        teamTypeSelect.addEventListener('change', function() {
            // Skip if we're in auto-selection mode to prevent infinite loops
            if (isAutoSelecting) {
                console.log('Skipping team type change event during auto-selection');
                return;
            }
            
            console.log('Team type changed to:', this.value);
            
            // Auto-select the corresponding club when team type is selected
            const clubSelect = document.getElementById('club_id');
            if (clubSelect) {
                if (this.value === 'home' && matchData.home_club_id) {
                    console.log('Auto-selecting home club:', matchData.home_club_id);
                    clubSelect.value = matchData.home_club_id;
                    clubSelect.dispatchEvent(new Event('change'));
                } else if (this.value === 'away' && matchData.away_club_id) {
                    console.log('Auto-selecting away club:', matchData.away_club_id);
                    clubSelect.value = matchData.away_club_id;
                    clubSelect.dispatchEvent(new Event('change'));
                }
            }
            
            if (window.updateSubmitButton) {
                window.updateSubmitButton();
            }
        });
    }
    
    // Auto-select team type and club on page load
    function autoSelectTeamAndClub() {
        console.log('Auto-selecting team and club...');
        
        isAutoSelecting = true; // Set flag to prevent infinite loops
        
        const teamTypeSelect = document.getElementById('team_type');
        const clubSelect = document.getElementById('club_id');
        
        if (teamTypeSelect && clubSelect && matchData) {
            // Auto-select home team by default
            console.log('Auto-selecting home team...');
            teamTypeSelect.value = 'home';
            
            // Auto-select the corresponding club
            if (matchData.home_club_id) {
                console.log('Auto-selecting home club:', matchData.home_club_id);
                clubSelect.value = matchData.home_club_id;
            }
            
            // Trigger change events to update the UI
            teamTypeSelect.dispatchEvent(new Event('change'));
            clubSelect.dispatchEvent(new Event('change'));
            
            console.log('Auto-selection completed:', {
                teamType: teamTypeSelect.value,
                clubId: clubSelect.value
            });
        }
        
        // Reset flag after a short delay
        setTimeout(() => {
            isAutoSelecting = false;
        }, 1000);
    }
    
    // Switch between home and away teams
    window.switchTeam = function() {
        console.log('Switching team...');
        
        isAutoSelecting = true; // Set flag to prevent infinite loops
        
        const teamTypeSelect = document.getElementById('team_type');
        const clubSelect = document.getElementById('club_id');
        
        if (teamTypeSelect && clubSelect && matchData) {
            const currentTeamType = teamTypeSelect.value;
            const newTeamType = currentTeamType === 'home' ? 'away' : 'home';
            
            console.log('Switching from', currentTeamType, 'to', newTeamType);
            
            teamTypeSelect.value = newTeamType;
            
            // Auto-select the corresponding club
            if (newTeamType === 'home' && matchData.home_club_id) {
                clubSelect.value = matchData.home_club_id;
            } else if (newTeamType === 'away' && matchData.away_club_id) {
                clubSelect.value = matchData.away_club_id;
            }
            
            // Trigger change events to update the UI
            teamTypeSelect.dispatchEvent(new Event('change'));
            clubSelect.dispatchEvent(new Event('change'));
            
            console.log('Team switched to:', newTeamType);
        }
        
        // Reset flag after a short delay
        setTimeout(() => {
            isAutoSelecting = false;
        }, 1000);
    };
    
    // Auto-select on page load
    autoSelectTeamAndClub();
    
    // Update submit button on page load
    if (window.updateSubmitButton) {
        window.updateSubmitButton();
    }
    
    // Handle form submission to filter players by selected club
    const importForm = document.getElementById('importForm');
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            console.log('Form submission started...');
            
            const clubSelect = document.getElementById('club_id');
            const clubSelected = clubSelect ? clubSelect.value : '';
            const selectedPlayers = document.querySelectorAll('input[type="checkbox"]:checked');
            
            console.log('Form submission data:', {
                clubSelected: clubSelected,
                totalSelectedPlayers: selectedPlayers.length
            });
            
            // Uncheck players from wrong club
            selectedPlayers.forEach(checkbox => {
                const playerClubId = checkbox.getAttribute('data-club-id');
                if (playerClubId !== clubSelected) {
                    console.log(`Unchecking player from wrong club: ${checkbox.value}`);
                    checkbox.checked = false;
                }
            });
            
            // Verify we have valid players
            const validPlayers = document.querySelectorAll(`input[type="checkbox"]:checked[data-club-id="${clubSelected}"]`);
            if (validPlayers.length === 0) {
                e.preventDefault();
                alert('Please select at least one player from the selected club.');
                return false;
            }
            
            console.log(`Submitting ${validPlayers.length} players from club ${clubSelected}`);
        });
    }
    

});
</script>
@endsection 