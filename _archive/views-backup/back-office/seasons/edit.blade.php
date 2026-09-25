<x-back-office-layout>
    <x-slot name="title">Edit Season - Back Office</x-slot>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Season</h1>
                    <p class="mt-1 text-sm text-gray-600">Update season information</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('back-office.seasons.show', $season) }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Back to Season
                    </a>
                    <a href="{{ route('back-office.seasons.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Back to Seasons
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('back-office.seasons.update', $season) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Season Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $season->name) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror" 
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="short_name" class="block text-sm font-medium text-gray-700">Short Name *</label>
                            <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $season->short_name) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('short_name') border-red-500 @enderror" 
                                   required>
                            @error('short_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $season->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select name="status" id="status" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror" 
                                    required>
                                <option value="draft" {{ old('status', $season->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="upcoming" {{ old('status', $season->status) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="active" {{ old('status', $season->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $season->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="archived" {{ old('status', $season->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Important Dates</h3>
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" 
                                   value="{{ old('start_date', $season->start_date ? $season->start_date->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date" 
                                   value="{{ old('end_date', $season->end_date ? $season->end_date->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_start" class="block text-sm font-medium text-gray-700">Registration Start</label>
                            <input type="date" name="registration_start" id="registration_start" 
                                   value="{{ old('registration_start', $season->registration_start ? $season->registration_start->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('registration_start') border-red-500 @enderror">
                            @error('registration_start')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_end" class="block text-sm font-medium text-gray-700">Registration End</label>
                            <input type="date" name="registration_end" id="registration_end" 
                                   value="{{ old('registration_end', $season->registration_end ? $season->registration_end->format('Y-m-d') : '') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('registration_end') border-red-500 @enderror">
                            @error('registration_end')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="max_teams" class="block text-sm font-medium text-gray-700">Maximum Teams</label>
                            <input type="number" name="max_teams" id="max_teams" 
                                   value="{{ old('max_teams', $season->max_teams) }}" 
                                   min="1" max="100"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('max_teams') border-red-500 @enderror">
                            @error('max_teams')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_players_per_team" class="block text-sm font-medium text-gray-700">Max Players per Team</label>
                            <input type="number" name="max_players_per_team" id="max_players_per_team" 
                                   value="{{ old('max_players_per_team', $season->max_players_per_team) }}" 
                                   min="1" max="50"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('max_players_per_team') border-red-500 @enderror">
                            @error('max_players_per_team')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_registration_open" id="is_registration_open" 
                                   {{ old('is_registration_open', $season->is_registration_open) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_registration_open" class="ml-2 block text-sm text-gray-900">
                                Registration is open
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_current" id="is_current" 
                                   {{ old('is_current', $season->is_current) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_current" class="ml-2 block text-sm text-gray-900">
                                This is the current season
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('back-office.seasons.show', $season) }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Update Season
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add client-side validation if needed
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const registrationStart = document.getElementById('registration_start');
    const registrationEnd = document.getElementById('registration_end');

    // Ensure end date is after start date
    startDate.addEventListener('change', function() {
        if (endDate.value && startDate.value > endDate.value) {
            endDate.value = startDate.value;
        }
    });

    // Ensure registration end is after registration start
    registrationStart.addEventListener('change', function() {
        if (registrationEnd.value && registrationStart.value > registrationEnd.value) {
            registrationEnd.value = registrationStart.value;
        }
    });
});
</script>
</x-back-office-layout> 