@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg mb-6">
            <div class="px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Create Match Report</h1>
                        <p class="text-orange-100 mt-2">Generate match reports for completed matches</p>
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


        <!-- Content -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Select Match for Report</h2>
                
                <!-- Matches Assignés (à venir) -->
                @if($assignedMatches->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Matches Assignés (À Venir)</h3>
                        <div class="space-y-4">
                            @foreach($assignedMatches as $match)
                                <div class="border border-blue-200 rounded-lg p-4 hover:bg-blue-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? optional($match->homeTeam)->name ?? 'TBD' }} vs {{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? optional($match->awayTeam)->name ?? 'TBD' }}
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $match->competition->name ?? 'Competition' }} • {{ $match->match_date ? \Carbon\Carbon::parse($match->match_date)->format('M j, Y H:i') : 'N/A' }}
                                            </p>
                                            <p class="text-sm text-blue-600">
                                                {{ $match->venue ?? 'TBD' }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($match->status ?? 'pending') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Matches Terminés -->
                @if($recentMatches->count() > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Matches Terminés - Créer un Rapport</h3>
                        <div class="space-y-4">
                            @foreach($recentMatches as $match)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? optional($match->homeTeam)->name ?? 'TBD' }} vs {{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? optional($match->awayTeam)->name ?? 'TBD' }}
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $match->competition->name ?? 'Competition' }} • {{ $match->match_date ? \Carbon\Carbon::parse($match->match_date)->format('M j, Y') : 'N/A' }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Final Score: {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                            </p>
                                        </div>
                                        <div>
                                            <a href="{{ route('referee.create-detailed-match-report', $match->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                Create Detailed Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No matches available</h3>
                        <p class="mt-1 text-sm text-gray-500">No completed matches found that require reports.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 