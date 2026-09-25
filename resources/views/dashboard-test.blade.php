<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Dashboard FIT</h1>
            <a href="{{ route('modules.index') }}" class="text-blue-700 font-semibold hover:underline">Modules</a>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-6 py-10">
        <h2 class="text-3xl font-bold mb-2">Vue d’ensemble</h2>
        <p class="text-gray-600 mb-8">Enregistrements accessibles à votre compte, comptés dans la base FIT.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($stats as $label => $count)
                <section class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-sm font-medium text-gray-600">{{ $label }}</h3>
                    <p class="text-3xl font-semibold mt-2">{{ number_format($count, 0, ',', ' ') }}</p>
                </section>
            @endforeach
        </div>
    </main>
</body>
</html>
