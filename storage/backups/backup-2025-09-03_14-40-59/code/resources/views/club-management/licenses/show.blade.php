@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @if(isset($club))
                            <!-- Club Logo -->
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                @if($club->hasLogo())
                                    <img src="{{ $club->getLogoUrl() }}" 
                                         alt="{{ $club->getLogoAlt() }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @endif
                            </div>
                        @endif
                        <div>
                            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                                Player License Details
                            </h2>
                            <p class="text-gray-600 mt-1">
                                FIFA Connect ID: {{ $license->fifa_connect_id ?? 'Not assigned' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('club-management.licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Licenses
                        </a>
                        @if($license->status === 'active')
                        <a href="{{ route('club-management.licenses.print', $license) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Print License
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- FIFA Connect Workflow Status -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">FIFA Connect Workflow Status</h3>
                <div class="flex items-center justify-center space-x-8">
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $license->status !== 'pending' ? 'bg-green-600' : 'bg-blue-600' }} text-white rounded-full flex items-center justify-center font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Pre-registration</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $license->status !== 'pending' ? 'bg-green-300' : 'bg-gray-300' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $license->status !== 'pending' ? 'bg-green-600' : 'bg-blue-600' }} text-white rounded-full flex items-center justify-center font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Submission</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $license->status === 'active' ? 'bg-green-300' : 'bg-gray-300' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $license->status === 'active' ? 'bg-green-600' : ($license->status === 'rejected' ? 'bg-red-600' : 'bg-gray-300') }} text-white rounded-full flex items-center justify-center font-semibold">
                            @if($license->status === 'active')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @elseif($license->status === 'rejected')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @else
                                3
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium {{ $license->status === 'active' ? 'text-gray-900' : ($license->status === 'rejected' ? 'text-red-600' : 'text-gray-500') }}">Regulatory Check</span>
                    </div>
                    <div class="flex-1 h-0.5 {{ $license->status === 'active' ? 'bg-green-300' : 'bg-gray-300' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $license->status === 'active' ? 'bg-green-600' : ($license->status === 'rejected' ? 'bg-red-600' : 'bg-gray-300') }} text-white rounded-full flex items-center justify-center font-semibold">
                            @if($license->status === 'active')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @elseif($license->status === 'rejected')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @else
                                4
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium {{ $license->status === 'active' ? 'text-gray-900' : ($license->status === 'rejected' ? 'text-red-600' : 'text-gray-500') }}">Approval</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main License Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Player Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Player Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 h-16 w-16">
                                    @if($license->player->player_face_url)
                                        <img src="{{ $license->player->player_face_url }}" 
                                             alt="{{ $license->player->name }}" 
                                             class="h-16 w-16 rounded-full object-cover border-2 border-gray-200">
                                    @else
                                        <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                            <span class="text-lg font-medium text-white">
                                                {{ substr($license->player->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">{{ $license->player->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $license->player->position }} • {{ $license->player->nationality }}</p>
                                    <p class="text-sm text-gray-500">Age: {{ $license->player->age ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">FIFA Connect ID:</span>
                                    <span class="text-sm text-gray-900 font-mono">{{ $license->fifa_connect_id ?? 'Not assigned' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">License Number:</span>
                                    <span class="text-sm text-gray-900 font-mono">{{ $license->license_number ?? 'Pending' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $license->getLicenseStatusColor() }}-100 text-{{ $license->getLicenseStatusColor() }}-800">
                                        {{ $license->getLicenseStatusText() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- License Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">License Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">License Type</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($license->license_type) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Age Category</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($license->license_category) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Contract Period</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $license->contract_start_date ? $license->contract_start_date->format('M d, Y') : 'N/A' }} - 
                                        {{ $license->contract_end_date ? $license->contract_end_date->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                @if($license->wage_agreement)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Wage Agreement</label>
                                    <p class="mt-1 text-sm text-gray-900">€{{ number_format($license->wage_agreement, 2) }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="space-y-4">
                                @if($license->release_clause)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Release Clause</label>
                                    <p class="mt-1 text-sm text-gray-900">€{{ number_format($license->release_clause, 2) }}</p>
                                </div>
                                @endif
                                @if($license->bonus_structure)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Bonus Structure</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $license->bonus_structure }}</p>
                                </div>
                                @endif
                                @if($license->issue_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Issue Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $license->issue_date->format('M d, Y') }}</p>
                                </div>
                                @endif
                                @if($license->expiry_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Expiry Date</label>
                                    <p class="mt-1 text-sm text-gray-900 {{ $license->isExpired() ? 'text-red-600 font-medium' : '' }}">
                                        {{ $license->expiry_date->format('M d, Y') }}
                                        @if($license->isExpired())
                                            (Expired)
                                        @elseif($license->requiresRenewal())
                                            (Expires in {{ $license->daysUntilExpiry() }} days)
                                        @endif
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Regulatory Compliance -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Regulatory Compliance</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($license->medical_clearance)
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="ml-3 text-sm text-gray-900">Medical Clearance</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($license->fitness_certificate)
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="ml-3 text-sm text-gray-900">Fitness Certificate</span>
                                </div>
                                @if($license->international_clearance !== null)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($license->international_clearance)
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="ml-3 text-sm text-gray-900">International Clearance</span>
                                </div>
                                @endif
                                @if($license->work_permit !== null)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($license->work_permit)
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="ml-3 text-sm text-gray-900">Work Permit</span>
                                </div>
                                @endif
                            </div>
                            <div class="space-y-3">
                                @if($license->disciplinary_record)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Disciplinary Record</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $license->disciplinary_record }}</p>
                                </div>
                                @endif
                                @if($license->visa_status)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Visa Status</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $license->visa_status }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($license->notes)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Notes & History</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ $license->notes }}</pre>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">License Status</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Current Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $license->getLicenseStatusColor() }}-100 text-{{ $license->getLicenseStatusColor() }}-800">
                                    {{ $license->getLicenseStatusText() }}
                                </span>
                            </div>
                            @if($license->approval_status)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Approval Status</span>
                                <span class="text-sm text-gray-900">{{ ucfirst($license->approval_status) }}</span>
                            </div>
                            @endif
                            @if($license->documentation_status)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Documentation</span>
                                <span class="text-sm text-gray-900">{{ ucfirst($license->documentation_status) }}</span>
                            </div>
                            @endif
                            @if($license->transfer_status)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Transfer Status</span>
                                <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $license->transfer_status)) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Approval Information -->
                @if($license->approved_by || $license->requested_by)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Information</h3>
                        <div class="space-y-4">
                            @if($license->requested_by)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Requested By</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $license->requestedByUser->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $license->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @endif
                            @if($license->approved_by)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Approved By</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $license->approvedByUser->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $license->approved_at->format('M d, Y H:i') }}</p>
                            </div>
                            @endif
                            @if($license->rejection_reason)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Rejection Reason</label>
                                <p class="mt-1 text-sm text-red-600">{{ $license->rejection_reason }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            @if($license->status === 'pending' && (auth()->user()->isAssociationAdmin() || auth()->user()->isAssociationRegistrar() || auth()->user()->isSystemAdmin()))
                            <button onclick="showApproveModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve License
                            </button>
                            <button onclick="showRejectModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject License
                            </button>
                            @endif

                            @if($license->status === 'active' && (auth()->user()->isAssociationAdmin() || auth()->user()->isAssociationRegistrar() || auth()->user()->isSystemAdmin()))
                            <button onclick="showRenewModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Renew License
                            </button>
                            <button onclick="showSuspendModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Suspend License
                            </button>
                            @endif

                            <a href="{{ route('club-management.licenses.edit', $license) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit License
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Approve License</h3>
            <form method="POST" action="{{ route('club-management.licenses.approve', $license) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date *</label>
                        <input type="date" name="expiry_date" id="expiry_date" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">License Number</label>
                        <input type="text" name="license_number" id="license_number" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Auto-generated if empty">
                    </div>
                    <div>
                        <label for="approval_notes" class="block text-sm font-medium text-gray-700">Approval Notes</label>
                        <textarea name="approval_notes" id="approval_notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="hideApproveModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject License</h3>
            <form method="POST" action="{{ route('club-management.licenses.reject', $license) }}">
                @csrf
                <div>
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason *</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Please provide a detailed reason for rejection..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Renew Modal -->
<div id="renewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Renew License</h3>
            <form method="POST" action="{{ route('club-management.licenses.renew', $license) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="new_expiry_date" class="block text-sm font-medium text-gray-700">New Expiry Date *</label>
                        <input type="date" name="new_expiry_date" id="new_expiry_date" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="renewal_notes" class="block text-sm font-medium text-gray-700">Renewal Notes</label>
                        <textarea name="renewal_notes" id="renewal_notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="hideRenewModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Renew</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Suspend License</h3>
            <form method="POST" action="{{ route('club-management.licenses.suspend', $license) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="suspension_reason" class="block text-sm font-medium text-gray-700">Suspension Reason *</label>
                        <textarea name="suspension_reason" id="suspension_reason" rows="4" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Please provide a detailed reason for suspension..."></textarea>
                    </div>
                    <div>
                        <label for="suspension_duration" class="block text-sm font-medium text-gray-700">Duration (days)</label>
                        <input type="number" name="suspension_duration" id="suspension_duration" min="1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Leave empty for indefinite">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="hideSuspendModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Suspend</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function hideApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function showRenewModal() {
    document.getElementById('renewModal').classList.remove('hidden');
}

function hideRenewModal() {
    document.getElementById('renewModal').classList.add('hidden');
}

function showSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
}

function hideSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = ['approveModal', 'rejectModal', 'renewModal', 'suspendModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
}
</script>
@endsection 