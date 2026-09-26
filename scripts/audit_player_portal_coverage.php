<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'health_records', 'player_real_time_health', 'player_fitness_logs',
    'player_sdoh_data', 'pcmas', 'player_performances', 'medical_records',
    'performance_alerts', 'medical_predictions', 'doping_controls',
    'player_connected_devices', 'player_season_stats', 'performance_trends',
    'player_licenses', 'injuries', 'license_requests',
];
$players = DB::table('players')->count();
echo "Joueurs : {$players}\n";
echo "Table | Joueurs couverts | Lignes joueur 1 | Total lignes\n";
foreach ($tables as $table) {
    if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'player_id')) {
        echo "{$table} | TABLE/CLÉ ABSENTE\n";
        continue;
    }
    $query = DB::table($table);
    $covered = (clone $query)->whereNotNull('player_id')->distinct()->count('player_id');
    $first = (clone $query)->where('player_id', 1)->count();
    $total = $query->count();
    echo "{$table} | {$covered}/{$players} | {$first} | {$total}\n";
}
