<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Med Predictor')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Scripts -->
    @php
        $manifest = file_exists(public_path('build/manifest.json')) ? json_decode(file_get_contents(public_path('build/manifest.json')), true) : [];
        $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
        $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
    @endphp
    @if($cssFile)
        <link rel="stylesheet" href="{{ asset('build/' . ltrim($cssFile, '/')) }}">
    @endif
    @if($jsFile)
        <script type="module" src="{{ asset('build/' . ltrim($jsFile, '/')) }}"></script>
    @endif
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left mb-4 md:mb-0">
                        <h3 class="text-lg font-semibold text-white mb-2">The Blue Healthtech Ltd</h3>
                        <p class="text-gray-400 text-sm">© The Blue Healthtech Ltd. All rights reserved.</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="https://tbhc.uk" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Visit TBHC
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
