@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg mb-6">
            <div class="px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Match Assignments</h1>
                        <p class="text-orange-100 mt-2">Your upcoming and recent match assignments</p>
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

        <!-- Match Assignments List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Your Assignments</h2>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $assignments->total() }} Total
                        </span>
                    </div>
                </div>

                @if($assignments->count() > 0)
                    <div class="space-y-4">
                        @foreach($assignments as $match)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-orange-100 rounded-full p-2">
                                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">
                                                    {{ $match->homeTeam->name ?? 'TBD' }} vs {{ $match->awayTeam->name ?? 'TBD' }}
                                                </h3>
                                                <p class="text-sm text-gray-600">
                                                    {{ $match->competition->name ?? 'Competition' }} • Matchday {{ $match->matchday ?? 'N/A' }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $match->kickoff_time ? $match->kickoff_time->format('D, M j, Y g:i A') : 'TBD' }} • {{ $match->venue ?? 'TBD' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $officialRole = $match->officials->where('user_id', auth()->id())->first();
                                            $role = $officialRole ? $officialRole->role : 'Official';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $role === 'referee' ? 'bg-green-100 text-green-800' : 
                                               ($role === 'fourth_official' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                                        </span>
                                        <div class="mt-2">
                                            <a href="{{ route('referee.match-sheet', $match->id) }}" 
                                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                Open Match Sheet
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $assignments->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No assignments</h3>
                        <p class="mt-1 text-sm text-gray-500">You don't have any match assignments at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 