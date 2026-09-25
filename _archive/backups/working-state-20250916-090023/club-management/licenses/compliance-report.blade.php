@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                                License Compliance Report
                            </h2>
                            <p class="text-gray-600 mt-1">
                                Comprehensive analysis of license compliance and statistics
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('club-management.licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Licenses
                        </a>
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Licenses</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $reportData['total_licenses'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Licenses</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $reportData['active_licenses'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Compliance Rate</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $reportData['compliance_rate'] }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Need Renewal</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $reportData['licenses_needing_renewal'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Status Distribution Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">License Status Distribution</h3>
                    <div class="space-y-3">
                        @foreach($reportData['licenses_by_status'] as $status => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3
                                    @if($status === 'approved') bg-green-500
                                    @elseif($status === 'pending') bg-yellow-500
                                    @elseif($status === 'expired') bg-red-500
                                    @elseif($status === 'rejected') bg-red-500
                                    @elseif($status === 'suspended') bg-orange-500
                                    @else bg-gray-500
                                    @endif"></div>
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($status) }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $reportData['total_licenses'] > 0 ? ($count / $reportData['total_licenses']) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500 w-8 text-right">{{ $count }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Monthly Trend Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly License Trends</h3>
                    <div class="space-y-3">
                        @foreach($reportData['licenses_by_month']->take(6) as $month => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ $month }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $reportData['licenses_by_month']->max() > 0 ? ($count / $reportData['licenses_by_month']->max()) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500 w-8 text-right">{{ $count }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Statistics -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detailed Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Pending Licenses</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $reportData['pending_licenses'] }}</dd>
                        <dd class="text-sm text-gray-500">Awaiting approval</dd>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Expired Licenses</dt>
                        <dd class="text-2xl font-bold text-red-600">{{ $reportData['expired_licenses'] }}</dd>
                        <dd class="text-sm text-gray-500">Require immediate attention</dd>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Suspended Licenses</dt>
                        <dd class="text-2xl font-bold text-orange-600">{{ $reportData['suspended_licenses'] }}</dd>
                        <dd class="text-sm text-gray-500">Temporarily inactive</dd>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Compliance Score</dt>
                        <dd class="text-2xl font-bold 
                            @if($reportData['compliance_rate'] >= 90) text-green-600
                            @elseif($reportData['compliance_rate'] >= 70) text-yellow-600
                            @else text-red-600
                            @endif">
                            {{ $reportData['compliance_rate'] }}%
                        </dd>
                        <dd class="text-sm text-gray-500">
                            @if($reportData['compliance_rate'] >= 90) Excellent
                            @elseif($reportData['compliance_rate'] >= 70) Good
                            @else Needs Improvement
                            @endif
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Licenses Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">License Details</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Player</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reportData['licenses']->take(10) as $license)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($license->player->photo_url)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $license->player->photo_url }}" alt="{{ $license->player->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $license->player->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $license->player->position ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $license->license_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $license->club->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($license->status === 'active') bg-green-100 text-green-800
                                        @elseif($license->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($license->status === 'expired') bg-red-100 text-red-800
                                        @elseif($license->status === 'rejected') bg-red-100 text-red-800
                                        @elseif($license->status === 'suspended') bg-orange-100 text-orange-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($license->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $license->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($license->expires_at)
                                        <span class="@if($license->expires_at->isPast()) text-red-600 @elseif($license->expires_at->diffInDays(now()) <= 30) text-yellow-600 @else text-gray-900 @endif">
                                            {{ $license->expires_at->format('M d, Y') }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('club-management.licenses.show', $license) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($reportData['licenses']->count() > 10)
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-500">Showing first 10 licenses. <a href="{{ route('club-management.licenses.index') }}" class="text-indigo-600 hover:text-indigo-900">View all licenses</a></p>
                </div>
                @endif
            </div>
        </div>

        <!-- Recommendations -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recommendations</h3>
                <div class="space-y-4">
                    @if($reportData['expired_licenses'] > 0)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Immediate Action Required</h4>
                            <p class="text-sm text-gray-600">You have {{ $reportData['expired_licenses'] }} expired licenses that require immediate attention. These players cannot participate in official matches.</p>
                        </div>
                    </div>
                    @endif

                    @if($reportData['licenses_needing_renewal'] > 0)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Renewal Planning</h4>
                            <p class="text-sm text-gray-600">{{ $reportData['licenses_needing_renewal'] }} licenses will expire within the next 3 months. Start the renewal process early to avoid compliance issues.</p>
                        </div>
                    </div>
                    @endif

                    @if($reportData['pending_licenses'] > 0)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Pending Approvals</h4>
                            <p class="text-sm text-gray-600">You have {{ $reportData['pending_licenses'] }} licenses awaiting approval. Review and process these applications promptly.</p>
                        </div>
                    </div>
                    @endif

                    @if($reportData['compliance_rate'] < 90)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Compliance Improvement</h4>
                            <p class="text-sm text-gray-600">Your compliance rate of {{ $reportData['compliance_rate'] }}% is below the recommended 90%. Focus on processing pending applications and renewing expired licenses.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-white {
        background-color: white !important;
        box-shadow: none !important;
    }
    
    .shadow-sm {
        box-shadow: none !important;
    }
}
</style>
@endsection 