<?php

namespace App\Console\Commands;

use App\Services\RoleEvaluationEngine\CalculatorConfigBuilder;
use App\Services\RoleEvaluationEngine\MatchStatsDataSource;
use App\Services\RoleEvaluationEngine\RoleFitEvaluator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Cockpit "Statistiques avancées" : courbe d'évolution du score sur la
 * saison. Réutilise role-eval:compute (RoleFitEvaluator::evaluate(), déjà
 * capable de restreindre le calcul à un sous-ensemble de matchs via
 * --matches) plusieurs fois, une fois par point de journée cumulé (ex. J5,
 * J10, J15...) au lieu d'une seule fois sur tous les matchs.
 *
 * Chaque point de la courbe est une ligne player_role_evaluations à part
 * entière, avec period_end renseigné (date du dernier match inclus dans ce
 * point). La ligne "instantané complet" produite par role-eval:compute
 * garde period_end = null : c'est ce qui distingue "le résultat courant"
 * (period_end null, ce que le cockpit affiche comme score/comparaisons)
 * des "points d'historique" (period_end renseigné, ce que le cockpit
 * affiche comme courbe). N'écrit QUE dans player_role_evaluations, comme
 * role-eval:compute.
 */
class RoleEvaluationComputeHistoryCommand extends Command
{
    protected $signature = 'role-eval:compute-history
        {--config-version= : Identifiant role_config_versions à utiliser}
        {--is-demo= : 1 pour traiter les données de démonstration, 0 pour les données réelles (obligatoire)}
        {--step=5 : Intervalle de journées entre deux points de la courbe}
        {--players= : Liste d\'identifiants players.id séparés par des virgules ; sinon, tous les joueurs avec au moins une participation correspondant à --is-demo}
        {--dry-run : N\'écrit rien, affiche seulement combien de lignes seraient produites par point}';

    protected $description = "Calcule des points d'historique (score par famille jouée) à intervalles de journées réguliers, pour la courbe d'évolution du cockpit";

    public function handle(): int
    {
        $configVersionOption = $this->option('config-version');
        $isDemoOption = $this->option('is-demo');

        if ($configVersionOption === null || $configVersionOption === '') {
            $this->error('--config-version est obligatoire (identifiant role_config_versions).');

            return self::FAILURE;
        }

        if ($isDemoOption === null || !in_array($isDemoOption, ['0', '1'], true)) {
            $this->error('--is-demo est obligatoire et doit valoir 0 ou 1 : cette commande ne devine jamais si les données sont des données de démonstration.');

            return self::FAILURE;
        }

        $roleConfigVersionId = (int) $configVersionOption;
        $isDemo = $isDemoOption === '1';
        $step = (int) $this->option('step');

        if ($step < 1) {
            $this->error('--step doit être un entier positif.');

            return self::FAILURE;
        }

        $version = DB::table('role_config_versions')->find($roleConfigVersionId);
        if ($version === null) {
            $this->error("role_config_versions #{$roleConfigVersionId} introuvable.");

            return self::FAILURE;
        }

        $maxMatchday = DB::table('matches')
            ->where('is_demo', $isDemo)
            ->whereNotNull('matchday')
            ->max('matchday');

        if ($maxMatchday === null) {
            $this->info('Aucun match avec une journée renseignée pour ce is_demo : rien à calculer.');

            return self::SUCCESS;
        }

        $requestedPlayerIds = $this->parseIdList($this->option('players'));
        $evaluator = new RoleFitEvaluator(new MatchStatsDataSource, new CalculatorConfigBuilder);
        $now = now();
        $totalWritten = 0;

        for ($checkpoint = $step; $checkpoint <= $maxMatchday; $checkpoint += $step) {
            $matchesAtCheckpoint = DB::table('matches')
                ->where('is_demo', $isDemo)
                ->whereNotNull('matchday')
                ->where('matchday', '<=', $checkpoint)
                ->get(['id', 'match_date']);

            if ($matchesAtCheckpoint->isEmpty()) {
                continue;
            }

            $matchIds = $matchesAtCheckpoint->pluck('id')->all();
            $periodEnd = $matchesAtCheckpoint->pluck('match_date')->filter()->max();

            $playerIds = $requestedPlayerIds ?? DB::table('match_participations')
                ->where('is_demo', $isDemo)
                ->whereIn('match_id', $matchIds)
                ->distinct()
                ->pluck('player_id')
                ->all();

            if ($playerIds === []) {
                continue;
            }

            try {
                $rows = $evaluator->evaluate($playerIds, $roleConfigVersionId, $isDemo, $matchIds);
            } catch (\RuntimeException $e) {
                $this->warn("Journée {$checkpoint} : {$e->getMessage()} — point ignoré.");

                continue;
            }

            if ($this->option('dry-run')) {
                $this->info("Journée {$checkpoint} (jusqu'au ".($periodEnd ?? '?').") : ".count($rows).' ligne(s) seraient écrites (--dry-run).');

                continue;
            }

            $toInsert = array_map(fn (array $row) => $row + [
                'role_config_version_id' => $roleConfigVersionId,
                'model_version' => RoleFitEvaluator::MODEL_VERSION,
                'is_demo' => $isDemo,
                'source_import_batch_id' => null,
                'source_demo_batch_id' => null,
                'period_end' => $periodEnd,
                'computed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ], $rows);

            foreach (array_chunk($toInsert, 200) as $chunk) {
                DB::table('player_role_evaluations')->insert($chunk);
            }

            $totalWritten += count($toInsert);
            $this->info("Journée {$checkpoint} (jusqu'au ".($periodEnd ?? '?').") : ".count($toInsert).' ligne(s) écrites.');
        }

        if (!$this->option('dry-run')) {
            $this->info("Total : {$totalWritten} ligne(s) d'historique écrites dans player_role_evaluations.");
        }

        return self::SUCCESS;
    }

    /** @return int[]|null */
    private function parseIdList(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return array_values(array_unique(array_map('intval', array_filter(array_map('trim', explode(',', $value)), fn ($v) => $v !== ''))));
    }
}
