@extends('layouts.app')

@section('title', 'Standings - ' . $competition->name)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header with navigation -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Standings') }} - {{ $competition->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('competition-management.competitions.competition.show', $competition) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Back to Competition') }}
                </a>
                <a href="{{ route('competition-management.competitions.fixtures', $competition) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    {{ __('Fixtures') }}
                </a>
            </div>
        </div>

        <!-- Competition Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500">{{ __('Season') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $competition->season }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500">{{ __('Format') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $competition->format_label }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500">{{ __('Teams') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ count($standings) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500">{{ __('Status') }}</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($competition->status === 'active') bg-green-100 text-green-800
                            @elseif($competition->status === 'upcoming') bg-yellow-100 text-yellow-800
                            @elseif($competition->status === 'completed') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($competition->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Standings Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('League Table') }}</h3>
                
                @if(count($standings) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Pos') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Team') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('P') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('W') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('D') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('L') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('GF') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('GA') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('GD') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Pts') }}
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Form') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($standings as $index => $team)
                                    <tr class="{{ $index < 3 ? 'bg-green-50' : ($index >= count($standings) - 3 ? 'bg-red-50' : '') }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $index + 1 }}
                                            @if($index < 3)
                                                <span class="ml-1 text-green-600">🏆</span>
                                            @elseif($index >= count($standings) - 3)
                                                <span class="ml-1 text-red-600">⚠️</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-indigo-600">
                                                            {{ strtoupper(substr($team['team_name'], 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $team['team_name'] }}
                                                    </div>
                                                    @if(isset($team['club_name']) && $team['club_name'] !== $team['team_name'])
                                                        <div class="text-sm text-gray-500">
                                                            {{ $team['club_name'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $team['played'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $team['won'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $team['drawn'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $team['lost'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $team['goals_for'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                            {{ $team['goals_against'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                            <span class="{{ $team['goal_difference'] > 0 ? 'text-green-600' : ($team['goal_difference'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                                {{ $team['goal_difference'] > 0 ? '+' : '' }}{{ $team['goal_difference'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold text-gray-900">
                                            {{ $team['points'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <div class="flex space-x-1 justify-center">
                                                @foreach(array_slice($team['form'], -5) as $result)
                                                    <span class="w-3 h-3 rounded-full text-xs flex items-center justify-center
                                                        @if($result === 'W') bg-green-500 text-white
                                                        @elseif($result === 'D') bg-yellow-500 text-white
                                                        @else bg-red-500 text-white @endif">
                                                        {{ $result }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-50 border border-green-200 rounded mr-2"></div>
                            <span class="text-sm text-gray-600">{{ __('Championship/Playoff spots') }}</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-50 border border-red-200 rounded mr-2"></div>
                            <span class="text-sm text-gray-600">{{ __('Relegation zone') }}</span>
                        </div>
                        <div class="flex items-center">
                            <div class="flex space-x-1 mr-2">
                                <span class="w-3 h-3 bg-green-500 rounded-full text-xs text-white flex items-center justify-center">W</span>
                                <span class="w-3 h-3 bg-yellow-500 rounded-full text-xs text-white flex items-center justify-center">D</span>
                                <span class="w-3 h-3 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">L</span>
                            </div>
                            <span class="text-sm text-gray-600">{{ __('Last 5 matches') }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No standings available') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Standings will appear once matches have been played.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Matches by Matchday with Navigation -->
        @if(isset($recentMatches) && count($recentMatches) > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Navigation Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Matchday Results') }}</h3>
                    
                    <!-- Navigation Buttons -->
                    <div class="flex items-center space-x-4">
                        @if($previousMatchday)
                            <a href="{{ route('competition-management.competitions.standings', ['competition' => $competition->id, 'matchday' => $previousMatchday]) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                {{ __('Previous') }}
                            </a>
                        @endif
                        
                        <!-- Matchday Selector -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">{{ __('Matchday') }}:</span>
                            <select onchange="window.location.href='{{ route('competition-management.competitions.standings', ['competition' => $competition->id]) }}?matchday=' + this.value" 
                                    class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($matchdayNumbers as $matchdayNum)
                                    <option value="{{ $matchdayNum }}" {{ $currentMatchday == $matchdayNum ? 'selected' : '' }}>
                                        {{ $matchdayNum }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @if($nextMatchday)
                            <a href="{{ route('competition-management.competitions.standings', ['competition' => $competition->id, 'matchday' => $nextMatchday]) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Next') }}
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Current Matchday Display -->
                @foreach($recentMatches as $matchdayData)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <!-- Matchday Header -->
                        <div class="bg-blue-50 px-4 py-3 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h4 class="text-lg font-semibold text-blue-900">
                                    {{ __('Matchday') }} {{ $matchdayData['matchday'] }}
                                </h4>
                                <span class="text-sm text-blue-700">
                                    {{ $matchdayData['date'] ? $matchdayData['date']->format('d/m/Y') : 'TBD' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Matches in this matchday -->
                        <div class="divide-y divide-gray-200">
                            @foreach($matchdayData['matches'] as $match)
                                <div class="flex items-center justify-between p-4 hover:bg-gray-50">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <div class="text-sm font-medium text-gray-900 text-right w-24">
                                            {{ $match->home_team_name }}
                                        </div>
                                        <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded">
                                            @if($match->home_score !== null && $match->away_score !== null)
                                                <span class="font-semibold">{{ $match->home_score }} - {{ $match->away_score }}</span>
                                            @else
                                                <span class="text-gray-400">{{ __('vs') }}</span>
                                            @endif
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 text-left w-24">
                                            {{ $match->away_team_name }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="text-sm text-gray-500">
                                            {{ $match->match_date ? $match->match_date->format('H:i') : '' }}
                                        </div>
                                        <!-- Match Sheet Link -->
                                        @if($match->has_match_sheet)
                                            <a href="{{ route('competition-management.matches.match-sheet', $match->id) }}" 
                                               class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md 
                                                      @if($match->match_sheet_status === 'validated')
                                                          bg-green-100 text-green-800 hover:bg-green-200
                                                      @elseif($match->match_sheet_status === 'submitted')
                                                          bg-yellow-100 text-yellow-800 hover:bg-yellow-200
                                                      @elseif($match->match_sheet_status === 'rejected')
                                                          bg-red-100 text-red-800 hover:bg-red-200
                                                      @else
                                                          bg-blue-100 text-blue-800 hover:bg-blue-200
                                                      @endif">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                @if($match->match_sheet_status === 'validated')
                                                    {{ __('View Match Sheet') }}
                                                @elseif($match->match_sheet_status === 'submitted')
                                                    {{ __('Review Match Sheet') }}
                                                @elseif($match->match_sheet_status === 'rejected')
                                                    {{ __('Rejected') }}
                                                @else
                                                    {{ __('Edit Match Sheet') }}
                                                @endif
                                            </a>
                                        @else
                                            @if($match->is_played)
                                                <a href="{{ route('competition-management.matches.match-sheet', $match->id) }}" 
                                                   class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-800 hover:bg-blue-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    {{ __('Create Match Sheet') }}
                                                </a>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ __('Not Played') }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                <!-- Navigation Info -->
                <div class="mt-4 text-center text-sm text-gray-500">
                    {{ __('Showing matchday') }} {{ $currentMatchday }} {{ __('of') }} {{ $matchdayNumbers->count() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 