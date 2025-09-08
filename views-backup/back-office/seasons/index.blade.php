<x-back-office-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Season Management</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage competition seasons and registration periods</p>
                </div>
                <a href="{{ route('back-office.seasons.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Create New Season
                </a>
            </div>
        </div>

        <!-- Seasons List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($seasons->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Season</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stats</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($seasons as $season)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $season->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $season->short_name }}</div>
                                            @if($season->is_current)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Current
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $season->duration }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $season->start_date->format('M d, Y') }} - {{ $season->end_date->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $season->registration_start_date->format('M d, Y') }} - {{ $season->registration_end_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @if($season->isRegistrationOpen())
                                                <span class="text-green-600">Registration Open</span>
                                            @else
                                                <span class="text-red-600">Registration Closed</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $season->status_color }}-100 text-{{ $season->status_color }}-800">
                                            {{ ucfirst($season->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $season->competitions_count }} competitions</div>
                                        <div>{{ $season->players_count }} players</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('back-office.seasons.show', $season) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <a href="{{ route('back-office.seasons.edit', $season) }}" 
                                               class="text-blue-600 hover:text-blue-900">Edit</a>
                                            
                                            @if(!$season->is_current)
                                                <form method="POST" action="{{ route('back-office.seasons.set-current', $season) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Set Current</button>
                                                </form>
                                            @endif
                                            
                                            @if($season->status === 'upcoming')
                                                <form method="POST" action="{{ route('back-office.seasons.activate', $season) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">Activate</button>
                                                </form>
                                            @endif
                                            
                                            @if($season->status === 'active')
                                                <form method="POST" action="{{ route('back-office.seasons.complete', $season) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-purple-600 hover:text-purple-900">Complete</button>
                                                </form>
                                            @endif
                                            
                                            @if($season->status === 'completed')
                                                <form method="POST" action="{{ route('back-office.seasons.archive', $season) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-gray-600 hover:text-gray-900">Archive</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $seasons->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No seasons</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new season.</p>
                        <div class="mt-6">
                            <a href="{{ route('back-office.seasons.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create Season
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-back-office-layout> 