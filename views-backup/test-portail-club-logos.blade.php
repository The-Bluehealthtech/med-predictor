<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Test Portail Joueur - Club Logos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                🧪 Test du Portail Joueur avec Club Logos
            </h1>
            <p class="text-lg text-gray-600">
                Test de l'intégration du composant <code>x-club-logo-working</code>
            </p>
        </div>

        <!-- Simulation du portail joueur -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">🏟️ Section Logos (Portail Joueur)</h2>
            
            <!-- COLONNE DROITE : Logo club + Logo association + Drapeau pays -->
            <div class="flex gap-3">
                <!-- Logo du club (w-40 h-40) -->
                <div class="w-40 h-40 bg-purple-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        $club = (object) [
                            'name' => 'Esperance Sportive de Tunis',
                            'code' => 'EST'
                        ];
                    @endphp
                    
                    <x-club-logo-working 
                        :club="$club"
                        class="w-full h-full"
                    />
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" 
                           class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏟️ Gérer
                        </a>
                    </div>
                </div>
                
                <!-- Logo de l'association (w-40 h-40) -->
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        $association = (object) [
                            'id' => 1,
                            'name' => 'Fédération Tunisienne de Football',
                            'country' => 'Tunisie'
                        ];
                    @endphp
                    
                    <x-association-logo-working 
                        :association="$association"
                        size="2xl"
                        :show-fallback="true"
                        class="w-full h-full"
                    />
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                
                <!-- Drapeau pays de l'association (w-40 h-40) -->
                <div class="w-40 h-40 bg-orange-100 rounded-lg p-3 flex items-center justify-center relative group">
                    <div class="text-center">
                        <div class="text-4xl mb-2">🇹🇳</div>
                        <div class="text-xs text-gray-500">Tunisie</div>
                    </div>
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" 
                           class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏁 Gérer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test avec différents clubs -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">🏆 Test avec différents clubs FTF</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $clubs = [
                        ['name' => 'Esperance Sportive de Tunis', 'code' => 'EST'],
                        ['name' => 'Etoile Sportive du Sahel', 'code' => 'ESS'],
                        ['name' => 'Club Africain', 'code' => 'CA'],
                        ['name' => 'CS Sfaxien', 'code' => 'CSS'],
                        ['name' => 'CA Bizertin', 'code' => 'CAB'],
                        ['name' => 'Stade Tunisien', 'code' => 'ST'],
                        ['name' => 'US Monastirienne', 'code' => 'USM'],
                        ['name' => 'US Ben Guerdane', 'code' => 'USBG']
                    ];
                @endphp
                
                @foreach($clubs as $club)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="w-20 h-20 mx-auto mb-3 bg-white rounded-lg p-2 flex items-center justify-center">
                            <x-club-logo-working 
                                :club="(object)$club"
                                class="w-full h-full"
                            />
                        </div>
                        <h3 class="font-bold text-sm text-gray-800 mb-1">{{ $club['code'] }}</h3>
                        <p class="text-xs text-gray-600">{{ $club['name'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Informations techniques -->
        <div class="bg-blue-50 rounded-xl p-6">
            <h2 class="text-xl font-bold text-blue-800 mb-4">📊 Informations Techniques</h2>
            <div class="text-blue-700 space-y-2">
                <p><strong>✅ Composant :</strong> <code>x-club-logo-working</code> intégré avec succès</p>
                <p><strong>✅ Logos :</strong> 16 logos WebP téléchargés depuis worldsoccerpins.com</p>
                <p><strong>✅ Format :</strong> WebP (moderne et optimisé)</p>
                <p><strong>✅ Fallback :</strong> Gestion automatique des erreurs</p>
                <p><strong>✅ Hover :</strong> Boutons "Gérer" au survol</p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center mt-8">
            <a href="/demo-vrais-logos-ftf" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors mr-4">
                🏆 Voir tous les logos
            </a>
            <a href="/test-clubs-ftf" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                🧪 Tester le composant
            </a>
        </div>
    </div>
</body>
</html>

