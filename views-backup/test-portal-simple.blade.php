<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Portail Simple</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Test Portail Simple</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Test avec objet simple -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🎯 Test avec Objet Simple</h3>
                
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        // Objet simple sans méthodes
                        $simpleAssoc = (object)[
                            'name' => 'Maroc',
                            'country' => 'Maroc'
                        ];
                    @endphp
                    
                    <x-association-logo 
                        :association="$simpleAssoc"
                        size="2xl"
                        :show-fallback="true"
                        class="w-full h-full"
                    />
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                
                <div class="mt-4 space-y-2 text-sm">
                    <p><strong>Association:</strong> {{ $simpleAssoc->name }}</p>
                    <p><strong>Pays:</strong> {{ $simpleAssoc->country }}</p>
                </div>
            </div>
            
            <!-- Test avec objet qui a des méthodes -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🔧 Test avec Objet + Méthodes</h3>
                
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        // Créer un objet avec des méthodes
                        $assocWithMethods = new class {
                            public $name = 'Maroc';
                            public $country = 'Maroc';
                            
                            public function getCountryCode() {
                                return 'MA';
                            }
                            
                            public function hasNationalLogo() {
                                return true;
                            }
                            
                            public function getLogoUrl() {
                                return 'http://localhost:8000/associations/MA.png';
                            }
                            
                            public function getLogoAlt() {
                                return 'Maroc Association Logo';
                            }
                        };
                    @endphp
                    
                    <x-association-logo 
                        :association="$assocWithMethods"
                        size="2xl"
                        :show-fallback="true"
                        class="w-full h-full"
                    />
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                
                <div class="mt-4 space-y-2 text-sm">
                    <p><strong>Association:</strong> {{ $assocWithMethods->name }}</p>
                    <p><strong>Pays:</strong> {{ $assocWithMethods->country }}</p>
                    <p><strong>Code pays:</strong> {{ $assocWithMethods->getCountryCode() }}</p>
                    <p><strong>Logo national:</strong> {{ $assocWithMethods->hasNationalLogo() ? 'OUI' : 'NON' }}</p>
                    <p><strong>URL logo:</strong> {{ $assocWithMethods->getLogoUrl() }}</p>
                </div>
            </div>
        </div>
        
        <!-- Debug des composants -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-md">
            <h3 class="text-lg font-semibold mb-4">🔍 Debug des Composants</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold mb-2">Test du composant association-logo :</h4>
                    @php
                        try {
                            $testAssoc = (object)['name' => 'Test', 'country' => 'Maroc'];
                            $view = view('components.association-logo', ['association' => $testAssoc, 'size' => 'md']);
                            echo '<div class="text-sm text-green-600">✅ Composant fonctionne</div>';
                            echo '<div class="text-sm">Longueur: ' . strlen($view->render()) . ' caractères</div>';
                        } catch (Exception $e) {
                            echo '<div class="text-sm text-red-600">❌ Erreur: ' . $e->getMessage() . '</div>';
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

