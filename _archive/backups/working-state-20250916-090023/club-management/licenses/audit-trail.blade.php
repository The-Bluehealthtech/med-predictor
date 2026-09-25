@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                                License Audit Trail
                            </h2>
                            <p class="text-gray-600 mt-1">
                                Complete history of changes for license #{{ $license->license_number }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('club-management.licenses.show', $license) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to License
                        </a>
                        <a href="{{ route('club-management.licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            All Licenses
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Summary -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">License Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Player</dt>
                        <dd class="text-sm text-gray-900">{{ $license->player->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">License Number</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ $license->license_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Club</dt>
                        <dd class="text-sm text-gray-900">{{ $license->club->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                        <dd class="text-sm">
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
                        </dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Trail -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Change History</h3>
                
                @if($auditTrail->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($auditTrail as $index => $trail)
                        <li>
                            <div class="relative pb-8">
                                @if($index < $auditTrail->count() - 1)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                            @if($trail->action === 'created') bg-green-500
                                            @elseif($trail->action === 'approved') bg-blue-500
                                            @elseif($trail->action === 'rejected') bg-red-500
                                            @elseif($trail->action === 'suspended') bg-orange-500
                                            @elseif($trail->action === 'renewed') bg-purple-500
                                            @elseif($trail->action === 'updated') bg-yellow-500
                                            @elseif($trail->action === 'deleted') bg-gray-500
                                            @else bg-gray-400
                                            @endif">
                                            @if($trail->action === 'created')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            @elseif($trail->action === 'approved')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($trail->action === 'rejected')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            @elseif($trail->action === 'suspended')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                            @elseif($trail->action === 'renewed')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            @elseif($trail->action === 'updated')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            @elseif($trail->action === 'deleted')
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            @else
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                <span class="font-medium text-gray-900">
                                                    {{ ucfirst($trail->action) }}
                                                </span>
                                                @if($trail->old_status && $trail->new_status)
                                                    from <span class="font-medium">{{ ucfirst($trail->old_status) }}</span> to <span class="font-medium">{{ ucfirst($trail->new_status) }}</span>
                                                @endif
                                                @if($trail->user)
                                                    by <span class="font-medium">{{ $trail->user->name }}</span>
                                                @endif
                                            </p>
                                            @if($trail->reason)
                                            <p class="text-sm text-gray-600 mt-1">
                                                <strong>Reason:</strong> {{ $trail->reason }}
                                            </p>
                                            @endif
                                            @if($trail->changes)
                                            <div class="mt-2 text-sm text-gray-600">
                                                <details class="group">
                                                    <summary class="cursor-pointer font-medium text-gray-700 hover:text-gray-900">
                                                        View Changes
                                                    </summary>
                                                    <div class="mt-2 pl-4 border-l-2 border-gray-200">
                                                        @foreach($trail->changes as $field => $change)
                                                        <div class="mb-1">
                                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                            @if(is_array($change))
                                                                <span class="text-red-600">"{{ $change['old'] ?? 'N/A' }}"</span>
                                                                <span class="mx-1">→</span>
                                                                <span class="text-green-600">"{{ $change['new'] ?? 'N/A' }}"</span>
                                                            @else
                                                                {{ $change }}
                                                            @endif
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <time datetime="{{ $trail->created_at->format('Y-m-d H:i:s') }}">
                                                {{ $trail->created_at->format('M d, Y H:i') }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No audit trail found</h3>
                    <p class="mt-1 text-sm text-gray-500">This license has no recorded changes yet.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Technical Details -->
        @if($auditTrail->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Technical Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Total Changes</dt>
                        <dd class="text-gray-900">{{ $auditTrail->count() }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">First Change</dt>
                        <dd class="text-gray-900">{{ $auditTrail->last()->created_at->format('M d, Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Last Change</dt>
                        <dd class="text-gray-900">{{ $auditTrail->first()->created_at->format('M d, Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">License Age</dt>
                        <dd class="text-gray-900">{{ $license->created_at->diffForHumans() }}</dd>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 