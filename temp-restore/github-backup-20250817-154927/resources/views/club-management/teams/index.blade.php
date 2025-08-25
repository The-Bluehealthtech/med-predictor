@extends('layouts.app')

@section('title', 'Teams Management')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 gap-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Teams Management
                </h1>
                <p class="text-gray-600 mt-2">Manage your club's teams and squads</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('club-management.teams.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Team
                </a>
                <a href="{{ route('club-management.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Teams</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $teams->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Teams</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $teams->where('status', 'active')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Players</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $totalPlayers }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">First Teams</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $teams->where('type', 'first_team')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teams List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Teams</h3>
                    <div class="flex space-x-2">
                        <select id="status-filter" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <select id="type-filter" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="first_team">First Team</option>
                            <option value="reserve">Reserve</option>
                            <option value="youth">Youth</option>
                            <option value="academy">Academy</option>
                        </select>
                    </div>
                </div>

                @if($teams->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($teams as $team)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            @if($team->logo_url)
                                                <img src="{{ $team->logo_url }}" alt="{{ $team->name }} Logo" class="w-10 h-10 rounded-full object-cover mr-3">
                                            @else
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-blue-600 font-bold text-sm">{{ substr($team->name, 0, 2) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $team->name }}</h4>
                                                <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $team->type)) }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $team->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($team->status) }}
                                        </span>
                                    </div>

                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Formation:</span>
                                            <span class="font-medium">{{ $team->formation }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Coach:</span>
                                            <span class="font-medium">{{ $team->coach_name ?: 'Not assigned' }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Players:</span>
                                            <span class="font-medium">{{ $team->players_count ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Season:</span>
                                            <span class="font-medium">{{ $team->season }}</span>
                                        </div>
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('club-management.teams.show', $team->id) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-md text-sm font-medium transition duration-150">
                                            View
                                        </a>
                                        <a href="{{ route('club-management.teams.edit', $team->id) }}" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white text-center py-2 px-3 rounded-md text-sm font-medium transition duration-150">
                                            Edit
                                        </a>
                                        <a href="{{ route('club-management.teams.manage-players', $team->id) }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded-md text-sm font-medium transition duration-150">
                                            Players
                                        </a>
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No teams</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new team.</p>
                        <div class="mt-6">
                            <a href="{{ route('club-management.teams.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Team
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('status-filter');
    const typeFilter = document.getElementById('type-filter');
    const teamCards = document.querySelectorAll('[data-team-status][data-team-type]');

    function filterTeams() {
        const selectedStatus = statusFilter.value;
        const selectedType = typeFilter.value;

        teamCards.forEach(card => {
            const teamStatus = card.getAttribute('data-team-status');
            const teamType = card.getAttribute('data-team-type');
            
            const statusMatch = !selectedStatus || teamStatus === selectedStatus;
            const typeMatch = !selectedType || teamType === selectedType;
            
            if (statusMatch && typeMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    statusFilter.addEventListener('change', filterTeams);
    typeFilter.addEventListener('change', filterTeams);
});
</script>
@endsection 