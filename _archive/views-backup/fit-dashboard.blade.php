@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 py-12 px-6 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                {{ __('dashboard.welcome') }}
            </h1>
            <p class="text-xl md:text-2xl text-blue-200">
                {{ __('dashboard.football_intelligence_tracking') }}
            </p>
            <div class="mt-6">
                <img src="{{ asset('images/logos/fit.png') }}" alt="FIT Logo" class="h-12 mx-auto">
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Medical Module -->
            <a href="/health-records" class="block">
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-6 h-full border border-gray-100 hover:border-blue-200 group">
                    <div class="flex flex-col items-center text-center h-full">
                        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            🏥
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                            {{ __('dashboard.medical_module') }}
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed flex-grow">
                            {{ __('dashboard.medical_module_desc') }}
                        </p>
                        <div class="mt-4 text-blue-600 group-hover:text-blue-700 transition-colors duration-300">
                            <span class="text-sm font-medium">{{ __('dashboard.access_module') }} →</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Licensing Module -->
            <a href="/licenses" class="block">
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-6 h-full border border-gray-100 hover:border-blue-200 group">
                    <div class="flex flex-col items-center text-center h-full">
                        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            📋
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                            {{ __('dashboard.licensing_module') }}
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed flex-grow">
                            {{ __('dashboard.licensing_module_desc') }}
                        </p>
                        <div class="mt-4 text-blue-600 group-hover:text-blue-700 transition-colors duration-300">
                            <span class="text-sm font-medium">{{ __('dashboard.access_module') }} →</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Competitions -->
            <a href="/competitions" class="block">
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-6 h-full border border-gray-100 hover:border-blue-200 group">
                    <div class="flex flex-col items-center text-center h-full">
                        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            🏆
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                            {{ __('dashboard.competitions') }}
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed flex-grow">
                            {{ __('dashboard.competitions_desc') }}
                        </p>
                        <div class="mt-4 text-blue-600 group-hover:text-blue-700 transition-colors duration-300">
                            <span class="text-sm font-medium">{{ __('dashboard.access_module') }} →</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Analytics & Reports -->
            <a href="/performances" class="block">
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-6 h-full border border-gray-100 hover:border-blue-200 group">
                    <div class="flex flex-col items-center text-center h-full">
                        <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">
                            📊
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                            {{ __('dashboard.analytics_reports') }}
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed flex-grow">
                            {{ __('dashboard.analytics_reports_desc') }}
                        </p>
                        <div class="mt-4 text-blue-600 group-hover:text-blue-700 transition-colors duration-300">
                            <span class="text-sm font-medium">{{ __('dashboard.access_module') }} →</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12">
            <p class="text-blue-200 text-sm">
                {{ $footballType ? __("dashboard.football_type", ["type" => $footballType]) : __('dashboard.football_type_default') }} • {{ __('dashboard.powered_by') }}
            </p>
        </div>
    </div>
</div>
@endsection 