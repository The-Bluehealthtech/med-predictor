<div style="background: red; color: white; font-size: 24px; padding: 20px; text-align: center;">DEBUG: THIS IS THE PLAYER REGISTRATION INDEX VIEW</div>
@extends('layouts.app')

@section('title', 'Players - Med Predictor')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 gap-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Players Management
                </h1>
                <p class="text-gray-600 mt-2 text-lg">Manage players and FIFA Connect integration</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="syncFifaPlayers()" 
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="hidden sm:inline">Sync FIFA</span>
                    <span class="sm:hidden">Sync</span>
                </button>
                <a href="{{ route('player-registration.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">New Player</span>
                    <span class="sm:hidden">New</span>
                </a>
                <a href="{{ route('players.bulk-import') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span class="hidden sm:inline">Bulk Import</span>
                    <span class="sm:hidden">Import</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 text-green-800 px-6 py-4 rounded-xl mb-6 flex items-center">
                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Advanced Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Advanced Search
                </h2>
                <button onclick="toggleAdvancedFilters()" 
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center transition-colors duration-200">
                    <span id="filterToggleText">Show more</span>
                    <svg class="w-4 h-4 ml-1 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Basic Search -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="md:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" id="searchQuery" placeholder="Name, nationality..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               oninput="debounceSearch()">
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                    <select id="positionFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" onchange="debounceSearch()">
                        <option value="">All Positions</option>
                        <option value="ST">Striker (ST)</option>
                        <option value="RW">Right Winger (RW)</option>
                        <option value="LW">Left Winger (LW)</option>
                        <option value="CAM">Attacking Midfielder (CAM)</option>
                        <option value="CM">Central Midfielder (CM)</option>
                        <option value="CDM">Defensive Midfielder (CDM)</option>
                        <option value="CB">Center Back (CB)</option>
                        <option value="RB">Right Back (RB)</option>
                        <option value="LB">Left Back (LB)</option>
                        <option value="GK">Goalkeeper (GK)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rating</label>
                    <div class="flex gap-2">
                        <input type="number" id="minRating" min="1" max="99" placeholder="Min" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               onchange="debounceSearch()">
                        <span class="text-gray-500 self-center">-</span>
                        <input type="number" id="maxRating" min="1" max="99" placeholder="Max" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               onchange="debounceSearch()">
                    </div>
                </div>
            </div>

            <!-- Advanced Filters (Hidden by default) -->
            <div id="advancedFilters" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nationality</label>
                        <input type="text" id="nationalityFilter" placeholder="Country..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               onchange="debounceSearch()">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Age</label>
                        <div class="flex gap-2">
                            <input type="number" id="ageMin" min="16" max="50" placeholder="Min" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                   onchange="debounceSearch()">
                            <span class="text-gray-500 self-center">-</span>
                            <input type="number" id="ageMax" min="16" max="50" placeholder="Max" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                   onchange="debounceSearch()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Preferred Foot</label>
                        <select id="preferredFootFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" onchange="debounceSearch()">
                            <option value="">All</option>
                            <option value="Left">Left</option>
                            <option value="Right">Right</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Work Rate</label>
                        <select id="workRateFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" onchange="debounceSearch()">
                            <option value="">All</option>
                            <option value="High/High">High/High</option>
                            <option value="High/Medium">High/Medium</option>
                            <option value="High/Low">High/Low</option>
                            <option value="Medium/High">Medium/High</option>
                            <option value="Medium/Medium">Medium/Medium</option>
                            <option value="Medium/Low">Medium/Low</option>
                            <option value="Low/High">Low/High</option>
                            <option value="Low/Medium">Low/Medium</option>
                            <option value="Low/Low">Low/Low</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sort by</label>
                        <select id="sortBy" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" onchange="debounceSearch()">
                            <option value="overall_rating">Overall Rating</option>
                            <option value="potential_rating">Potential</option>
                            <option value="name">Name</option>
                            <option value="age">Age</option>
                            <option value="value_eur">Value</option>
                            <option value="wage_eur">Wage</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
                        <select id="sortOrder" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" onchange="debounceSearch()">
                            <option value="desc">Descending</option>
                            <option value="asc">Ascending</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Results per page</label>
                        <select id="limitResults" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" onchange="debounceSearch()">
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <button onclick="searchPlayers()" 
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                    <button onclick="clearFilters()" 
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-gray-600 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </button>
                </div>
                <div class="text-sm text-gray-600 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span id="resultsCount">{{ $players->total() }}</span> players found
                </div>
            </div>
        </div>

        <!-- FIFA Connect Status -->
        <div id="fifaStatus" class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 animate-pulse"></div>
                    <span class="text-blue-800 font-medium">Checking FIFA connection...</span>
                </div>
                <button onclick="checkFifaStatus()" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200">
                    Refresh
                </button>
            </div>
        </div>

        <!-- Players List -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Players List
                    </h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">{{ $players->total() }} total players</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            </div>
            
            <div id="playersTable">
                @if($players->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Joueur
                                    </th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Position
                                    </th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Note
                                    </th>
                                    <th class="hidden md:table-cell px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Club
                                    </th>
                                    <th class="hidden lg:table-cell px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Association
                                    </th>
                                    <th class="hidden lg:table-cell px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dossiers
                                    </th>
                                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($players as $player)
                                    {{-- DEBUG: Player loop is running --}}
                                    <div style="color: green; font-size: 12px;">PLAYER LOOP: {{ $player->id ?? 'NO ID' }}</div>
                                    @if($loop->first)
                                        <div style="color: orange; font-size: 10px;">{{ json_encode($player) }}</div>
                                    @endif
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 lg:h-12 lg:w-12">
                                                    @if($player->has_picture)
                                                        <img class="h-10 w-10 lg:h-12 lg:w-12 rounded-full object-cover border-2 border-gray-200" 
                                                             src="{{ $player->player_picture_url }}" 
                                                             alt="{{ $player->full_name }}"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                             onload="this.nextElementSibling.style.display='none';">
                                                        <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-full bg-blue-100 flex items-center justify-center border-2 border-gray-200" style="display: none;">
                                                            <span class="text-blue-600 font-semibold text-sm lg:text-lg">
                                                                {{ substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-full bg-blue-100 flex items-center justify-center border-2 border-gray-200">
                                                            <span class="text-blue-600 font-semibold text-sm lg:text-lg">
                                                                {{ substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-3 lg:ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $player->full_name }}
                                                    </div>
                                                    <div class="text-xs lg:text-sm text-gray-500">
                                                        {{ $player->nationality }} • {{ $player->age }} ans
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $player->position }}
                                            </span>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $player->overall_rating ?? 'N/A' }}
                                                </div>
                                                @if($player->potential_rating && $player->potential_rating > $player->overall_rating)
                                                    <div class="ml-2 text-xs text-green-600">
                                                        +{{ $player->potential_rating - $player->overall_rating }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="hidden md:table-cell px-4 lg:px-6 py-4 whitespace-nowrap">
                                            @if($player->club)
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                        @if($player->club->logo_url)
                                                            <img class="h-6 w-6 object-contain" src="{{ $player->club->logo_url }}" alt="{{ $player->club->name }} logo">
                                                        @else
                                                            <div class="h-6 w-6 bg-gray-200 rounded flex items-center justify-center">
                                                                <span class="text-xs text-gray-500 font-bold">{{ substr($player->club->name, 0, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-900">{{ $player->club->name }}</div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">Sans club</span>
                                            @endif
                                        </td>
                                        <td class="hidden lg:table-cell px-4 lg:px-6 py-4 whitespace-nowrap">
                                            @if($player->association)
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-6 w-6 mr-2">
                                                        @if($player->association->association_logo_url)
                                                            <img class="h-6 w-6 object-contain" src="{{ $player->association->association_logo_url }}" alt="{{ $player->association->name }} logo">
                                                        @elseif($player->association->nation_flag_url)
                                                            <img class="h-6 w-6 object-contain" src="{{ $player->association->nation_flag_url }}" alt="{{ $player->association->country }} flag">
                                                        @else
                                                            <div class="h-6 w-6 bg-gray-200 rounded flex items-center justify-center">
                                                                <span class="text-xs text-gray-500 font-bold">{{ substr($player->association->name, 0, 2) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-900">{{ $player->association->name }}</div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="hidden lg:table-cell px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex space-x-2">
                                                <span class="text-blue-600">{{ $player->healthRecords->count() }} dossiers</span>
                                                <span class="text-green-600">{{ $player->medicalPredictions->count() }} prédictions</span>
                                            </div>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <a href="{{ route('player-registration.show', $player) }}" 
                                                   class="text-blue-600 hover:text-blue-900">Voir</a>
                                                <a href="{{ route('player-registration.edit', $player) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                                <a href="{{ route('player-registration.health-records', $player) }}" 
                                                   class="text-green-600 hover:text-green-900">Dossiers</a>
                                                <form action="{{ route('player-registration.destroy', $player) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?')">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-4 lg:px-6 py-4 border-t border-gray-200">
                        {{ $players->links() }}
                    </div>
                @else
                    <div class="px-4 lg:px-6 py-12 text-center">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun joueur</h3>
                        <p class="text-gray-500 mb-6">Commencez par synchroniser les données FIFA ou créer un joueur manuellement.</p>
                        <div class="flex flex-col sm:flex-row justify-center gap-3">
                            <button onclick="syncFifaPlayers()" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                🔄 Synchroniser FIFA
                            </button>
                            <a href="{{ route('player-registration.create') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Créer un joueur
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
let searchTimeout;

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(searchPlayers, 500);
}

function toggleAdvancedFilters() {
    const filters = document.getElementById('advancedFilters');
    const toggleText = document.getElementById('filterToggleText');
    
    if (filters.classList.contains('hidden')) {
        filters.classList.remove('hidden');
        toggleText.textContent = 'Masquer';
    } else {
        filters.classList.add('hidden');
        toggleText.textContent = 'Afficher plus';
    }
}

function clearFilters() {
    document.getElementById('searchQuery').value = '';
    document.getElementById('positionFilter').value = '';
    document.getElementById('minRating').value = '';
    document.getElementById('maxRating').value = '';
    document.getElementById('nationalityFilter').value = '';
    document.getElementById('ageMin').value = '';
    document.getElementById('ageMax').value = '';
    document.getElementById('preferredFootFilter').value = '';
    document.getElementById('workRateFilter').value = '';
    document.getElementById('sortBy').value = 'overall_rating';
    document.getElementById('sortOrder').value = 'desc';
    document.getElementById('limitResults').value = '20';
    
    searchPlayers();
}

function searchPlayers() {
    const params = new URLSearchParams();
    
    const searchQuery = document.getElementById('searchQuery').value;
    const position = document.getElementById('positionFilter').value;
    const minRating = document.getElementById('minRating').value;
    const maxRating = document.getElementById('maxRating').value;
    const nationality = document.getElementById('nationalityFilter').value;
    const ageMin = document.getElementById('ageMin').value;
    const ageMax = document.getElementById('ageMax').value;
    const preferredFoot = document.getElementById('preferredFootFilter').value;
    const workRate = document.getElementById('workRateFilter').value;
    const sortBy = document.getElementById('sortBy').value;
    const sortOrder = document.getElementById('sortOrder').value;
    const limit = document.getElementById('limitResults').value;
    
    if (searchQuery) params.append('q', searchQuery);
    if (position) params.append('position', position);
    if (minRating) params.append('min_rating', minRating);
    if (maxRating) params.append('max_rating', maxRating);
    if (nationality) params.append('nationality', nationality);
    if (ageMin) params.append('age_min', ageMin);
    if (ageMax) params.append('age_max', ageMax);
    if (preferredFoot) params.append('preferred_foot', preferredFoot);
    if (workRate) params.append('work_rate', workRate);
    if (sortBy) params.append('sort_by', sortBy);
    if (sortOrder) params.append('sort_order', sortOrder);
    if (limit) params.append('limit', limit);
    
    // Show loading state
    const table = document.getElementById('playersTable');
    table.innerHTML = '<div class="p-8 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">Recherche en cours...</p></div>';
    
    // Make AJAX request
    fetch(`/fifa/search?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePlayersTable(data.data);
                document.getElementById('resultsCount').textContent = data.total;
            } else {
                console.error('Search error:', data.message);
            }
        })
        .catch(error => {
            console.error('Search failed:', error);
            table.innerHTML = '<div class="p-8 text-center text-red-600">Erreur lors de la recherche</div>';
        });
}

function updatePlayersTable(players) {
    const table = document.getElementById('playersTable');
    
    if (players.length === 0) {
        table.innerHTML = `
            <div class="px-4 lg:px-6 py-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun joueur trouvé</h3>
                <p class="text-gray-500">Aucun joueur ne correspond à vos critères de recherche.</p>
            </div>
        `;
        return;
    }
    
    let tableHTML = `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joueur</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                        <th class="hidden md:table-cell px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                        <th class="hidden lg:table-cell px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Association</th>
                        <th class="hidden lg:table-cell px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dossiers</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    `;
    
    players.forEach(player => {
        const initials = (player.first_name?.charAt(0) || '') + (player.last_name?.charAt(0) || '');
        const age = player.date_of_birth ? new Date().getFullYear() - new Date(player.date_of_birth).getFullYear() : 'N/A';
        
        tableHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 lg:h-12 lg:w-12">
                            ${player.player_face_url ? 
                                `<img class="h-10 w-10 lg:h-12 lg:w-12 rounded-full object-cover border-2 border-gray-200" src="${player.player_face_url}" alt="${player.name}">` :
                                `<div class="h-10 w-10 lg:h-12 lg:w-12 rounded-full bg-blue-100 flex items-center justify-center border-2 border-gray-200">
                                    <span class="text-blue-600 font-semibold text-sm lg:text-lg">${initials}</span>
                                </div>`
                            }
                        </div>
                        <div class="ml-3 lg:ml-4">
                            <div class="text-sm font-medium text-gray-900">${player.name}</div>
                            <div class="text-xs lg:text-sm text-gray-500">${player.nationality} • ${age} ans</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">${player.position}</span>
                </td>
                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="text-sm font-medium text-gray-900">${player.overall_rating || 'N/A'}</div>
                        ${player.potential_rating && player.potential_rating > player.overall_rating ? 
                            `<div class="ml-2 text-xs text-green-600">+${player.potential_rating - player.overall_rating}</div>` : ''
                        }
                    </div>
                </td>
                <td class="hidden md:table-cell px-4 lg:px-6 py-4 whitespace-nowrap">
                    ${player.club ? 
                        `<div class="flex items-center">
                            <div class="flex-shrink-0 h-6 w-6 mr-2">
                                ${player.club.logo_url ? 
                                    `<img class="h-6 w-6 object-contain" src="${player.club.logo_url}" alt="${player.club.name} logo">` :
                                    `<div class="h-6 w-6 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-xs text-gray-500 font-bold">${player.club.name.charAt(0)}${player.club.name.charAt(1)}</span>
                                    </div>`
                                }
                            </div>
                            <div class="text-sm text-gray-900">${player.club.name}</div>
                        </div>` :
                        `<span class="text-sm text-gray-500">Sans club</span>`
                    }
                </td>
                <td class="hidden lg:table-cell px-4 lg:px-6 py-4 whitespace-nowrap">
                    ${player.association ? 
                        `<div class="flex items-center">
                            <div class="flex-shrink-0 h-6 w-6 mr-2">
                                ${player.association.association_logo_url ? 
                                    `<img class="h-6 w-6 object-contain" src="${player.association.association_logo_url}" alt="${player.association.name} logo">` :
                                    player.association.nation_flag_url ? 
                                        `<img class="h-6 w-6 object-contain" src="${player.association.nation_flag_url}" alt="${player.association.country} flag">` :
                                        `<div class="h-6 w-6 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-xs text-gray-500 font-bold">${player.association.name.charAt(0)}${player.association.name.charAt(1)}</span>
                                        </div>`
                                }
                            </div>
                            <div class="text-sm text-gray-900">${player.association.name}</div>
                        </div>` :
                        `<span class="text-sm text-gray-500">N/A</span>`
                    }
                </td>
                <td class="hidden lg:table-cell px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex space-x-2">
                        <span class="text-blue-600">0 dossiers</span>
                        <span class="text-green-600">0 prédictions</span>
                    </div>
                </td>
                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="/players/${player.id}" class="text-blue-600 hover:text-blue-900">Voir</a>
                        <a href="/players/${player.id}/edit" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                        <a href="/players/${player.id}/health-records" class="text-green-600 hover:text-green-900">Dossiers</a>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tableHTML += `
                </tbody>
            </table>
        </div>
    `;
    
    table.innerHTML = tableHTML;
}

function syncFifaPlayers() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<span class="mr-2">⏳</span>Synchronisation...';
    button.disabled = true;
    
    fetch('/fifa/sync', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const message = `Synchronisation réussie: ${data.new_players} nouveaux joueurs, ${data.updated_players} mis à jour`;
            showNotification(message, 'success');
            
            // Reload the page to show updated data
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification('Erreur lors de la synchronisation: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Sync failed:', error);
        showNotification('Erreur lors de la synchronisation', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function checkFifaStatus() {
    const statusDiv = document.getElementById('fifaStatus');
    statusDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 animate-pulse"></div>
                <span class="text-blue-800 font-medium">Vérification de la connexion FIFA...</span>
            </div>
        </div>
    `;
    
    fetch('/fifa/connectivity')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.status === 'connected') {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-green-800 font-medium">Connecté à FIFA Connect API (${data.response_time}ms)</span>
                        </div>
                        <button onclick="checkFifaStatus()" class="text-green-600 hover:text-green-800 text-sm">
                            Actualiser
                        </button>
                    </div>
                `;
            } else {
                statusDiv.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                            <span class="text-yellow-800 font-medium">Mode hors ligne - Données simulées disponibles</span>
                        </div>
                        <button onclick="checkFifaStatus()" class="text-yellow-600 hover:text-yellow-800 text-sm">
                            Actualiser
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            statusDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-red-800 font-medium">Erreur de connexion - Mode hors ligne activé</span>
                    </div>
                    <button onclick="checkFifaStatus()" class="text-red-600 hover:text-red-800 text-sm">
                        Actualiser
                    </button>
                </div>
            `;
        });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    checkFifaStatus();
});
</script>
@endsection 