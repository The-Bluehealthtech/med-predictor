@extends('layouts.app')

@section('title', 'Player Registration Dashboard - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">👥 Player Registration Dashboard</h1>
        <p class="text-gray-600 mt-2">Manage player registrations and FIFA Connect IDs</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Players</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_players'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">This Month</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['registered_this_month'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Licenses</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_licenses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Licenses</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_licenses'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <a href="{{ route('player-registration.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Register New Player</h4>
                    <p class="text-sm text-gray-500">Add a new player with FIFA Connect ID</p>
                </div>
            </a>

                                    <a href="{{ route('players.bulk-import') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">Bulk Import</h4>
                    <p class="text-sm text-gray-500">Import multiple players from CSV</p>
                </div>
            </a>

                                    <a href="{{ route('player-registration.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900">View All Players</h4>
                    <p class="text-sm text-gray-500">Browse and manage all players</p>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Players -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Players</h3>
                                        <a href="{{ route('player-registration.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($recentPlayers as $player)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-semibold text-sm">
                                    {{ substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $player->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $player->position }} • {{ $player->nationality }}</p>
                                <p class="text-xs text-blue-600 font-mono">{{ $player->fifa_connect_id }}</p>
                            </div>
                        </div>
                                                        <a href="{{ route('player-registration.show', $player) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            View
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <p>No players registered yet</p>
                        <a href="{{ route('player-registration.create') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                            Register your first player
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Licenses -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Pending Licenses</h3>
                <span class="text-sm text-gray-500">{{ $pendingLicenses->count() }} pending</span>
            </div>
            <div class="space-y-3">
                @forelse($pendingLicenses as $license)
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $license->player->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $license->club->name ?? 'No Club' }}</p>
                                <p class="text-xs text-yellow-600 font-mono">{{ $license->fifa_connect_id }}</p>
                            </div>
                        </div>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                            Pending
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>No pending licenses</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 