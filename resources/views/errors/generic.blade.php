<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('errors.generic_page_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 text-white">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="glass-effect rounded-2xl p-8 max-w-md w-full text-center">
            <!-- Icône d'erreur -->
            <div class="text-6xl mb-6">⚠️</div>
            
            <!-- Titre de l'erreur -->
            <h1 class="text-2xl font-bold text-red-400 mb-4">
                {{ $error ?? __('errors.generic_default_error') }}
            </h1>
            
            <!-- Message détaillé -->
            @if(isset($message))
                <p class="text-gray-300 mb-6 text-sm">
                    {{ $message }}
                </p>
            @endif
            
            <!-- Boutons d'action -->
            <div class="space-y-3">
                <a href="{{ url()->previous() }}" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    {{ __('errors.generic_back') }}
                </a>
                
                <a href="{{ route('admin.dashboard') }}" 
                   class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    {{ __('errors.generic_admin_dashboard') }}
                </a>
                
                <a href="{{ route('login') }}" 
                   class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    {{ __('errors.generic_login') }}
                </a>
            </div>
            
            <!-- Footer -->
            <div class="mt-8 text-center">
                <div class="text-2xl mb-2">⚽</div>
                <p class="text-gray-400 text-sm">{{ __('errors.generic_footer') }}</p>
            </div>
        </div>
    </div>
</body>
</html>








