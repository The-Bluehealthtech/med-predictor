cette ver@extends('layouts.app')

@section('title', 'Devices Portal - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-10 h-10 mr-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Devices Portal
                                </h1>
                                <p class="text-sm text-gray-600">Portail de gestion des appareils connectés</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/modules" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux modules</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Devices Portal</h2>
                <p class="text-lg text-gray-600 mb-6">
                    Portail de gestion des appareils connectés - Module en cours de développement
                </p>
                <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-6">
                    <p class="text-cyan-800">
                        Cette fonctionnalité sera bientôt disponible.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection