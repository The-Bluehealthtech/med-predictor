<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\ExternalPlayerPerformanceMetric;
use App\Models\Player;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportKsaPlayerStatistics extends Command
{
    protected $signature = 'app:import-ksa-player-statistics {file=storage/app/imports/ksa_player_statistics.csv} {--create-players} {--club=}';
    protected $description = 'Importe les statistiques KSA et peut créer les joueurs absents du référentiel.';

    public function handle(): int
    {
        $argument = $this->argument('file');
        $path = is_file($argument) ? $argument : base_path($argument);
        if (!is_file($path)) {
            $this->error("Fichier introuvable: {$path}");
            return self::FAILURE;
        }

        $club = null;
        if ($this->option('create-players')) {
            $club = Club::where('name', $this->option('club'))->first();
            if (!$club) {
                $this->error('Club introuvable: ' . $this->option('club'));
                return self::FAILURE;
            }
        }

        $map = config('ksa_metrics', []);
        $handle = fopen($path, 'r');
        $headers = array_map('trim', fgetcsv($handle));
        $playersCreated = 0;
        $metricsCreated = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            $name = trim($data['Player'] ?? '');
            if ($name === '') {
                continue;
            }

            $player = Player::where('name', $name)
                ->orWhereRaw("concat(first_name, ' ', last_name) = ?", [$name])
                ->first();

            if (!$player && $club) {
                [$firstName, $lastName] = array_pad(explode(' ', $name, 2), 2, '');
                $player = Player::create([
                    'name' => $name,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'age' => is_numeric($data['Age'] ?? null) ? (int) $data['Age'] : null,
                    'nationality' => $data['Nationality'] ?? null,
                    'position' => $data['Position'] ?? null,
                    'club_id' => $club->id,
                    'association_id' => $club->association_id,
                    'fifa_sync_status' => 'pending',
                ]);
                $playersCreated++;
            }

            if (!$player) {
                $skipped++;
                continue;
            }

            foreach ($data as $excelHeader => $rawValue) {
                if (in_array($excelHeader, ['№', 'Player', 'Age', 'Height', 'Weight', 'Nationality', 'Position'], true)) {
                    continue;
                }
                if ($rawValue === '' || $rawValue === '-') {
                    continue;
                }

                $definition = $map[$excelHeader] ?? [
                    'name' => Str::slug($excelHeader, '_'),
                    'unit' => str_contains($excelHeader, '%') ? 'percent' : 'count',
                ];
                $numeric = is_numeric($rawValue) ? (float) $rawValue : null;

                ExternalPlayerPerformanceMetric::updateOrCreate(
                    ['player_id' => $player->id, 'metric_name' => $definition['name'], 'source' => 'KSA'],
                    [
                        'metric_value' => $numeric,
                        'metric_unit' => $definition['unit'],
                        'season' => '2026/27',
                        'competition' => 'Saudi Professional League',
                        'measured_at' => now()->toDateString(),
                        'raw_data' => $data,
                    ]
                );
                $metricsCreated++;
            }
        }

        fclose($handle);
        $this->info("Joueurs créés: {$playersCreated}; métriques KSA créées ou mises à jour: {$metricsCreated}; joueurs ignorés: {$skipped}");
        return self::SUCCESS;
    }
}
