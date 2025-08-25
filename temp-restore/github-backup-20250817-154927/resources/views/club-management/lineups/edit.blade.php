@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ __('Edit Lineup') }} - {{ $club->name }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Update lineup information and player selection
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('club-management.lineups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Lineups
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('club-management.lineups.update', $lineup->id) }}" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Lineup Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $lineup->name) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="team_id" class="block text-sm font-medium text-gray-700">Team</label>
                                <select name="team_id" id="team_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select a team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('team_id', $lineup->team_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="formation" class="block text-sm font-medium text-gray-700">Formation</label>
                                <select name="formation" id="formation" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select formation</option>
                                    <option value="4-4-2" {{ old('formation', $lineup->formation) == '4-4-2' ? 'selected' : '' }}>4-4-2</option>
                                    <option value="4-3-3" {{ old('formation', $lineup->formation) == '4-3-3' ? 'selected' : '' }}>4-3-3</option>
                                    <option value="3-5-2" {{ old('formation', $lineup->formation) == '3-5-2' ? 'selected' : '' }}>3-5-2</option>
                                    <option value="4-2-3-1" {{ old('formation', $lineup->formation) == '4-2-3-1' ? 'selected' : '' }}>4-2-3-1</option>
                                    <option value="3-4-3" {{ old('formation', $lineup->formation) == '3-4-3' ? 'selected' : '' }}>3-4-3</option>
                                    <option value="5-3-2" {{ old('formation', $lineup->formation) == '5-3-2' ? 'selected' : '' }}>5-3-2</option>
                                </select>
                                @error('formation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="match_type" class="block text-sm font-medium text-gray-700">Match Type</label>
                                <select name="match_type" id="match_type" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select match type</option>
                                    <option value="league" {{ old('match_type', $lineup->match_type) == 'league' ? 'selected' : '' }}>League</option>
                                    <option value="cup" {{ old('match_type', $lineup->match_type) == 'cup' ? 'selected' : '' }}>Cup</option>
                                    <option value="friendly" {{ old('match_type', $lineup->match_type) == 'friendly' ? 'selected' : '' }}>Friendly</option>
                                    <option value="training" {{ old('match_type', $lineup->match_type) == 'training' ? 'selected' : '' }}>Training</option>
                                </select>
                                @error('match_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Match Details -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Match Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="opponent" class="block text-sm font-medium text-gray-700">Opponent</label>
                                <input type="text" name="opponent" id="opponent" value="{{ old('opponent', $lineup->opponent) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('opponent')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="venue" class="block text-sm font-medium text-gray-700">Venue</label>
                                <input type="text" name="venue" id="venue" value="{{ old('venue', $lineup->venue) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('venue')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="weather_conditions" class="block text-sm font-medium text-gray-700">Weather Conditions</label>
                                <select name="weather_conditions" id="weather_conditions"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select weather</option>
                                    <option value="sunny" {{ old('weather_conditions', $lineup->weather_conditions) == 'sunny' ? 'selected' : '' }}>Sunny</option>
                                    <option value="cloudy" {{ old('weather_conditions', $lineup->weather_conditions) == 'cloudy' ? 'selected' : '' }}>Cloudy</option>
                                    <option value="rainy" {{ old('weather_conditions', $lineup->weather_conditions) == 'rainy' ? 'selected' : '' }}>Rainy</option>
                                    <option value="windy" {{ old('weather_conditions', $lineup->weather_conditions) == 'windy' ? 'selected' : '' }}>Windy</option>
                                </select>
                                @error('weather_conditions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pitch_condition" class="block text-sm font-medium text-gray-700">Pitch Condition</label>
                                <select name="pitch_condition" id="pitch_condition"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select pitch condition</option>
                                    <option value="excellent" {{ old('pitch_condition', $lineup->pitch_condition) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ old('pitch_condition', $lineup->pitch_condition) == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="fair" {{ old('pitch_condition', $lineup->pitch_condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="poor" {{ old('pitch_condition', $lineup->pitch_condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                                </select>
                                @error('pitch_condition')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Tactical Settings -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tactical Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="pressing_intensity" class="block text-sm font-medium text-gray-700">Pressing Intensity</label>
                                <select name="pressing_intensity" id="pressing_intensity"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select intensity</option>
                                    <option value="high" {{ old('pressing_intensity', $lineup->pressing_intensity) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="medium" {{ old('pressing_intensity', $lineup->pressing_intensity) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="low" {{ old('pressing_intensity', $lineup->pressing_intensity) == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>

                            <div>
                                <label for="possession_style" class="block text-sm font-medium text-gray-700">Possession Style</label>
                                <select name="possession_style" id="possession_style"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select style</option>
                                    <option value="possession" {{ old('possession_style', $lineup->possession_style) == 'possession' ? 'selected' : '' }}>Possession</option>
                                    <option value="counter" {{ old('possession_style', $lineup->possession_style) == 'counter' ? 'selected' : '' }}>Counter Attack</option>
                                    <option value="direct" {{ old('possession_style', $lineup->possession_style) == 'direct' ? 'selected' : '' }}>Direct</option>
                                </select>
                            </div>

                            <div>
                                <label for="defensive_line_height" class="block text-sm font-medium text-gray-700">Defensive Line Height</label>
                                <select name="defensive_line_height" id="defensive_line_height"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select height</option>
                                    <option value="high" {{ old('defensive_line_height', $lineup->defensive_line_height) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="medium" {{ old('defensive_line_height', $lineup->defensive_line_height) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="low" {{ old('defensive_line_height', $lineup->defensive_line_height) == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>

                            <div>
                                <label for="marking_system" class="block text-sm font-medium text-gray-700">Marking System</label>
                                <select name="marking_system" id="marking_system"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select system</option>
                                    <option value="man" {{ old('marking_system', $lineup->marking_system) == 'man' ? 'selected' : '' }}>Man Marking</option>
                                    <option value="zone" {{ old('marking_system', $lineup->marking_system) == 'zone' ? 'selected' : '' }}>Zone Marking</option>
                                    <option value="mixed" {{ old('marking_system', $lineup->marking_system) == 'mixed' ? 'selected' : '' }}>Mixed</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="tactical_notes" class="block text-sm font-medium text-gray-700">Tactical Notes</label>
                            <textarea name="tactical_notes" id="tactical_notes" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('tactical_notes', $lineup->tactical_notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Special Roles -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Special Roles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="captain_id" class="block text-sm font-medium text-gray-700">Captain</label>
                                <select name="captain_id" id="captain_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select captain</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}" {{ old('captain_id', $lineup->captain_id) == $player->id ? 'selected' : '' }}>
                                            {{ $player->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="vice_captain_id" class="block text-sm font-medium text-gray-700">Vice Captain</label>
                                <select name="vice_captain_id" id="vice_captain_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select vice captain</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}" {{ old('vice_captain_id', $lineup->vice_captain_id) == $player->id ? 'selected' : '' }}>
                                            {{ $player->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="penalty_taker_id" class="block text-sm font-medium text-gray-700">Penalty Taker</label>
                                <select name="penalty_taker_id" id="penalty_taker_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select penalty taker</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}" {{ old('penalty_taker_id', $lineup->penalty_taker_id) == $player->id ? 'selected' : '' }}>
                                            {{ $player->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="free_kick_taker_id" class="block text-sm font-medium text-gray-700">Free Kick Taker</label>
                                <select name="free_kick_taker_id" id="free_kick_taker_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select free kick taker</option>
                                    @foreach($players as $player)
                                        <option value="{{ $player->id }}" {{ old('free_kick_taker_id', $lineup->free_kick_taker_id) == $player->id ? 'selected' : '' }}>
                                            {{ $player->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('club-management.lineups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update Lineup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 