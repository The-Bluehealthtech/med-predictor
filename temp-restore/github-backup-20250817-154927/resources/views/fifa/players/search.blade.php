@extends('layouts.app')

@section('title', 'FIFA Players Search')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">FIFA Players Search</h1>
                    <p class="mt-2 text-gray-600">Search and sync player data from FIFA Connect</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('fifa.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Search Players</h2>
                
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="player_name" class="block text-sm font-medium text-gray-700 mb-2">Player Name</label>
                            <input type="text" id="player_name" name="player_name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Enter player name">
                        </div>
                        
                        <div>
                            <label for="fifa_id" class="block text-sm font-medium text-gray-700 mb-2">FIFA ID</label>
                            <input type="text" id="fifa_id" name="fifa_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Enter FIFA ID">
                        </div>
                        
                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">Nationality</label>
                            <select id="nationality" name="nationality" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select nationality</option>
                                <option value="FRA">France</option>
                                <option value="ESP">Spain</option>
                                <option value="GER">Germany</option>
                                <option value="ITA">Italy</option>
                                <option value="ENG">England</option>
                                <option value="BRA">Brazil</option>
                                <option value="ARG">Argentina</option>
                                <option value="POR">Portugal</option>
                                <option value="NED">Netherlands</option>
                                <option value="BEL">Belgium</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search Players
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Results -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Search Results</h2>
                    <div class="text-sm text-gray-500">Showing 0 results</div>
                </div>
                
                <!-- Placeholder for search results -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No players found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria to find players.</p>
                </div>
                
                <!-- Example result structure (hidden by default) -->
                <div class="hidden">
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-500 font-semibold">JD</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">John Doe</h3>
                                    <p class="text-sm text-gray-500">FIFA ID: 123456789</p>
                                    <p class="text-sm text-gray-500">Nationality: France</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Sync
                                </button>
                                <button class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 