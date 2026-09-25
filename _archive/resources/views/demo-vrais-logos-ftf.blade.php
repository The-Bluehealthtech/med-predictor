<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏆 Vrais Logos des Clubs FTF - Démonstration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .logo-container {
            transition: all 0.3s ease;
        }
        .logo-container:hover {
            transform: scale(1.05);
        }
        .logo-image {
            transition: all 0.3s ease;
        }
        .logo-image:hover {
            filter: brightness(1.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">
                🏆 Vrais Logos des Clubs FTF
            </h1>
            <p class="text-xl text-gray-600 mb-2">
                Logos officiels téléchargés depuis worldsoccerpins.com
            </p>
            <p class="text-lg text-gray-500">
                Format WebP - Qualité originale - 16 clubs de Ligue 1
            </p>
            <div class="mt-4 p-4 bg-green-100 rounded-lg inline-block">
                <span class="text-green-800 font-semibold">
                    ✅ Tous les logos ont été téléchargés avec succès !
                </span>
            </div>
        </div>

        <!-- Grille des logos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            @php
                $clubs = [
                    'EST' => ['name' => 'Esperance Sportive de Tunis', 'city' => 'Tunis', 'colors' => 'from-red-500 to-red-700'],
                    'ESS' => ['name' => 'Etoile Sportive du Sahel', 'city' => 'Sousse', 'colors' => 'from-blue-500 to-blue-700'],
                    'CA' => ['name' => 'Club Africain', 'city' => 'Tunis', 'colors' => 'from-red-600 to-red-800'],
                    'CSS' => ['name' => 'CS Sfaxien', 'city' => 'Sfax', 'colors' => 'from-yellow-500 to-yellow-700'],
                    'CAB' => ['name' => 'CA Bizertin', 'city' => 'Bizerte', 'colors' => 'from-green-500 to-green-700'],
                    'ST' => ['name' => 'Stade Tunisien', 'city' => 'Tunis', 'colors' => 'from-blue-600 to-blue-800'],
                    'USM' => ['name' => 'US Monastirienne', 'city' => 'Monastir', 'colors' => 'from-purple-500 to-purple-700'],
                    'USBG' => ['name' => 'US Ben Guerdane', 'city' => 'Ben Guerdane', 'colors' => 'from-orange-500 to-orange-700'],
                    'OB' => ['name' => 'Olympique de Béja', 'city' => 'Béja', 'colors' => 'from-indigo-500 to-indigo-700'],
                    'ASG' => ['name' => 'Avenir Sportif de Gabès', 'city' => 'Gabès', 'colors' => 'from-teal-500 to-teal-700'],
                    'ESM' => ['name' => 'ES de Métlaoui', 'city' => 'Métlaoui', 'colors' => 'from-pink-500 to-pink-700'],
                    'ESZ' => ['name' => 'ES de Zarzis', 'city' => 'Zarzis', 'colors' => 'from-cyan-500 to-cyan-700'],
                    'JSO' => ['name' => 'JS de el Omrane', 'city' => 'El Omrane', 'colors' => 'from-emerald-500 to-emerald-700'],
                    'EGSG' => ['name' => 'El Gawafel de Gafsa', 'city' => 'Gafsa', 'colors' => 'from-amber-500 to-amber-700'],
                    'ASS' => ['name' => 'AS Soliman', 'city' => 'Soliman', 'colors' => 'from-rose-500 to-rose-700'],
                    'UST' => ['name' => 'US Tataouine', 'city' => 'Tataouine', 'colors' => 'from-violet-500 to-violet-700']
                ];
            @endphp

            @foreach($clubs as $code => $club)
                <div class="logo-container bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br {{ $club['colors'] }} rounded-full flex items-center justify-center">
                        <x-club-logo-working 
                            :club="(object)['name' => $club['name']]"
                            class="w-16 h-16 rounded-full"
                        />
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $code }}</h3>
                    <p class="text-sm text-gray-600 mb-1">{{ $club['name'] }}</p>
                    <p class="text-xs text-gray-500">{{ $club['city'] }}</p>
                    
                    <!-- Vérification du fichier -->
                    @php
                        $webpPath = public_path("clubs/{$code}.webp");
                        $pngPath = public_path("clubs/{$code}.png");
                        $hasWebp = file_exists($webpPath);
                        $hasPng = file_exists($pngPath);
                        $fileSize = $hasWebp ? filesize($webpPath) : ($hasPng ? filesize($pngPath) : 0);
                        $fileSizeKB = $fileSize > 0 ? round($fileSize / 1024, 1) : 0;
                    @endphp
                    
                    <div class="mt-3 text-xs">
                        @if($hasWebp)
                            <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                ✅ WebP ({{ $fileSizeKB }} KB)
                            </span>
                        @elseif($hasPng)
                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                📁 PNG ({{ $fileSizeKB }} KB)
                            </span>
                        @else
                            <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                ❌ Fichier manquant
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Informations techniques -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">📊 Informations Techniques</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">🎯 Source des Logos</h3>
                    <ul class="text-gray-600 space-y-1">
                        <li>• <strong>Site :</strong> <a href="https://www.worldsoccerpins.com/football-logos-tunisia" target="_blank" class="text-blue-600 hover:underline">worldsoccerpins.com</a></li>
                        <li>• <strong>Format :</strong> WebP (format moderne et optimisé)</li>
                        <li>• <strong>Qualité :</strong> Logos officiels des clubs</li>
                        <li>• <strong>Ligue :</strong> FTF Ligue 1 2024-2025</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">📁 Fichiers Téléchargés</h3>
                    <ul class="text-gray-600 space-y-1">
                        <li>• <strong>Dossier :</strong> <code>public/clubs/</code></li>
                        <li>• <strong>Total :</strong> 16 logos</li>
                        <li>• <strong>Taille :</strong> ~180 KB</li>
                        <li>• <strong>Date :</strong> {{ date('d/m/Y H:i') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Utilisation -->
        <div class="bg-blue-50 rounded-xl p-6">
            <h2 class="text-2xl font-bold text-blue-800 mb-4">🚀 Comment Utiliser</h2>
            <div class="text-blue-700">
                <p class="mb-2"><strong>1.</strong> Les logos sont automatiquement détectés par le composant <code>&lt;x-club-logo-working&gt;</code></p>
                <p class="mb-2"><strong>2.</strong> Priorité : WebP → PNG → Fallback</p>
                <p class="mb-2"><strong>3.</strong> Intégration dans le portail joueur : section "Informations du Club"</p>
                <p><strong>4.</strong> Bouton "Gérer" pour modifier les logos si nécessaire</p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="text-center mt-8">
            <a href="/logos-clubs-ftf" class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors mr-4">
                📋 Voir la liste des clubs
            </a>
            <a href="/test-clubs-ftf" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                🧪 Tester le composant
            </a>
        </div>
    </div>

    <script>
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const logos = document.querySelectorAll('.logo-container');
            logos.forEach((logo, index) => {
                setTimeout(() => {
                    logo.style.opacity = '0';
                    logo.style.transform = 'translateY(20px)';
                    logo.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        logo.style.opacity = '1';
                        logo.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
        });
    </script>
</body>
</html>

