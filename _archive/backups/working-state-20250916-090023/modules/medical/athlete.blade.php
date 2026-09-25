@extends('layouts.app')

@section('title', 'Athlete Medical Profile - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        🏥 Athlete Medical Profile
                        @if($player)
                            - {{ $player->full_name }}
                            @if(isset($isDemo) && $isDemo)
                                <span class="text-sm bg-yellow-100 text-yellow-800 px-2 py-1 rounded ml-2">Demo Mode</span>
                            @endif
                        @endif
                    </h1>
                    <p class="text-gray-600 mt-2">Comprehensive medical information and predictions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('modules.medical.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Back to Medical</span>
                    </a>
                </div>
            </div>
        </div>

        @if($player)
            <!-- Athlete Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Athlete Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold text-xl">
                                    {{ substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $player->full_name }}</h3>
                                <p class="text-gray-600">{{ $player->position ?? 'Position not specified' }}</p>
                                <p class="text-sm text-gray-500">{{ $player->club->name ?? 'No club assigned' }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Age:</span>
                                <span class="font-medium">{{ $player->age ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Height:</span>
                                <span class="font-medium">{{ $player->height ?? 'N/A' }} cm</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Weight:</span>
                                <span class="font-medium">{{ $player->weight ?? 'N/A' }} kg</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Medical Records:</span>
                                <span class="font-medium">
                                    @if(isset($isDemo) && $isDemo)
                                        0 (Demo Mode)
                                    @else
                                        {{ $player->healthRecords->count() ?? 0 }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('health-records.create') }}?player_id={{ $player->id }}" 
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-center">
                            New Medical Record
                        </a>
                        <a href="{{ route('medical-predictions.create') }}?player_id={{ $player->id }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">
                            Medical Prediction
                        </a>
                        <a href="{{ route('health-records.index') }}?player_id={{ $player->id }}" 
                           class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-center">
                            View Records
                        </a>
                        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-center">
                            Performance Analysis
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Medical Records -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Medical Records</h2>
                </div>
                <div class="p-6">
                    @if(isset($isDemo) && $isDemo)
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Demo Mode</h3>
                            <p class="text-gray-600">This is a demo athlete. No medical records are available in demo mode.</p>
                            <a href="{{ route('health-records.create') }}?player_id={{ $player->id }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Create Demo Record
                            </a>
                        </div>
                    @elseif($player->healthRecords && $player->healthRecords->count() > 0)
                        <div class="space-y-4">
                            @foreach($player->healthRecords->take(5) as $record)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $record->title ?? 'Medical Record' }}</h4>
                                            <p class="text-sm text-gray-600">{{ $record->record_date->format('d/m/Y') }}</p>
                                            <p class="text-sm text-gray-500">{{ $record->description ?? 'No description' }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $record->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($record->status ?? 'unknown') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Medical Records</h3>
                            <p class="text-gray-600">This athlete doesn't have any medical records yet.</p>
                            <a href="{{ route('health-records.create') }}?player_id={{ $player->id }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Create First Record
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        @else
            <!-- Player Not Found -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Athlete Not Found</h3>
                    <p class="text-gray-600">The requested athlete could not be found.</p>
                    <a href="{{ route('modules.medical.index') }}" 
                       class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Back to Medical Module
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 