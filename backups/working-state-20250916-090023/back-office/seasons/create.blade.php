<x-back-office-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create New Season</h1>
                        <p class="mt-1 text-sm text-gray-600">Set up a new competition season with registration periods</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('back-office.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('back-office.seasons.store') }}" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Season Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="e.g., 2024/25 Premier League Season">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="short_name" class="block text-sm font-medium text-gray-700">Short Name</label>
                            <input type="text" name="short_name" id="short_name" value="{{ old('short_name') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="e.g., 2024/25">
                            @error('short_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                      placeholder="Optional description of the season">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Season Dates -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Season Dates</h3>
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Season Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Season End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Registration Period -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registration Period</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="registration_start_date" class="block text-sm font-medium text-gray-700">Registration Start Date</label>
                            <input type="date" name="registration_start_date" id="registration_start_date" value="{{ old('registration_start_date') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">When clubs can start registering players</p>
                            @error('registration_start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_end_date" class="block text-sm font-medium text-gray-700">Registration End Date</label>
                            <input type="date" name="registration_end_date" id="registration_end_date" value="{{ old('registration_end_date') }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">Deadline for player registration</p>
                            @error('registration_end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Season Settings -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Season Settings</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="space-y-4">
                            <div>
                                <label for="max_players_per_team" class="block text-sm font-medium text-gray-700">Maximum Players per Team</label>
                                <input type="number" name="settings[max_players_per_team]" id="max_players_per_team" 
                                       value="{{ old('settings.max_players_per_team', 25) }}" min="1" max="50"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="max_foreign_players" class="block text-sm font-medium text-gray-700">Maximum Foreign Players per Team</label>
                                <input type="number" name="settings[max_foreign_players]" id="max_foreign_players" 
                                       value="{{ old('settings.max_foreign_players', 5) }}" min="0" max="20"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="min_age" class="block text-sm font-medium text-gray-700">Minimum Player Age</label>
                                <input type="number" name="settings[min_age]" id="min_age" 
                                       value="{{ old('settings.min_age', 16) }}" min="12" max="25"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="max_age" class="block text-sm font-medium text-gray-700">Maximum Player Age</label>
                                <input type="number" name="settings[max_age]" id="max_age" 
                                       value="{{ old('settings.max_age', 40) }}" min="25" max="50"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('back-office.seasons.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Create Season
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-calculate end date based on start date (1 year later)
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = new Date(this.value);
            const endDate = new Date(startDate);
            endDate.setFullYear(endDate.getFullYear() + 1);
            endDate.setDate(endDate.getDate() - 1); // One day before next year
            
            document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
        });

        // Auto-calculate registration end date based on season start date (1 month before)
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = new Date(this.value);
            const regEndDate = new Date(startDate);
            regEndDate.setMonth(regEndDate.getMonth() - 1);
            
            document.getElementById('registration_end_date').value = regEndDate.toISOString().split('T')[0];
        });
    </script>
</x-back-office-layout> 