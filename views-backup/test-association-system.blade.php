<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Système Association</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Test Système Association</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Test du composant -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🎯 Test du Composant</h3>
                
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        try {
                            $assoc = App\Models\Association::find(7); // Maroc
                            if ($assoc) {
                                $player = (object)['association' => $assoc];
                            } else {
                                $player = null;
                            }
                        } catch (Exception $e) {
                            $player = null;
                            $error = $e->getMessage();
                        }
                    @endphp
                    
                    @if($player && $player->association)
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
                        @if($player && $player->association)
                            <a href="{{ route('associations.edit-logo', $player->association->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                                🏆 Gérer
                            </a>
                        @else
                            <span class="bg-gray-500 text-white px-3 py-1 rounded text-sm font-medium">
                                ❌ Erreur
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="mt-4 space-y-2 text-sm">
                    @if(isset($error))
                        <p class="text-red-600"><strong>Erreur:</strong> {{ $error }}</p>
                    @elseif($player && $player->association)
                        <p><strong>Association:</strong> {{ $player->association->name }}</p>
                        <p><strong>Pays:</strong> {{ $player->association->country }}</p>
                        <p><strong>ID:</strong> {{ $player->association->id }}</p>
                        <p><strong>Route Gérer:</strong> {{ route('associations.edit-logo', $player->association->id) }}</p>
                    @else
                        <p class="text-gray-500">Aucune association trouvée</p>
                    @endif
                </div>
            </div>
            
            <!-- Test des routes -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🔗 Test des Routes</h3>
                
                <div class="space-y-3">
                    @if($player && $player->association)
                        <div>
                            <h4 class="font-medium mb-2">Routes disponibles :</h4>
                            <div class="space-y-2">
                                <a href="{{ route('associations.edit-logo', $player->association->id) }}" 
                                   class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center transition-colors">
                                    📝 Éditer le logo
                                </a>
                                
                                <form action="{{ route('associations.update-national-logos') }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors">
                                        🔄 Mettre à jour logos nationaux
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500">Aucune association disponible pour tester les routes</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Debug des fichiers -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-md">
            <h3 class="text-lg font-semibold mb-4">🔍 Debug des Fichiers</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h4 class="font-semibold mb-2">Logos nationaux :</h4>
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
                                    $sample = array_slice($pngFiles, 0, 10);
                                    foreach($sample as $file) {
                                        $size = filesize(public_path("associations/{$file}"));
                                        echo '<div class="text-xs text-gray-600">• ' . $file . ' (' . number_format($size/1024, 1) . ' KB)</div>';
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
                <div>
                    <h4 class="font-semibold mb-2">Stockage public :</h4>
                    @php
                        try {
                            $storagePath = public_path('storage');
                            if (is_dir($storagePath)) {
                                echo '<div class="text-sm text-green-600">✅ Lien storage existe</div>';
                                
                                $assocStoragePath = storage_path('app/public/associations');
                                if (is_dir($assocStoragePath)) {
                                    echo '<div class="text-sm text-green-600">✅ Répertoire storage associations existe</div>';
                                    $files = scandir($assocStoragePath);
                                    $uploadedFiles = array_filter($files, function($file) {
                                        return $file !== '.' && $file !== '..';
                                    });
                                    echo '<div class="text-sm">Fichiers uploadés: ' . count($uploadedFiles) . '</div>';
                                } else {
                                    echo '<div class="text-sm text-yellow-600">⚠️ Répertoire storage associations n\'existe pas</div>';
                                }
                            } else {
                                echo '<div class="text-sm text-red-600">❌ Lien storage n\'existe pas</div>';
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

