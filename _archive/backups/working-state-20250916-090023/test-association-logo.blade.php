<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Association Logo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Test du Composant Association Logo</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Test avec différentes tailles -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">📏 Différentes Tailles</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-16">XS:</span>
                        <x-association-logo size="xs" />
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-16">SM:</span>
                        <x-association-logo size="sm" />
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-16">MD:</span>
                        <x-association-logo size="md" />
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-16">LG:</span>
                        <x-association-logo size="lg" />
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-16">XL:</span>
                        <x-association-logo size="xl" />
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-16">2XL:</span>
                        <x-association-logo size="2xl" />
                    </div>
                </div>
            </div>
            
            <!-- Test avec différents pays -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🌍 Différents Pays</h3>
                <div class="space-y-4">
                    @php
                        $testCountries = [
                            'FR' => 'France',
                            'GB-ENG' => 'Angleterre',
                            'DE' => 'Allemagne',
                            'IT' => 'Italie',
                            'ES' => 'Espagne',
                            'MA' => 'Maroc',
                            'TN' => 'Tunisie'
                        ];
                    @endphp
                    
                    @foreach($testCountries as $code => $name)
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600 w-20">{{ $name }}:</span>
                            <x-association-logo 
                                :association="(object)['country' => $code, 'name' => $name]"
                                size="md"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Test des fonctionnalités -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">⚙️ Fonctionnalités</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-24">Avec fallback:</span>
                        <x-association-logo :show-fallback="true" />
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-24">Sans fallback:</span>
                        <x-association-logo :show-fallback="false" />
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-600 w-24">Custom class:</span>
                        <x-association-logo class="border-2 border-blue-500" />
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informations sur les logos disponibles -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-md">
            <h3 class="text-lg font-semibold mb-4">📊 Logos Disponibles</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @php
                    $logoFiles = glob(public_path('associations/*.png'));
                    $availableLogos = array_map(function($file) {
                        return basename($file, '.png');
                    }, $logoFiles);
                    $availableLogos = array_slice($availableLogos, 0, 24); // Limiter à 24 pour l'affichage
                @endphp
                
                @foreach($availableLogos as $code)
                    <div class="text-center p-2 bg-gray-50 rounded">
                        <div class="text-xs text-gray-600 mb-1">{{ $code }}</div>
                        <x-association-logo 
                            :association="(object)['country' => $code, 'name' => $code]"
                            size="sm"
                        />
                    </div>
                @endforeach
            </div>
            <p class="text-sm text-gray-600 mt-4">
                Total des logos disponibles : {{ count(glob(public_path('associations/*.png'))) }}
            </p>
        </div>
    </div>
</body>
</html>

