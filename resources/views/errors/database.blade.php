<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('errors.database_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto text-center">
        <div class="bg-red-600/20 border border-red-500/50 rounded-lg p-8">
            <div class="text-6xl mb-4">⚠️</div>
            <h1 class="text-2xl font-bold mb-4">{{ __('errors.database_title') }}</h1>
            <p class="text-gray-300 mb-6">{{ $message ?? __('errors.database_default_message') }}</p>
            <div class="space-y-3">
                <a href="{{ route('modules.index') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors">
                    {{ __('errors.back_to_modules') }}
                </a>
                <a href="{{ url()->previous() }}" class="block w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">
                    {{ __('errors.previous_page') }}
                </a>
            </div>
        </div>
    </div>
</body>
</html>
