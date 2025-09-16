<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Player Portal Admin - FIT Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                    <span class="text-white text-xl font-bold">FIT</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Test Player Portal Admin</h1>
                    <p class="text-gray-600">Vérification de l'accès admin au Player Portal</p>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center mb-4">
                @if($status === 'success')
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-green-800">✅ Test Réussi</h2>
                @else
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-red-800">❌ Test Échoué</h2>
                @endif
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-lg font-medium text-gray-800 mb-2">{{ $message }}</p>
                @if($info)
                    <p class="text-gray-600">{{ $info }}</p>
                @endif
            </div>
        </div>

        <!-- User Information -->
        @if($adminUser)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">👤 Informations Utilisateur</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID</label>
                    <p class="text-gray-900">{{ $adminUser->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <p class="text-gray-900">{{ $adminUser->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="text-gray-900">{{ $adminUser->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rôle</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $adminUser->role }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Redirect Information -->
        @if($redirectTo)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🔄 Redirection</h3>
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-gray-700 mb-2">
                    <strong>Route de redirection :</strong> <code class="bg-gray-200 px-2 py-1 rounded">{{ $redirectTo }}</code>
                </p>
                <p class="text-gray-600">
                    Les administrateurs sont redirigés vers la liste des joueurs pour sélectionner un joueur spécifique et accéder à son portail FIT.
                </p>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🔗 Actions Disponibles</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="/test-player-portal-admin" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">
                    🔄 Recharger le Test
                </a>
                <a href="/modules" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-center">
                    📋 Retour aux Modules
                </a>
                <a href="/test-finance-controller" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-center">
                    💰 Test Finance Controller
                </a>
                <a href="/test-modules-finance" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors text-center">
                    🧪 Test Modules Finance
                </a>
            </div>
        </div>

        <!-- Debug Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🐛 Informations de Debug</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <pre class="text-sm text-gray-700 overflow-x-auto">{{ json_encode([
                    'status' => $status,
                    'message' => $message,
                    'redirect_to' => $redirectTo,
                    'info' => $info,
                    'admin_user' => $adminUser,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
</body>
</html>
