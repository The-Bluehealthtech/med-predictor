
@extends('layouts.app')

@section('content')
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Player Registration & License Management') }}
                </h2>
                <div class="flex space-x-2">
                    <x-primary-button onclick="window.location.href='{{ route('player-registration.export') }}'">
                        {{ __('Export') }}
                    </x-primary-button>
                    <x-primary-button onclick="window.location.href='{{ route('player-registration.create') }}'">
                        {{ __('Add Player') }}
                    </x-primary-button>
                    <x-primary-button onclick="window.location.href='{{ route('player-registration.create-stakeholder') }}'" class="bg-green-600 hover:bg-green-700">
                        {{ __('Add Stakeholder') }}
                    </x-primary-button>
                </div>
            </div>

            <!-- License Request Management Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('License Request Workflow') }}</h3>
                        <div class="flex space-x-2">
                            <button onclick="filterLicenseRequests('all')" class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200">
                                All ({{ $licenseStats['total_requests'] }})
                            </button>
                            <button onclick="filterLicenseRequests('pending')" class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-md hover:bg-yellow-200">
                                Pending ({{ $licenseStats['pending_approval'] }})
                            </button>
                            <button onclick="filterLicenseRequests('approved')" class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-md hover:bg-green-200">
                                Approved ({{ $licenseStats['approved'] }})
                            </button>
                            <button onclick="filterLicenseRequests('rejected')" class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-md hover:bg-red-200">
                                Rejected ({{ $licenseStats['rejected'] }})
                            </button>
                        </div>
                    </div>

                    <!-- License Request Statistics -->
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-600">Total Requests</p>
                                    <p class="text-lg font-semibold text-blue-900">{{ $licenseStats['total_requests'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-600">Pending Approval</p>
                                    <p class="text-lg font-semibold text-yellow-900">{{ $licenseStats['pending_approval'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-600">Approved</p>
                                    <p class="text-lg font-semibold text-green-900">{{ $licenseStats['approved'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-600">Rejected</p>
                                    <p class="text-lg font-semibold text-red-900">{{ $licenseStats['rejected'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600">Expired</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $licenseStats['expired'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-orange-600">Need Renewal</p>
                                    <p class="text-lg font-semibold text-orange-900">{{ $licenseStats['needs_renewal'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- License Requests Table -->
                    @if($licenseRequests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Player') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Club') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('License Type') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Requested By') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Documents') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($licenseRequests as $license)
                                        <tr class="license-row" data-status="{{ $license->status }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ strtoupper(substr($license->player->first_name ?? 'N', 0, 1) . substr($license->player->last_name ?? 'A', 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $license->player->first_name ?? 'N/A' }} {{ $license->player->last_name ?? 'N/A' }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $license->player->nationality ?? 'N/A' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $license->club->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $license->club->country ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ ucfirst($license->license_type ?? 'N/A') }}</div>
                                                <div class="text-sm text-gray-500">{{ $license->license_category ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'active' => 'bg-green-100 text-green-800',
                                                        'suspended' => 'bg-red-100 text-red-800',
                                                        'expired' => 'bg-gray-100 text-gray-800',
                                                        'revoked' => 'bg-red-100 text-red-800'
                                                    ];
                                                    $statusColor = $statusColors[$license->status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                    {{ ucfirst($license->status ?? 'Unknown') }}
                                                </span>
                                                @if($license->status === 'active' && $license->requiresRenewal())
                                                    <div class="mt-1">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                            Renewal Due
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $license->requestedByUser->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $license->created_at ? $license->created_at->format('d/m/Y') : 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex space-x-2">
                                                    @if($license->medical_clearance)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Medical ✓
                                                        </span>
                                                    @endif
                                                    @if($license->fitness_certificate)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Fitness ✓
                                                        </span>
                                                    @endif
                                                    @if($license->work_permit)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Work Permit ✓
                                                        </span>
                                                    @endif
                                                    @if($license->document_path)
                                                        <a href="{{ asset('storage/' . $license->document_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm">
                                                            View Docs
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button onclick="viewLicenseDetails({{ $license->id }})" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('View') }}
                                                    </button>
                                                    @if($license->status === 'pending' && in_array(auth()->user()->role, ['association_admin', 'association_registrar', 'system_admin', 'admin']))
                                                        <button onclick="approveLicense({{ $license->id }})" class="text-green-600 hover:text-green-900">
                                                            {{ __('Approve') }}
                                                        </button>
                                                        <button onclick="rejectLicense({{ $license->id }})" class="text-red-600 hover:text-red-900">
                                                            {{ __('Reject') }}
                                                        </button>
                                                    @endif
                                                    @if($license->status === 'active')
                                                        <button onclick="renewLicense({{ $license->id }})" class="text-blue-600 hover:text-blue-900">
                                                            {{ __('Renew') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No license requests') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('No license requests found for your scope.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Players Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Registered Players') }}</h3>
                        <form action="{{ route('player-registration.bulk-sync') }}" method="POST" class="inline">
                            @csrf
                            <x-secondary-button type="submit">
                                {{ __('Sync All with FIFA') }}
                            </x-secondary-button>
                        </form>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-blue-100">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500">{{ __('Total Players') }}</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-green-100">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500">{{ __('With FIFA ID') }}</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['with_fifa_id'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-yellow-100">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500">{{ __('Without FIFA ID') }}</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['without_fifa_id'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-blue-100">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-500">{{ __('With Club') }}</p>
                                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['with_club'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Players Table -->
                    @if($players->total() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Player') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('FIFA ID') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Position') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Club') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Age') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($players as $player)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($player->player_face_url)
                                                            <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" 
                                                                 src="{{ $player->player_face_url }}" 
                                                                 alt="{{ $player->first_name }} {{ $player->last_name }}"
                                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                                 onload="this.nextElementSibling.style.display='none';">
                                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-200" style="display: none;">
                                                                <span class="text-sm font-medium text-gray-700">
                                                                    {{ strtoupper(substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center border-2 border-gray-200">
                                                                <span class="text-sm font-medium text-gray-700">
                                                                    {{ strtoupper(substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $player->first_name }} {{ $player->last_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $player->nationality }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $player->fifaConnectId?->fifa_id ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $player->position }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $player->club?->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $player->age ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('player-registration.show', $player) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                                    <a href="{{ route('player-registration.edit', $player) }}" 
                                                       class="text-blue-600 hover:text-blue-900">{{ __('Edit') }}</a>
                                                    <form action="{{ route('player-registration.sync', $player) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                                            {{ __('Sync') }}
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('player-registration.destroy', $player) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                                onclick="return confirm('{{ __('Are you sure?') }}')">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $players->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No players') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('No players found for your scope.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- License Details Modal -->
    <div id="licenseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">License Details</h3>
                    <button onclick="closeLicenseModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modalContent">
                    <!-- Content will be dynamically inserted -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterLicenseRequests(status) {
            const rows = document.querySelectorAll('.license-row');
            rows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function viewLicenseDetails(licenseId) {
            // This would typically make an AJAX call to get license details
            // For now, we'll show a placeholder
            document.getElementById('modalTitle').textContent = 'License Details #' + licenseId;
            document.getElementById('modalContent').innerHTML = `
                <div class="space-y-4">
                    <p class="text-sm text-gray-600">Loading license details...</p>
                    <p class="text-sm text-gray-600">This would show all license information, documents, and workflow history.</p>
                </div>
            `;
            document.getElementById('licenseModal').classList.remove('hidden');
        }

        function closeLicenseModal() {
            document.getElementById('licenseModal').classList.add('hidden');
        }

        function approveLicense(licenseId) {
            if (confirm('Are you sure you want to approve this license request?')) {
                // This would typically make an AJAX call to approve the license
                alert('License approved! This would update the status and notify the club.');
            }
        }

        function rejectLicense(licenseId) {
            const reason = prompt('Please provide a reason for rejection:');
            if (reason !== null) {
                // This would typically make an AJAX call to reject the license
                alert('License rejected! This would update the status and notify the club.');
            }
        }

        function renewLicense(licenseId) {
            if (confirm('Are you sure you want to renew this license?')) {
                // This would typically make an AJAX call to renew the license
                alert('License renewed! This would update the expiry date.');
            }
        }

        // Close modal when clicking outside
        document.getElementById('licenseModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLicenseModal();
            }
        });
    </script>
@endsection 