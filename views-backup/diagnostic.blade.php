<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic FIT - Identification du Problème</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">🔍 Diagnostic FIT - Identification du Problème</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Test 1: Connexion de base -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🔌 Test 1: Connexion de base</h2>
                @php
                    try {
                        echo '<div class="text-green-600 font-medium">✅ Connexion réussie</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Application accessible</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Erreur de connexion</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                    }
                @endphp
            </div>

            <!-- Test 2: Base de données -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🗄️ Test 2: Base de données</h2>
                @php
                    try {
                        $pdo = \DB::connection()->getPdo();
                        echo '<div class="text-green-600 font-medium">✅ Base accessible</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Connexion MySQL OK</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Base inaccessible</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                    }
                @endphp
            </div>

            <!-- Test 3: Modèles -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">👥 Test 3: Modèles</h2>
                @php
                    try {
                        $playerCount = \App\Models\Player::count();
                        echo '<div class="text-green-600 font-medium">✅ Modèle Player OK</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Joueurs: ' . $playerCount . '</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Erreur modèle</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                    }
                @endphp
            </div>

            <!-- Test 4: Vue welcome -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">👁️ Test 4: Vue welcome</h2>
                @php
                    try {
                        $view = view('welcome');
                        echo '<div class="text-green-600 font-medium">✅ Vue compilée</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Compilation OK</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Erreur vue</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                    }
                @endphp
            </div>

            <!-- Test 5: Rendu de la vue -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🎨 Test 5: Rendu de la vue</h2>
                @php
                    try {
                        $view = view('welcome');
                        $content = $view->render();
                        echo '<div class="text-green-600 font-medium">✅ Vue rendue</div>';
                        echo '<div class="text-sm text-gray-600 mt-2">Longueur: ' . strlen($content) . ' caractères</div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600 font-medium">❌ Erreur rendu</div>';
                        echo '<div class="text-sm text-red-500 mt-2">' . $e->getMessage() . '</div>';
                        echo '<div class="text-xs text-red-400 mt-1">Fichier: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
                    }
                @endphp
            </div>

            <!-- Test 6: Variables d'environnement -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">⚙️ Test 6: Configuration</h2>
                @php
                    echo '<div class="text-sm text-gray-600">';
                    echo 'DB_HOST: ' . config('database.connections.mysql.host') . '<br>';
                    echo 'DB_PORT: ' . config('database.connections.mysql.port') . '<br>';
                    echo 'DB_DATABASE: ' . config('database.connections.mysql.database') . '<br>';
                    echo 'APP_ENV: ' . config('app.env') . '<br>';
                    echo 'APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false') . '<br>';
                    echo '</div>';
                @endphp
            </div>
        </div>

        <div class="mt-8 text-center space-x-4">
            <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Retour au Dashboard Principal
            </a>
            <a href="/test-dashboard" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Test Dashboard
            </a>
        </div>
    </div>
</body>
</html>




