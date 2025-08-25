@extends('layouts.app')

@section('title', 'Match Details - League Championship - Med Predictor')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('league-championship.index') }}" 
                       class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">⚽ Match Details</h1>
                        <p class="mt-1 text-sm text-gray-500">League Championship Management</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-4">
                    <!-- FIFA Connect Status -->
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-600">FIFA Connect Active</span>
                    </div>
                    
                    <!-- Export Button -->
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                    
                    <!-- Print Button -->
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4 py-4">
                    <li>
                        <div>
                            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                <span class="sr-only">Home</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('league-championship.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                League Championship
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-900">Match Details</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1">
        <!-- Vue.js App Container -->
        <div id="match-app" class="min-h-screen">
            <!-- Loading State -->
            <div class="flex items-center justify-center min-h-64">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Loading match details...</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">© 2024 Med Predictor. All rights reserved.</span>
                    <span class="text-sm text-gray-400">|</span>
                    <span class="text-sm text-gray-500">FIFA Connect Integration</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Version 1.0.0</span>
                    <span class="text-sm text-gray-400">|</span>
                    <span class="text-sm text-gray-500">Production Environment</span>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- Scripts -->
@vite(['resources/css/app.css', 'resources/js/league-championship.js'])

<script>
    // Pass Laravel data to Vue
    window.Laravel = {
        user: @json(auth()->user()),
        csrfToken: '{{ csrf_token() }}',
        apiUrl: '{{ url("/api") }}',
        baseUrl: '{{ url("/") }}',
        matchId: {{ request()->route('match') }},
        permissions: @json(auth()->user()?->permissions ?? []),
        role: '{{ auth()->user()?->role ?? "" }}'
    };
    
    // Production configuration
    window.AppConfig = {
        environment: 'production',
        debug: false,
        version: '1.0.0',
        features: {
            realTimeUpdates: true,
            exportFunctionality: true,
            printFunctionality: true,
            fifaConnect: true
        }
    };
    
    // Error handling for production
    window.addEventListener('error', function(e) {
        console.error('Application error:', e.error);
        // In production, you might want to send this to an error tracking service
    });
    
    // Performance monitoring
    window.addEventListener('load', function() {
        if ('performance' in window) {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
        }
    });
</script>

<style>
    /* Production-specific styles */
    .production-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .status-indicator {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
    
    /* Responsive design improvements */
    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            gap: 1rem;
        }
        
        .breadcrumb {
            overflow-x: auto;
            white-space: nowrap;
        }
    }
    
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-only {
            display: block !important;
        }
    }
</style>
@endsection 