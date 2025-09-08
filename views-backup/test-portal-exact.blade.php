<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Portail Exact - Association Logo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Test Portail Exact - Association Logo</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Test exact du portail patient -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🎯 Test Exact du Portail</h3>
                
                <!-- Copie exacte de la section du portail -->
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        // Simuler exactement les données du portail
                        $player = (object)[
                            'id' => 7,
                            'association' => (object)[
                                'id' => 1,
                                'name' => 'Association Test',
                                'country' => 'France',
                                'association_logo_url' => null
                            ]
                        ];
                    @endphp
                    
                    @if($player->association)
                        <x-association-logo 
                            :association="$player->association"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    @else
                        <div class="text-4xl text-gray-400">🏆</div>
                    @endif
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                
                <div class="mt-4 space-y-2 text-sm">
                    <p><strong>Joueur ID:</strong> {{ $player->id }}</p>
                    <p><strong>Association:</strong> {{ $player->association->name }}</p>
                    <p><strong>Pays:</strong> {{ $player->association->country }}</p>
                    <p><strong>Code pays:</strong> 
                        @php
                            try {
                                echo $player->association->getCountryCode();
                            } catch (Exception $e) {
                                echo 'ERREUR: ' . $e->getMessage();
                            }
                        @endphp
                    </p>
                    <p><strong>Logo national:</strong> 
                        @php
                            try {
                                echo $player->association->hasNationalLogo() ? 'OUI' : 'NON';
                            } catch (Exception $e) {
                                echo 'ERREUR: ' . $e->getMessage();
                            }
                        @endphp
                    </p>
                    <p><strong>URL logo:</strong> 
                        @php
                            try {
                                echo $player->association->getLogoUrl();
                            } catch (Exception $e) {
                                echo 'ERREUR: ' . $e->getMessage();
                            }
                        @endphp
                    </p>
                </div>
            </div>
            
            <!-- Test avec vraie association de la base -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🗄️ Test avec Vraie Association</h3>
                
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        try {
                            $realAssoc = App\Models\Association::find(7); // Maroc
                            if ($realAssoc) {
                                $player = (object)['association' => $realAssoc];
                            } else {
                                $player = null;
                            }
                        } catch (Exception $e) {
                            $player = null;
                            $error = $e->getMessage();
                        }
                    @endphp
                    
                    @if($player && $player->association)
                        <x-association-logo 
                            :association="$player->association"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    @else
                        <div class="text-4xl text-gray-400">🏆</div>
                    @endif
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                
                <div class="mt-4 space-y-2 text-sm">
                    @if(isset($error))
                        <p class="text-red-600"><strong>Erreur:</strong> {{ $error }}</p>
                    @elseif($player && $player->association)
                        <p><strong>Association:</strong> {{ $player->association->name }}</p>
                        <p><strong>Pays:</strong> {{ $player->association->country }}</p>
                        <p><strong>Code pays:</strong> {{ $player->association->getCountryCode() }}</p>
                        <p><strong>Logo national:</strong> {{ $player->association->hasNationalLogo() ? 'OUI' : 'NON' }}</p>
                        <p><strong>URL logo:</strong> {{ $player->association->getLogoUrl() }}</p>
                    @else
                        <p class="text-gray-500">Aucune association trouvée</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Debug des logos disponibles -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-md">
            <h3 class="text-lg font-semibold mb-4">🔍 Debug des Logos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold mb-2">Logos dans /public/associations/ :</h4>
                    @php
                        try {
                            $logoFiles = glob(public_path('associations/*.png'));
                            if ($logoFiles) {
                                $availableLogos = array_map(function($file) {
                                    return basename($file, '.png');
                                }, $logoFiles);
                                $availableLogos = array_slice($availableLogos, 0, 15);
                                foreach($availableLogos as $code) {
                                    echo '<div class="text-sm text-gray-600">• ' . $code . '.png</div>';
                                }
                                echo '<p class="text-xs text-gray-500 mt-1">Total: ' . count($logoFiles) . ' logos</p>';
                            } else {
                                echo '<p class="text-red-600">❌ Aucun logo trouvé dans /public/associations/</p>';
                            }
                        } catch (Exception $e) {
                            echo '<p class="text-red-600">❌ Erreur: ' . $e->getMessage() . '</p>';
                        }
                    @endphp
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Test du Helper :</h4>
                    @php
                        try {
                            use App\Helpers\CountryCodeHelper;
                            echo '<p class="text-sm text-gray-600">France → ' . CountryCodeHelper::getCountryCode('France') . '</p>';
                            echo '<p class="text-sm text-gray-600">Maroc → ' . CountryCodeHelper::getCountryCode('Maroc') . '</p>';
                            echo '<p class="text-sm text-gray-600">Tunisie → ' . CountryCodeHelper::getCountryCode('Tunisie') . '</p>';
                        } catch (Exception $e) {
                            echo '<p class="text-red-600">❌ Erreur Helper: ' . $e->getMessage() . '</p>';
                        }
                    @endphp
                </div>
            </div>
        </div>
    </div>
</body>
</html>

