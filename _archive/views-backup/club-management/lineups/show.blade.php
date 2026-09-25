@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ $lineup->name ?? 'Unnamed Lineup' }} - {{ $club->name }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Lineup details and tactical information
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('club-management.lineups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Lineups
                        </a>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Team</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->team->name ?? 'No Team' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Formation</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->formation }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Match Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($lineup->match_type ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $lineup->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($lineup->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($lineup->status ?? 'Unknown') }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Competition</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->competition->name ?? 'No Competition' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Match Details -->
                @if($lineup->opponent || $lineup->venue || $lineup->weather_conditions || $lineup->pitch_condition)
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Match Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($lineup->opponent)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Opponent</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->opponent }}</p>
                        </div>
                        @endif
                        @if($lineup->venue)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Venue</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->venue }}</p>
                        </div>
                        @endif
                        @if($lineup->weather_conditions)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Weather</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($lineup->weather_conditions) }}</p>
                        </div>
                        @endif
                        @if($lineup->pitch_condition)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pitch Condition</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($lineup->pitch_condition) }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Tactical Settings -->
                @if($lineup->pressing_intensity || $lineup->possession_style || $lineup->defensive_line_height || $lineup->marking_system)
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tactical Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($lineup->pressing_intensity)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pressing Intensity</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($lineup->pressing_intensity) }}</p>
                        </div>
                        @endif
                        @if($lineup->possession_style)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Possession Style</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($lineup->possession_style) }}</p>
                        </div>
                        @endif
                        @if($lineup->defensive_line_height)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Defensive Line Height</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($lineup->defensive_line_height) }}</p>
                        </div>
                        @endif
                        @if($lineup->marking_system)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Marking System</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($lineup->marking_system) }}</p>
                        </div>
                        @endif
                    </div>
                    @if($lineup->tactical_notes)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Tactical Notes</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $lineup->tactical_notes }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Special Roles -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Special Roles</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($lineup->captain)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Captain</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->captain->name }}</p>
                        </div>
                        @endif
                        @if($lineup->viceCaptain)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vice Captain</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->viceCaptain->name }}</p>
                        </div>
                        @endif
                        @if($lineup->penaltyTaker)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penalty Taker</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->penaltyTaker->name }}</p>
                        </div>
                        @endif
                        @if($lineup->freeKickTaker)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Free Kick Taker</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->freeKickTaker->name }}</p>
                        </div>
                        @endif
                        @if($lineup->cornerTaker)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Corner Taker</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lineup->cornerTaker->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Starting XI -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Starting XI</h3>
                    @if($lineup->starters->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($lineup->starters as $lineupPlayer)
                                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $lineupPlayer->player->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $lineupPlayer->player->position }} • #{{ $lineupPlayer->player->jersey_number ?? 'N/A' }}</p>
                                                                                         @if($lineupPlayer->assigned_position && $lineupPlayer->assigned_position !== 'TBD')
                                                 <p class="text-xs text-blue-600 font-medium">Position: {{ $lineupPlayer->assigned_position }}</p>
                                             @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Starter
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No starting players assigned</p>
                    @endif
                </div>

                <!-- Substitutes -->
                @if($lineup->substitutes->count() > 0)
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Substitutes</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($lineup->substitutes as $lineupPlayer)
                            <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $lineupPlayer->player->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $lineupPlayer->player->position }} • #{{ $lineupPlayer->player->jersey_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Substitute
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Formation Visualization -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Formation: {{ $lineup->formation }}</h3>
                    <div class="bg-green-600 rounded-lg p-8 text-center">
                        <div class="text-white text-sm mb-4">Football Pitch</div>
                        <div class="text-white text-xs opacity-75">
                            Formation visualization would be displayed here
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 