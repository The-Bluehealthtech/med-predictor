@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ __('Create Team') }} - {{ $club->name }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Create a new team for the club
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('club-management.teams.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Teams
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('club-management.teams.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Team Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Team Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Team Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Team Type</label>
                            <select name="type" id="type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select team type</option>
                                @foreach($teamTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Formation -->
                        <div>
                            <label for="formation" class="block text-sm font-medium text-gray-700">Formation</label>
                            <select name="formation" id="formation" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select formation</option>
                                @foreach($formations as $value => $label)
                                    <option value="{{ $value }}" {{ old('formation') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('formation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Season -->
                        <div>
                            <label for="season" class="block text-sm font-medium text-gray-700">Season</label>
                            <input type="text" name="season" id="season" value="{{ old('season', '2024/25') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('season')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Coach Name -->
                        <div>
                            <label for="coach_name" class="block text-sm font-medium text-gray-700">Coach Name</label>
                            <input type="text" name="coach_name" id="coach_name" value="{{ old('coach_name') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('coach_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Budget Allocation -->
                        <div>
                            <label for="budget_allocation" class="block text-sm font-medium text-gray-700">Budget Allocation (€)</label>
                            <input type="number" name="budget_allocation" id="budget_allocation" value="{{ old('budget_allocation') }}" step="0.01" min="0"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('budget_allocation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tactical Style -->
                    <div>
                        <label for="tactical_style" class="block text-sm font-medium text-gray-700">Tactical Style</label>
                        <input type="text" name="tactical_style" id="tactical_style" value="{{ old('tactical_style') }}"
                            placeholder="e.g., Possession-based, Counter-attacking, High-pressing"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('tactical_style')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Playing Philosophy -->
                    <div>
                        <label for="playing_philosophy" class="block text-sm font-medium text-gray-700">Playing Philosophy</label>
                        <textarea name="playing_philosophy" id="playing_philosophy" rows="3"
                            placeholder="Describe the team's playing philosophy and approach to the game..."
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('playing_philosophy') }}</textarea>
                        @error('playing_philosophy')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('club-management.teams.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Create Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 