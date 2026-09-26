<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$cohort = DB::table('player_real_time_health')
    ->where('notes', 'like', 'synthetic_demo%')
    ->distinct()->pluck('player_id');
$total = $cohort->count();

if ($total === 0) {
    throw new RuntimeException('Aucun joueur de test trouvé.');
}

$ids = $cohort->all();
$counts = [
    'Profil complet' => DB::table('players')->whereIn('id', $ids)
        ->whereNotNull('height')->whereNotNull('weight')
        ->whereNotNull('preferred_foot')->count(),
    'Nutrition de test' => DB::table('player_real_time_health')
        ->whereIn('player_id', $ids)
        ->whereRaw("metadata::jsonb #>> '{nutrition,total_calories}' IS NOT NULL")
        ->whereRaw("metadata::jsonb #>> '{nutrition,protein_grams}' IS NOT NULL")
        ->whereRaw("metadata::jsonb #>> '{nutrition,meal_quality_score}' IS NOT NULL")
        ->distinct()->count('player_id'),
    'Traitement actif' => DB::table('health_records')
        ->whereIn('player_id', $ids)
        ->whereRaw("medications::jsonb @> '[{\"status\":\"active\"}]'::jsonb")
        ->distinct()->count('player_id'),
    'Match fictif' => DB::table('performances')
        ->whereIn('player_id', $ids)
        ->where('notes', 'like', 'synthetic_demo%')
        ->distinct()->count('player_id'),
    'FIT complet' => DB::table('fit_score_snapshots')
        ->whereIn('player_id', $ids)
        ->where('calculation_version', 'fit_v1')
        ->where('is_complete', true)
        ->whereNotNull('fit_score')
        ->distinct()->count('player_id'),
];

echo "Joueurs de test : {$total}\n";
foreach ($counts as $label => $count) {
    echo "{$label} : {$count}/{$total}\n";
}

$snapshot = DB::table('fit_score_snapshots')
    ->where('player_id', 1)->where('is_complete', true)
    ->orderByDesc('snapshot_at')->first(['fit_score']);
echo 'FIT joueur 1 : ' . ($snapshot?->fit_score ?? 'absent') . "/100\n";

foreach ($counts as $label => $count) {
    if ($count < $total) {
        throw new RuntimeException("Couverture incomplète : {$label} ({$count}/{$total}).");
    }
}
