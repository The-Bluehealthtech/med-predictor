@extends('layouts.app')

@section('title', $competition->name . ' - Fixtures')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $competition->name }} - Fixtures</h2>
                        <p class="text-gray-600 mt-1">{{ $competition->season }} - {{ $matches->count() }} matches</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('competition-management.competitions.competition.show', $competition) }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Back to Competition
                        </a>
                        <a href="{{ route('competition-management.competitions.standings', $competition) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            View Standings
                        </a>
                    </div>
                </div>

                <!-- Fixtures Status -->
                <div class="mb-6">
                    @if($competition->fixtures_validated)
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Fixtures Locked</h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Fixtures have been validated and locked for the season. No changes are allowed.</p>
                                        <p class="mt-1">Validated on {{ $competition->fixtures_validated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Fixtures Not Validated</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Fixtures are not yet validated. Changes can still be made.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Fixtures Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matchday</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Home Team</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Away Team</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($matches as $match)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $match->matchday }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->match_date ? $match->match_date->format('d/m/Y') : 'TBD' }}
                                        @if($match->kickoff_time)
                                            <br><span class="text-xs text-gray-500">{{ $match->kickoff_time->format('H:i') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                @if($match->homeTeam && $match->homeTeam->club && $match->homeTeam->club->logo_url)
                                                    <img class="h-8 w-8 rounded-full" src="{{ $match->homeTeam->club->logo_url }}" alt="{{ $match->homeTeam->club->name }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-500">{{ substr($match->homeTeam->club->name ?? 'TBD', 0, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $match->homeTeam->club->name ?? 'TBD' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($match->home_score !== null && $match->away_score !== null)
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ $match->home_score }} - {{ $match->away_score }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">vs</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                @if($match->awayTeam && $match->awayTeam->club && $match->awayTeam->club->logo_url)
                                                    <img class="h-8 w-8 rounded-full" src="{{ $match->awayTeam->club->logo_url }}" alt="{{ $match->awayTeam->club->name }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-500">{{ substr($match->awayTeam->club->name ?? 'TBD', 0, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $match->awayTeam->club->name ?? 'TBD' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->stadium ?? 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($match->match_status === 'completed') bg-green-100 text-green-800
                                            @elseif($match->match_status === 'in_progress') bg-yellow-100 text-yellow-800
                                            @elseif($match->match_status === 'scheduled') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $match->match_status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex space-x-2 justify-center">
                                            @if($match->match_status === 'scheduled')
                                                <a href="{{ route('competition-management.matches.match-sheet.show', $match) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">View Match Sheet</a>
                                            @elseif($match->match_status === 'completed')
                                                <a href="{{ route('competition-management.matches.match-sheet.show', $match) }}" 
                                                   class="text-green-600 hover:text-green-900">View Results</a>
                                            @else
                                                <span class="text-gray-400">No Action</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No fixtures found for this competition.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($matches->hasPages())
                    <div class="mt-6">
                        {{ $matches->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 