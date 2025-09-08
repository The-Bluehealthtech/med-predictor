@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Dashboard Button Debug</h1>
                
                @auth
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Current User:</h2>
                        <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                        <p><strong>Role:</strong> {{ auth()->user()->role }}</p>
                    </div>

                    @php
                        $dashboardRoute = match(auth()->user()->role) {
                            'system_admin' => 'back-office.dashboard',
                            'association_admin' => 'back-office.dashboard',
                            'association_registrar' => 'back-office.dashboard',
                            'association_medical' => 'back-office.dashboard',
                            'club_admin' => 'club-management.dashboard',
                            'club_manager' => 'club-management.dashboard',
                            'club_medical' => 'club-management.dashboard',
                            'player' => 'player-dashboard.index',
                            'referee' => 'referee.dashboard',
                            'admin' => 'dashboard',
                            default => 'dashboard'
                        };
                        
                        $isDashboardPage = request()->routeIs([
                            'dashboard',
                            'back-office.dashboard',
                            'club-management.dashboard',
                            'player-dashboard.index',
                            'referee.dashboard',
                            'healthcare.dashboard',
                            'medical-predictions.dashboard',
                            'player-registration.dashboard',
                            'user-management.dashboard'
                        ]);
                    @endphp

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Dashboard Routing:</h2>
                        <p><strong>Calculated Route:</strong> {{ $dashboardRoute }}</p>
                        <p><strong>Route URL:</strong> {{ route($dashboardRoute) }}</p>
                        <p><strong>Is Dashboard Page:</strong> {{ $isDashboardPage ? 'Yes' : 'No' }}</p>
                        <p><strong>Current Route Name:</strong> {{ request()->route()->getName() ?? 'N/A' }}</p>
                        <p><strong>Current URL:</strong> {{ request()->url() }}</p>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Button Display Logic:</h2>
                        <p><strong>Authenticated:</strong> {{ auth()->check() ? 'Yes' : 'No' }}</p>
                        <p><strong>Is Dashboard Page:</strong> {{ $isDashboardPage ? 'Yes' : 'No' }}</p>
                        <p><strong>Button Should Show:</strong> {{ auth()->check() && !$isDashboardPage ? 'Yes' : 'No' }}</p>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Manual Button Test:</h2>
                        @if(auth()->check() && !$isDashboardPage)
                            <div class="mb-4">
                                <a href="{{ route($dashboardRoute) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Back to Dashboard (Manual Test)
                                </a>
                            </div>
                            <p class="text-green-600">✅ Button should be visible above</p>
                        @else
                            <p class="text-red-600">❌ Button should NOT be visible (on dashboard page or not authenticated)</p>
                        @endif
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Route Testing:</h2>
                        <div class="space-y-2">
                            @foreach(['dashboard', 'back-office.dashboard', 'club-management.dashboard', 'player-dashboard.index', 'referee.dashboard'] as $routeName)
                                @php
                                    $isCurrentRoute = request()->routeIs($routeName);
                                @endphp
                                <p class="{{ $isCurrentRoute ? 'text-green-600 font-bold' : 'text-gray-600' }}">
                                    {{ $routeName }}: {{ $isCurrentRoute ? '✅ CURRENT' : '❌ Not current' }}
                                </p>
                            @endforeach
                        </div>
                    </div>

                @else
                    <p>Please log in to test dashboard routing.</p>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection 