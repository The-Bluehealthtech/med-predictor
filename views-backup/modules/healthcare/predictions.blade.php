@extends('layouts.app')

@section('title', 'Medical Predictions - Healthcare Module')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Medical Predictions</h1>
            <p class="mt-2 text-gray-600">AI-powered medical predictions and risk assessments for your organization</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Predictions</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $predictions->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">High Confidence</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $highConfidenceCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Medium Risk</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $mediumRiskCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">High Risk</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $highRiskCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Search & Filters</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('healthcare.predictions') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search Player</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Name, FIFA ID...">
                    </div>
                    
                    <div>
                        <label for="confidence_level" class="block text-sm font-medium text-gray-700">Confidence Level</label>
                        <select name="confidence_level" id="confidence_level" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Levels</option>
                            <option value="high" {{ request('confidence_level') == 'high' ? 'selected' : '' }}>High (>80%)</option>
                            <option value="medium" {{ request('confidence_level') == 'medium' ? 'selected' : '' }}>Medium (60-80%)</option>
                            <option value="low" {{ request('confidence_level') == 'low' ? 'selected' : '' }}>Low (<60%)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700">Risk Level</label>
                        <select name="risk_level" id="risk_level" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Levels</option>
                            <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low Risk</option>
                            <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium Risk</option>
                            <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High Risk</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Predictions Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Medical Predictions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Player</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prediction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($predictions as $prediction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-indigo-600">
                                                {{ substr($prediction->healthRecord->player->first_name, 0, 1) }}{{ substr($prediction->healthRecord->player->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $prediction->healthRecord->player->first_name }} {{ $prediction->healthRecord->player->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $prediction->healthRecord->player->fifa_connect_id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $prediction->prediction_type }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($prediction->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $riskClass = $prediction->healthRecord->risk_score > 0.7 ? 'text-red-600' : 
                                               ($prediction->healthRecord->risk_score > 0.3 ? 'text-yellow-600' : 'text-green-600');
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $riskClass }}">
                                    {{ number_format($prediction->healthRecord->risk_score * 100, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $confidenceClass = $prediction->confidence_score > 0.8 ? 'text-green-600' : 
                                                     ($prediction->confidence_score > 0.6 ? 'text-yellow-600' : 'text-red-600');
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $confidenceClass }}">
                                    {{ number_format($prediction->confidence_score * 100, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $prediction->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('healthcare.records.show', ['record' => $prediction->healthRecord]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No predictions found. Create health records to generate predictions.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($predictions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $predictions->links() }}
            </div>
            @endif
        </div>

        <!-- AI Insights Section -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">AI Insights & Recommendations</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Risk Trends -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Risk Trends</h4>
                        <p class="text-sm text-blue-700">
                            Based on recent data, {{ $highRiskCount > 0 ? $highRiskCount . ' players' : 'no players' }} 
                            are currently classified as high risk. Consider scheduling additional checkups for these individuals.
                        </p>
                    </div>

                    <!-- Prediction Accuracy -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-green-900 mb-2">Prediction Accuracy</h4>
                        <p class="text-sm text-green-700">
                            {{ $highConfidenceCount > 0 ? $highConfidenceCount . ' predictions' : 'No predictions' }} 
                            have high confidence levels (>80%). These predictions are most reliable for decision-making.
                        </p>
                    </div>

                    <!-- Recommendations -->
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-yellow-900 mb-2">Recommendations</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• Schedule follow-up appointments for high-risk players</li>
                            <li>• Review prediction accuracy monthly</li>
                            <li>• Update health records regularly for better predictions</li>
                        </ul>
                    </div>

                    <!-- System Health -->
                    <div class="bg-purple-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-purple-900 mb-2">System Health</h4>
                        <p class="text-sm text-purple-700">
                            AI prediction model is operating normally. 
                            Last updated: {{ now()->format('M d, Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 