<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login_page_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Styles pour les messages de notification -->
    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 min-h-screen flex items-center justify-center">
    <!-- Messages de session -->
    @if(session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 animate-slide-in">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session('message'))
        <div class="fixed top-4 right-4 z-50 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 animate-slide-in">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-12 h-12 mr-3">
                <div>
                    <h1 class="text-3xl font-bold text-white">FIT</h1>
                    <p class="text-sm text-blue-200 -mt-1">Football Intelligence & Tracking</p>
                </div>
            </div>
            <p class="text-blue-200">{{ __('auth.login_secure') }}</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 mb-6">
                <ul class="text-red-200 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="user_type" class="block text-sm font-medium text-blue-200 mb-2">
                    {{ __('auth.login_user_type') }}
                </label>
                <select name="user_type" id="user_type" required 
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">{{ __('auth.login_select_placeholder') }}</option>
                    <option value="player">{{ __('auth.role_player') }}</option>
                    <option value="referee">{{ __('auth.role_referee') }}</option>
                    <option value="club_admin">{{ __('auth.role_club_admin') }}</option>
                    <option value="club_manager">{{ __('auth.role_club_manager') }}</option>
                    <option value="club_medical">{{ __('auth.role_club_medical') }}</option>
                    <option value="association_admin">{{ __('auth.role_association_admin') }}</option>
                    <option value="association_registrar">{{ __('auth.role_association_registrar') }}</option>
                    <option value="association_medical">{{ __('auth.role_association_medical') }}</option>
                    <option value="system_admin">{{ __('auth.role_system_admin') }}</option>
                </select>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-blue-200 mb-2">
                    Email
                </label>
                <input type="email" name="email" id="email" required 
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="{{ __('auth.login_email_placeholder') }}">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-blue-200 mb-2">
                    Mot de passe
                </label>
                <input type="password" name="password" id="password" required 
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="••••••••">
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200 transform hover:scale-105">
                {{ __('auth.sign_in') }}
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-blue-200 text-sm">
                <strong>{{ __('auth.role_summary_players_label') }}</strong> {{ __('auth.role_summary_players_desc') }}<br>
                <strong>{{ __('auth.role_summary_referees_label') }}</strong> {{ __('auth.role_summary_referees_desc') }}<br>
                <strong>{{ __('auth.role_summary_clubs_label') }}</strong> {{ __('auth.role_summary_clubs_desc') }}<br>
                <strong>{{ __('auth.role_summary_associations_label') }}</strong> {{ __('auth.role_summary_associations_desc') }}<br>
                <strong>{{ __('auth.role_summary_system_label') }}</strong> {{ __('auth.role_summary_system_desc') }}
            </p>
        </div>

        <div class="mt-6 text-center space-y-2">
            <a href="/" class="text-blue-300 hover:text-blue-200 text-sm underline block">
                {{ __('auth.login_back_home') }}
            </a>
            <div class="flex justify-center space-x-4">
                <a href="/joueur/7" class="text-blue-300 hover:text-blue-200 text-sm underline">
                    {{ __('auth.login_demo_player_access') }}
                </a>
                <a href="/referee-dashboard-working" class="text-blue-300 hover:text-blue-200 text-sm underline">
                    {{ __('auth.login_referee_portal_link') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.style.opacity = '0';
            form.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                form.style.transition = 'all 0.6s ease-out';
                form.style.opacity = '1';
                form.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>

    </script>
</body>
</html>
