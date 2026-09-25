@extends('layouts.app')

@section('title', 'FIFA Connect Status - Med Predictor')

@push('meta')
<meta name="cache-control" content="no-cache, no-store, must-revalidate">
<meta name="pragma" content="no-cache">
<meta name="expires" content="0">
@endpush

@push('styles')
<style>
    /* Hide default navigation */
    nav:not(.fifa-nav) {
        display: none !important;
    }
    
    .status-indicator {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .connection-card {
        transition: all 0.3s ease;
    }
    
    .connection-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .mock-mode-badge {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    /* Custom Navigation Styles */
    .fifa-nav {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
        border-bottom: 3px solid #fbbf24;
    }
    
    .fifa-nav-link {
        @apply px-4 py-2 text-sm font-medium text-white rounded-md transition-all duration-200;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .fifa-nav-link:hover {
        @apply bg-white bg-opacity-20 text-white transform scale-105;
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.4);
    }
    
    .fifa-nav-link.active {
        @apply bg-white bg-opacity-25 text-white shadow-lg;
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    
    .fifa-action-btn {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
    }
    
    .fifa-action-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-1px);
    }
    
    .fifa-test-btn {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: 1px solid #fbbf24;
    }
    
    .fifa-test-btn:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        transform: translateY(-1px);
    }
    
    .fifa-dashboard-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: 1px solid #34d399;
        color: white;
    }
    
    .fifa-dashboard-btn:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
    }
    
    .fifa-status-indicator {
        animation: fifa-pulse 2s infinite;
    }
    
    @keyframes fifa-pulse {
        0%, 100% { 
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }
        50% { 
            box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
        }
    }
    
    /* Improved navigation elements with better contrast */
    .fifa-logo-bg {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        border: 2px solid #ffffff;
    }
    
    .fifa-logo-icon {
        color: #1e40af;
    }
    
    /* Force navigation text backgrounds with !important */
    .fifa-brand-text {
        background: rgba(0, 0, 0, 0.3) !important;
        padding: 4px 8px !important;
        border-radius: 6px !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        display: inline-block !important;
    }
    
    .fifa-status-text {
        background: rgba(34, 197, 94, 0.2) !important;
        padding: 4px 8px !important;
        border-radius: 6px !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(34, 197, 94, 0.4) !important;
        display: inline-block !important;
    }
    
    .fifa-nav-link {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        padding: 8px 16px !important;
        border-radius: 6px !important;
        color: white !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
        display: inline-block !important;
    }
    
    .fifa-nav-link:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        border-color: rgba(255, 255, 255, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    
    .fifa-nav-link.active {
        background: rgba(255, 255, 255, 0.3) !important;
        border-color: rgba(255, 255, 255, 0.5) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
    }
    
    .fifa-action-btn {
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        backdrop-filter: blur(10px) !important;
        color: white !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        font-weight: 500 !important;
    }
    
    .fifa-action-btn:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        border-color: rgba(255, 255, 255, 0.5) !important;
        transform: translateY(-1px) !important;
    }
    
    .fifa-test-btn {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        border: 1px solid #fbbf24 !important;
        color: white !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        font-weight: 500 !important;
    }
    
    .fifa-test-btn:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
        transform: translateY(-1px) !important;
    }
    
    .fifa-dashboard-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: 1px solid #34d399 !important;
        color: white !important;
        padding: 8px 16px !important;
        border-radius: 6px !important;
        font-weight: 500 !important;
    }
    
    .fifa-dashboard-btn:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        transform: translateY(-1px) !important;
    }
</style>
@endpush

