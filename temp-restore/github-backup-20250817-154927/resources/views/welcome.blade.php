<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('/home') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Home</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <svg viewBox="0 0 62 55" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-16 w-auto bg-gray-100 dark:bg-gray-900">
                    <path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7976 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9101 61.7558 29.0605L56.9827 37.5488L49.2852 49.3955C49.1871 49.5639 48.9883 49.6568 48.7814 49.6568H13.2186C13.0117 49.6568 12.8129 49.5639 12.7148 49.3955L5.01734 37.5488L0.244263 29.0605C0.156646 28.9101 0.110352 28.737 0.110352 28.5615V14.8858C0.110352 14.7976 0.122117 14.7102 0.145146 14.6253L2.41176 7.875C2.47168 7.65234 2.65723 7.47266 2.89063 7.40234L30.1094 0.179688C30.2188 0.144531 30.3359 0.144531 30.4453 0.179688L57.6641 7.40234C57.8975 7.47266 58.083 7.65234 58.1429 7.875L60.4095 14.6253H61.8548ZM59.8897 16.5858V27.5615L55.5176 35.5488L48.0859 46.6568H13.9141L6.48242 35.5488L2.11035 27.5615V16.5858L4.03711 10.0858L28.4453 3.17969H31.5547L55.9629 10.0858L59.8897 16.5858Z" fill="#FF2D20"/>
                    <path d="M30.5 5.65625L7.25 12.6562L30.5 19.6562L53.75 12.6562L30.5 5.65625Z" fill="#FF2D20"/>
                    <path d="M7.25 12.6562V32.6562L30.5 39.6562V19.6562L7.25 12.6562Z" fill="#FF2D20"/>
                    <path d="M53.75 12.6562V32.6562L30.5 39.6562V19.6562L53.75 12.6562Z" fill="#FF2D20"/>
                    <path d="M30.5 39.6562L53.75 32.6562V12.6562L30.5 19.6562V39.6562Z" fill="#FF2D20"/>
                    <path d="M7.25 32.6562L30.5 39.6562V19.6562L7.25 12.6562V32.6562Z" fill="#FF2D20"/>
                </svg>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                    <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Documentation</h2>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel has wonderful documentation covering every aspect of the framework. Whether you're new to the framework or have previous experience, we recommend reading our documentation from beginning to end.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel takes the pain out of development by easing common tasks used in many web projects, such as authentication, routing, sessions, and caching.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Laravel</h2>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel takes the pain out of development by easing common tasks used in many web projects, such as authentication, routing, sessions, and caching.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel has wonderful documentation covering every aspect of the framework. Whether you're new to the framework or have previous experience, we recommend reading our documentation from beginning to end.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Vibrant Ecosystem</h2>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel's robust library of first-party tools and libraries, such as <a href="https://forge.laravel.com" class="underline hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:outline-red-500">Forge</a>, <a href="https://vapor.laravel.com" class="underline hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:outline-red-500">Vapor</a>, <a href="https://nova.laravel.com" class="underline hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:outline-red-500">Nova</a>, and <a href="https://envoyer.io" class="underline hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:outline-red-500">Envoyer</a> help you take your projects to the next level. Pair them with powerful open source packages like <a href="https://github.com/laravel/telescope" class="underline hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:outline-red-500">Telescope</a>, <a href="https://github.com/laravel/horizon" class="underline hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:outline-red-500">Horizon</a>, and <a href="https://github.com/laravel/sanctum" class="underline hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:outline-red-500">Sanctum</a> to discover all they have to offer.
                            </p>
                        </div>
                    </div>

                    <div class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">A Next Generation Experience</h2>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel takes the pain out of development by easing common tasks used in many web projects, such as authentication, routing, sessions, and caching.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.
                            </p>

                            <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                Laravel has wonderful documentation covering every aspect of the framework. Whether you're new to the framework or have previous experience, we recommend reading our documentation from beginning to end.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-start">
                    <div class="flex items-center gap-4">
                        <a href="https://github.com/sponsors/taylorotwell" class="group inline-flex items-center gap-x-2 focus:outline focus:outline-2 focus:outline-red-500 focus:outline-offset-2">
                            <svg class="w-5 h-5 transition group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                            </svg>

                            <span>Sponsor</span>
                        </a>

                        <div class="border-l border-gray-300 dark:border-gray-700"></div>

                        <a href="https://github.com/laravel/laravel" class="group inline-flex items-center gap-x-2 focus:outline focus:outline-2 focus:outline-red-500 focus:outline-offset-2">
                            <svg class="w-5 h-5 transition group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                            </svg>

                            <span>Laravel</span>
                        </a>
                    </div>
                </div>

                <div class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>
            </div>
        </div>
    </div>
</body>
</html>
