@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Performance Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('performances.edit', $performance) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('performances.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Player Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Player Information</h2>
                <div class="space-y-2">
                    <p><strong>Player:</strong> {{ $performance->player->first_name }} {{ $performance->player->last_name }}</p>
                    <p><strong>Position:</strong> {{ $performance->player->position ?? 'N/A' }}</p>
                    <p><strong>Club:</strong> {{ $performance->player->club->name ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Match Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Match Information</h2>
                <div class="space-y-2">
                    <p><strong>Match Date:</strong> {{ $performance->match_date->format('M d, Y') }}</p>
                    <p><strong>Minutes Played:</strong> {{ $performance->minutes_played ?? 'N/A' }}</p>
                    <p><strong>Rating:</strong> {{ $performance->rating }}/10</p>
                </div>
            </div>

            <!-- Physical Performance -->
            <div class="bg-blue-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Physical Performance</h2>
                <div class="space-y-2">
                    <p><strong>Distance Covered:</strong> {{ number_format($performance->distance_covered) }}m</p>
                    <p><strong>Sprint Count:</strong> {{ $performance->sprint_count }}</p>
                    <p><strong>Max Speed:</strong> {{ $performance->max_speed }} km/h</p>
                    <p><strong>Average Speed:</strong> {{ $performance->avg_speed }} km/h</p>
                </div>
            </div>

            <!-- Technical Performance -->
            <div class="bg-green-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Technical Performance</h2>
                <div class="space-y-2">
                    <p><strong>Passes Completed:</strong> {{ $performance->passes_completed ?? 'N/A' }}</p>
                    <p><strong>Passes Attempted:</strong> {{ $performance->passes_attempted ?? 'N/A' }}</p>
                    <p><strong>Pass Accuracy:</strong> 
                        @if($performance->passes_attempted && $performance->passes_completed)
                            {{ round(($performance->passes_completed / $performance->passes_attempted) * 100, 1) }}%
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Tackles Won:</strong> {{ $performance->tackles_won ?? 'N/A' }}</p>
                    <p><strong>Tackles Attempted:</strong> {{ $performance->tackles_attempted ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Attacking Performance -->
            <div class="bg-yellow-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Attacking Performance</h2>
                <div class="space-y-2">
                    <p><strong>Goals Scored:</strong> {{ $performance->goals_scored ?? 'N/A' }}</p>
                    <p><strong>Assists:</strong> {{ $performance->assists ?? 'N/A' }}</p>
                    <p><strong>Shots on Target:</strong> {{ $performance->shots_on_target ?? 'N/A' }}</p>
                    <p><strong>Total Shots:</strong> {{ $performance->shots_total ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Discipline -->
            <div class="bg-red-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Discipline</h2>
                <div class="space-y-2">
                    <p><strong>Yellow Cards:</strong> {{ $performance->yellow_cards ?? 'N/A' }}</p>
                    <p><strong>Red Cards:</strong> {{ $performance->red_cards ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($performance->notes)
        <div class="mt-6 bg-gray-50 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-2">Notes</h2>
            <p class="text-gray-700">{{ $performance->notes }}</p>
        </div>
        @endif

        <!-- Performance Chart -->
        <div class="mt-6 bg-white p-4 rounded-lg border">
            <h2 class="text-lg font-semibold mb-4">Performance Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $performance->distance_covered }}</div>
                    <div class="text-sm text-gray-600">Distance (m)</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $performance->sprint_count }}</div>
                    <div class="text-sm text-gray-600">Sprints</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $performance->max_speed }}</div>
                    <div class="text-sm text-gray-600">Max Speed (km/h)</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $performance->rating }}</div>
                    <div class="text-sm text-gray-600">Rating</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 