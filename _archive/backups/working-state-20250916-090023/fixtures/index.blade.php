@extends('layouts.app')

@section('title', 'Fixtures')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 gap-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Fixtures
                </h1>
                <p class="text-gray-600 mt-2">View and manage fixtures across all competitions</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
                <a href="{{ route('competition-management.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Manage Competitions
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Competitions</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_competitions'] }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Fixtures</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['upcoming_fixtures'] }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Recent Fixtures</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['recent_fixtures'] }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Today's Fixtures</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['today_fixtures'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Competitions Overview -->
        @if($competitions->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Active Competitions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($competitions as $competition)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-medium text-gray-900">{{ $competition->name }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $competition->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($competition->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ $competition->season }}</p>
                        <p class="text-sm text-gray-500 mb-3">{{ $competition->association->name ?? 'N/A' }}</p>
                        <div class="flex space-x-2">
                            <a href="{{ route('competition-management.competitions.fixtures', $competition) }}" 
                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">View Fixtures</a>
                            <a href="{{ route('competition-management.competitions.standings', $competition) }}" 
                               class="text-green-600 hover:text-green-900 text-sm font-medium">Standings</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Upcoming Fixtures -->
        @if($upcomingFixtures->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Fixtures</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Home Team</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">vs</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Away Team</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($upcomingFixtures as $fixture)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $fixture->scheduled_at ? $fixture->scheduled_at->format('M d, Y') : 'TBD' }}</div>
                                    <div class="text-sm text-gray-500">{{ $fixture->scheduled_at ? $fixture->scheduled_at->format('H:i') : 'TBD' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fixture->competition->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $fixture->competition->type ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($fixture->homeTeam && $fixture->homeTeam->club && $fixture->homeTeam->club->logo_url)
                                                <img class="h-8 w-8 rounded-full" src="{{ $fixture->homeTeam->club->logo_url }}" alt="{{ $fixture->homeTeam->club->name }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-500">{{ substr($fixture->homeTeam->club->name ?? 'TBD', 0, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $fixture->homeTeam->club->name ?? 'TBD' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium text-gray-900">vs</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($fixture->awayTeam && $fixture->awayTeam->club && $fixture->awayTeam->club->logo_url)
                                                <img class="h-8 w-8 rounded-full" src="{{ $fixture->awayTeam->club->logo_url }}" alt="{{ $fixture->awayTeam->club->name }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-500">{{ substr($fixture->awayTeam->club->name ?? 'TBD', 0, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $fixture->awayTeam->club->name ?? 'TBD' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fixture->venue ?? 'TBD' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('competition-management.matches.match-sheet', $fixture) }}" class="text-blue-600 hover:text-blue-900 mr-3">Match Sheet</a>
                                    <a href="{{ route('competition-management.matches.match.show', $fixture) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Fixtures -->
        @if($recentFixtures->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Fixtures</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Home Team</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Away Team</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentFixtures as $fixture)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $fixture->scheduled_at ? $fixture->scheduled_at->format('M d, Y') : 'TBD' }}</div>
                                    <div class="text-sm text-gray-500">{{ $fixture->scheduled_at ? $fixture->scheduled_at->diffForHumans() : 'TBD' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $fixture->competition->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $fixture->competition->type ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($fixture->homeTeam && $fixture->homeTeam->club && $fixture->homeTeam->club->logo_url)
                                                <img class="h-8 w-8 rounded-full" src="{{ $fixture->homeTeam->club->logo_url }}" alt="{{ $fixture->homeTeam->club->name }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-500">{{ substr($fixture->homeTeam->club->name ?? 'TBD', 0, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $fixture->homeTeam->club->name ?? 'TBD' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($fixture->home_team_score !== null && $fixture->away_team_score !== null)
                                            {{ $fixture->home_team_score }} - {{ $fixture->away_team_score }}
                                        @else
                                            <span class="text-gray-500">TBD</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($fixture->awayTeam && $fixture->awayTeam->club && $fixture->awayTeam->club->logo_url)
                                                <img class="h-8 w-8 rounded-full" src="{{ $fixture->awayTeam->club->logo_url }}" alt="{{ $fixture->awayTeam->club->name }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-500">{{ substr($fixture->awayTeam->club->name ?? 'TBD', 0, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $fixture->awayTeam->club->name ?? 'TBD' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'scheduled' => 'bg-blue-100 text-blue-800',
                                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'postponed' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusColor = $statusColors[$fixture->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ ucfirst($fixture->status ?? 'unknown') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('competition-management.matches.match-sheet', $fixture) }}" class="text-blue-600 hover:text-blue-900 mr-3">Match Sheet</a>
                                    <a href="{{ route('competition-management.matches.match.show', $fixture) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Empty State -->
        @if($competitions->count() === 0 && $upcomingFixtures->count() === 0 && $recentFixtures->count() === 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No fixtures found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating competitions and generating fixtures.</p>
                <div class="mt-6">
                    <a href="{{ route('competition-management.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Manage Competitions
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 