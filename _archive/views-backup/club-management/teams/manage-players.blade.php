@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ __('Manage Players') }} - {{ $team->name }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Add or remove players from the team
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('club-management.teams.show', [$club, $team]) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Team
                        </a>
                        <a href="{{ route('club-management.teams.index', $club) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Teams
                        </a>
                    </div>
                </div>

                <!-- Current Team Players -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Current Team Players ({{ $team->players->count() }})</h3>
                    
                    @if($team->players->count() > 0)
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <ul class="divide-y divide-gray-200">
                                @foreach($team->players as $teamPlayer)
                                    <li class="px-6 py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ $teamPlayer->jersey_number ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $teamPlayer->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $teamPlayer->position }} • Rating: {{ $teamPlayer->overall_rating }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $teamPlayer->position }}
                                                </span>
                                                <form method="POST" action="{{ route('club-management.teams.remove-player', [$club, $team, $teamPlayer]) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to remove this player from the team?')"
                                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No players</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding players to the team.</p>
                        </div>
                    @endif
                </div>

                <!-- Add Player Form -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Player to Team</h3>
                    
                    <form method="POST" action="{{ route('club-management.teams.add-player', [$club, $team]) }}" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Player Selection -->
                            <div>
                                <label for="player_id" class="block text-sm font-medium text-gray-700">Player</label>
                                <select name="player_id" id="player_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a player</option>
                                    @foreach($availablePlayers as $player)
                                        <option value="{{ $player->id }}" {{ old('player_id') == $player->id ? 'selected' : '' }}>
                                            {{ $player->name }} ({{ $player->position }} - {{ $player->overall_rating }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('player_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Position -->
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                                <select name="position" id="position" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select position</option>
                                    <option value="GK" {{ old('position') == 'GK' ? 'selected' : '' }}>Goalkeeper (GK)</option>
                                    <option value="CB" {{ old('position') == 'CB' ? 'selected' : '' }}>Center Back (CB)</option>
                                    <option value="RB" {{ old('position') == 'RB' ? 'selected' : '' }}>Right Back (RB)</option>
                                    <option value="LB" {{ old('position') == 'LB' ? 'selected' : '' }}>Left Back (LB)</option>
                                    <option value="CDM" {{ old('position') == 'CDM' ? 'selected' : '' }}>Defensive Midfielder (CDM)</option>
                                    <option value="CM" {{ old('position') == 'CM' ? 'selected' : '' }}>Center Midfielder (CM)</option>
                                    <option value="CAM" {{ old('position') == 'CAM' ? 'selected' : '' }}>Attacking Midfielder (CAM)</option>
                                    <option value="RW" {{ old('position') == 'RW' ? 'selected' : '' }}>Right Winger (RW)</option>
                                    <option value="LW" {{ old('position') == 'LW' ? 'selected' : '' }}>Left Winger (LW)</option>
                                    <option value="ST" {{ old('position') == 'ST' ? 'selected' : '' }}>Striker (ST)</option>
                                </select>
                                @error('position')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select name="role" id="role" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select role</option>
                                    <option value="starter" {{ old('role') == 'starter' ? 'selected' : '' }}>Starter</option>
                                    <option value="substitute" {{ old('role') == 'substitute' ? 'selected' : '' }}>Substitute</option>
                                    <option value="reserve" {{ old('role') == 'reserve' ? 'selected' : '' }}>Reserve</option>
                                    <option value="loan" {{ old('role') == 'loan' ? 'selected' : '' }}>Loan</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jersey Number -->
                            <div>
                                <label for="jersey_number" class="block text-sm font-medium text-gray-700">Jersey Number</label>
                                <input type="number" name="jersey_number" id="jersey_number" value="{{ old('jersey_number') }}" min="1" max="99"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('jersey_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select status</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="injured" {{ old('status') == 'injured' ? 'selected' : '' }}>Injured</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="loaned_out" {{ old('status') == 'loaned_out' ? 'selected' : '' }}>Loaned Out</option>
                                    <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Add Player
                            </button>
                        </div>
                    </form>
                </div>

                @if($availablePlayers->count() == 0)
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">No available players</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>All players are already assigned to teams or have active licenses. You may need to create new players or manage existing licenses first.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 