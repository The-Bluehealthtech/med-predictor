<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Ultra Simple</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Test Ultra Simple</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Test 1: Composant simple -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">Test 1: Composant Simple</h3>
                <x-test-logo size="md" />
                <p class="mt-2 text-sm text-gray-600">Si vous voyez un carré bleu avec 🏆, le composant fonctionne</p>
            </div>
            
            <!-- Test 2: Composant association-logo sans association -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">Test 2: Association Logo Simple</h3>
                <x-association-logo-simple size="md" />
                <p class="mt-2 text-sm text-gray-600">Si vous voyez un carré vert avec 🏆, le composant fonctionne</p>
            </div>
            
            <!-- Test 3: Composant association-logo avec objet simple -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">Test 3: Association Logo Simple (objet)</h3>
                @php
                    $simpleAssoc = (object)[
                        'name' => 'Test',
                        'country' => 'France'
                    ];
                @endphp
                <x-association-logo-simple :association="$simpleAssoc" size="md" />
                <p class="mt-2 text-sm text-gray-600">Test avec un objet simple (sans méthodes)</p>
            </div>
            
            <!-- Test 4: Affichage direct des données -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">Test 4: Affichage Direct</h3>
                @php
                    $testData = [
                        'name' => 'Test Association',
                        'country' => 'France',
                        'code' => 'FR'
                    ];
                @endphp
                <div class="space-y-2 text-sm">
                    <p><strong>Nom:</strong> {{ $testData['name'] }}</p>
                    <p><strong>Pays:</strong> {{ $testData['country'] }}</p>
                    <p><strong>Code:</strong> {{ $testData['code'] }}</p>
                </div>
            </div>
        </div>
        
        <!-- Debug des fichiers -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-md">
            <h3 class="text-lg font-semibold mb-4">🔍 Debug des Fichiers</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold mb-2">Fichiers de composants :</h4>
                    @php
                        $componentFiles = [
                            'components/test-logo.blade.php' => file_exists(resource_path('views/components/test-logo.blade.php')),
                            'components/association-logo.blade.php' => file_exists(resource_path('views/components/association-logo.blade.php'))
                        ];
                        
                        foreach($componentFiles as $file => $exists) {
                            $status = $exists ? '✅' : '❌';
                            echo '<div class="text-sm">' . $status . ' ' . $file . '</div>';
                        }
                    @endphp
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Logos disponibles :</h4>
                    @php
                        try {
                            $logoPath = public_path('associations');
                            if (is_dir($logoPath)) {
                                $files = scandir($logoPath);
                                $pngFiles = array_filter($files, function($file) {
                                    return pathinfo($file, PATHINFO_EXTENSION) === 'png';
                                });
                                echo '<div class="text-sm text-green-600">✅ Répertoire existe</div>';
                                echo '<div class="text-sm">Total logos: ' . count($pngFiles) . '</div>';
                                if (count($pngFiles) > 0) {
                                    $sample = array_slice($pngFiles, 0, 5);
                                    foreach($sample as $file) {
                                        echo '<div class="text-xs text-gray-600">• ' . $file . '</div>';
                                    }
                                }
                            } else {
                                echo '<div class="text-sm text-red-600">❌ Répertoire n\'existe pas</div>';
                            }
                        } catch (Exception $e) {
                            echo '<div class="text-sm text-red-600">❌ Erreur: ' . $e->getMessage() . '</div>';
                        }
                    @endphp
                </div>
            </div>
        </div>
    </div>
</body>
</html>
