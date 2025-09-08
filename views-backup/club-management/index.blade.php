@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            @if(isset($clubs))
                                Club Management - All Clubs
                            @else
                                {{ $club->name }} - Club Management
                            @endif
                        </h1>
                        <p class="text-gray-600 mt-2">
                            @if(isset($clubs))
                                Manage all clubs, teams, and player licensing across the association
                            @else
                                Manage your club's teams, players, and licensing
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('club-management.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Dashboard
                        </a>
                        
                        <a href="{{ route('club-management.players.import') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Import Players
                        </a>
                        
                        <a href="{{ route('club-management.players.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Players
                        </a>
                        
                        <a href="{{ route('club-management.teams.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Teams
                        </a>
                        
                        <a href="{{ route('club-management.licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Licenses
                        </a>
                    </div>
                </div>

                @if(isset($clubs))
                <!-- All Clubs View for Association/System Admins -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($clubs as $club)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Club Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                        @if($club->club_logo_url)
                                            <img src="{{ $club->club_logo_url }}" alt="{{ $club->name }} Logo" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <span class="text-white font-bold text-lg">{{ substr($club->name, 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">{{ $club->name }}</h3>
                                        <p class="text-blue-100 text-sm">{{ $club->country }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-white text-sm">Players</div>
                                    <div class="text-2xl font-bold text-white">{{ $club->players_count }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Club Stats -->
                        <div class="px-6 py-4 bg-gray-50">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $club->players_count }}</div>
                                    <div class="text-xs text-gray-600">Players</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-green-600">{{ $club->teams_count }}</div>
                                    <div class="text-xs text-gray-600">Teams</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-yellow-600">{{ $club->player_licenses_count }}</div>
                                    <div class="text-xs text-gray-600">Licenses</div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Players -->
                        <div class="px-6 py-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Top Players</h4>
                            @if($club->players->count() > 0)
                                <div class="space-y-2">
                                    @foreach($club->players->take(5) as $player)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">{{ substr($player->name, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $player->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $player->position }} • {{ $player->age }}y</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-blue-600">{{ $player->overall_rating }}</div>
                                            <div class="text-xs text-gray-500">Rating</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($club->players_count > 5)
                                    <div class="text-center mt-3">
                                        <span class="text-sm text-gray-500">+{{ $club->players_count - 5 }} more players</span>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <div class="text-gray-400 text-sm">No players assigned</div>
                                </div>
                            @endif
                        </div>

                        <!-- Club Actions -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex space-x-2">
                                <a href="{{ route('club-management.teams.index') }}?club={{ $club->id }}" class="flex-1 bg-blue-600 text-white text-center py-2 px-3 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
                                    View Teams
                                </a>
                                <a href="{{ route('club-management.licenses.index') }}?club={{ $club->id }}" class="flex-1 bg-yellow-600 text-white text-center py-2 px-3 rounded-md text-sm font-medium hover:bg-yellow-700 transition-colors duration-200">
                                    View Licenses
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Summary Stats -->
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Association Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $clubs->count() }}</div>
                            <div class="text-sm text-gray-600">Total Clubs</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $clubs->sum('players_count') }}</div>
                            <div class="text-sm text-gray-600">Total Players</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ $clubs->sum('teams_count') }}</div>
                            <div class="text-sm text-gray-600">Total Teams</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-600">{{ $clubs->sum('player_licenses_count') }}</div>
                            <div class="text-sm text-gray-600">Total Licenses</div>
                        </div>
                    </div>
                </div>

                @else
                <!-- Single Club View for Club Users -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                    <!-- Club Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    @if($club->club_logo_url)
                                        <img src="{{ $club->club_logo_url }}" alt="{{ $club->name }} Logo" class="w-14 h-14 rounded-full object-cover">
                                    @else
                                        <span class="text-white font-bold text-2xl">{{ substr($club->name, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">{{ $club->name }}</h2>
                                    <p class="text-blue-100">{{ $club->country }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-white text-lg">Total Players</div>
                                <div class="text-4xl font-bold text-white">{{ $club->players->count() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Club Stats -->
                    <div class="px-6 py-6 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ $club->players->count() }}</div>
                                <div class="text-sm text-gray-600">Players</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">{{ $club->teams->count() }}</div>
                                <div class="text-sm text-gray-600">Teams</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-yellow-600">{{ $club->playerLicenses->where('status', 'active')->count() }}</div>
                                <div class="text-sm text-gray-600">Active Licenses</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ $club->playerLicenses->where('status', 'pending')->count() }}</div>
                                <div class="text-sm text-gray-600">Pending Licenses</div>
                            </div>
                        </div>
                    </div>

                    <!-- All Players -->
                    <div class="px-6 py-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">All Players</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($club->players as $player)
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">{{ substr($player->name, 0, 2) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $player->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $player->position }} • {{ $player->age }}y • {{ $player->nationality }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-blue-600">{{ $player->overall_rating }}</div>
                                        <div class="text-xs text-gray-500">Rating</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 