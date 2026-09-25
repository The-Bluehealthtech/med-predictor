<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Profile Selection - FIT - Football Intelligence & Tracking</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <!-- FIT Logo -->
                    <div class="flex items-center">
                        <div class="text-3xl font-bold">
                            <span class="text-gray-800">F</span>
                            <span class="bg-blue-600 text-white px-1 relative">
                                I
                                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 20 20" fill="none">
                                    <path d="M2 10 L6 6 L10 10 L14 6 L18 10" stroke="white" stroke-width="2" fill="none"/>
                                </svg>
                            </span>
                            <span class="text-gray-800">T</span>
                        </div>
                        <div class="ml-3 text-sm text-blue-600 font-semibold uppercase tracking-wide">
                            <div>FOOTBALL</div>
                            <div>INTELLIGENCE</div>
                            <div>& TRACKING</div>
                        </div>
                    </div>

                    <!-- FIFA Connect Badge -->
                    <div class="bg-green-100 border border-green-300 rounded-lg px-3 py-2">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xs font-medium text-green-800">FIFA Connect</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Breadcrumb -->
            <div class="mb-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                Football Type
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Profile Selection</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Page Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Select Your Profile</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Choose the profile that best matches your role in the 
                    <span class="font-semibold text-blue-600" id="footballTypeDisplay">{{ $footballType ?? 'selected' }}</span> 
                    football ecosystem
                </p>
            </div>

            <!-- Profile Cards Grid -->
            <div id="profile-selector-app"></div>

            <!-- Back Button -->
            <div class="text-center mt-12">
                <a href="/" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Football Type Selection
                </a>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-16">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        © 2024 The Blue Healthtech Ltd. All rights reserved.
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Powered by The Blue Healthtech Ltd
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Get football type from URL parameter or session storage
        const urlParams = new URLSearchParams(window.location.search);
        const footballType = urlParams.get('footballType') || sessionStorage.getItem('selectedFootballType');
        
        // Update display
        if (footballType) {
            const display = document.getElementById('footballTypeDisplay');
            const footballTypeNames = {
                '11aside': '11-a-side',
                'womens': 'Women\'s',
                'futsal': 'Futsal',
                'beach': 'Beach Soccer'
            };
            display.textContent = footballTypeNames[footballType] || footballType;
        }
    </script>
</body>
</html> 