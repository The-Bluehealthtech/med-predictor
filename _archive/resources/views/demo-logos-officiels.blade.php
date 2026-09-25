<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Démonstration Logos Officiels des Fédérations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto p-6">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">🏆 Logos Officiels des Fédérations</h1>
            <p class="text-xl text-gray-600">Démonstration des vrais logos des fédérations nationales de football</p>
        </div>

        <!-- Logos téléchargés avec succès -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-green-600">✅ Logos Téléchargés avec Succès</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Logo FRMF (Maroc) -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center">
                    <div class="w-32 h-32 mx-auto mb-4 bg-white rounded-lg p-3 shadow-md">
                        <img src="/associations/MA.png" 
                             alt="Logo FRMF - Fédération Royale Marocaine de Football"
                             class="w-full h-full object-contain"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-400" style="display: none;">
                            🏆
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">🇲🇦 Maroc</h3>
                    <p class="text-sm text-gray-600 mb-2">Fédération Royale Marocaine de Football</p>
                    <p class="text-xs text-green-600 font-medium">FRMF</p>
                    <div class="mt-3 text-xs text-gray-500">
                        <p>Taille: 10.9 KB</p>
                        <p>Format: PNG</p>
                    </div>
                </div>

                <!-- Logo FTF (Tunisie) -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-6 text-center">
                    <div class="w-32 h-32 mx-auto mb-4 bg-white rounded-lg p-3 shadow-md">
                        <img src="/associations/TN.png" 
                             alt="Logo FTF - Fédération Tunisienne de Football"
                             class="w-full h-full object-contain"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-400" style="display: none;">
                            🏆
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">🇹🇳 Tunisie</h3>
                    <p class="text-sm text-gray-600 mb-2">Fédération Tunisienne de Football</p>
                    <p class="text-xs text-red-600 font-medium">FTF</p>
                    <div class="mt-3 text-xs text-gray-500">
                        <p>Taille: 250.5 KB</p>
                        <p>Format: PNG</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Test du composant association-logo-working -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-6 text-blue-600">🧪 Test du Composant Association Logo</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Test avec association Maroc -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold mb-4">🇲🇦 Test Association Maroc</h3>
                    
                    <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group mx-auto">
                        @php
                            $assocMaroc = (object)[
                                'name' => 'Fédération Royale Marocaine de Football',
                                'country' => 'Maroc'
                            ];
                        @endphp
                        
                        <x-association-logo-working 
                            :association="$assocMaroc"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                        
                        <!-- Bouton Gérer qui apparaît au survol -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <span class="bg-indigo-600 text-white px-3 py-1 rounded text-sm font-medium">
                                🏆 FRMF
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-sm text-gray-600">
                        <p><strong>Pays:</strong> {{ $assocMaroc->country }}</p>
                        <p><strong>Fédération:</strong> {{ $assocMaroc->name }}</p>
                    </div>
                </div>

                <!-- Test avec association Tunisie -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold mb-4">🇹🇳 Test Association Tunisie</h3>
                    
                    <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group mx-auto">
                        @php
                            $assocTunisie = (object)[
                                'name' => 'Fédération Tunisienne de Football',
                                'country' => 'Tunisie'
                            ];
                        @endphp
                        
                        <x-association-logo-working 
                            :association="$assocTunisie"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                        
                        <!-- Bouton Gérer qui apparaît au survol -->
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <span class="bg-indigo-600 text-white px-3 py-1 rounded text-sm font-medium">
                                🏆 FTF
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-sm text-gray-600">
                        <p><strong>Pays:</strong> {{ $assocTunisie->country }}</p>
                        <p><strong>Fédération:</strong> {{ $assocTunisie->name }}</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Informations techniques -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-6 text-gray-700">🔍 Informations Techniques</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3">📁 Fichiers Téléchargés</h3>
                    <div class="space-y-2 text-sm">
                        @php
                            $logoPath = public_path('associations');
                            if (is_dir($logoPath)) {
                                $files = scandir($logoPath);
                                $pngFiles = array_filter($files, function($file) {
                                    return pathinfo($file, PATHINFO_EXTENSION) === 'png';
                                });
                                
                                echo '<div class="text-green-600 font-medium">Total logos: ' . count($pngFiles) . '</div>';
                                
                                // Afficher les logos officiels en premier
                                $officialLogos = ['MA', 'TN'];
                                foreach($officialLogos as $code) {
                                    $filePath = public_path("associations/{$code}.png");
                                    if (file_exists($filePath)) {
                                        $size = number_format(filesize($filePath)/1024, 1);
                                        echo '<div class="text-sm text-gray-700">• <strong>' . $code . '.png</strong> (' . $size . ' KB) - Logo officiel</div>';
                                    }
                                }
                                
                                // Afficher quelques autres logos
                                $otherLogos = array_slice(array_diff($pngFiles, $officialLogos), 0, 5);
                                foreach($otherLogos as $file) {
                                    $filePath = public_path("associations/{$file}");
                                    $size = number_format(filesize($filePath)/1024, 1);
                                    echo '<div class="text-sm text-gray-500">• ' . $file . ' (' . $size . ' KB)</div>';
                                }
                            }
                        @endphp
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-3">🌐 URLs Sources</h3>
                    <div class="space-y-2 text-sm">
                        <div>
                            <strong>FRMF (Maroc):</strong>
                            <div class="text-blue-600 break-all">
                                https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQV-2baljpCz0zRD0FWSoAWhnxVNg0DlIsQtQ&s
                            </div>
                        </div>
                        <div>
                            <strong>FTF (Tunisie):</strong>
                            <div class="text-blue-600 break-all">
                                https://upload.wikimedia.org/wikipedia/fr/thumb/3/33/Logo_federation_tunisienne_de_football.svg/1200px-Logo_federation_tunisienne_de_football.svg.png
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton de retour -->
        <div class="text-center mt-8">
            <a href="/test-association-system" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                🔙 Retour au Test Système
            </a>
        </div>
    </div>
</body>
</html>
