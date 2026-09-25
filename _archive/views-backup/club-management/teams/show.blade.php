@extends('layouts.app')

@section('title', 'Team Details')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $team->name }}</h1>
                        <p class="text-gray-600">{{ $club->name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('club-management.teams.edit', $team->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit Team
                        </a>
                        <a href="{{ route('club-management.teams.manage-players', $team->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Manage Players
                        </a>
                    </div>
                </div>

                <!-- Team Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Basic Info -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="font-medium text-gray-700">Type:</span>
                                <span class="ml-2 text-gray-900">{{ ucfirst(str_replace('_', ' ', $team->type)) }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Formation:</span>
                                <span class="ml-2 text-gray-900">{{ $team->formation }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Status:</span>
                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full {{ $team->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($team->status) }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Season:</span>
                                <span class="ml-2 text-gray-900">{{ $team->season }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Competition Level:</span>
                                <span class="ml-2 text-gray-900">{{ $team->competition_level }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tactical Information -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Tactical Information</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="font-medium text-gray-700">Tactical Style:</span>
                                <p class="mt-1 text-gray-900">{{ $team->tactical_style }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Playing Philosophy:</span>
                                <p class="mt-1 text-gray-900">{{ $team->playing_philosophy }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Staff Information -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Staff</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="font-medium text-gray-700">Head Coach:</span>
                                <span class="ml-2 text-gray-900">{{ $team->coach_name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Assistant Coach:</span>
                                <span class="ml-2 text-gray-900">{{ $team->assistant_coach }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Fitness Coach:</span>
                                <span class="ml-2 text-gray-900">{{ $team->fitness_coach }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Goalkeeper Coach:</span>
                                <span class="ml-2 text-gray-900">{{ $team->goalkeeper_coach }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Strength Analysis -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Team Strength Analysis</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-800">Attack</h4>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm">
                                    <span>Rating</span>
                                    <span class="font-medium">{{ number_format($teamStrength['attack'], 1) }}/100</span>
                                </div>
                                <div class="w-full bg-blue-200 rounded-full h-2 mt-1">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $teamStrength['attack'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-green-800">Midfield</h4>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm">
                                    <span>Rating</span>
                                    <span class="font-medium">{{ number_format($teamStrength['midfield'], 1) }}/100</span>
                                </div>
                                <div class="w-full bg-green-200 rounded-full h-2 mt-1">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $teamStrength['midfield'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-red-800">Defense</h4>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm">
                                    <span>Rating</span>
                                    <span class="font-medium">{{ number_format($teamStrength['defense'], 1) }}/100</span>
                                </div>
                                <div class="w-full bg-red-200 rounded-full h-2 mt-1">
                                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $teamStrength['defense'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-yellow-800">Goalkeeping</h4>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm">
                                    <span>Rating</span>
                                    <span class="font-medium">{{ number_format($teamStrength['goalkeeping'], 1) }}/100</span>
                                </div>
                                <div class="w-full bg-yellow-200 rounded-full h-2 mt-1">
                                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $teamStrength['goalkeeping'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Best Lineup -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Best Lineup ({{ $team->formation }})</h2>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($bestLineup as $position => $players)
                                <div class="bg-white p-4 rounded-lg border">
                                    <h4 class="font-semibold text-gray-800 mb-3">{{ strtoupper($position) }} ({{ count($players) }})</h4>
                                    <div class="space-y-2">
                                        @foreach($players as $player)
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="font-medium">{{ $player->name }}</span>
                                                <span class="text-gray-600">{{ $player->overall_rating }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Squad Analysis -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Squad Analysis</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-3">Squad Overview</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Total Players:</span>
                                    <span class="font-medium">{{ $squadAnalysis['total_players'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Average Age:</span>
                                    <span class="font-medium">{{ number_format($squadAnalysis['average_age'], 1) }} years</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Average Rating:</span>
                                    <span class="font-medium">{{ number_format($squadAnalysis['average_rating'], 1) }}/100</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-3">Positions</h4>
                            <div class="space-y-2">
                                @foreach($squadAnalysis['positions'] as $position => $count)
                                    <div class="flex justify-between">
                                        <span>{{ $position }}:</span>
                                        <span class="font-medium">{{ $count }} players</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="font-semibold text-gray-800 mb-3">Value</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Total Value:</span>
                                    <span class="font-medium">€{{ number_format($squadAnalysis['value_distribution']['total_value'] / 1000000, 1) }}M</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Average Value:</span>
                                    <span class="font-medium">€{{ number_format($squadAnalysis['value_distribution']['average_value'] / 1000000, 1) }}M</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Players -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Available Players ({{ count($availablePlayers) }})</h2>
                    <div class="bg-white overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Player</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationality</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($availablePlayers as $player)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">{{ $player->name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $player->position }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $player->age }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $player->overall_rating }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $player->nationality }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(count($availablePlayers) > 0)
                            <div class="px-6 py-3 bg-gray-50 text-center">
                                <span class="text-sm text-gray-600">Showing all {{ count($availablePlayers) }} available players</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-8">
                    <a href="{{ route('club-management.teams.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to Teams
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 