@extends('back-office.layouts.app')

@section('title', 'Audit Trail Details - Back Office')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Audit Trail Details</h1>
                <p class="text-gray-600 mt-2">Detailed information about this audit trail entry</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('back-office.audit-trail.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Entry ID</label>
                        <p class="mt-1 text-sm text-gray-900">#{{ $auditTrail->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Timestamp</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->occurred_at->format('M j, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Action</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $auditTrail->action_label }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Event Type</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $auditTrail->event_type_label }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Severity</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $auditTrail->severity_color }}-100 text-{{ $auditTrail->severity_color }}-800">
                                {{ ucfirst($auditTrail->severity) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($auditTrail->user)
                                {{ $auditTrail->user->name }} ({{ $auditTrail->user->email }})
                            @else
                                System
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                <p class="text-gray-900">{{ $auditTrail->description }}</p>
            </div>

            <!-- Model Information -->
            @if($auditTrail->model_type)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Record</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Model Type</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->model_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Record ID</label>
                        <p class="mt-1 text-sm text-gray-900">#{{ $auditTrail->model_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Table</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->table_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Changes Summary</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->changes_summary }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Changes Details -->
            @if($auditTrail->old_values || $auditTrail->new_values)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Changes Details</h3>
                
                @if($auditTrail->old_values && $auditTrail->new_values)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($auditTrail->new_values as $field => $newValue)
                                @php
                                    $oldValue = $auditTrail->old_values[$field] ?? null;
                                @endphp
                                @if($oldValue !== $newValue)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $field)) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="max-w-xs truncate" title="{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}">
                                            {{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="max-w-xs truncate" title="{{ is_array($newValue) ? json_encode($newValue) : $newValue }}">
                                            {{ is_array($newValue) ? json_encode($newValue) : $newValue }}
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @elseif($auditTrail->new_values)
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">New Values:</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <pre class="text-sm text-gray-900 overflow-x-auto">{{ json_encode($auditTrail->new_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @elseif($auditTrail->old_values)
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Old Values:</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <pre class="text-sm text-gray-900 overflow-x-auto">{{ json_encode($auditTrail->old_values, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Metadata -->
            @if($auditTrail->metadata)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Metadata</h3>
                <div class="bg-gray-50 p-4 rounded-md">
                    <pre class="text-sm text-gray-900 overflow-x-auto">{{ json_encode($auditTrail->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Request Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IP Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->ip_address }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Request Method</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->request_method }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Request URL</label>
                        <p class="mt-1 text-sm text-gray-900 break-all">{{ $auditTrail->request_url }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Session ID</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $auditTrail->session_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Request ID</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $auditTrail->request_id }}</p>
                    </div>
                </div>
            </div>

            <!-- User Agent -->
            @if($auditTrail->user_agent)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Agent</h3>
                <p class="text-sm text-gray-900 break-all">{{ $auditTrail->user_agent }}</p>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timestamps</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Occurred At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->occurred_at->format('M j, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->created_at->format('M j, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Updated At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditTrail->updated_at->format('M j, Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Related Entries -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Entries</h3>
                <div class="space-y-2">
                    @if($auditTrail->user_id)
                    <a href="{{ route('back-office.audit-trail.index', ['user_id' => $auditTrail->user_id]) }}" 
                       class="block text-sm text-blue-600 hover:text-blue-900">
                        View all entries by this user
                    </a>
                    @endif
                    @if($auditTrail->model_type && $auditTrail->model_id)
                    <a href="{{ route('back-office.audit-trail.index', ['model_type' => $auditTrail->model_type, 'model_id' => $auditTrail->model_id]) }}" 
                       class="block text-sm text-blue-600 hover:text-blue-900">
                        View all entries for this record
                    </a>
                    @endif
                    @if($auditTrail->ip_address)
                    <a href="{{ route('back-office.audit-trail.index', ['ip_address' => $auditTrail->ip_address]) }}" 
                       class="block text-sm text-blue-600 hover:text-blue-900">
                        View all entries from this IP
                    </a>
                    @endif
                    @if($auditTrail->session_id)
                    <a href="{{ route('back-office.audit-trail.index', ['session_id' => $auditTrail->session_id]) }}" 
                       class="block text-sm text-blue-600 hover:text-blue-900">
                        View all entries from this session
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 