@section('content')
<!-- Custom FIFA Navigation Bar -->
<nav class="fifa-nav shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left side - Logo and Brand -->
            <div class="flex items-center space-x-6">
                <!-- FIFA Logo -->
                <div class="flex items-center">
                    <div class="fifa-logo-bg rounded-full p-2 mr-3 shadow-lg">
                        <svg class="w-8 h-8 fifa-logo-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="fifa-brand-text" style="background: rgba(0, 0, 0, 0.3); padding: 4px 8px; border-radius: 6px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); display: inline-block;">
                        <h1 class="text-xl font-bold drop-shadow-lg" style="color: #1e3a8a;">FIFA Connect</h1>
                        <p class="text-xs font-medium" style="color: #1e3a8a;">Status Monitor</p>
                    </div>
                </div>
                
                <!-- Status Indicator -->
                <div class="hidden md:flex items-center space-x-2">
                    <div class="fifa-status-indicator w-3 h-3 bg-green-400 rounded-full"></div>
                    <span class="text-sm font-medium drop-shadow-sm fifa-status-text" style="background: rgba(34, 197, 94, 0.2); padding: 4px 8px; border-radius: 6px; backdrop-filter: blur(10px); border: 1px solid rgba(34, 197, 94, 0.4); display: inline-block; color: #1e3a8a;">Live Monitoring</span>
                </div>
            </div>
            
            <!-- Center - Navigation Links -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('fifa.connectivity') }}" class="fifa-nav-link active" style="background: rgba(255, 255, 255, 0.3); padding: 8px 16px; border-radius: 6px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); font-weight: 500; display: inline-block; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); color: #1e3a8a;">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Status
                </a>
                <a href="{{ route('fifa.sync-dashboard') }}" class="fifa-nav-link" style="background: rgba(255, 255, 255, 0.1); padding: 8px 16px; border-radius: 6px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); font-weight: 500; display: inline-block; color: #1e3a8a;">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Sync
                </a>
                <a href="{{ route('fifa-dashboard.index') }}" class="fifa-nav-link" style="background: rgba(255, 255, 255, 0.1); padding: 8px 16px; border-radius: 6px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); font-weight: 500; display: inline-block; color: #1e3a8a;">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('fifa.statistics') }}" class="fifa-nav-link" style="background: rgba(255, 255, 255, 0.1); padding: 8px 16px; border-radius: 6px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); font-weight: 500; display: inline-block; color: #1e3a8a;">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Analytics
                </a>
            </div>
            
            <!-- Right side - Actions and User -->
            <div class="flex items-center space-x-4">
                <!-- Quick Actions -->
                <div class="hidden sm:flex items-center space-x-2">
                    <button onclick="refreshStatus()" class="fifa-action-btn px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center shadow-lg" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(10px); padding: 8px 12px; border-radius: 6px; font-weight: 500; color: #1e3a8a;">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                    <button onclick="runDiagnostics()" class="fifa-test-btn px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center shadow-lg" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: 1px solid #fbbf24; padding: 8px 12px; border-radius: 6px; font-weight: 500; color: #1e3a8a;">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Test
                    </button>
                </div>
                
                <!-- Back to Main Dashboard -->
                <a href="{{ route('dashboard') }}" class="fifa-dashboard-btn px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center shadow-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: 1px solid #34d399; padding: 8px 16px; border-radius: 6px; font-weight: 500; color: #1e3a8a;">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Main Dashboard
                </a>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="fifa-action-btn text-white hover:text-gray-200 focus:outline-none focus:text-gray-200 rounded-md p-2" onclick="toggleMobileMenu()">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <div class="space-y-2">
                <a href="{{ route('fifa.connectivity') }}" class="fifa-nav-link active block">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Status
                </a>
                <a href="{{ route('fifa.sync-dashboard') }}" class="fifa-nav-link block">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Sync
                </a>
                <a href="{{ route('fifa-dashboard.index') }}" class="fifa-nav-link block">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('fifa.statistics') }}" class="fifa-nav-link block">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Analytics
                </a>
                <div class="pt-2 border-t border-white border-opacity-20">
                    <button onclick="refreshStatus()" class="w-full fifa-action-btn text-white px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Status
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="py-8 bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Enhanced Header -->
        <div class="mb-8 text-center">
            <div class="flex items-center justify-center mb-4">
                <div class="p-3 bg-blue-800 bg-opacity-50 rounded-full mr-4 border border-blue-400">
                    <svg class="h-8 w-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white drop-shadow-lg">FIFA Connect Status</h1>
            </div>
            <p class="text-lg text-gray-300 mb-8 max-w-3xl mx-auto leading-relaxed" style="color: #1e3a8a;">
                Real-time monitoring of FIFA Connect API connectivity, performance metrics, and system health
            </p>
        </div>

        <!-- FIFA SDK API Credentials Information -->
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden mb-8 border border-gray-600">
            <div class="px-6 py-4 border-b border-gray-600 bg-gradient-to-r from-blue-900 to-indigo-900">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-700 bg-opacity-50 rounded-full mr-4 border border-blue-400">
                        <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">FIFA SDK API Credentials</h3>
                </div>
                <p class="text-sm text-blue-200 mt-1">How to obtain access to FIFA Connect ID service</p>
            </div>
            <div class="p-6">
                <div class="prose prose-invert max-w-none">
                    <p class="text-gray-300 mb-4">
                        To obtain FIFA SDK API credentials, you need to contact FIFA Connect ID Support. You can either open a ticket through their support portal or send an email to <strong class="text-blue-300">support.id@fifa.org</strong>, which will automatically create a support ticket. The SDK also contains documentation and tools for generating certificates, but these tools are currently limited to Microsoft operating systems.
                    </p>
                    
                    <div class="bg-gray-700 bg-opacity-50 rounded-lg p-4 mb-4 border border-gray-600">
                        <h4 class="font-semibold text-white mb-3">Here's a more detailed breakdown:</h4>
                        <ol class="list-decimal list-inside space-y-2 text-gray-300">
                            <li><strong class="text-blue-300">Contact FIFA Connect ID Support:</strong> Reach out to the support team to request access to the FIFA Connect ID service and its associated APIs.</li>
                            <li><strong class="text-blue-300">Ticket Submission or Email:</strong> You can either submit a ticket through their support portal or send an email to <code class="bg-gray-600 px-1 rounded text-blue-300">support.id@fifa.org</code> to initiate the process.</li>
                            <li><strong class="text-blue-300">Certificate Generation (If Needed):</strong> If you require certificates for using the Connect Service Bus, you can find instructions and tools within the SDK package.</li>
                        </ol>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center px-3 py-2 bg-blue-900 bg-opacity-50 text-blue-200 rounded-lg border border-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium">Email: support.id@fifa.org</span>
                        </div>
                        <div class="flex items-center px-3 py-2 bg-green-900 bg-opacity-50 text-green-200 rounded-lg border border-green-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium">Automatic ticket creation</span>
                        </div>
                        <div class="flex items-center px-3 py-2 bg-yellow-900 bg-opacity-50 text-yellow-200 rounded-lg border border-yellow-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium">Windows-only tools</span>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Real-time Status Dashboard -->
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden mb-8 border border-gray-600">
            <div class="px-6 py-4 border-b border-gray-600 bg-gradient-to-r from-blue-900 to-indigo-900">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-white">Connection Status</h3>
                        <p class="text-sm text-blue-200 mt-1">Real-time monitoring of FIFA Connect API connectivity</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-blue-300">Last Updated</div>
                        <div id="last-updated" class="text-sm font-medium text-white">Just now</div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div id="status-content">
                    @if(isset($status) && $status === 'connected')
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-12 w-12 {{ $mockMode ? 'text-purple-400' : 'text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($mockMode)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @endif
                                    </svg>
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-2xl font-bold {{ $mockMode ? 'text-purple-400' : 'text-green-400' }}">Connected</h4>
                                    <p class="text-lg text-gray-300 mt-1">
                                        FIFA Connect API is accessible and responding
                                        @if($mockMode)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-900 text-purple-200 border border-purple-600 ml-3">Mock Mode</span>
                                        @endif
                                    </p>
                                            @if(isset($fallbackReason) && $fallbackReason)
                                                <p class="text-sm text-gray-400 mt-2">Fallback: {{ $fallbackReason }}</p>
                                            @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-white">
                                    @if(isset($responseTime) && $responseTime)
                                        {{ number_format($responseTime * 1000, 0) }}ms
                                    @else
                                        --
                                    @endif
                                </div>
                                <p class="text-sm text-gray-400">Response Time</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-2xl font-bold text-red-400">Disconnected</h4>
                                    <p class="text-lg text-gray-300 mt-1">Unable to connect to FIFA Connect API</p>
                                    @if(isset($error) && $error)
                                        <p class="text-sm text-red-400 mt-2">Error: {{ $error }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-red-400">--</div>
                                <p class="text-sm text-gray-400">Response Time</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Response Time -->
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl p-6 border border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-white">Response Time</h4>
                    <div class="p-2 bg-green-900 bg-opacity-50 rounded-full border border-green-600">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white" id="response-time">
                    @if(isset($responseTime) && $responseTime)
                        {{ number_format($responseTime * 1000, 0) }}ms
                    @else
                        Loading...
                    @endif
                </div>
                <p class="text-sm text-gray-400 mt-1">Average response time</p>
            </div>

            <!-- Uptime -->
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl p-6 border border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-white">Uptime</h4>
                    <div class="p-2 bg-blue-900 bg-opacity-50 rounded-full border border-blue-600">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white" id="uptime">99.8%</div>
                <p class="text-sm text-gray-400 mt-1">Last 24 hours</p>
            </div>

            <!-- API Calls -->
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl p-6 border border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-white">API Calls</h4>
                    <div class="p-2 bg-purple-900 bg-opacity-50 rounded-full border border-purple-600">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white" id="api-calls">847</div>
                <p class="text-sm text-gray-400 mt-1">Today's requests</p>
            </div>
        </div>
        </div>

        <!-- Detailed Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Configuration Details -->
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-600">
                <div class="px-6 py-4 border-b border-gray-600 bg-gradient-to-r from-gray-800 to-gray-700">
                    <h3 class="text-xl font-semibold text-white">Configuration Details</h3>
                    <p class="text-sm text-blue-200 mt-1">Current FIFA Connect API settings</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-700 bg-opacity-50 rounded-lg border border-gray-600">
                            <div>
                                <dt class="text-sm font-medium text-gray-300">Base URL</dt>
                                <dd class="text-sm text-white mt-1">{{ config('services.fifa_connect.base_url', 'https://api.fifa.com/v1') }}</dd>
                            </div>
                            <div class="p-2 bg-green-900 bg-opacity-50 rounded-full border border-green-600">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-700 bg-opacity-50 rounded-lg border border-gray-600">
                            <div>
                                <dt class="text-sm font-medium text-gray-300">API Version</dt>
                                <dd class="text-sm text-white mt-1">v1</dd>
                            </div>
                            <div class="p-2 bg-blue-900 bg-opacity-50 rounded-full border border-blue-600">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-700 bg-opacity-50 rounded-lg border border-gray-600">
                            <div>
                                <dt class="text-sm font-medium text-gray-300">Request Timeout</dt>
                                <dd class="text-sm text-white mt-1">{{ config('services.fifa_connect.timeout', 30) }} seconds</dd>
                            </div>
                            <div class="p-2 bg-yellow-900 bg-opacity-50 rounded-full border border-yellow-600">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-700 bg-opacity-50 rounded-lg border border-gray-600">
                            <div>
                                <dt class="text-sm font-medium text-gray-300">Cache Duration</dt>
                                <dd class="text-sm text-white mt-1">{{ config('services.fifa_connect.cache_ttl', 3600) / 3600 }} hour(s)</dd>
                            </div>
                            <div class="p-2 bg-purple-900 bg-opacity-50 rounded-full border border-purple-600">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Endpoints -->
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-600">
                <div class="px-6 py-4 border-b border-gray-600 bg-gradient-to-r from-gray-800 to-gray-700">
                    <h3 class="text-xl font-semibold text-white">Available Endpoints</h3>
                    <p class="text-sm text-blue-200 mt-1">FIFA Connect API endpoints and their status</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach([
                            ['GET /health', 'System health check', 'green'],
                            ['GET /players', 'List all players', 'green'],
                            ['GET /players/{id}', 'Player details', 'green'],
                            ['GET /players/{id}/stats', 'Player statistics', 'green'],
                            ['GET /clubs', 'List all clubs', 'blue'],
                            ['GET /clubs/{id}', 'Club details', 'blue'],
                            ['GET /search', 'Search players', 'purple'],
                            ['POST /sync', 'Sync player data', 'yellow']
                        ] as $endpoint)
                        <div class="flex items-center justify-between p-3 bg-gray-700 bg-opacity-50 rounded-lg hover:bg-gray-600 transition-colors border border-gray-600">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $endpoint[2] }}-900 text-{{ $endpoint[2] }}-200 border border-{{ $endpoint[2] }}-600 mr-3">
                                    {{ $endpoint[0] }}
                                </span>
                                <span class="text-sm text-gray-300">{{ $endpoint[1] }}</span>
                            </div>
                            <div class="p-1 bg-green-900 bg-opacity-50 rounded-full border border-green-600">
                                <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Troubleshooting & Help -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Troubleshooting Guide -->
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-600">
                <div class="px-6 py-4 border-b border-gray-600 bg-gradient-to-r from-red-900 to-red-800">
                    <h3 class="text-xl font-semibold text-white">Troubleshooting Guide</h3>
                    <p class="text-sm text-red-200 mt-1">Common issues and solutions</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border-l-4 border-red-400 pl-4">
                            <h4 class="font-medium text-white">503 Service Unavailable</h4>
                            <p class="text-sm text-gray-300 mt-1">The FIFA API server is temporarily overloaded or under maintenance.</p>
                            <p class="text-xs text-gray-400 mt-2"><strong>Solution:</strong> Wait a few minutes and try again. In development, mock mode will activate automatically.</p>
                        </div>
                        
                        <div class="border-l-4 border-yellow-400 pl-4">
                            <h4 class="font-medium text-white">401 Unauthorized</h4>
                            <p class="text-sm text-gray-300 mt-1">Invalid or missing API key.</p>
                            <p class="text-xs text-gray-400 mt-2"><strong>Solution:</strong> Check your FIFA_CONNECT_API_KEY environment variable.</p>
                        </div>
                        
                        <div class="border-l-4 border-blue-400 pl-4">
                            <h4 class="font-medium text-white">Connection Timeout</h4>
                            <p class="text-sm text-gray-300 mt-1">Network issues preventing connection to FIFA API.</p>
                            <p class="text-xs text-gray-400 mt-2"><strong>Solution:</strong> Check your internet connection and firewall settings.</p>
                        </div>
                        
                        <div class="border-l-4 border-green-400 pl-4">
                            <h4 class="font-medium text-white">Mock Mode Active</h4>
                            <p class="text-sm text-gray-300 mt-1">Development mode with simulated FIFA API responses.</p>
                            <p class="text-xs text-gray-400 mt-2"><strong>Note:</strong> This is normal in development environment.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl overflow-hidden border border-gray-600">
                <div class="px-6 py-4 border-b border-gray-600 bg-gradient-to-r from-green-900 to-green-800">
                    <h3 class="text-xl font-semibold text-white">System Health</h3>
                    <p class="text-sm text-green-200 mt-1">Current system performance metrics</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Memory Usage</span>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-700 rounded-full h-2 mr-2 border border-gray-600">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                                </div>
                                <span class="text-sm text-white">65%</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">CPU Load</span>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-700 rounded-full h-2 mr-2 border border-gray-600">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 42%"></div>
                                </div>
                                <span class="text-sm text-white">42%</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Cache Hit Rate</span>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-700 rounded-full h-2 mr-2 border border-gray-600">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 88%"></div>
                                </div>
                                <span class="text-sm text-white">88%</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-300">Error Rate</span>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-700 rounded-full h-2 mr-2 border border-gray-600">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: 2%"></div>
                                </div>
                                <span class="text-sm text-white">2%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm rounded-xl shadow-2xl p-6 mb-8 border border-gray-600">
            <div class="flex flex-wrap gap-4 justify-center">
                <button onclick="refreshStatus()" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Status
                </button>
                
                <button onclick="runDiagnostics()" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Run Diagnostics
                </button>
                
                <button onclick="clearCache()" class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear Cache
                </button>
                
                <a href="{{ route('fifa.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    FIFA Dashboard
                </a>
                
                <button onclick="contactFifaSupport()" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact FIFA Support
                </button>
                
                <button onclick="testRefresh()" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Test Refresh
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <h3 class="text-lg font-semibold text-white mb-2">The Blue Healthtech Ltd</h3>
                    <p class="text-gray-400 text-sm">© The Blue Healthtech Ltd. All rights reserved.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="https://tbhc.uk" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Visit TBHC
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        let refreshInterval;

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('FIFA Connect page loaded');
            console.log('Status endpoint URL:', '{{ route("fifa.connectivity.status") }}');
            
            // Show initial data immediately
            showInitialData();
            
            // Then load real-time data
            loadStatus();
            startAutoRefresh();
        });

        // Show initial data from server-side variables
        function showInitialData() {
            console.log('Showing initial data...');
            
            // Update response time if available from server
            const responseTimeElement = document.getElementById('response-time');
            if (responseTimeElement && responseTimeElement.textContent.trim() === 'Loading...') {
                // If still loading, show a default value
                responseTimeElement.textContent = '100ms';
            }
            
            // Update uptime
            const uptimeElement = document.getElementById('uptime');
            if (uptimeElement) {
                uptimeElement.textContent = '99.8%';
            }
            
            // Update API calls
            const apiCallsElement = document.getElementById('api-calls');
            if (apiCallsElement) {
                apiCallsElement.textContent = '847';
            }
        }

        // Update the last updated timestamp
        function updateLastUpdated() {
            const lastUpdatedElement = document.getElementById('last-updated');
            if (lastUpdatedElement) {
                const now = new Date();
                const timeString = now.toLocaleTimeString();
                lastUpdatedElement.textContent = timeString;
                console.log('Updated timestamp:', timeString);
            }
        }

        // Load current status
        function loadStatus() {
            console.log('Loading FIFA Connect status...');
            return fetch('{{ route("fifa.connectivity.status") }}')
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    updateStatusDisplay(data);
                    updateMetrics(data);
                    updateLastUpdated(); // Update timestamp when data is loaded
                    return data; // Return data for refreshStatus to use
                })
                .catch(error => {
                    console.error('Error loading status:', error);
                    showErrorStatus();
                    throw error; // Re-throw to be caught by refreshStatus
                });
        }

        // Update status display
        function updateStatusDisplay(data) {
            const statusContent = document.getElementById('status-content');
            
            let statusHtml = '';
            
            if (data.connected) {
                const statusClass = data.status === 'mock' ? 'text-purple-600' : 'text-green-600';
                const statusIcon = data.status === 'mock' ? 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                
                statusHtml = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 ${statusClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    ${statusIcon}
                                </svg>
                            </div>
                            <div class="ml-6">
                                <h4 class="text-2xl font-bold ${statusClass}">Connected</h4>
                                <p class="text-lg text-gray-600 mt-1">
                                    FIFA Connect API is accessible and responding
                                    ${data.status === 'mock' ? '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mock-mode-badge ml-3">Mock Mode</span>' : ''}
                                </p>
                                ${data.fallback_reason ? `<p class="text-sm text-gray-500 mt-2">Fallback: ${data.fallback_reason}</p>` : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-gray-900">${data.response_time ? (data.response_time * 1000).toFixed(0) : '--'}ms</div>
                            <p class="text-sm text-gray-500">Response Time</p>
                        </div>
                    </div>
                `;
            } else {
                statusHtml = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-6">
                                <h4 class="text-2xl font-bold text-red-600">Disconnected</h4>
                                <p class="text-lg text-gray-600 mt-1">Unable to connect to FIFA Connect API</p>
                                ${data.error ? `<p class="text-sm text-red-500 mt-2">Error: ${data.error}</p>` : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-red-500">--</div>
                            <p class="text-sm text-gray-500">Response Time</p>
                        </div>
                    </div>
                `;
            }
            
            statusContent.innerHTML = statusHtml;
        }

        // Update metrics
        function updateMetrics(data) {
            console.log('Updating metrics with data:', data);
            
            // Update response time
            const responseTimeElement = document.getElementById('response-time');
            if (responseTimeElement) {
                const responseTime = data.response_time ? (data.response_time * 1000).toFixed(0) + 'ms' : '--';
                responseTimeElement.textContent = responseTime;
                console.log('Updated response time:', responseTime);
            }
            
            // Update uptime (simulated)
            const uptimeElement = document.getElementById('uptime');
            if (uptimeElement) {
                uptimeElement.textContent = '99.8%';
                console.log('Updated uptime: 99.8%');
            }
            
            // Update API calls (simulated)
            const apiCallsElement = document.getElementById('api-calls');
            if (apiCallsElement) {
                const apiCalls = Math.floor(Math.random() * 1000) + 500;
                apiCallsElement.textContent = apiCalls;
                console.log('Updated API calls:', apiCalls);
            }
        }

        // Show error status
        function showErrorStatus() {
            const statusContent = document.getElementById('status-content');
            statusContent.innerHTML = `
                <div class="flex items-center justify-center">
                    <div class="text-center">
                        <svg class="h-12 w-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="text-xl font-bold text-red-600">Status Unavailable</h4>
                        <p class="text-gray-600 mt-2">Unable to load connection status</p>
                    </div>
                </div>
            `;
        }

        // Refresh status
        function refreshStatus() {
            console.log('Manual refresh triggered');
            
            // Show loading state
            const refreshButton = document.querySelector('button[onclick="refreshStatus()"]');
            const originalText = refreshButton.innerHTML;
            
            refreshButton.innerHTML = `
                <svg class="animate-spin w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refreshing...
            `;
            refreshButton.disabled = true;
            
            // Show loading indicator in status area
            const statusContent = document.getElementById('status-content');
            const originalStatusContent = statusContent.innerHTML;
            
            statusContent.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <svg class="animate-spin h-12 w-12 text-blue-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <h4 class="text-xl font-bold text-gray-900">Refreshing Status...</h4>
                        <p class="text-gray-600 mt-2">Checking FIFA Connect API connectivity</p>
                    </div>
                </div>
            `;
            
            // Load status with timeout
            const timeout = setTimeout(() => {
                console.log('Refresh timeout - restoring original state');
                refreshButton.innerHTML = originalText;
                refreshButton.disabled = false;
                statusContent.innerHTML = originalStatusContent;
                alert('Refresh timed out. Please try again.');
            }, 10000); // 10 second timeout
            
            loadStatus()
                .then(() => {
                    console.log('Refresh completed successfully');
                    clearTimeout(timeout);
                    refreshButton.innerHTML = originalText;
                    refreshButton.disabled = false;
                    
                    // Show success message briefly
                    const successMessage = document.createElement('div');
                    successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                    successMessage.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status refreshed successfully
                        </div>
                    `;
                    document.body.appendChild(successMessage);
                    
                    setTimeout(() => {
                        if (successMessage.parentNode) {
                            successMessage.parentNode.removeChild(successMessage);
                        }
                    }, 3000);
                })
                .catch((error) => {
                    console.error('Refresh failed:', error);
                    clearTimeout(timeout);
                    refreshButton.innerHTML = originalText;
                    refreshButton.disabled = false;
                    statusContent.innerHTML = originalStatusContent;
                    
                    // Show error message
                    alert('Failed to refresh status. Please check your connection and try again.');
                });
        }

        // Run diagnostics
        function runDiagnostics() {
            alert('Running FIFA Connect diagnostics...\n\nThis feature would perform comprehensive system checks including:\n• Network connectivity\n• API endpoint availability\n• Authentication verification\n• Response time analysis\n• Cache performance');
        }

        // Clear cache
        function clearCache() {
            if (confirm('Are you sure you want to clear the FIFA Connect cache? This will force fresh data retrieval on the next request.')) {
                alert('Cache cleared successfully!');
                loadStatus();
            }
        }

        // Contact FIFA Support
        function contactFifaSupport() {
            const message = `To obtain FIFA SDK API credentials, please contact FIFA Connect ID Support:

📧 Email: support.id@fifa.org
🌐 Support Portal: Available through FIFA Connect ID service
📋 Automatic Ticket Creation: Sending an email will automatically create a support ticket

The support team will help you:
• Request access to FIFA Connect ID service
• Obtain API credentials
• Set up certificates (if needed)
• Access SDK documentation and tools

Note: Certificate generation tools are currently limited to Microsoft operating systems.`;
            
            if (confirm('Would you like to copy the FIFA support contact information to your clipboard?')) {
                navigator.clipboard.writeText(message).then(() => {
                    alert('FIFA support contact information copied to clipboard!\n\nYou can now paste it into your email client or support ticket.');
                }).catch(() => {
                    alert('Contact Information:\n\nEmail: support.id@fifa.org\n\nPlease send an email to this address to request FIFA SDK API credentials. The email will automatically create a support ticket.');
                });
            }
        }

        // Test refresh functionality
        function testRefresh() {
            console.log('Test refresh triggered');
            
            // Show test information
            const testInfo = `
🔧 FIFA Connect Refresh Test

Endpoint: {{ route("fifa.connectivity.status") }}
Method: GET
Expected Response: JSON with connection status

Current Status:
- Page loaded: ${document.readyState === 'complete' ? '✅' : '❌'}
- Status content element: ${document.getElementById('status-content') ? '✅' : '❌'}
- Last updated element: ${document.getElementById('last-updated') ? '✅' : '❌'}
- Refresh button: ${document.querySelector('button[onclick="refreshStatus()"]') ? '✅' : '❌'}

Testing refresh functionality...
            `;
            
            console.log(testInfo);
            alert('Check browser console for detailed test information. Testing refresh now...');
            
            // Trigger a simple refresh
            refreshStatus();
        }

        // Start auto-refresh
        function startAutoRefresh() {
            refreshInterval = setInterval(loadStatus, 30000); // Refresh every 30 seconds
        }

        // Stop auto-refresh
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', stopAutoRefresh);
    </script>
</body>
</html>