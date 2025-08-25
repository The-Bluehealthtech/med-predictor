@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg mb-6">
            <div class="px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Match Sheet</h1>
                        <p class="text-orange-100 mt-2">
                            {{ $match->homeTeam->name ?? 'TBD' }} vs {{ $match->awayTeam->name ?? 'TBD' }}
                        </p>
                        <p class="text-orange-100 text-sm">
                            {{ $match->competition->name ?? 'Competition' }} • {{ $match->kickoff_time ? $match->kickoff_time->format('D, M j, Y g:i A') : 'TBD' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('referee.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Match Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Match Information -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Match Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-medium text-gray-900">Home Team</h3>
                                <p class="text-gray-600">{{ $match->homeTeam->name ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Away Team</h3>
                                <p class="text-gray-600">{{ $match->awayTeam->name ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Competition</h3>
                                <p class="text-gray-600">{{ $match->competition->name ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Venue</h3>
                                <p class="text-gray-600">{{ $match->venue ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Kickoff Time</h3>
                                <p class="text-gray-600">{{ $match->kickoff_time ? $match->kickoff_time->format('D, M j, Y g:i A') : 'TBD' }}</p>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">Status</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $match->match_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($match->match_status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $match->match_status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Match Events -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Match Events</h2>
                        @if($events->count() > 0)
                            <div class="space-y-3">
                                @foreach($events as $event)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-orange-100 rounded-full p-2">
                                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">
                                                        {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                                    </p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $event->minute }}' {{ $event->extra_time_minute ? '+' . $event->extra_time_minute : '' }}
                                                        @if($event->player)
                                                            • {{ $event->player->name }}
                                                        @endif
                                                    </p>
                                                    @if($event->description)
                                                        <p class="text-sm text-gray-500">{{ $event->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $event->period)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No events recorded</h3>
                                <p class="mt-1 text-sm text-gray-500">No match events have been recorded yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <button class="w-full flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Record Event
                            </button>
                            <button class="w-full flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Update Score
                            </button>
                            <button class="w-full flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Match Officials -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Match Officials</h3>
                        <div class="space-y-3">
                            @foreach($match->officials as $official)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $official->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $official->role)) }}</p>
                                    </div>
                                    @if($official->user_id === auth()->id())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            You
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 