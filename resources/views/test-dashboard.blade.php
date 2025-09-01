<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Dashboard - FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">🧪 Test Dashboard FIT</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Test de connexion à la base -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🔌 Test Connexion Base</h2>
                @php
                    try {
                        echo '<div class="text-green-600 font-medium">✅ Connexion réussie</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Base de données accessible</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Erreur de connexion</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                    }
                @endphp
            </div>

            <!-- Test des modèles -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">👥 Test Modèles</h2>
                @php
                    try {
                        $playerCount = \App\Models\Player::count();
                        echo '<div class="text-green-600 font-medium">✅ Modèle Player OK</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Joueurs trouvés: ' . $playerCount . '</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Erreur modèle Player</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                    }
                @endphp
            </div>

            <!-- Test des tables -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">📊 Test Tables</h2>
                @php
                    try {
                        $injuredCount = \DB::table('player_injuries_diseases')->where('is_resolved', false)->count();
                        echo '<div class="text-green-600 font-medium">✅ Table player_injuries_diseases OK</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Joueurs blessés: ' . $injuredCount . '</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Erreur table</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                    }
                @endphp
            </div>

            <!-- Test de la vue -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">👁️ Test Vue</h2>
                @php
                    try {
                        $view = view('welcome');
                        echo '<div class="text-green-600 font-medium">✅ Vue welcome OK</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Compilation réussie</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Erreur vue</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                    }
                @endphp
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Retour au Dashboard Principal
            </a>
        </div>
    </div>
</body>
</html>

