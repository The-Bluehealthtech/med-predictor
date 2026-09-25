@extends('layouts.app')

@section('title', 'OAuth2 Tokens Management - Device Connections')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">OAuth2 Tokens Management</h2>
                        <p class="text-gray-600 mt-1">Manage authentication tokens for device connections</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('device-connections.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Devices
                        </a>
                        <button onclick="refreshTokens()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connection Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Catapult Connect -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Catapult Connect</h3>
                            <p class="text-sm text-gray-500">GPS & Biometrics</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Token Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Expires</span>
                            <span class="text-sm text-gray-500" id="catapult-expires">In 1 hour</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Last Used</span>
                            <span class="text-sm text-gray-500">2 hours ago</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button onclick="renewToken('catapult')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded text-sm font-medium">
                            Renew Token
                        </button>
                        <button onclick="revokeToken('catapult')" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded text-sm font-medium">
                            Revoke
                        </button>
                    </div>
                </div>
            </div>

            <!-- Apple HealthKit -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Apple HealthKit</h3>
                            <p class="text-sm text-gray-500">Health Data</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Token Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Expires</span>
                            <span class="text-sm text-gray-500" id="apple-expires">In 1 hour</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Last Used</span>
                            <span class="text-sm text-gray-500">30 min ago</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button onclick="renewToken('apple')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded text-sm font-medium">
                            Renew Token
                        </button>
                        <button onclick="revokeToken('apple')" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded text-sm font-medium">
                            Revoke
                        </button>
                    </div>
                </div>
            </div>

            <!-- Garmin Connect -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Garmin Connect</h3>
                            <p class="text-sm text-gray-500">Fitness Data</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Token Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Expired
                            </span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Expires</span>
                            <span class="text-sm text-gray-500" id="garmin-expires">1 hour ago</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Last Used</span>
                            <span class="text-sm text-gray-500">2 days ago</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button onclick="renewToken('garmin')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded text-sm font-medium">
                            Renew Token
                        </button>
                        <button onclick="revokeToken('garmin')" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded text-sm font-medium">
                            Revoke
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Token Details Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Token Details</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tokens-table">
                            <!-- Token rows will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- OAuth2 Configuration -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">OAuth2 Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-medium text-gray-700 mb-3">Client Credentials</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Client ID</label>
                                <input type="text" value="fit_oauth_client_123" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Redirect URI</label>
                                <input type="text" value="http://localhost:8000/device-connections/oauth2/callback" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-md font-medium text-gray-700 mb-3">Scopes</h4>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" checked disabled class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label class="ml-2 text-sm text-gray-700">Read health data</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" checked disabled class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label class="ml-2 text-sm text-gray-700">Read activity data</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" checked disabled class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label class="ml-2 text-sm text-gray-700">Read biometric data</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load tokens on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTokens();
});

function loadTokens() {
    // Simulate loading tokens from API
    const tokens = [
        {
            service: 'catapult',
            status: 'active',
            expires_at: new Date(Date.now() + 3600000).toISOString(),
            created_at: new Date(Date.now() - 86400000).toISOString(),
            last_used: new Date(Date.now() - 7200000).toISOString()
        },
        {
            service: 'apple',
            status: 'active',
            expires_at: new Date(Date.now() + 3600000).toISOString(),
            created_at: new Date(Date.now() - 43200000).toISOString(),
            last_used: new Date(Date.now() - 1800000).toISOString()
        },
        {
            service: 'garmin',
            status: 'expired',
            expires_at: new Date(Date.now() - 3600000).toISOString(),
            created_at: new Date(Date.now() - 172800000).toISOString(),
            last_used: new Date(Date.now() - 172800000).toISOString()
        }
    ];

    updateTokensTable(tokens);
    updateExpiryTimes(tokens);
}

function updateTokensTable(tokens) {
    const tbody = document.getElementById('tokens-table');
    tbody.innerHTML = '';

    tokens.forEach(token => {
        const row = document.createElement('tr');
        const statusClass = token.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const statusText = token.status === 'active' ? 'Active' : 'Expired';
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 capitalize">${token.service}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(token.expires_at)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(token.created_at)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(token.last_used)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="renewToken('${token.service}')" class="text-blue-600 hover:text-blue-900 mr-3">Renew</button>
                <button onclick="revokeToken('${token.service}')" class="text-red-600 hover:text-red-900">Revoke</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function updateExpiryTimes(tokens) {
    tokens.forEach(token => {
        const element = document.getElementById(`${token.service}-expires`);
        if (element) {
            const expiresAt = new Date(token.expires_at);
            const now = new Date();
            const diff = expiresAt - now;
            
            if (diff > 0) {
                const hours = Math.floor(diff / 3600000);
                const minutes = Math.floor((diff % 3600000) / 60000);
                element.textContent = `In ${hours}h ${minutes}m`;
            } else {
                const hours = Math.floor(Math.abs(diff) / 3600000);
                element.textContent = `${hours} hour${hours !== 1 ? 's' : ''} ago`;
            }
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

function refreshTokens() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Refreshing...';
    button.disabled = true;

    // Simulate API call
    setTimeout(() => {
        loadTokens();
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Show success message
        showNotification('Tokens refreshed successfully!', 'success');
    }, 1000);
}

function renewToken(service) {
    // Show loading state
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Renewing...';
    button.disabled = true;

    // Simulate API call
    setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
        showNotification(`${service} token renewed successfully!`, 'success');
        loadTokens(); // Refresh the data
    }, 1500);
}

function revokeToken(service) {
    if (confirm(`Are you sure you want to revoke the ${service} token? This will disconnect the service.`)) {
        // Show loading state
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Revoking...';
        button.disabled = true;

        // Simulate API call
        setTimeout(() => {
            button.textContent = originalText;
            button.disabled = false;
            showNotification(`${service} token revoked successfully!`, 'success');
            loadTokens(); // Refresh the data
        }, 1000);
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection 