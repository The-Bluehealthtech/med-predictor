@extends('layouts.app')

@section('title', 'Players - Club Management')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">👥 Players</h1>
                    <p class="text-gray-600 mt-2">Manage your club's players</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('club-management.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Club Management
                    </a>
                    <a href="{{ route('player-registration.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Register a Player
                    </a>
                    <a href="{{ route('club-management.players.import') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import Players
                    </a>
                    <a href="{{ route('club-management.players.export') }}?club_id={{ $club->id ?? '' }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Players
                    </a>
                </div>
            </div>
        </div>

        <!-- Club Selection (for admins) -->
        @if($clubs && count($clubs) > 1)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🏢 Select Club</h3>
                <form method="GET" class="flex space-x-4">
                    <select name="club_id" onchange="this.form.submit()" 
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a club...</option>
                        @foreach($clubs as $clubOption)
                            <option value="{{ $clubOption->id }}" {{ request('club_id') == $clubOption->id ? 'selected' : '' }}>
                                {{ $clubOption->name }} ({{ $clubOption->players_count ?? 0 }} players)
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        @endif

        @if($club)
        <!-- Club Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                            @if($club->club_logo_url)
                                <img src="{{ $club->club_logo_url }}" alt="{{ $club->name }} Logo" class="w-14 h-14 rounded-full object-cover">
                            @else
                                <span class="text-white font-bold text-2xl">{{ substr($club->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $club->name }}</h2>
                            <p class="text-gray-600">{{ $club->country }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">{{ $club->players->count() }}</div>
                        <div class="text-sm text-gray-600">Total Players</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Players Grid -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">All Players</h3>
                
                @if($club->players->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($club->players as $player)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200 border border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    @if($player->player_face_url)
                                        <img src="{{ $player->player_face_url }}" alt="{{ $player->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <span class="text-white font-bold text-sm">{{ substr($player->name, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ $player->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $player->position }} • {{ $player->age }}y • {{ $player->nationality }}
                                    </div>
                                    @if($player->overall_rating)
                                        <div class="text-xs text-gray-400">Rating: {{ $player->overall_rating }}</div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($player->overall_rating)
                                        <div class="text-lg font-bold text-blue-600">{{ $player->overall_rating }}</div>
                                        <div class="text-xs text-gray-500">Rating</div>
                                    @else
                                        <div class="text-sm text-gray-400">No rating</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Player Details -->
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                    @if($player->height)
                                        <div>Height: {{ $player->height }}cm</div>
                                    @endif
                                    @if($player->weight)
                                        <div>Weight: {{ $player->weight }}kg</div>
                                    @endif
                                    @if($player->value_eur)
                                        <div>Value: €{{ number_format($player->value_eur / 1000000, 1) }}M</div>
                                    @endif
                                    @if($player->wage_eur)
                                        <div>Wage: €{{ number_format($player->wage_eur / 1000, 0) }}K</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No players</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by importing players to this club.</p>
                        <div class="mt-6 flex space-x-3">
                            <a href="{{ route('player-registration.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Register a Player
                            </a>
                            <a href="{{ route('club-management.players.import') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Import Players
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @else
        <!-- No Club Selected -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No club selected</h3>
                    <p class="mt-1 text-sm text-gray-500">Please select a club to view its players.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 