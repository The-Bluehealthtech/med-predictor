<?php

echo "=== INJECTION FINALE DES DONNÉES DYNAMIQUES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-donnees-finales.blade.php';

// Sauvegarde
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

// Lire
$content = file_get_contents($portalFile);

// Injection des vraies données dynamiques
$dataInjection = [
    // === STATISTIQUES OFFENSIVES ===
    '{ name: \'Buts marqués\', display: \'12\', percentage: 85, teamAvg: \'8.5\', leagueAvg: \'7.2\' }' => '{ name: \'Buts marqués\', display: \'{{ $player->performances->sum("goals") ?? 0 }}\', percentage: 85, teamAvg: \'8.5\', leagueAvg: \'7.2\' }',
    '{ name: \'Tirs cadrés\', display: \'{{ $player->performances->sum("shots_on_target") ?? 0 }}\', percentage: 78, teamAvg: \'35.2\', leagueAvg: \'32.1\' }' => '{ name: \'Tirs cadrés\', display: \'{{ $player->performances->sum("shots_on_target") ?? 0 }}\', percentage: 78, teamAvg: \'35.2\', leagueAvg: \'32.1\' }',
    '{ name: \'Tirs totaux\', display: \'{{ $player->performances->sum("shots") ?? 0 }}\', percentage: {{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}, teamAvg: \'58.4\', leagueAvg: \'55.7\' }' => '{ name: \'Tirs totaux\', display: \'{{ $player->performances->sum("shots") ?? 0 }}\', percentage: 72, teamAvg: \'58.4\', leagueAvg: \'55.7\' }',
    '{ name: \'Précision tirs\', display: \'78%\', percentage: 78, teamAvg: \'65%\', leagueAvg: \'62%\' }' => '{ name: \'Précision tirs\', display: \'{{ $player->performances->count() > 0 ? round(($player->performances->sum("shots_on_target") / $player->performances->sum("shots")) * 100) : 0 }}%\', percentage: 78, teamAvg: \'65%\', leagueAvg: \'62%\' }',
    '{ name: \'Passes décisives\', display: \'8\', percentage: 82, teamAvg: \'6.8\', leagueAvg: \'5.9\' }' => '{ name: \'Passes décisives\', display: \'{{ $player->performances->sum("assists") ?? 0 }}\', percentage: 82, teamAvg: \'6.8\', leagueAvg: \'5.9\' }',
    '{ name: \'Passes clés\', display: \'{{ $player->performances->sum("goals") ?? 0 }}\', percentage: 78, teamAvg: \'22.1\', leagueAvg: \'19.8\' }' => '{ name: \'Passes clés\', display: \'{{ $player->performances->sum("key_passes") ?? 0 }}\', percentage: 78, teamAvg: \'22.1\', leagueAvg: \'19.8\' }',
    '{ name: \'Centres réussis\', display: \'18\', percentage: 75, teamAvg: \'15.3\', leagueAvg: \'13.7\' }' => '{ name: \'Centres réussis\', display: \'{{ $player->performances->sum("crosses") ?? 0 }}\', percentage: 75, teamAvg: \'15.3\', leagueAvg: \'13.7\' }',
    '{ name: \'Dribbles réussis\', display: \'{{ $player->performances->sum("direction_changes") ?? 0 }}\', percentage: 82, teamAvg: \'{{ $player->performances->count() }}.2\', leagueAvg: \'38.9\' }' => '{ name: \'Dribbles réussis\', display: \'{{ $player->performances->sum("dribbles") ?? 0 }}\', percentage: 82, teamAvg: \'{{ $player->performances->count() }}.2\', leagueAvg: \'38.9\' }',
    
    // === STATISTIQUES PHYSIQUES ===
    '{ name: \'Distance parcourue\', display: \'2{{ $player->performances->count() }} km\', percentage: 78, teamAvg: \'198 km\', leagueAvg: \'185 km\' }' => '{ name: \'Distance parcourue\', display: \'{{ $player->performances->sum("distance_covered") ?? 0 }} km\', percentage: 78, teamAvg: \'198 km\', leagueAvg: \'185 km\' }',
    '{ name: \'Vitesse maximale\', display: \'{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}.2 km/h\', percentage: 85, teamAvg: \'32.1 km/h\', leagueAvg: \'30.8 km/h\' }' => '{ name: \'Vitesse maximale\', display: \'{{ $player->performances->max("max_speed") ?? 0 }} km/h\', percentage: 85, teamAvg: \'32.1 km/h\', leagueAvg: \'30.8 km/h\' }',
    '{ name: \'Vitesse moyenne\', display: \'12.3 km/h\', percentage: 82, teamAvg: \'11.2 km/h\', leagueAvg: \'10.9 km/h\' }' => '{ name: \'Vitesse moyenne\', display: \'{{ $player->performances->avg("avg_speed") ?? 0 }} km/h\', percentage: 82, teamAvg: \'11.2 km/h\', leagueAvg: \'10.9 km/h\' }',
    '{ name: \'Sprints\', display: \'156\', percentage: 82, teamAvg: \'1{{ $player->performances->sum("goals") ?? 0 }}\', leagueAvg: \'115\' }' => '{ name: \'Sprints\', display: \'{{ $player->performances->sum("sprints") ?? 0 }}\', percentage: 82, teamAvg: \'{{ $player->performances->count() > 0 ? round($player->performances->avg("sprints")) : 0 }}\', leagueAvg: \'115\' }',
    
    // === DONNÉES MÉDICALES ===
    '{ name: \'Fréquence Cardiaque\', value: \'72\', unit: \'bpm\', icon: \'fas fa-heartbeat\', color: \'#ef4444\', status: \'normal\' }' => '{ name: \'Fréquence Cardiaque\', value: \'{{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}\', unit: \'bpm\', icon: \'fas fa-heartbeat\', color: \'#ef4444\', status: \'normal\' }',
    '{ name: \'Température\', value: \'36.8°\', unit: \'C\', icon: \'fas fa-temperature-low\', color: \'#f59e0b\', status: \'normal\' }' => '{ name: \'Température\', value: \'{{ $player->healthRecords->first() ? $player->healthRecords->first()->temperature : "N/A" }}°\', unit: \'C\', icon: \'fas fa-temperature-low\', color: \'#f59e0b\', status: \'normal\' }'
];

$total = 0;
foreach ($dataInjection as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Injecté: $search ($count fois)\n";
    }
}

// Écrire
file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total injections\n";
echo "✅ Données dynamiques injectées!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";






