<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Débogueur de Syntaxe PHP - FIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">🔍 Débogueur de Syntaxe PHP</h1>
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🧪 Test de Syntaxe Section par Section</h2>
            
            @php
                $sections = [
                    'Section 1: Variables de base' => '<?php $totalPlayers = 25; $activePlayers = 22; $avgAge = 24.5; ?>',
                    'Section 2: Logique conditionnelle' => '<?php if ($totalPlayers > 0) { $healthScore = 100; } else { $healthScore = 0; } ?>',
                    'Section 3: Calculs mathématiques' => '<?php $predictedPosition = max(1, min(20, round(2 * (1 - (65 - 50) / 100)))); ?>',
                    'Section 4: Logique ternaire' => '<?php $positionText = $predictedPosition === 1 ? "1ère Place" : $predictedPosition . "ème Place"; ?>',
                    'Section 5: Arrays et boucles' => '<?php $positions = ["Attaquant", "Milieu", "Défenseur", "Gardien"]; foreach ($positions as $pos) { $count = 5; } ?>',
                    'Section 6: Requêtes Eloquent' => '<?php try { $playerCount = \App\Models\Player::count(); } catch (Exception $e) { $playerCount = 0; } ?>',
                    'Section 7: Requêtes DB' => '<?php try { $injuredCount = \DB::table("player_injuries_diseases")->where("is_resolved", false)->count(); } catch (Exception $e) { $injuredCount = 0; } ?>',
                    'Section 8: Chaînes complexes' => '<?php $message = $playerCount . " joueur" . ($playerCount > 1 ? "s" : "") . " à risque"; ?>',
                    'Section 9: Logique if/elseif' => '<?php if ($predictedPosition <= 3) { $color = "green"; } elseif ($predictedPosition <= 6) { $color = "blue"; } else { $color = "orange"; } ?>',
                    'Section 10: Try/catch' => '<?php try { $test = 1; } catch (Exception $e) { $test = 0; } ?>'
                ];
                
                foreach ($sections as $name => $code) {
                    echo '<div class="mb-4 p-3 border rounded">';
                    echo '<h3 class="font-semibold text-gray-800 mb-2">' . $name . '</h3>';
                    
                    try {
                        eval($code);
                        echo '<div class="text-green-600">✅ Syntaxe correcte</div>';
                        echo '<div class="text-xs text-gray-600 mt-1">Code: <code class="bg-gray-100 px-1 rounded">' . htmlspecialchars($code) . '</code></div>';
                    } catch (ParseError $e) {
                        echo '<div class="text-red-600">❌ Erreur de syntaxe</div>';
                        echo '<div class="text-xs text-red-500 mt-1">' . $e->getMessage() . '</div>';
                        echo '<div class="text-xs text-gray-600 mt-1">Code: <code class="bg-red-100 px-1 rounded">' . htmlspecialchars($code) . '</code></div>';
                    } catch (Exception $e) {
                        echo '<div class="text-orange-600">⚠️ Erreur d\'exécution</div>';
                        echo '<div class="text-xs text-orange-500 mt-1">' . $e->getMessage() . '</div>';
                        echo '<div class="text-xs text-gray-600 mt-1">Code: <code class="bg-orange-100 px-1 rounded">' . htmlspecialchars($code) . '</code></div>';
                    }
                    
                    echo '</div>';
                }
            @endphp
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🔧 Test de Compilation Blade</h2>
            
            @php
                $bladeTests = [
                    'Test 1: Variables simples' => '{{ $totalPlayers }}',
                    'Test 2: Conditions' => '@if($totalPlayers > 0) Oui @else Non @endif',
                    'Test 3: Boucles' => '@for($i = 0; $i < 3; $i++) {{ $i }} @endfor',
                    'Test 4: PHP inline' => '@php $test = 1; @endphp {{ $test }}',
                    'Test 5: Échappement' => '{{ "Test avec \'apostrophes\'" }}',
                    'Test 6: Conditions complexes' => '@if($totalPlayers > 0 && $activePlayers > 0) OK @else KO @endif'
                ];
                
                foreach ($bladeTests as $name => $code) {
                    echo '<div class="mb-4 p-3 border rounded">';
                    echo '<h3 class="font-semibold text-gray-800 mb-2">' . $name . '</h3>';
                    
                    try {
                        $view = view('test-blade', ['code' => $code, 'totalPlayers' => 25, 'activePlayers' => 22]);
                        $content = $view->render();
                        echo '<div class="text-green-600">✅ Blade correct</div>';
                        echo '<div class="text-xs text-gray-600 mt-1">Rendu: <code class="bg-gray-100 px-1 rounded">' . htmlspecialchars($content) . '</code></div>';
                    } catch (Exception $e) {
                        echo '<div class="text-red-600">❌ Erreur Blade</div>';
                        echo '<div class="text-xs text-red-500 mt-1">' . $e->getMessage() . '</div>';
                    }
                    
                    echo '</div>';
                }
            @endphp
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🎯 Test de Fichier Complet</h2>
            
            @php
                try {
                    echo '<div class="mb-4">';
                    echo '<h3 class="font-semibold text-gray-800 mb-2">Test de compilation welcome.blade.php</h3>';
                    
                    $view = view('welcome');
                    echo '<div class="text-green-600">✅ Vue compilée avec succès</div>';
                    
                    $content = $view->render();
                    echo '<div class="text-green-600">✅ Vue rendue avec succès</div>';
                    echo '<div class="text-xs text-gray-600 mt-1">Longueur: ' . strlen($content) . ' caractères</div>';
                    
                    echo '</div>';
                } catch (ParseError $e) {
                    echo '<div class="text-red-600">❌ Erreur de syntaxe PHP</div>';
                    echo '<div class="text-xs text-red-500 mt-1">' . $e->getMessage() . '</div>';
                    echo '<div class="text-xs text-red-400 mt-1">Fichier: ' . $e->getFile() . ':' . $e->getLine() . '</div>';
                } catch (Exception $e) {
                    echo '<div class="text-orange-600">⚠️ Erreur d\'exécution</div>';
                    echo '<div class="text-xs text-orange-500 mt-1">' . $e->getMessage() . '</div>';
                }
            @endphp
        </div>

        <div class="mt-8 text-center space-x-4">
            <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Test Dashboard Principal
            </a>
            <a href="/dashboard-test" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Dashboard Test
            </a>
        </div>
    </div>
</body>
</html>



