<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FIT - Football Intelligence & Tracking') }} - {{ ucfirst($footballType) }} Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div id="app">
        <dynamic-dashboard 
            :initial-football-type="{{ json_encode($footballType) }}"
        ></dynamic-dashboard>
    </div>

    <script>
        // Pass the football type to the Vue component
        window.initialFootballType = @json($footballType);
    </script>
</body>
</html> 