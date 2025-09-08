@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ __('Lineup Generator') }} - {{ $club->name }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Generate lineups for upcoming matches
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('club-management.lineups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Lineups
                        </a>
                    </div>
                </div>

                <!-- Bulk Generation Form -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Lineup Generation</h3>
                    <form method="POST" action="{{ route('club-management.lineups.bulk-generate') }}" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="bulk_formation" class="block text-sm font-medium text-gray-700">Formation</label>
                                <select name="formation" id="bulk_formation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Use team default</option>
                                    <option value="4-4-2">4-4-2</option>
                                    <option value="4-3-3">4-3-3</option>
                                    <option value="3-5-2">3-5-2</option>
                                    <option value="4-2-3-1">4-2-3-1</option>
                                    <option value="3-4-3">3-4-3</option>
                                    <option value="5-3-2">5-3-2</option>
                                </select>
                            </div>

                            <div>
                                <label for="bulk_tactical_style" class="block text-sm font-medium text-gray-700">Tactical Style</label>
                                <select name="tactical_style" id="bulk_tactical_style" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="balanced">Balanced</option>
                                    <option value="attacking">Attacking</option>
                                    <option value="defensive">Defensive</option>
                                    <option value="possession">Possession</option>
                                    <option value="counter">Counter Attack</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Generate All Lineups
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Matches</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-64 overflow-y-auto">
                                @foreach($upcomingMatches as $match)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="match_ids[]" value="{{ $match->id }}" id="match_{{ $match->id }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="match_{{ $match->id }}" class="ml-2 text-sm text-gray-700">
                                            <div class="font-medium">{{ $match->home_team->name }} vs {{ $match->away_team->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $match->match_date->format('M d, Y H:i') }}</div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Individual Match Generation -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Individual Match Lineup Generation</h3>
                    
                    @if($upcomingMatches->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingMatches as $match)
                                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $match->home_team->name }} vs {{ $match->away_team->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $match->competition->name ?? 'No Competition' }}</p>
                                            <p class="text-sm text-gray-500">{{ $match->match_date->format('M d, Y H:i') }} • {{ $match->stadium }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $match->match_status }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Home Team Lineup -->
                                        @if($match->home_team->club_id === $club->id)
                                            <div class="border border-gray-200 rounded-lg p-3">
                                                <h5 class="font-medium text-gray-900 mb-2">{{ $match->home_team->name }} (Home)</h5>
                                                <form method="POST" action="{{ route('club-management.lineups.generate') }}" class="space-y-3">
                                                    @csrf
                                                    <input type="hidden" name="team_id" value="{{ $match->home_team->id }}">
                                                    <input type="hidden" name="match_id" value="{{ $match->id }}">
                                                    
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Formation</label>
                                                            <select name="formation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                                                                <option value="">Default</option>
                                                                <option value="4-4-2">4-4-2</option>
                                                                <option value="4-3-3">4-3-3</option>
                                                                <option value="3-5-2">3-5-2</option>
                                                                <option value="4-2-3-1">4-2-3-1</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Style</label>
                                                            <select name="tactical_style" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                                                                <option value="balanced">Balanced</option>
                                                                <option value="attacking">Attacking</option>
                                                                <option value="defensive">Defensive</option>
                                                                <option value="possession">Possession</option>
                                                                <option value="counter">Counter</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Generate Lineup
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        <!-- Away Team Lineup -->
                                        @if($match->away_team->club_id === $club->id)
                                            <div class="border border-gray-200 rounded-lg p-3">
                                                <h5 class="font-medium text-gray-900 mb-2">{{ $match->away_team->name }} (Away)</h5>
                                                <form method="POST" action="{{ route('club-management.lineups.generate') }}" class="space-y-3">
                                                    @csrf
                                                    <input type="hidden" name="team_id" value="{{ $match->away_team->id }}">
                                                    <input type="hidden" name="match_id" value="{{ $match->id }}">
                                                    
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Formation</label>
                                                            <select name="formation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                                                                <option value="">Default</option>
                                                                <option value="4-4-2">4-4-2</option>
                                                                <option value="4-3-3">4-3-3</option>
                                                                <option value="3-5-2">3-5-2</option>
                                                                <option value="4-2-3-1">4-2-3-1</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Style</label>
                                                            <select name="tactical_style" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-xs">
                                                                <option value="balanced">Balanced</option>
                                                                <option value="attacking">Attacking</option>
                                                                <option value="defensive">Defensive</option>
                                                                <option value="possession">Possession</option>
                                                                <option value="counter">Counter</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Generate Lineup
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No upcoming matches found</h3>
                            <p class="text-gray-500">
                                There are no scheduled matches for {{ $club->name }} teams.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Team Information -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Team Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($teams as $team)
                            <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                <h4 class="font-medium text-gray-900">{{ $team->name }}</h4>
                                <p class="text-sm text-gray-600">{{ ucfirst($team->type) }}</p>
                                <div class="mt-2 space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500">Formation:</span>
                                        <span class="font-medium">{{ $team->formation ?? 'Not set' }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500">Squad Size:</span>
                                        <span class="font-medium">{{ $team->getSquadSize() }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-500">Avg Rating:</span>
                                        <span class="font-medium">{{ number_format($team->getAverageRating(), 1) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all matches checkbox
    const selectAllCheckbox = document.getElementById('select_all_matches');
    const matchCheckboxes = document.querySelectorAll('input[name="match_ids[]"]');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            matchCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
});
</script>
@endsection 