<x-back-office-layout>
    <x-slot name="title">System Logs - Back Office</x-slot>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">System Logs</h1>
                    <p class="mt-1 text-sm text-gray-600">Monitor system activity and errors</p>
                </div>
                <div>
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Filters -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="GET" action="{{ route('back-office.logs') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700">Log Level</label>
                        <select name="level" id="level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Levels</option>
                            <option value="emergency" {{ request('level') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="alert" {{ request('level') == 'alert' ? 'selected' : '' }}>Alert</option>
                            <option value="critical" {{ request('level') == 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                            <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="notice" {{ request('level') == 'notice' ? 'selected' : '' }}>Notice</option>
                            <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="debug" {{ request('level') == 'debug' ? 'selected' : '' }}>Debug</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Search in logs..." 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Filter Logs
                        </button>
                        <a href="{{ route('back-office.logs') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Clear Filters
                        </a>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" onclick="refreshLogs()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Refresh
                        </button>
                        <button type="button" onclick="downloadLogs()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Download
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Log Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Errors</h3>
                        <p class="text-lg font-semibold text-red-600">{{ $stats['errors'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Warnings</h3>
                        <p class="text-lg font-semibold text-yellow-600">{{ $stats['warnings'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Info</h3>
                        <p class="text-lg font-semibold text-blue-600">{{ $stats['info'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Total</h3>
                        <p class="text-lg font-semibold text-green-600">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Entries -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Log Entries</h3>
                <div class="text-sm text-gray-500">
                    Showing {{ $logs->count() }} entries
                </div>
            </div>

            @if($logs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Context</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->timestamp ?? now()->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $log->level == 'error' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $log->level == 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $log->level == 'info' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $log->level == 'debug' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($log->level ?? 'info') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $log->message ?? 'No message' }}">
                                            {{ $log->message ?? 'No message' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="max-w-xs truncate" title="{{ $log->context ?? 'No context' }}">
                                            {{ $log->context ?? 'No context' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewLogDetails('{{ $log->id ?? '' }}')" 
                                                class="text-indigo-600 hover:text-indigo-900">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination removed since logs is a Collection, not a paginator -->
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No logs found</h3>
                    <p class="mt-1 text-sm text-gray-500">No log entries match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div id="logModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Log Details</h3>
                <button onclick="closeLogModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="logDetails" class="space-y-4">
                <!-- Log details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function refreshLogs() {
    window.location.reload();
}

function downloadLogs() {
    const params = new URLSearchParams(window.location.search);
    window.open(`{{ route('back-office.logs.download') }}?${params.toString()}`, '_blank');
}

function viewLogDetails(logId) {
    // Add AJAX call to get log details
    console.log('Viewing log details for:', logId);
    
    // For now, just show the modal
    document.getElementById('logModal').classList.remove('hidden');
}

function closeLogModal() {
    document.getElementById('logModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('logModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogModal();
    }
});
</script>
</x-back-office-layout> 