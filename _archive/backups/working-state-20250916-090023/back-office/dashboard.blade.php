<x-back-office-layout>
    <x-slot name="title">Dashboard - Back Office</x-slot>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="text-gray-900">
                    <h1 class="text-2xl font-bold text-gray-900">Back Office Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-600">System overview and management</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Exit Back Office
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Season Status -->
    @if($currentSeason)
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Current Season</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-800">{{ $currentSeason->name }}</h3>
                    <p class="text-2xl font-bold text-blue-900">{{ $currentSeason->short_name }}</p>
                    <p class="text-sm text-blue-600">{{ $currentSeason->duration }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-green-800">Competitions</h3>
                    <p class="text-2xl font-bold text-green-900">{{ $seasonStats['competitions_count'] }}</p>
                    <p class="text-sm text-green-600">Active competitions</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-purple-800">Players</h3>
                    <p class="text-2xl font-bold text-purple-900">{{ $seasonStats['players_count'] }}</p>
                    <p class="text-sm text-purple-600">Registered players</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-yellow-800">Registration</h3>
                    <p class="text-2xl font-bold text-yellow-900">
                        {{ $seasonStats['registration_open'] ? 'Open' : 'Closed' }}
                    </p>
                    <p class="text-sm text-yellow-600">
                        {{ $seasonStats['days_remaining'] > 0 ? $seasonStats['days_remaining'] . ' days left' : 'Ended' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Seasons</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_seasons'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Competitions</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_competitions'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Players</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_players'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Clubs</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_clubs'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Associations</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_associations'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Content Files</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_content'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Seasons -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Seasons</h3>
                <div class="space-y-3">
                    @forelse($recentSeasons as $season)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $season->name }}</h4>
                            <p class="text-xs text-gray-600">{{ $season->duration }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $season->status_color }}-100 text-{{ $season->status_color }}-800">
                            {{ ucfirst($season->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No seasons found</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('back-office.seasons.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                        View all seasons →
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Content -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Content</h3>
                <div class="space-y-3">
                    @forelse($recentContent as $content)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $content->name }}</h4>
                            <p class="text-xs text-gray-600">{{ ucfirst($content->type) }} - {{ ucfirst($content->category) }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $content->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $content->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No content found</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('back-office.content.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                        View all content →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('back-office.seasons.create') }}" 
                   class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-indigo-900">Create Season</h4>
                        <p class="text-xs text-indigo-600">Start a new season</p>
                    </div>
                </a>

                <a href="{{ route('back-office.content.create') }}" 
                   class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-green-900">Upload Content</h4>
                        <p class="text-xs text-green-600">Add logos and files</p>
                    </div>
                </a>

                <a href="{{ route('back-office.system-status') }}" 
                   class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-900">System Status</h4>
                        <p class="text-xs text-yellow-600">Check system health</p>
                    </div>
                </a>

                <a href="{{ route('back-office.settings') }}" 
                   class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-purple-900">Settings</h4>
                        <p class="text-xs text-purple-600">System configuration</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
</x-back-office-layout> 