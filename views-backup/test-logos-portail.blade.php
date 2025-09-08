<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Logos Portail Patient</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto p-6">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">🏥 Test Logos Portail Patient</h1>
            <p class="text-xl text-gray-600">Test des logos des fédérations dans le contexte du portail</p>
        </div>

        <!-- Simulation du portail patient -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-blue-600">🎯 Section Informations de l'Association</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Logo de l'association (copie exacte du portail) -->
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group mx-auto">
                    @php
                        $player = (object)[
                            'id' => 7,
                            'association' => (object)[
                                'id' => 7,
                                'name' => 'Fédération Royale Marocaine de Football',
                                'country' => 'Maroc'
                            ]
                        ];
                    @endphp
                    
                    @if($player->association)
                        <x-association-logo-working 
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
                        <a href="{{ route('associations.edit-logo', $player->association->id) }}" 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                
                <!-- Drapeau pays de l'association (copie exacte du portail) -->
                <div class="w-40 h-40 bg-orange-100 rounded-lg p-3 flex items-center justify-center relative group mx-auto">
                    @if($player->association && $player->association->country)
                        @php
                            $associationCountryCode = \App\Helpers\CountryCodeHelper::getCountryCode($player->association->country);
                        @endphp
                        @if($associationCountryCode)
                            <x-country-flag 
                                :countryCode="$associationCountryCode" 
                                :countryName="$player->association->country"
                                size="2xl"
                                format="svg"
                                class="w-full h-full"
                            />
                        @else
                            <div class="text-4xl text-gray-400">🏴</div>
                        @endif
                    @endif
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏴 Gérer
                        </a>
                    </div>
                </div>
                
                <!-- Informations de l'association -->
                <div class="w-40 h-40 bg-blue-100 rounded-lg p-3 flex items-center justify-center relative group mx-auto">
                    <div class="text-center">
                        <div class="text-2xl mb-2">📋</div>
                        <div class="text-sm font-medium text-gray-800">{{ $player->association->name }}</div>
                        <div class="text-xs text-gray-600 mt-1">{{ $player->association->country }}</div>
                    </div>
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            📋 Gérer
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    <strong>Joueur ID:</strong> {{ $player->id }} | 
                    <strong>Association:</strong> {{ $player->association->name }} | 
                    <strong>Pays:</strong> {{ $player->association->country }}
                </p>
            </div>
        </div>

        <!-- Test des composants individuels -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-green-600">🧪 Test des Composants Individuels</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Test association-logo-working -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold mb-4">🏆 Test Association Logo Working</h3>
                    
                    <div class="w-32 h-32 bg-gray-100 rounded-lg p-3 flex items-center justify-center mx-auto">
                        <x-association-logo-working 
                            :association="$player->association"
                            size="xl"
                            :show-fallback="true"
                        />
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-600">
                        <p><strong>Composant:</strong> x-association-logo-working</p>
                        <p><strong>Taille:</strong> xl</p>
                        <p><strong>Fallback:</strong> Activé</p>
                    </div>
                </div>
                
                <!-- Test country-flag -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold mb-4">🏴 Test Country Flag</h3>
                    
                    <div class="w-32 h-32 bg-gray-100 rounded-lg p-3 flex items-center justify-center mx-auto">
                        @if($player->association && $player->association->country)
                            @php
                                $countryCode = \App\Helpers\CountryCodeHelper::getCountryCode($player->association->country);
                            @endphp
                            @if($countryCode)
                                <x-country-flag 
                                    :countryCode="$countryCode" 
                                    :countryName="$player->association->country"
                                    size="xl"
                                    format="svg"
                                />
                            @else
                                <div class="text-4xl text-gray-400">🏴</div>
                            @endif
                        @endif
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-600">
                        <p><strong>Composant:</strong> x-country-flag</p>
                        <p><strong>Code pays:</strong> {{ $countryCode ?? 'Non défini' }}</p>
                        <p><strong>Format:</strong> SVG</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations techniques -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-6 text-gray-700">🔍 Informations Techniques</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3">📁 Logos Disponibles</h3>
                    <div class="space-y-2 text-sm">
                        @php
                            $logoPath = public_path('associations');
                            if (is_dir($logoPath)) {
                                $files = scandir($logoPath);
                                $pngFiles = array_filter($files, function($file) {
                                    return pathinfo($file, PATHINFO_EXTENSION) === 'png';
                                });
                                
                                echo '<div class="text-green-600 font-medium">Total logos: ' . count($pngFiles) . '</div>';
                                
                                // Logos officiels
                                $officialLogos = ['MA', 'TN'];
                                foreach($officialLogos as $code) {
                                    $filePath = public_path("associations/{$code}.png");
                                    if (file_exists($filePath)) {
                                        $size = number_format(filesize($filePath)/1024, 1);
                                        echo '<div class="text-sm text-gray-700">• <strong>' . $code . '.png</strong> (' . $size . ' KB) - Logo officiel</div>';
                                    }
                                }
                            }
                        @endphp
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-3">🔗 Routes Disponibles</h3>
                    <div class="space-y-2 text-sm">
                        <div>
                            <a href="{{ route('associations.edit-logo', $player->association->id) }}" 
                               class="text-blue-600 hover:text-blue-800 underline">
                                📝 Éditer le logo de l'association
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('demo.logos.officiels') }}" 
                               class="text-blue-600 hover:text-blue-800 underline">
                                🏆 Démonstration des logos officiels
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('test.association.system') }}" 
                               class="text-blue-600 hover:text-blue-800 underline">
                                🧪 Test du système association
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

