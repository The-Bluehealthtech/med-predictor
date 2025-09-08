<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Test Clubs Réels - Base de Données</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                🧪 Test des Clubs Réels de la Base de Données
            </h1>
            <p class="text-lg text-gray-600">
                Test avec les vrais noms des clubs trouvés dans la base
            </p>
        </div>

        <!-- Test avec les vrais noms des clubs -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">🏟️ Clubs de la Base de Données</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $clubsReels = [
                        ['name' => 'Espérance de Tunis', 'expected' => 'EST'],
                        ['name' => 'Étoile du Sahel', 'expected' => 'ESS'],
                        ['name' => 'Club Africain', 'expected' => 'CA'],
                        ['name' => 'CS Sfaxien', 'expected' => 'CSS'],
                        ['name' => 'CA Bizertin', 'expected' => 'CAB'],
                        ['name' => 'Stade Tunisien', 'expected' => 'ST'],
                        ['name' => 'US Monastir', 'expected' => 'USM'],
                        ['name' => 'Olympique Béja', 'expected' => 'OB'],
                        ['name' => 'AS Gabès', 'expected' => 'ASG'],
                        ['name' => 'JS Kairouan', 'expected' => 'JSO']
                    ];
                @endphp
                
                @foreach($clubsReels as $club)
                    <div class="bg-gray-50 rounded-lg p-6 text-center border-2 border-gray-200">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $club['name'] }}</h3>
                        <p class="text-sm text-gray-600 mb-3">Attendu: <strong>{{ $club['expected'] }}</strong></p>
                        
                        <div class="w-24 h-24 mx-auto mb-4 bg-white rounded-lg p-2 flex items-center justify-center">
                            <x-club-logo-working 
                                :club="(object)['name' => $club['name']]"
                                class="w-full h-full"
                            />
                        </div>
                        
                        <!-- Vérification du fichier -->
                        @php
                            $webpPath = public_path("clubs/{$club['expected']}.webp");
                            $hasLogo = file_exists($webpPath);
                            $fileSize = $hasLogo ? filesize($webpPath) : 0;
                            $fileSizeKB = $fileSize > 0 ? round($fileSize / 1024, 1) : 0;
                        @endphp
                        
                        <div class="text-xs">
                            @if($hasLogo)
                                <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                    ✅ Logo {{ $club['expected'] }} ({{ $fileSizeKB }} KB)
                                </span>
                            @else
                                <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                    ❌ Logo {{ $club['expected'] }} manquant
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Informations techniques -->
        <div class="bg-blue-50 rounded-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-800 mb-4">📊 Informations Techniques</h2>
            <div class="text-blue-700 space-y-2">
                <p><strong>✅ Mapping mis à jour :</strong> Noms exacts de la base de données</p>
                <p><strong>✅ Logos téléchargés :</strong> 16 logos WebP depuis worldsoccerpins.com</p>
                <p><strong>✅ Composant :</strong> <code>x-club-logo-working</code> avec fallback</p>
                <p><strong>✅ Base de données :</strong> 11 clubs trouvés</p>
            </div>
        </div>

        <!-- Résumé des correspondances -->
        <div class="bg-green-50 rounded-xl p-6">
            <h2 class="text-xl font-bold text-green-800 mb-4">🎯 Correspondances Club → Logo</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-green-700">
                <div>
                    <h3 class="font-semibold mb-2">✅ Correspondances exactes :</h3>
                    <ul class="space-y-1 text-sm">
                        <li>• Espérance de Tunis → EST.webp</li>
                        <li>• Étoile du Sahel → ESS.webp</li>
                        <li>• Club Africain → CA.webp</li>
                        <li>• CS Sfaxien → CSS.webp</li>
                        <li>• CA Bizertin → CAB.webp</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-2">✅ Autres correspondances :</h3>
                    <ul class="space-y-1 text-sm">
                        <li>• Stade Tunisien → ST.webp</li>
                        <li>• US Monastir → USM.webp</li>
                        <li>• Olympique Béja → OB.webp</li>
                        <li>• AS Gabès → ASG.webp</li>
                        <li>• JS Kairouan → JSO.webp</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center mt-8">
            <a href="/demo-vrais-logos-ftf" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors mr-4">
                🏆 Voir tous les logos
            </a>
            <a href="/test-portail-club-logos-simple" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                🧪 Tester le portail
            </a>
        </div>
    </div>
</body>
</html>

