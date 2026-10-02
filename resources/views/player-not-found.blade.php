<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Joueur introuvable') }} - FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div style="position:fixed;top:16px;right:16px">@include('partials.logout-button', ['variant' => 'dark'])</div>
    <p class="bg-white/10 rounded-lg p-4 border border-white/20 text-gray-300">{{ __('Joueur introuvable') }}</p>
</body>
</html>
