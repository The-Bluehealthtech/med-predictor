@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('Manual Fixture Entry') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ $competition->name }} - {{ $competition->season }}</p>
                    </div>
                    <a href="{{ route('competition-management.competitions.show', $competition) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        {{ __('Back to Competition') }}
                    </a>
                </div>

                @if($existingMatches->count() > 0)
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    {{ __('Existing Fixtures Found') }}
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>{{ __('There are already') }} {{ $existingMatches->count() }} {{ __('matches scheduled. Creating new manual fixtures will replace all existing fixtures.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('competition-management.competitions.manual-fixtures.store', $competition) }}" method="POST" id="manualFixturesForm">
                    @csrf
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Available Match Combinations') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ __('Select the matches you want to schedule and set their dates and times.') }}</p>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                            @foreach($matchCombinations as $index => $match)
                                <div class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <input type="checkbox" 
                                                       name="matches[{{ $index }}][enabled]" 
                                                       value="1" 
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                       onchange="toggleMatchRow({{ $index }})">
                                                <span class="font-medium text-gray-900">{{ $match['home_team_name'] }}</span>
                                                <span class="text-gray-500">vs</span>
                                                <span class="font-medium text-gray-900">{{ $match['away_team_name'] }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $match['home_club_name'] }} vs {{ $match['away_club_name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ __('Stadium') }}: {{ $match['stadium'] }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3 match-details" id="match-details-{{ $index }}" style="display: none;">
                                        <input type="hidden" name="matches[{{ $index }}][home_team_id]" value="{{ $match['home_team_id'] }}">
                                        <input type="hidden" name="matches[{{ $index }}][away_team_id]" value="{{ $match['away_team_id'] }}">
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Matchday') }}</label>
                                                <input type="number" 
                                                       name="matches[{{ $index }}][matchday]" 
                                                       min="1" 
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Match Date') }}</label>
                                                <input type="date" 
                                                       name="matches[{{ $index }}][match_date]" 
                                                       min="{{ $competition->start_date }}" 
                                                       max="{{ $competition->end_date }}"
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Kickoff Time') }}</label>
                                                <input type="time" 
                                                       name="matches[{{ $index }}][kickoff_time]" 
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Referee') }}</label>
                                                <input type="text" 
                                                       name="matches[{{ $index }}][referee]" 
                                                       placeholder="TBD"
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Assistant Referee 1') }}</label>
                                                <input type="text" 
                                                       name="matches[{{ $index }}][assistant_referee_1]" 
                                                       placeholder="TBD"
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Assistant Referee 2') }}</label>
                                                <input type="text" 
                                                       name="matches[{{ $index }}][assistant_referee_2]" 
                                                       placeholder="TBD"
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('Fourth Official') }}</label>
                                                <input type="text" 
                                                       name="matches[{{ $index }}][fourth_official]" 
                                                       placeholder="TBD"
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <span id="selectedMatchesCount">0</span> {{ __('matches selected') }}
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" 
                                    onclick="selectAllMatches()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                {{ __('Select All') }}
                            </button>
                            <button type="button" 
                                    onclick="clearAllMatches()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                {{ __('Clear All') }}
                            </button>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Create Manual Fixtures') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMatchRow(index) {
    const checkbox = document.querySelector(`input[name="matches[${index}][enabled]"]`);
    const details = document.getElementById(`match-details-${index}`);
    
    if (checkbox.checked) {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
    
    updateSelectedCount();
}

function selectAllMatches() {
    const checkboxes = document.querySelectorAll('input[name$="[enabled]"]');
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = true;
        toggleMatchRow(index);
    });
}

function clearAllMatches() {
    const checkboxes = document.querySelectorAll('input[name$="[enabled]"]');
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = false;
        toggleMatchRow(index);
    });
}

function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('input[name$="[enabled]"]:checked').length;
    document.getElementById('selectedMatchesCount').textContent = selectedCount;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>
@endsection

@section('title', 'Manual Fixture Entry - ' . $competition->name) 