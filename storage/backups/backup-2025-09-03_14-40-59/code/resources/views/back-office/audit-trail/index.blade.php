@extends('back-office.layouts.app')

@section('title', 'Audit Trail - Back Office')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Audit Trail</h1>
                <p class="text-gray-600 mt-2">Track all user actions and system events for security and compliance</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Back to Dashboard
                </a>
                <a href="{{ route('back-office.audit-trail.export') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </a>
                <button onclick="toggleRealtime()" id="realtimeBtn" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span id="realtimeText">Enable Real-time</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Entries</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Recent Activity (24h)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['recent_activity']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Security Events</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['security_events']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Critical Events</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['critical_events']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('back-office.audit-trail.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Search descriptions, actions...">
                </div>
                
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                    <select name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Users</option>
                        @foreach($filterOptions['users'] as $id => $name)
                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                    <select name="event_type" id="event_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($filterOptions['event_types'] as $type)
                            <option value="{{ $type }}" {{ request('event_type') === $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                    <select name="severity" id="severity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Severities</option>
                        @foreach($filterOptions['severities'] as $severity)
                            <option value="{{ $severity }}" {{ request('severity') === $severity ? 'selected' : '' }}>
                                {{ ucfirst($severity) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="action" class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                    <select name="action" id="action" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Actions</option>
                        @foreach($filterOptions['actions'] as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-2">IP Address</label>
                    <input type="text" name="ip_address" id="ip_address" value="{{ request('ip_address') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Filter by IP...">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Trail Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="auditTableBody">
                    @forelse($auditTrails as $trail)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $trail->occurred_at->format('M j, Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($trail->user)
                                <div class="text-sm font-medium text-gray-900">{{ $trail->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $trail->user->email }}</div>
                            @else
                                <span class="text-sm text-gray-500">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $trail->action_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $trail->event_type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                bg-{{ $trail->severity_color }}-100 text-{{ $trail->severity_color }}-800">
                                {{ ucfirst($trail->severity) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $trail->description }}">
                                {{ $trail->description }}
                            </div>
                            @if($trail->model_type)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $trail->model_name }} #{{ $trail->model_id }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $trail->ip_address }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('back-office.audit-trail.show', $trail) }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No audit trail entries found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($auditTrails->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $auditTrails->links() }}
            </div>
        @endif
    </div>

    <!-- Cleanup Section -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Management</h3>
        <div class="flex items-center space-x-4">
            <input type="number" id="cleanupDays" min="1" max="365" value="90" 
                   class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Days">
            <button onclick="clearOldEntries()" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors">
                Clear Entries Older Than
            </button>
            <span class="text-sm text-gray-500">This action cannot be undone.</span>
        </div>
    </div>
</div>

<script>
let realtimeEnabled = false;
let realtimeInterval = null;
let lastId = {{ $auditTrails->first() ? $auditTrails->first()->id : 0 }};

function toggleRealtime() {
    if (realtimeEnabled) {
        // Disable realtime
        if (realtimeInterval) {
            clearInterval(realtimeInterval);
            realtimeInterval = null;
        }
        realtimeEnabled = false;
        document.getElementById('realtimeText').textContent = 'Enable Real-time';
        document.getElementById('realtimeBtn').classList.remove('bg-green-600', 'hover:bg-green-700');
        document.getElementById('realtimeBtn').classList.add('bg-blue-600', 'hover:bg-blue-700');
    } else {
        // Enable realtime
        realtimeEnabled = true;
        document.getElementById('realtimeText').textContent = 'Disable Real-time';
        document.getElementById('realtimeBtn').classList.remove('bg-blue-600', 'hover:bg-blue-700');
        document.getElementById('realtimeBtn').classList.add('bg-green-600', 'hover:bg-green-700');
        
        // Start polling
        realtimeInterval = setInterval(checkNewEntries, 5000); // Check every 5 seconds
    }
}

function checkNewEntries() {
    fetch(`{{ route('back-office.audit-trail.realtime') }}?last_id=${lastId}`)
        .then(response => response.json())
        .then(data => {
            if (data.entries && data.entries.length > 0) {
                // Update last ID
                lastId = data.last_id;
                
                // Add new entries to the top of the table
                const tbody = document.getElementById('auditTableBody');
                data.entries.forEach(entry => {
                    const row = createAuditRow(entry);
                    tbody.insertBefore(row, tbody.firstChild);
                });
                
                // Remove old rows if we have too many
                while (tbody.children.length > 50) {
                    tbody.removeChild(tbody.lastChild);
                }
            }
        })
        .catch(error => console.error('Error checking for new entries:', error));
}

function createAuditRow(entry) {
    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50 bg-yellow-50'; // Highlight new entries
    
    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            ${new Date(entry.occurred_at).toLocaleString()}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            ${entry.user ? `
                <div class="text-sm font-medium text-gray-900">${entry.user.name}</div>
                <div class="text-sm text-gray-500">${entry.user.email}</div>
            ` : '<span class="text-sm text-gray-500">System</span>'}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                ${entry.action.charAt(0).toUpperCase() + entry.action.slice(1)}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                ${entry.event_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getSeverityColor(entry.severity)}-100 text-${getSeverityColor(entry.severity)}-800">
                ${entry.severity.charAt(0).toUpperCase() + entry.severity.slice(1)}
            </span>
        </td>
        <td class="px-6 py-4 text-sm text-gray-900">
            <div class="max-w-xs truncate" title="${entry.description}">
                ${entry.description}
            </div>
            ${entry.model_type ? `
                <div class="text-xs text-gray-500 mt-1">
                    ${entry.model_type.split('\\').pop()} #${entry.model_id}
                </div>
            ` : ''}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            ${entry.ip_address}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <a href="/back-office/audit-trail/${entry.id}" 
               class="text-indigo-600 hover:text-indigo-900">
                View Details
            </a>
        </td>
    `;
    
    // Remove highlight after 5 seconds
    setTimeout(() => {
        row.classList.remove('bg-yellow-50');
    }, 5000);
    
    return row;
}

function getSeverityColor(severity) {
    switch(severity) {
        case 'critical': return 'red';
        case 'error': return 'orange';
        case 'warning': return 'yellow';
        case 'info': return 'blue';
        default: return 'gray';
    }
}

function clearOldEntries() {
    const days = document.getElementById('cleanupDays').value;
    if (!days || days < 1 || days > 365) {
        alert('Please enter a valid number of days (1-365)');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete all audit trail entries older than ${days} days? This action cannot be undone.`)) {
        return;
    }
    
    fetch('{{ route("back-office.audit-trail.clear-old-entries") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ days: parseInt(days) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while clearing old entries');
    });
}
</script>
@endsection 