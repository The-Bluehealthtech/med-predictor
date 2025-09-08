<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test FIFA Simple</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">Test FIFA Simple</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Test HTML Statique</h2>
            <p class="text-green-600">✅ Si vous voyez ce texte, HTML fonctionne</p>
            <div class="bg-blue-100 p-4 rounded mt-4">
                <p class="font-bold">Zone de test statique</p>
                <p>Cette zone doit être visible immédiatement</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Test CSS Tailwind</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-red-500 text-white p-4 rounded text-center">
                    <p class="font-bold">Rouge</p>
                </div>
                <div class="bg-green-500 text-white p-4 rounded text-center">
                    <p class="font-bold">Vert</p>
                </div>
                <div class="bg-blue-500 text-white p-4 rounded text-center">
                    <p class="font-bold">Bleu</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Test de Navigation</h2>
            <div class="flex space-x-4">
                <button class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                    Bouton 1
                </button>
                <button class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                    Bouton 2
                </button>
                <button class="bg-purple-500 text-white px-6 py-2 rounded hover:bg-purple-600">
                    Bouton 3
                </button>
            </div>
        </div>
    </div>
</body>
</html>
