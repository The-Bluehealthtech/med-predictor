<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Sécurité Frontend -->
    <meta name="user-role" content="{{ auth()->user()?->role ?? '' }}">
    <meta name="user-id" content="{{ auth()->user()?->id ?? '' }}">
    <meta name="user-club-id" content="{{ auth()->user()?->club_id ?? '' }}">
    <meta name="security-version" content="v1.0">
    
    <!-- Headers de sécurité -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">

    <title>{{ $title ?? 'Back Office - Med Predictor' }}</title>

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
    
    <!-- Sécurité Frontend -->
    <!-- <script type="module" src="{{ asset('js/security-init.js') }}"></script> -->
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <img src="{{ asset('images/logos/fit.png') }}" alt="FIT Logo" style="height:40px;width:auto;" class="inline-block align-middle mb-2">
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('back-office.dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('back-office.dashboard') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Dashboard
                            </a>
                            <a href="{{ route('back-office.seasons.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('back-office.seasons.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Seasons
                            </a>
                            <a href="{{ route('back-office.content.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('back-office.content.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Content
                            </a>
                            <a href="{{ route('back-office.system-status') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('back-office.system-status') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                System Status
                            </a>
                            <a href="{{ route('back-office.settings') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('back-office.settings*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Settings
                            </a>
                            <a href="{{ route('back-office.logs') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('back-office.logs*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                Logs
                            </a>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="relative">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-16">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        © 2024 Med Predictor. Tous droits réservés.
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>Back Office v1.0</span>
                        <span>FIFA Connect ID v3.3</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <div id="notifications"></div>
</body>
</html> 