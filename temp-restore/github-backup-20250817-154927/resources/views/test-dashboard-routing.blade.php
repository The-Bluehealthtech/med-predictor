@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Dashboard Routing Test</h1>
                
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
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Test Links:</h2>
                        <div class="space-y-2">
                            <a href="{{ route('back-office.dashboard') }}" class="block text-blue-600 hover:text-blue-800">Back Office Dashboard</a>
                            <a href="{{ route('club-management.dashboard') }}" class="block text-blue-600 hover:text-blue-800">Club Management Dashboard</a>
                            <a href="{{ route('player-dashboard.index') }}" class="block text-blue-600 hover:text-blue-800">Player Dashboard</a>
                            <a href="{{ route('referee.dashboard') }}" class="block text-blue-600 hover:text-blue-800">Referee Dashboard</a>
                            <a href="{{ route('dashboard') }}" class="block text-blue-600 hover:text-blue-800">General Dashboard</a>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Role-Based Dashboard Mapping:</h2>
                        <div class="bg-gray-100 p-4 rounded">
                            <ul class="space-y-1 text-sm">
                                <li><strong>system_admin</strong> → back-office.dashboard</li>
                                <li><strong>association_admin</strong> → back-office.dashboard</li>
                                <li><strong>association_registrar</strong> → back-office.dashboard</li>
                                <li><strong>association_medical</strong> → back-office.dashboard</li>
                                <li><strong>club_admin</strong> → club-management.dashboard</li>
                                <li><strong>club_manager</strong> → club-management.dashboard</li>
                                <li><strong>club_medical</strong> → club-management.dashboard</li>
                                <li><strong>player</strong> → player-dashboard.index</li>
                                <li><strong>referee</strong> → referee.dashboard</li>
                                <li><strong>admin</strong> → dashboard</li>
                                <li><strong>default/other</strong> → dashboard</li>
                            </ul>
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