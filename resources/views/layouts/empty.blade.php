<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'FIT'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/fit-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fit-logo.png') }}">
    @yield('styles')
</head>
<body>
    @yield('content')
    @yield('scripts')
</body>
</html>
