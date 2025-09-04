<x-back-office-layout>
    <x-slot name="title">Season Details - Back Office</x-slot>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Season Details</h1>
                    <p class="mt-1 text-sm text-gray-600">View and manage season information</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('back-office.seasons.edit', $season) }}" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Edit Season
                    </a>
                    <a href="{{ route('back-office.seasons.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Back to Seasons
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Season Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $season->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Short Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $season->short_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Duration</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $season->duration }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $season->status_color }}-100 text-{{ $season->status_color }}-800">
                            {{ ucfirst($season->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Important Dates</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $season->start_date ? $season->start_date->format('F j, Y') : 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $season->end_date ? $season->end_date->format('F j, Y') : 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Registration Start</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $season->registration_start ? $season->registration_start->format('F j, Y') : 'Not set' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Registration End</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $season->registration_end ? $season->registration_end->format('F j, Y') : 'Not set' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Season Statistics -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Season Statistics</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $season->competitions()->count() }}</div>
                    <div class="text-sm text-gray-600">Competitions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $season->players()->count() }}</div>
                    <div class="text-sm text-gray-600">Players</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $season->clubs()->count() }}</div>
                    <div class="text-sm text-gray-600">Clubs</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $season->matches()->count() }}</div>
                    <div class="text-sm text-gray-600">Matches</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Competitions -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Competitions</h3>
                <a href="{{ route('competition-management.competitions.create') }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                    Add Competition
                </a>
            </div>
            
            @if($season->competitions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teams</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($season->competitions as $competition)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $competition->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($competition->type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $competition->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $competition->status == 'upcoming' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $competition->status == 'completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($competition->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $competition->teams()->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('competition-management.competitions.show', $competition) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No competitions</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new competition.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Season Actions -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Season Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @if($season->status !== 'active')
                    <form method="POST" action="{{ route('back-office.seasons.activate', $season) }}" class="inline">
                        @csrf
                        <button type="submit" class="w-full flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-green-900">Activate Season</h4>
                                <p class="text-xs text-green-600">Make this the current season</p>
                            </div>
                        </button>
                    </form>
                @endif

                @if($season->status === 'active')
                    <form method="POST" action="{{ route('back-office.seasons.complete', $season) }}" class="inline">
                        @csrf
                        <button type="submit" class="w-full flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-yellow-900">Complete Season</h4>
                                <p class="text-xs text-yellow-600">Mark season as completed</p>
                            </div>
                        </button>
                    </form>
                @endif

                @if($season->status === 'completed')
                    <form method="POST" action="{{ route('back-office.seasons.archive', $season) }}" class="inline">
                        @csrf
                        <button type="submit" class="w-full flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-gray-900">Archive Season</h4>
                                <p class="text-xs text-gray-600">Move to archive</p>
                            </div>
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('back-office.seasons.destroy', $season) }}" 
                      onsubmit="return confirm('Are you sure you want to delete this season? This action cannot be undone.')" 
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full flex items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-red-900">Delete Season</h4>
                            <p class="text-xs text-red-600">Permanently delete</p>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-back-office-layout> 