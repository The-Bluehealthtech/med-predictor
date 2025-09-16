@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Match Report</h1>
                    <p class="mt-2 text-gray-600">Create and submit match reports.</p>
                </div>
                <a href="{{ url()->previous() }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <!-- Match Report Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Match Report Form</h2>
                <form class="space-y-6">
                    <!-- Match Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="match_date" class="block text-sm font-medium text-gray-700">Match Date</label>
                            <input type="date" id="match_date" name="match_date" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="competition" class="block text-sm font-medium text-gray-700">Competition</label>
                            <input type="text" id="competition" name="competition" placeholder="Competition name" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Teams -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="home_team" class="block text-sm font-medium text-gray-700">Home Team</label>
                            <input type="text" id="home_team" name="home_team" placeholder="Home team name" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="away_team" class="block text-sm font-medium text-gray-700">Away Team</label>
                            <input type="text" id="away_team" name="away_team" placeholder="Away team name" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Score -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="home_score" class="block text-sm font-medium text-gray-700">Home Team Score</label>
                            <input type="number" id="home_score" name="home_score" min="0" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="away_score" class="block text-sm font-medium text-gray-700">Away Team Score</label>
                            <input type="number" id="away_score" name="away_score" min="0" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="yellow_cards" class="block text-sm font-medium text-gray-700">Yellow Cards</label>
                            <input type="number" id="yellow_cards" name="yellow_cards" min="0" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="red_cards" class="block text-sm font-medium text-gray-700">Red Cards</label>
                            <input type="number" id="red_cards" name="red_cards" min="0" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="penalties" class="block text-sm font-medium text-gray-700">Penalties</label>
                            <input type="number" id="penalties" name="penalties" min="0" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Comments -->
                    <div>
                        <label for="comments" class="block text-sm font-medium text-gray-700">Match Comments</label>
                        <textarea id="comments" name="comments" rows="4" placeholder="Additional comments about the match..." 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Submit Match Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
