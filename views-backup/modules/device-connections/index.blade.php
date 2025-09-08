@extends('layouts.app')

@section('title', 'Device Connections - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-blue-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🔗</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Device Connections
                                </h1>
                                <p class="text-sm text-gray-600">Gestion des connexions d'appareils et données IoT</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">🔗 Device Connections</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Gestion des connexions d'appareils et données IoT
                    </p>
                    <div class="flex justify-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Système opérationnel
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            IoT connecté
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Connection Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Connected Devices</p>
                        <p class="text-2xl font-bold text-gray-900">Gérer</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Data Sync</p>
                        <p class="text-2xl font-bold text-gray-900">Synchroniser</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Analytics</p>
                        <p class="text-2xl font-bold text-gray-900">Analyser</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Management -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Connected Devices -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Connected Devices</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <div>
                                <p class="font-medium text-green-900">Fitness Tracker</p>
                                <p class="text-sm text-green-700">Connected - Last sync: 2 min ago</p>
                            </div>
                        </div>
                        <span class="text-green-600">Online</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <div>
                                <p class="font-medium text-blue-900">Heart Rate Monitor</p>
                                <p class="text-sm text-blue-700">Connected - Last sync: 5 min ago</p>
                            </div>
                        </div>
                        <span class="text-blue-600">Online</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                            <div>
                                <p class="font-medium text-yellow-900">GPS Tracker</p>
                                <p class="text-sm text-yellow-700">Connected - Last sync: 10 min ago</p>
                            </div>
                        </div>
                        <span class="text-yellow-600">Online</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                            <div>
                                <p class="font-medium text-red-900">Blood Pressure Monitor</p>
                                <p class="text-sm text-red-700">Disconnected - Last sync: 1 hour ago</p>
                            </div>
                        </div>
                        <span class="text-red-600">Offline</span>
                    </div>
                </div>
            </div>

            <!-- Data Sync Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Sync Status</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-green-900">Real-time Sync</p>
                            <p class="text-sm text-green-700">All devices syncing in real-time</p>
                        </div>
                        <span class="text-green-600">✓ Active</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-blue-900">Data Backup</p>
                            <p class="text-sm text-blue-700">Last backup: 2 hours ago</p>
                        </div>
                        <span class="text-blue-600">✓ Complete</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-purple-900">API Integration</p>
                            <p class="text-sm text-purple-700">FIFA Connect API active</p>
                        </div>
                        <span class="text-purple-600">✓ Connected</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="font-medium text-yellow-900">Data Processing</p>
                            <p class="text-sm text-yellow-700">Processing 1,247 data points</p>
                        </div>
                        <span class="text-yellow-600">⏳ Processing</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Analytics -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Device Analytics</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">12</div>
                    <div class="text-sm text-gray-600">Total Devices</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">9</div>
                    <div class="text-sm text-gray-600">Online Devices</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">2.4GB</div>
                    <div class="text-sm text-gray-600">Data Transferred</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">99.2%</div>
                    <div class="text-sm text-gray-600">Uptime</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔗 Add Device
                </a>
                <a href="#" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔄 Sync Data
                </a>
                <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📊 Analytics
                </a>
                <a href="#" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ⚙️ Settings
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 