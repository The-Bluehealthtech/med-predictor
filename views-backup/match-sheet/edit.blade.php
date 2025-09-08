@extends('layouts.app')

@section('title', 'Edit Match Sheet - ' . ($match->homeTeam->club->name ?? $match->homeTeam->name) . ' vs ' . ($match->awayTeam->club->name ?? $match->awayTeam->name))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        Edit Match Sheet - {{ $match->homeTeam->club->name ?? $match->homeTeam->name }} vs {{ $match->awayTeam->club->name ?? $match->awayTeam->name }}
                    </h2>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($matchSheet->status === 'draft') bg-gray-100 text-gray-800
                            @elseif($matchSheet->status === 'submitted') bg-yellow-100 text-yellow-800
                            @elseif($matchSheet->status === 'validated') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $matchSheet->status_label }}
                        </span>
                        <a href="{{ route('competition-management.matches.match-sheet', $match) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            View Match Sheet
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('competition-management.matches.match-sheet.update', $match) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Match Information Section -->
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-900 mb-4">1. Match Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Competition</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $match->competition->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Season & Matchday</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $match->competition->season }} - Matchday {{ $match->matchday }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $match->match_date?->format('d/m/Y') ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <label for="match_number" class="block text-sm font-medium text-gray-700">Match Number</label>
                                <input type="text" name="match_number" id="match_number" value="{{ old('match_number', $matchSheet->match_number) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="kickoff_time" class="block text-sm font-medium text-gray-700">Kickoff Time</label>
                                <input type="time" name="kickoff_time" id="kickoff_time" value="{{ old('kickoff_time', $matchSheet->kickoff_time?->format('H:i')) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="stadium_venue" class="block text-sm font-medium text-gray-700">Stadium/Venue</label>
                                <input type="text" name="stadium_venue" id="stadium_venue" value="{{ old('stadium_venue', $matchSheet->stadium_venue) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="weather_conditions" class="block text-sm font-medium text-gray-700">Weather Conditions</label>
                                <select name="weather_conditions" id="weather_conditions" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select weather</option>
                                    <option value="sunny" {{ old('weather_conditions', $matchSheet->weather_conditions) === 'sunny' ? 'selected' : '' }}>Sunny</option>
                                    <option value="cloudy" {{ old('weather_conditions', $matchSheet->weather_conditions) === 'cloudy' ? 'selected' : '' }}>Cloudy</option>
                                    <option value="rainy" {{ old('weather_conditions', $matchSheet->weather_conditions) === 'rainy' ? 'selected' : '' }}>Rainy</option>
                                    <option value="snowy" {{ old('weather_conditions', $matchSheet->weather_conditions) === 'snowy' ? 'selected' : '' }}>Snowy</option>
                                    <option value="windy" {{ old('weather_conditions', $matchSheet->weather_conditions) === 'windy' ? 'selected' : '' }}>Windy</option>
                                </select>
                            </div>
                            <div>
                                <label for="pitch_conditions" class="block text-sm font-medium text-gray-700">Pitch Conditions</label>
                                <select name="pitch_conditions" id="pitch_conditions" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select condition</option>
                                    <option value="excellent" {{ old('pitch_conditions', $matchSheet->pitch_conditions) === 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ old('pitch_conditions', $matchSheet->pitch_conditions) === 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="fair" {{ old('pitch_conditions', $matchSheet->pitch_conditions) === 'fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="poor" {{ old('pitch_conditions', $matchSheet->pitch_conditions) === 'poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Teams Section -->
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-900 mb-4">2. Teams</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Home Team -->
                            <div>
                                <h4 class="text-md font-semibold text-green-800 mb-3">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }} (Home)</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label for="home_team_coach" class="block text-sm font-medium text-gray-700">Coach</label>
                                        <input type="text" name="home_team_coach" id="home_team_coach" value="{{ old('home_team_coach', $matchSheet->home_team_coach) }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="home_team_manager" class="block text-sm font-medium text-gray-700">Manager</label>
                                        <input type="text" name="home_team_manager" id="home_team_manager" value="{{ old('home_team_manager', $matchSheet->home_team_manager) }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Away Team -->
                            <div>
                                <h4 class="text-md font-semibold text-green-800 mb-3">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }} (Away)</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label for="away_team_coach" class="block text-sm font-medium text-gray-700">Coach</label>
                                        <input type="text" name="away_team_coach" id="away_team_coach" value="{{ old('away_team_coach', $matchSheet->away_team_coach) }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="away_team_manager" class="block text-sm font-medium text-gray-700">Manager</label>
                                        <input type="text" name="away_team_manager" id="away_team_manager" value="{{ old('away_team_manager', $matchSheet->away_team_manager) }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Player Rosters Section -->
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-900 mb-4">3. Player Rosters</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Home Team Roster -->
                            <div>
                                <h4 class="text-md font-semibold text-purple-800 mb-3">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }} - Starting XI</h4>
                                <div class="space-y-2">
                                    @for($i = 1; $i <= 11; $i++)
                                        <div class="flex items-center space-x-2">
                                            <span class="w-8 text-sm font-medium text-gray-600">{{ $i }}</span>
                                            <select name="home_team_roster[]" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                                <option value="">Select player</option>
                                                @foreach($homeTeamPlayers as $player)
                                                    <option value="{{ $player->id }}" {{ old("home_team_roster.{$i-1}", $matchSheet->home_team_roster[$i-1] ?? '') == $player->id ? 'selected' : '' }}>
                                                        {{ $player->name }} ({{ $player->position }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endfor
                                </div>
                                
                                <h5 class="text-sm font-semibold text-purple-700 mt-4 mb-2">Substitutes</h5>
                                <div class="space-y-2">
                                    @for($i = 1; $i <= 7; $i++)
                                        <div class="flex items-center space-x-2">
                                            <span class="w-8 text-sm font-medium text-gray-600">{{ 11 + $i }}</span>
                                            <select name="home_team_substitutes[]" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                                <option value="">Select substitute</option>
                                                @foreach($homeTeamPlayers as $player)
                                                    <option value="{{ $player->id }}" {{ old("home_team_substitutes.{$i-1}", $matchSheet->home_team_substitutes[$i-1] ?? '') == $player->id ? 'selected' : '' }}>
                                                        {{ $player->name }} ({{ $player->position }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <!-- Away Team Roster -->
                            <div>
                                <h4 class="text-md font-semibold text-purple-800 mb-3">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }} - Starting XI</h4>
                                <div class="space-y-2">
                                    @for($i = 1; $i <= 11; $i++)
                                        <div class="flex items-center space-x-2">
                                            <span class="w-8 text-sm font-medium text-gray-600">{{ $i }}</span>
                                            <select name="away_team_roster[]" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                                <option value="">Select player</option>
                                                @foreach($awayTeamPlayers as $player)
                                                    <option value="{{ $player->id }}" {{ old("away_team_roster.{$i-1}", $matchSheet->away_team_roster[$i-1] ?? '') == $player->id ? 'selected' : '' }}>
                                                        {{ $player->name }} ({{ $player->position }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endfor
                                </div>
                                
                                <h5 class="text-sm font-semibold text-purple-700 mt-4 mb-2">Substitutes</h5>
                                <div class="space-y-2">
                                    @for($i = 1; $i <= 7; $i++)
                                        <div class="flex items-center space-x-2">
                                            <span class="w-8 text-sm font-medium text-gray-600">{{ 11 + $i }}</span>
                                            <select name="away_team_substitutes[]" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                                <option value="">Select substitute</option>
                                                @foreach($awayTeamPlayers as $player)
                                                    <option value="{{ $player->id }}" {{ old("away_team_substitutes.{$i-1}", $matchSheet->away_team_substitutes[$i-1] ?? '') == $player->id ? 'selected' : '' }}>
                                                        {{ $player->name }} ({{ $player->position }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Officials Section -->
                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-4">4. Match Officials</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="main_referee_id" class="block text-sm font-medium text-gray-700">Main Referee *</label>
                                <select name="main_referee_id" id="main_referee_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select main referee</option>
                                    @foreach($referees as $referee)
                                        <option value="{{ $referee->id }}" {{ old('main_referee_id', $matchSheet->main_referee_id) == $referee->id ? 'selected' : '' }}>
                                            {{ $referee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="assistant_referee_1_id" class="block text-sm font-medium text-gray-700">Assistant Referee 1</label>
                                <select name="assistant_referee_1_id" id="assistant_referee_1_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select assistant referee</option>
                                    @foreach($referees as $referee)
                                        <option value="{{ $referee->id }}" {{ old('assistant_referee_1_id', $matchSheet->assistant_referee_1_id) == $referee->id ? 'selected' : '' }}>
                                            {{ $referee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="assistant_referee_2_id" class="block text-sm font-medium text-gray-700">Assistant Referee 2</label>
                                <select name="assistant_referee_2_id" id="assistant_referee_2_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select assistant referee</option>
                                    @foreach($referees as $referee)
                                        <option value="{{ $referee->id }}" {{ old('assistant_referee_2_id', $matchSheet->assistant_referee_2_id) == $referee->id ? 'selected' : '' }}>
                                            {{ $referee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="fourth_official_id" class="block text-sm font-medium text-gray-700">Fourth Official</label>
                                <select name="fourth_official_id" id="fourth_official_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select fourth official</option>
                                    @foreach($referees as $referee)
                                        <option value="{{ $referee->id }}" {{ old('fourth_official_id', $matchSheet->fourth_official_id) == $referee->id ? 'selected' : '' }}>
                                            {{ $referee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="var_referee_id" class="block text-sm font-medium text-gray-700">VAR Referee</label>
                                <select name="var_referee_id" id="var_referee_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select VAR referee</option>
                                    @foreach($referees as $referee)
                                        <option value="{{ $referee->id }}" {{ old('var_referee_id', $matchSheet->var_referee_id) == $referee->id ? 'selected' : '' }}>
                                            {{ $referee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="var_assistant_id" class="block text-sm font-medium text-gray-700">VAR Assistant</label>
                                <select name="var_assistant_id" id="var_assistant_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="">Select VAR assistant</option>
                                    @foreach($referees as $referee)
                                        <option value="{{ $referee->id }}" {{ old('var_assistant_id', $matchSheet->var_assistant_id) == $referee->id ? 'selected' : '' }}>
                                            {{ $referee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Match Results Section -->
                    <div class="bg-red-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-red-900 mb-4">5. Match Results</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="home_team_score" class="block text-sm font-medium text-gray-700">{{ $match->homeTeam->club->name ?? $match->homeTeam->name }} Score</label>
                                <input type="number" name="home_team_score" id="home_team_score" min="0" max="50" value="{{ old('home_team_score', $matchSheet->home_team_score) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div class="flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-600">vs</span>
                            </div>
                            <div>
                                <label for="away_team_score" class="block text-sm font-medium text-gray-700">{{ $match->awayTeam->club->name ?? $match->awayTeam->name }} Score</label>
                                <input type="number" name="away_team_score" id="away_team_score" min="0" max="50" value="{{ old('away_team_score', $matchSheet->away_team_score) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="match_status" class="block text-sm font-medium text-gray-700">Match Status *</label>
                            <select name="match_status" id="match_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                <option value="completed" {{ old('match_status', $matchSheet->match_status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="suspended" {{ old('match_status', $matchSheet->match_status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="abandoned" {{ old('match_status', $matchSheet->match_status) === 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <label for="suspension_reason" class="block text-sm font-medium text-gray-700">Suspension/Abandonment Reason</label>
                            <textarea name="suspension_reason" id="suspension_reason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">{{ old('suspension_reason', $matchSheet->suspension_reason) }}</textarea>
                        </div>
                    </div>

                    <!-- Referee Report Section -->
                    <div class="bg-indigo-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-indigo-900 mb-4">6. Referee Report</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="referee_report" class="block text-sm font-medium text-gray-700">Match Report *</label>
                                <textarea name="referee_report" id="referee_report" rows="6" placeholder="Provide a detailed report of the match including key incidents, decisions, and overall conduct..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('referee_report', $matchSheet->referee_report) }}</textarea>
                            </div>
                            <div>
                                <label for="crowd_issues" class="block text-sm font-medium text-gray-700">Crowd Issues</label>
                                <textarea name="crowd_issues" id="crowd_issues" rows="3" placeholder="Any crowd-related incidents or issues..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('crowd_issues', $matchSheet->crowd_issues) }}</textarea>
                            </div>
                            <div>
                                <label for="protests_incidents" class="block text-sm font-medium text-gray-700">Protests/Incidents</label>
                                <textarea name="protests_incidents" id="protests_incidents" rows="3" placeholder="Any protests, incidents, or disciplinary matters..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('protests_incidents', $matchSheet->protests_incidents) }}</textarea>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                <textarea name="notes" id="notes" rows="3" placeholder="Any additional notes or observations..."
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $matchSheet->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Match Sheet
                            </button>
                            @if($matchSheet->status === 'draft')
                                <a href="{{ route('competition-management.matches.match-sheet.submit', $match) }}" 
                                   onclick="return confirm('Are you sure you want to submit this match sheet for validation?')"
                                   class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    Submit for Validation
                                </a>
                            @endif
                        </div>
                        <a href="{{ route('competition-management.competitions.standings', $match->competition) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Back to Standings
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 