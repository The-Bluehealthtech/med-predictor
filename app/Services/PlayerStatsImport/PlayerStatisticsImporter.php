<?php

namespace App\Services\PlayerStatsImport;

use App\Models\Club;
use App\Models\ExternalPlayerPerformanceMetric;
use App\Models\Player;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Import d'un export « Player statistics » reconnu : rapprochement des joueurs
 * du club par leur nom, aperçu avant écriture, puis enregistrement des
 * indicateurs de période selon la même règle que l'import KSA existant
 * (une ligne par joueur, indicateur et source), pour rester lisible par les portails.
 */
final class PlayerStatisticsImporter
{
    /** Aperçu : joueurs reconnus, joueurs inconnus, saison proposée. */
    public function preview(array $file, Club $club): array
    {
        $index = $this->playerIndex($club);
        $matched = [];
        $unmatched = [];
        foreach ($file['rows'] as $row) {
            $name = trim((string) $row['Player']);
            $player = $index[$this->normalize($name)] ?? null;
            if ($player) {
                $matched[] = ['name' => $name, 'player_id' => $player->id, 'player_name' => trim($player->first_name . ' ' . $player->last_name) ?: $player->name,
                    'minutes' => PlayerStatisticsFile::number($row['Minutes played'] ?? null), 'position' => $row['Position'] ?? null];
            } else {
                $unmatched[] = ['name' => $name, 'position' => $row['Position'] ?? null, 'minutes' => PlayerStatisticsFile::number($row['Minutes played'] ?? null)];
            }
        }

        return ['matched' => $matched, 'unmatched' => $unmatched, 'season' => $this->season($file['file_date'] ?? null)];
    }

    /**
     * Enregistre les indicateurs de période des joueurs reconnus (et, sur demande,
     * crée les joueurs inconnus dans le club). Tout ou rien.
     */
    public function importPeriod(array $file, Club $club, array $options): array
    {
        $index = $this->playerIndex($club);
        $measuredAt = ($file['file_date'] ?? null) ? Carbon::parse($file['file_date'])->toDateString() : now()->toDateString();
        $metricMap = config('ksa_metrics', []);
        $stats = ['players' => 0, 'created' => 0, 'skipped' => 0, 'metrics' => 0];

        DB::transaction(function () use ($file, $club, $options, &$index, $measuredAt, $metricMap, &$stats) {
            foreach ($file['rows'] as $row) {
                $name = trim((string) $row['Player']);
                $player = $index[$this->normalize($name)] ?? null;
                if (!$player && !empty($options['create_missing'])) {
                    $player = $this->createPlayer($club, $row);
                    $index[$this->normalize($name)] = $player;
                    $stats['created']++;
                }
                if (!$player) {
                    $stats['skipped']++;
                    continue;
                }
                $stats['players']++;
                foreach ($file['metric_columns'] as $header) {
                    $value = PlayerStatisticsFile::number($row[$header] ?? null);
                    if ($value === null) {
                        continue;
                    }
                    $definition = $metricMap[$header] ?? ['name' => Str::slug($header, '_'), 'unit' => str_contains($header, '%') ? 'percent' : 'count'];
                    ExternalPlayerPerformanceMetric::updateOrCreate(
                        ['player_id' => $player->id, 'metric_name' => $definition['name'], 'source' => $options['source']],
                        ['metric_value' => $value, 'metric_unit' => $definition['unit'], 'season' => $options['season'],
                            'competition' => $options['competition'], 'measured_at' => $measuredAt, 'raw_data' => $row, 'score_origin' => 'observed']
                    );
                    $stats['metrics']++;
                }
            }
        });

        return $stats;
    }

    /** Saison sportive d'une date : juillet à juin (« 2026/27 »). */
    public function season(?Carbon $date): string
    {
        $date ??= now();
        $start = $date->month >= 7 ? $date->year : $date->year - 1;

        return $start . '/' . substr((string) ($start + 1), -2);
    }

    /** Nom normalisé : minuscules, sans accents, tirets et ponctuation remplacés par des espaces. */
    public function normalize(string $name): string
    {
        $ascii = Str::ascii(mb_strtolower(trim($name)));

        return trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-z0-9]+/', ' ', $ascii)));
    }

    /** Index des joueurs du club par nom normalisé (nom complet, prénom + nom, nom + prénom). */
    private function playerIndex(Club $club): array
    {
        $index = [];
        Player::withoutGlobalScopes()->where('club_id', $club->id)->get(['id', 'name', 'first_name', 'last_name'])->each(function ($p) use (&$index) {
            foreach (array_filter([$p->name, trim($p->first_name . ' ' . $p->last_name), trim($p->last_name . ' ' . $p->first_name)]) as $variant) {
                $index[$this->normalize($variant)] ??= $p;
            }
        });

        return $index;
    }

    private function createPlayer(Club $club, array $row): Player
    {
        $name = trim((string) $row['Player']);
        $parts = preg_split('/\s+/', $name, 2);
        $player = new Player;
        $player->forceFill(array_filter([
            'name' => $name,
            'first_name' => $parts[0] ?? $name,
            'last_name' => $parts[1] ?? '',
            'age' => is_numeric($row['Age'] ?? null) ? (int) $row['Age'] : null,
            'nationality' => ($row['Nationality'] ?? '-') !== '-' ? $row['Nationality'] : null,
            'position' => ($row['Position'] ?? '-') !== '-' ? $row['Position'] : null,
            'club_id' => $club->id,
            'association_id' => $club->association_id,
            'fifa_sync_status' => 'pending',
        ], fn ($v) => $v !== null));
        $player->save();

        return $player;
    }
}
