<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Player;
use App\Models\ExternalPlayerPerformanceMetric;
class ImportKsaPlayerStatistics extends Command
{
    protected $signature = 'app:import-ksa-player-statistics {file=storage/app/imports/ksa_player_statistics.csv}';
    protected $description = 'Importe les statistiques KSA sans modifier les performances FIT natives.';
    public function handle()
    {
        $path = base_path($this->argument('file'));
        if (!is_file($path)) { $this->error("Fichier introuvable: {$path}"); return self::FAILURE; }
        $map = config('ksa_metrics'); $handle = fopen($path, 'r');
        $headers = array_map('trim', fgetcsv($handle)); $created = 0; $skipped = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row); $name = trim($data['Player'] ?? '');
            $player = Player::where('name', $name)->orWhereRaw("concat(first_name, ' ', last_name) = ?", [$name])->first();
            if (!$player) { $skipped++; continue; }
            foreach ($map as $excel => $definition) {
                if (!array_key_exists($excel, $data) || $data[$excel] === '' || $data[$excel] === '-') continue;
                ExternalPlayerPerformanceMetric::updateOrCreate(
                    ['player_id' => $player->id, 'metric_name' => $definition['name'], 'source' => 'KSA'],
                    ['metric_value' => is_numeric($data[$excel]) ? $data[$excel] : null, 'metric_unit' => $definition['unit'], 'raw_data' => $data]
                ); $created++;
            }
        }
        fclose($handle); $this->info("Métriques KSA créées ou mises à jour: {$created}; joueurs ignorés: {$skipped}");
        return self::SUCCESS;
    }
}
