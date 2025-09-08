@extends('layouts.app')

@section('title', 'Stakeholder Gallery - Med Predictor')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">👥 Stakeholder Gallery</h1>
                    <p class="text-gray-600 mt-2">View all stakeholders in your organization</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Total Stakeholders</div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $groupedStakeholders['players']->count() + $groupedStakeholders['staff']->count() + $groupedStakeholders['officials']->count() + $groupedStakeholders['medical']->count() + $groupedStakeholders['admin']->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" action="{{ route('stakeholder-gallery.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Stakeholders</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Search by name, email, or role..."
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="sm:w-48">
                    <label for="filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Type</label>
                    <select id="filter" 
                            name="filter" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Stakeholders</option>
                        <option value="players" {{ $filter === 'players' ? 'selected' : '' }}>Players</option>
                        <option value="staff" {{ $filter === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="officials" {{ $filter === 'officials' ? 'selected' : '' }}>Officials</option>
                        <option value="medical" {{ $filter === 'medical' ? 'selected' : '' }}>Medical</option>
                        <option value="admin" {{ $filter === 'admin' ? 'selected' : '' }}>Administrators</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        🔍 Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Stakeholder Categories -->
        @if($filter === 'all' || $filter === 'players')
        <!-- Players Section -->
        @if($groupedStakeholders['players']->count() > 0)
        <div class="mb-12">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-blue-600 text-lg">⚽</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Players ({{ $groupedStakeholders['players']->count() }})</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($groupedStakeholders['players'] as $player)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-4">
                            @if($player['profile_picture_url'])
                                @if(filter_var($player['profile_picture_url'], FILTER_VALIDATE_URL))
                                    <img src="{{ $player['profile_picture_url'] }}" 
                                         alt="{{ $player['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-blue-100">
                                @else
                                    <img src="{{ Storage::url($player['profile_picture_url']) }}" 
                                         alt="{{ $player['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-blue-100">
                                @endif
                            @else
                                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center border-4 border-blue-100">
                                    <span class="text-blue-600 font-bold text-2xl">{{ $player['initials'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $player['name'] }}</h3>
                            <p class="text-sm text-blue-600 font-medium mb-2">{{ $player['role_display'] }}</p>
                            @if(isset($player['position']))
                                <p class="text-sm text-gray-600 mb-1">{{ $player['position'] }}</p>
                            @endif
                            @if(isset($player['overall_rating']))
                                <p class="text-sm text-gray-600 mb-2">Rating: {{ $player['overall_rating'] }}</p>
                            @endif
                            <p class="text-xs text-gray-500">{{ $player['email'] }}</p>
                            @if($player['club'])
                                <p class="text-xs text-gray-500 mt-1">{{ $player['club']->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        @if($filter === 'all' || $filter === 'staff')
        <!-- Staff Section -->
        @if($groupedStakeholders['staff']->count() > 0)
        <div class="mb-12">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-green-600 text-lg">👔</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Staff ({{ $groupedStakeholders['staff']->count() }})</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($groupedStakeholders['staff'] as $staff)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-4">
                            @if($staff['profile_picture_url'])
                                @if(filter_var($staff['profile_picture_url'], FILTER_VALIDATE_URL))
                                    <img src="{{ $staff['profile_picture_url'] }}" 
                                         alt="{{ $staff['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-green-100">
                                @else
                                    <img src="{{ Storage::url($staff['profile_picture_url']) }}" 
                                         alt="{{ $staff['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-green-100">
                                @endif
                            @else
                                <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center border-4 border-green-100">
                                    <span class="text-green-600 font-bold text-2xl">{{ $staff['initials'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $staff['name'] }}</h3>
                            <p class="text-sm text-green-600 font-medium mb-2">{{ $staff['role_display'] }}</p>
                            <p class="text-xs text-gray-500">{{ $staff['email'] }}</p>
                            @if($staff['club'])
                                <p class="text-xs text-gray-500 mt-1">{{ $staff['club']->name }}</p>
                            @endif
                            @if($staff['association'])
                                <p class="text-xs text-gray-500">{{ $staff['association']->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        @if($filter === 'all' || $filter === 'officials')
        <!-- Officials Section -->
        @if($groupedStakeholders['officials']->count() > 0)
        <div class="mb-12">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-yellow-600 text-lg">⚖️</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Officials ({{ $groupedStakeholders['officials']->count() }})</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($groupedStakeholders['officials'] as $official)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-4">
                            @if($official['profile_picture_url'])
                                @if(filter_var($official['profile_picture_url'], FILTER_VALIDATE_URL))
                                    <img src="{{ $official['profile_picture_url'] }}" 
                                         alt="{{ $official['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-yellow-100">
                                @else
                                    <img src="{{ Storage::url($official['profile_picture_url']) }}" 
                                         alt="{{ $official['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-yellow-100">
                                @endif
                            @else
                                <div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center border-4 border-yellow-100">
                                    <span class="text-yellow-600 font-bold text-2xl">{{ $official['initials'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $official['name'] }}</h3>
                            <p class="text-sm text-yellow-600 font-medium mb-2">{{ $official['role_display'] }}</p>
                            <p class="text-xs text-gray-500">{{ $official['email'] }}</p>
                            @if($official['association'])
                                <p class="text-xs text-gray-500 mt-1">{{ $official['association']->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        @if($filter === 'all' || $filter === 'medical')
        <!-- Medical Section -->
        @if($groupedStakeholders['medical']->count() > 0)
        <div class="mb-12">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-red-600 text-lg">🏥</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Medical Staff ({{ $groupedStakeholders['medical']->count() }})</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($groupedStakeholders['medical'] as $medical)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-4">
                            @if($medical['profile_picture_url'])
                                @if(filter_var($medical['profile_picture_url'], FILTER_VALIDATE_URL))
                                    <img src="{{ $medical['profile_picture_url'] }}" 
                                         alt="{{ $medical['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-red-100">
                                @else
                                    <img src="{{ Storage::url($medical['profile_picture_url']) }}" 
                                         alt="{{ $medical['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-red-100">
                                @endif
                            @else
                                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center border-4 border-red-100">
                                    <span class="text-red-600 font-bold text-2xl">{{ $medical['initials'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $medical['name'] }}</h3>
                            <p class="text-sm text-red-600 font-medium mb-2">{{ $medical['role_display'] }}</p>
                            <p class="text-xs text-gray-500">{{ $medical['email'] }}</p>
                            @if($medical['club'])
                                <p class="text-xs text-gray-500 mt-1">{{ $medical['club']->name }}</p>
                            @endif
                            @if($medical['association'])
                                <p class="text-xs text-gray-500">{{ $medical['association']->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        @if($filter === 'all' || $filter === 'admin')
        <!-- Administrators Section -->
        @if($groupedStakeholders['admin']->count() > 0)
        <div class="mb-12">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-purple-600 text-lg">👑</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Administrators ({{ $groupedStakeholders['admin']->count() }})</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($groupedStakeholders['admin'] as $admin)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-center mb-4">
                            @if($admin['profile_picture_url'])
                                @if(filter_var($admin['profile_picture_url'], FILTER_VALIDATE_URL))
                                    <img src="{{ $admin['profile_picture_url'] }}" 
                                         alt="{{ $admin['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-purple-100">
                                @else
                                    <img src="{{ Storage::url($admin['profile_picture_url']) }}" 
                                         alt="{{ $admin['profile_picture_alt'] }}" 
                                         class="w-20 h-20 rounded-full object-cover border-4 border-purple-100">
                                @endif
                            @else
                                <div class="w-20 h-20 rounded-full bg-purple-100 flex items-center justify-center border-4 border-purple-100">
                                    <span class="text-purple-600 font-bold text-2xl">{{ $admin['initials'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $admin['name'] }}</h3>
                            <p class="text-sm text-purple-600 font-medium mb-2">{{ $admin['role_display'] }}</p>
                            <p class="text-xs text-gray-500">{{ $admin['email'] }}</p>
                            @if($admin['association'])
                                <p class="text-xs text-gray-500 mt-1">{{ $admin['association']->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        <!-- Empty State -->
        @if($groupedStakeholders['players']->count() === 0 && $groupedStakeholders['staff']->count() === 0 && $groupedStakeholders['officials']->count() === 0 && $groupedStakeholders['medical']->count() === 0 && $groupedStakeholders['admin']->count() === 0)
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-gray-400 text-3xl">👥</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No stakeholders found</h3>
            <p class="text-gray-500">
                @if($search)
                    No stakeholders match your search criteria.
                @else
                    No stakeholders are available in your organization.
                @endif
            </p>
        </div>
        @endif
    </div>
</div>
@endsection 