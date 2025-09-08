<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel')) - Back Office</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
    <div class="min-h-screen bg-gray-100">
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- FIT Platform -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="text-2xl font-bold">
                                <span class="text-white">F</span>
                                <span class="bg-blue-600 text-white px-1 relative">
                                    I
                                    <svg class="absolute inset-0 w-full h-full" viewBox="0 0 20 20" fill="none">
                                        <path d="M2 10 L6 6 L10 10 L14 6 L18 10" stroke="white" stroke-width="2" fill="none"/>
                                    </svg>
                                </span>
                                <span class="text-white">T</span>
                            </div>
                            <div class="ml-3 text-sm text-blue-300 font-semibold uppercase tracking-wide">
                                <div>FOOTBALL</div>
                                <div>INTELLIGENCE</div>
                                <div>& TRACKING</div>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm">
                            Smarter Football Through Digital Intelligence
                        </p>
                    </div>

                    <!-- FIFA Connect -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">FIFA Connect Compliant</h3>
                        <p class="text-gray-400 text-sm mb-4">
                            Adhering to FIFA's global standards for digital identity and licensing.
                        </p>
                        <a href="https://www.fifa.com/what-we-do/fifa-connect" target="_blank" class="text-blue-400 hover:text-blue-300 text-sm">
                            Learn more →
                        </a>
                    </div>

                    <!-- Powered By -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Powered By</h3>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <div class="text-center">
                                <h4 class="font-semibold text-white mb-2">The Blue Healthtech Ltd</h4>
                                <p class="text-gray-400 text-sm mb-3">
                                    © The Blue Healthtech Ltd. All rights reserved.
                                </p>
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
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400 text-sm">
                        © 2024 The Blue Healthtech Ltd. All rights reserved. | FIT - Football Intelligence & Tracking
                    </p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html> 