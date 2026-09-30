<?php

namespace Database\Seeders;

use App\Models\ExternalPlayerPerformanceMetric;
use App\Models\Player;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KsaConfirmedMatchCountsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('external_player_performance_metrics')) {
            return;
        }

        $path = base_path('storage/app/imports/ksa_confirmed_match_counts.csv');
        if (!is_file($path)) {
            return;
        }

        $handle = fopen($path, 'r');
        try {
            $headers = fgetcsv($handle);
            while (($values = fgetcsv($handle)) !== false) {
                if (count($values) !== count($headers)) {
                    continue;
                }
                $row = array_combine($headers, $values);
                $matches = filter_var($row['matches'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if ($matches === false) {
                    continue;
                }
                $player = Player::query()
                    ->where('first_name', $row['first_name'])
                    ->where('last_name', $row['last_name'])
                    ->whereHas('club', fn ($query) => $query->where('name', $row['club_name']))
                    ->first();
                if (!$player || !DB::table('external_player_performance_metrics')
                    ->where('player_id', $player->id)->where('source', 'KSA')->exists()) {
                    continue;
                }

                ExternalPlayerPerformanceMetric::firstOrCreate(
                    ['player_id' => $player->id, 'metric_name' => 'matches_played', 'source' => 'KSA'],
                    [
                        'metric_value' => $matches,
                        'metric_unit' => 'count',
                        'season' => $row['season'],
                        'notes' => 'Nombre de matchs confirmé par le propriétaire des données ; absent du fichier KSA initial.',
                    ]
                );
            }
        } finally {
            fclose($handle);
        }
    }
}
