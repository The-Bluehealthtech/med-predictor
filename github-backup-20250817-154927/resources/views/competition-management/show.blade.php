@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Competition Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Basic Info -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Basic Information') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->name }}</dd>
                                </div>
                                @if($competition->short_name)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Short Name') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->short_name }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Type') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->type_label }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Format') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->format_label }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Season') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->season }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                    <dd>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($competition->status === 'active') bg-green-100 text-green-800
                                            @elseif($competition->status === 'upcoming') bg-yellow-100 text-yellow-800
                                            @elseif($competition->status === 'completed') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($competition->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Dates -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Important Dates') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Start Date') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->start_date->format('d/m/Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('End Date') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->end_date->format('d/m/Y') }}</dd>
                                </div>
                                @if($competition->registration_deadline)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Registration Deadline') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->registration_deadline->format('d/m/Y') }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- FIFA Connect -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('FIFA Connect') }}</h3>
                            <dl class="space-y-3">
                                @if($competition->fifaConnectId)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('FIFA ID') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $competition->fifaConnectId->fifa_id }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Federation License Required') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($competition->require_federation_license)
                                            <span class="text-green-600">{{ __('Yes') }}</span>
                                        @else
                                            <span class="text-gray-600">{{ __('No') }}</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($competition->description || $competition->rules)
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($competition->description)
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-2">{{ __('Description') }}</h4>
                            <p class="text-sm text-gray-700">{{ $competition->description }}</p>
                        </div>
                        @endif
                        @if($competition->rules)
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-2">{{ __('Rules') }}</h4>
                            <p class="text-sm text-gray-700">{{ $competition->rules }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Registered Teams -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Registered Teams') }} ({{ $competition->teams->count() }}/{{ $competition->max_teams ?? '∞' }})</h3>
                        <a href="{{ route('competition-management.competitions.register-team-form', $competition) }}" 
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Register New Team') }}
                        </a>
                    </div>

                    @if($competition->teams->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($competition->teams as $team)
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $team->club->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $team->name }}</p>
                                            <p class="text-sm text-gray-600">{{ __('Players') }}: {{ $team->players->count() }}</p>
                                            @if($team->club->fifa_id)
                                                <p class="text-xs text-gray-500">FIFA: {{ $team->club->fifa_id }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ __('Registered') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No teams registered') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Get started by registering the first team.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Matches -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Matches') }}</h3>
                            @if($competition->fixtures_validated)
                                <div class="mt-2 flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('Fixtures Locked') }}
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500">
                                        Validated on {{ $competition->fixtures_validated_at->format('d/m/Y H:i') }}
                                        @if($competition->validatedBy)
                                            by {{ $competition->validatedBy->name }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            @if(!$competition->fixtures_validated)
                                <a href="{{ route('competition-management.competitions.fixtures', $competition) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    {{ __('Automated Fixture Generation') }}
                                </a>
                                <a href="{{ route('competition-management.competitions.manual-fixtures', $competition) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    {{ __('Manual Fixture Entry') }}
                                </a>
                                <a href="{{ route('competition-management.competitions.regenerate-fixtures', $competition) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700"
                                   onclick="return confirm('This will delete all existing fixtures and generate new ones. Are you sure?')">
                                    {{ __('Regenerate Fixtures') }}
                                </a>
                                <a href="{{ route('competition-management.competitions.validate-fixtures', $competition) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                   onclick="return confirm('This will lock all fixtures for the season. No changes will be allowed after validation. Are you sure?')">
                                    {{ __('Validate & Lock Fixtures') }}
                                </a>
                            @else
                                <span class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    {{ __('Fixtures Locked') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($matches->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Date') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Home Team') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Score') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Away Team') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($matches as $match)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($match->match_date && method_exists($match->match_date, 'format'))
                                                    {{ $match->match_date->format('d/m/Y H:i') }}
                                                @elseif($match->match_date)
                                                    {{ $match->match_date }}
                                                @else
                                                    TBD
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $match->homeTeam->club->name ?? $match->homeTeam->name ?? $match->home_team_name ?? 'TBD' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($match->home_score !== null && $match->away_score !== null)
                                                    {{ $match->home_score }} - {{ $match->away_score }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $match->awayTeam->club->name ?? $match->awayTeam->name ?? $match->away_team_name ?? 'TBD' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                        @if($match->match_status === 'completed') bg-green-100 text-green-800
                                                        @elseif($match->match_status === 'scheduled') bg-blue-100 text-blue-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($match->match_status) }}
                                                    </span>
                                                    <a href="{{ route('competition-management.matches.match-sheet', $match) }}" 
                                                       class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                                                        {{ __('Match Sheet') }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $matches->links() }}
                        </div>
                        
                        <!-- Match Count Info -->
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500">
                                {{ __('Showing') }} {{ $matches->firstItem() ?? 0 }} - {{ $matches->lastItem() ?? 0 }} 
                                {{ __('of') }} {{ $matches->total() }} {{ __('matches') }}
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No matches scheduled') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Generate fixtures to create matches for this competition.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Match Sheets Management -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Match Sheets Management') }}</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('competition-management.matches.match-sheet.export-all', $competition) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                {{ __('Export All Match Sheets') }}
                            </a>
                        </div>
                    </div>

                    @if($matches->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Match') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Date') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Referee Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Lineup Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Match Sheet Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($matches->take(10) as $match)
                                        @php
                                            $matchSheet = App\Models\MatchSheet::where('match_id', $match->id)->first();
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium">
                                                    {{ $match->homeTeam->club->name ?? $match->homeTeam->name ?? 'TBD' }} vs 
                                                    {{ $match->awayTeam->club->name ?? $match->awayTeam->name ?? 'TBD' }}
                                                </div>
                                                <div class="text-xs text-gray-500">Match #{{ $match->id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($match->match_date && method_exists($match->match_date, 'format'))
                                                    {{ $match->match_date->format('d/m/Y H:i') }}
                                                @elseif($match->match_date)
                                                    {{ $match->match_date }}
                                                @else
                                                    TBD
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($matchSheet && $matchSheet->assigned_referee_id)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ __('Assigned') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ __('Pending') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($matchSheet && $matchSheet->lineups_locked)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ __('Locked') }}
                                                    </span>
                                                @elseif($matchSheet && ($matchSheet->home_team_lineup_signature || $matchSheet->away_team_lineup_signature))
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ __('Partial') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ __('Pending') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($matchSheet)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $matchSheet->status_color }}-100 text-{{ $matchSheet->status_color }}-800">
                                                        {{ $matchSheet->status_label }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ __('Not Created') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('competition-management.matches.match-sheet', $match) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('View') }}
                                                    </a>
                                                    @if($matchSheet && $matchSheet->canEdit(Auth::user()))
                                                        <a href="{{ route('competition-management.matches.match-sheet.edit', $match) }}" 
                                                           class="text-blue-600 hover:text-blue-900">
                                                            {{ __('Edit') }}
                                                        </a>
                                                    @endif
                                                    @if($matchSheet)
                                                        <a href="{{ route('competition-management.matches.match-sheet.export', $match) }}" 
                                                           class="text-green-600 hover:text-green-900">
                                                            {{ __('Export') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($matches->count() > 10)
                            <div class="mt-4 text-center">
                                <p class="text-sm text-gray-500">
                                    {{ __('Showing first 10 matches.') }} 
                                    <a href="{{ route('competition-management.competitions.standings', $competition) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ __('View all matches') }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No match sheets available') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Generate fixtures first to create match sheets.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('title', $competition->name . ' - Competition Details') 