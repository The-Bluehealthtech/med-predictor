@extends('layouts.app')

@section('title', 'Club Management Dashboard')

@section('content')


<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative">
        <!-- Header -->
        <div class="mb-8">
            @if($dashboardData['is_association_admin'] ?? false)
                <!-- Association Admin Header - Title on Top -->
                <div class="text-center mb-6">
                    <div class="flex justify-center mb-4">
                        <div class="w-32 h-32 bg-white rounded-lg shadow-lg p-4 border-2 border-gray-200 flex items-center justify-center">
                            @if(isset($dashboardData['association']) && $dashboardData['association']->association_logo_url)
                                <img src="{{ $dashboardData['association']->association_logo_url }}" 
                                     alt="{{ $dashboardData['association']->name }} Logo" 
                                     class="w-full h-full object-contain"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full bg-blue-100 rounded flex items-center justify-center text-blue-600 font-bold text-2xl" style="display: none;">
                                    {{ substr($dashboardData['association']->name, 0, 2) }}
                                </div>
                            @else
                                <div class="w-full h-full bg-blue-100 rounded flex items-center justify-center text-blue-600 font-bold text-2xl">
                                    {{ substr($dashboardData['association']->name ?? 'FA', 0, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <h2 class="font-bold text-3xl text-gray-900 leading-tight mb-2">
                        {{ __('Association License Validation Dashboard') }}
                    </h2>
                    <p class="text-gray-600 text-lg">
                        Manage and validate player licenses across all clubs
                    </p>
                </div>
                
                <!-- Action Buttons Centered -->
                <div class="flex justify-center space-x-3 mb-6">
                    <a href="{{ route('players.bulk-import') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Import Players
                    </a>
                    <a href="{{ route('fifa.connectivity') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        FIFA Connect
                    </a>
                </div>
                
                <!-- User Profile Top Right -->
                <div class="absolute top-4 right-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center shadow-md">
                            @if(auth()->user()->hasProfilePicture())
                                <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                                     alt="{{ auth()->user()->getProfilePictureAlt() }}" 
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <span class="text-white font-semibold text-sm">{{ auth()->user()->getInitials() }}</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->getDisplayName() }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Club User Header - Original Layout -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-4">
                            <!-- Association Logo -->
                            <div class="w-24 h-24 bg-white rounded-lg shadow-lg p-3 border-2 border-gray-200 flex items-center justify-center">
                                @if(isset($dashboardData['club']) && $dashboardData['club']->association && $dashboardData['club']->association->association_logo_url)
                                    <img src="{{ $dashboardData['club']->association->association_logo_url }}" 
                                         alt="{{ $dashboardData['club']->association->name }} Logo" 
                                         class="w-full h-full object-contain"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-full h-full bg-blue-100 rounded flex items-center justify-center text-blue-600 font-bold text-xl" style="display: none;">
                                        {{ substr($dashboardData['club']->association->name, 0, 2) }}
                                    </div>
                                @else
                                    <div class="w-full h-full bg-blue-100 rounded flex items-center justify-center text-blue-600 font-bold text-xl">
                                        {{ substr($dashboardData['club']->association->name ?? 'FA', 0, 2) }}
                                    </div>
                                @endif
                            </div>
                            <!-- Club Logo -->
                            <div class="w-24 h-24 bg-white rounded-lg shadow-lg p-3 border-2 border-gray-200 flex items-center justify-center">
                                @if($dashboardData['is_association_admin'] ?? false)
                                    <!-- Association Admin - Show association logo or initials -->
                                    @if(isset($dashboardData['association']) && $dashboardData['association']->logo_url)
                                        <img src="{{ $dashboardData['association']->logo_url }}" 
                                             alt="{{ $dashboardData['association']->name }} Logo" 
                                             class="w-full h-full object-contain"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-full h-full bg-red-100 rounded flex items-center justify-center text-red-600 font-bold text-xl" style="display: none;">
                                            {{ substr($dashboardData['association']->name, 0, 2) }}
                                        </div>
                                    @else
                                        <div class="w-full h-full bg-red-100 rounded flex items-center justify-center text-red-600 font-bold text-xl">
                                            {{ isset($dashboardData['association']) ? substr($dashboardData['association']->name, 0, 2) : 'FA' }}
                                        </div>
                                    @endif
                                @elseif(isset($dashboardData['club']))
                                    <!-- Club User - Show club logo or initials -->
                                    @if($dashboardData['club']->club_logo_url)
                                        <img src="{{ $dashboardData['club']->club_logo_url }}" 
                                             alt="{{ $dashboardData['club']->name }} Logo" 
                                             class="w-full h-full object-contain"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-full h-full bg-red-100 rounded flex items-center justify-center text-red-600 font-bold text-xl" style="display: none;">
                                            {{ substr($dashboardData['club']->name, 0, 2) }}
                                        </div>
                                    @else
                                        <div class="w-full h-full bg-red-100 rounded flex items-center justify-center text-red-600 font-bold text-xl">
                                            {{ substr($dashboardData['club']->name, 0, 2) }}
                                        </div>
                                    @endif
                                @else
                                    <!-- System Admin - Show default logo -->
                                    <div class="w-full h-full bg-red-100 rounded flex items-center justify-center text-red-600 font-bold text-xl">
                                        SA
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                                    {{ __('Club Management Dashboard') }}
                                </h2>
                                <p class="text-gray-600 mt-1">
                                    Manage your football clubs, teams, and player licensing
                                </p>
                                @if($dashboardData['is_association_admin'] ?? false)
                                    @if(isset($dashboardData['association']))
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $dashboardData['association']->name }}
                                        </p>
                                    @endif
                                @elseif(isset($dashboardData['club']) && $dashboardData['club']->association)
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $dashboardData['club']->association->name }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- User Profile Picture -->
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center shadow-md">
                                @if(auth()->user()->hasProfilePicture())
                                    <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                                         alt="{{ auth()->user()->getProfilePictureAlt() }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <span class="text-white font-semibold text-sm">{{ auth()->user()->getInitials() }}</span>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->getDisplayName() }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('players.bulk-import') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Import Players
                        </a>
                        <a href="{{ route('fifa.connectivity') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            FIFA Connect
                        </a>
                    </div>
                </div>
            @endif
        </div>
            <!-- Overview Stats with Enhanced Design -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @if($dashboardData['is_association_admin'] ?? false)
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">Total Clubs</p>
                                    <p class="text-xl font-bold text-white">{{ $dashboardData['stats']['total_clubs'] }}</p>
                                    <p class="text-blue-200 text-xs mt-1">Registered clubs</p>
                                </div>
                                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(isset($dashboardData['club']))
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">Club Name</p>
                                    <p class="text-xl font-bold text-white">{{ $dashboardData['club']->name }}</p>
                                    <p class="text-blue-200 text-xs mt-1">{{ $dashboardData['club']->country }}</p>
                                </div>
                                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                                    @if($dashboardData['club']->club_logo_url)
                                        <img src="{{ $dashboardData['club']->club_logo_url }}" 
                                             alt="{{ $dashboardData['club']->name }} Logo" 
                                             class="h-8 w-8 rounded-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="h-8 w-8 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-xs" style="display: none;">
                                            {{ substr($dashboardData['club']->name, 0, 2) }}
                                        </div>
                                    @else
                                        <div class="h-8 w-8 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-xs">
                                            {{ substr($dashboardData['club']->name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm font-medium">System Admin</p>
                                    <p class="text-xl font-bold text-white">All Clubs</p>
                                    <p class="text-blue-200 text-xs mt-1">Overview</p>
                                </div>
                                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                                    <div class="h-8 w-8 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-xs">
                                        SA
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Total Players</p>
                                <p class="text-3xl font-bold text-white">{{ $dashboardData['stats']['total_players'] }}</p>
                                <p class="text-green-200 text-xs mt-1">Registered athletes</p>
                            </div>
                            <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Total Teams</p>
                                <p class="text-3xl font-bold text-white">{{ $dashboardData['stats']['total_teams'] }}</p>
                                <p class="text-purple-200 text-xs mt-1">Active squads</p>
                            </div>
                            <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-medium">Active Licenses</p>
                                <p class="text-3xl font-bold text-white">{{ $dashboardData['stats']['active_licenses'] }}</p>
                                <p class="text-yellow-200 text-xs mt-1">Valid permits</p>
                            </div>
                            <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">License Status</h3>
                            <div class="bg-blue-100 rounded-full p-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">Pending Approval</span>
                                </div>
                                <span class="text-lg font-bold text-yellow-600">{{ $dashboardData['stats']['pending_licenses'] }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">Expiring Soon</span>
                                </div>
                                <span class="text-lg font-bold text-red-600">{{ $dashboardData['stats']['expiring_licenses'] }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">Active</span>
                                </div>
                                <span class="text-lg font-bold text-green-600">{{ $dashboardData['stats']['active_licenses'] }}</span>
                            </div>
                            @if($dashboardData['is_association_admin'] ?? false)
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">Total Licenses</span>
                                </div>
                                <span class="text-lg font-bold text-purple-600">{{ $dashboardData['stats']['total_licenses'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                            <div class="bg-green-100 rounded-full p-2">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-3">
                            @if($dashboardData['is_association_admin'] ?? false)
                            <!-- Association Admin Actions -->
                            <a href="{{ route('club-management.licenses.index') }}" class="flex items-center p-3 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200 group">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">License Validation</span>
                                @if($dashboardData['stats']['pending_licenses'] > 0)
                                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $dashboardData['stats']['pending_licenses'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('club-management.teams.index') }}" class="flex items-center p-3 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 group">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="font-medium">Manage Teams</span>
                            </a>
                            <a href="{{ route('club-management.lineups.index') }}" class="flex items-center p-3 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 group">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span class="font-medium">View Lineups</span>
                            </a>
                            @else
                            <!-- Club User Actions -->
                            <a href="{{ route('players.bulk-import') }}" class="flex items-center p-3 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200 group">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <span class="font-medium">Import Players</span>
                            </a>
                            <a href="{{ route('club-management.licenses.index') }}" class="flex items-center p-3 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors duration-200 group">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">Manage Licenses</span>
                                @if($dashboardData['stats']['pending_licenses'] > 0)
                                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">{{ $dashboardData['stats']['pending_licenses'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('fifa.connectivity') }}" class="flex items-center p-3 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200 group">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <span class="font-medium">FIFA Connect Status</span>
                            </a>
                            <a href="{{ route('fifa.players.search') }}" class="flex items-center p-3 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200 group">
                                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span class="text-sm font-medium">Search FIFA Data</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!($dashboardData['is_association_admin'] ?? false))
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Club Information</h3>
                            <div class="bg-green-100 rounded-full p-2">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">Squad Size</span>
                                </div>
                                <span class="text-xs text-blue-600 font-medium">{{ $dashboardData['stats']['total_players'] }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">Teams</span>
                                </div>
                                <span class="text-xs text-green-600 font-medium">{{ $dashboardData['stats']['total_teams'] }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">Active Licenses</span>
                                </div>
                                <span class="text-xs text-purple-600 font-medium">{{ $dashboardData['stats']['active_licenses'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Club Overview -->
            @if($dashboardData['is_association_admin'] ?? false)
            <!-- Association Admin Overview -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Association Overview</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">All Clubs</span>
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $dashboardData['stats']['total_clubs'] }}</div>
                            <div class="text-sm text-gray-500">Total Clubs</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $dashboardData['stats']['total_players'] }}</div>
                            <div class="text-sm text-gray-500">Total Players</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $dashboardData['stats']['total_teams'] }}</div>
                            <div class="text-sm text-gray-500">Total Teams</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $dashboardData['stats']['total_licenses'] }}</div>
                            <div class="text-sm text-gray-500">Total Licenses</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- License Validation Section for Association Admins -->
            @if($dashboardData['stats']['pending_licenses'] > 0)
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-3 mr-4">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">License Validation Required</h3>
                            <p class="text-yellow-100">You have {{ $dashboardData['stats']['pending_licenses'] }} license(s) waiting for approval</p>
                        </div>
                    </div>
                    <a href="{{ route('club-management.licenses.index') }}" class="bg-white text-yellow-600 px-6 py-3 rounded-lg font-semibold hover:bg-yellow-50 transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Review Licenses
                    </a>
                </div>
            </div>
            @endif
            @elseif(isset($dashboardData['club']))
            <!-- Club User Overview -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Club Overview</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">{{ $dashboardData['club']->name }}</span>
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $dashboardData['club']->name }}</div>
                            <div class="text-sm text-gray-500">{{ $dashboardData['club']->country }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $dashboardData['stats']['total_players'] }}</div>
                            <div class="text-sm text-gray-500">Players</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $dashboardData['stats']['total_teams'] }}</div>
                            <div class="text-sm text-gray-500">Teams</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $dashboardData['stats']['active_licenses'] }}</div>
                            <div class="text-sm text-gray-500">Active Licenses</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 