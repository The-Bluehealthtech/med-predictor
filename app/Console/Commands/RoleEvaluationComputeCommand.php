<?php

namespace App\Console\Commands;

use App\Services\RoleEvaluationEngine\CalculatorConfigBuilder;
use App\Services\RoleEvaluationEngine\MatchStatsDataSource;
use App\Services\RoleEvaluationEngine\RoleFitEvaluator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Matérialise des lignes player_role_evaluations (score, fiabilité,
 * intervalle, adéquation au rôle) pour un ensemble de joueurs et une
 * version de configuration de poids donnée.
 *
 * N'écrit QUE dans player_role_evaluations. Ne modifie jamais
 * match_participations, player_match_detailed_stats ni aucune autre table
 * de statistiques source — ce sont des données dérivées, recalculables à
 * volonté sans toucher aux données d'entrée.
 *
 * --is-demo est TOUJOURS requis explicitement (pas de valeur par défaut) :
 * cette commande ne devine jamais si elle traite des données de
 * démonstration ou réelles, pour ne jamais risquer d'écrire un is_demo
 * incorrect sur un résultat calculé.
 *
 * source_import_batch_id / source_demo_batch_id restent NULL dans cette
 * première version : une évaluation peut agréger des lignes de plusieurs
 * lots d'import, et leur attribution à un lot unique n'est pas nécessaire
 * pour que le bandeau "Données de démonstration" fonctionne (il ne dépend
 * que de is_demo). Limite documentée au rapport, pas un oubli.
 */
class RoleEvaluationComputeCommand extends Command
{
    protected $signature = 'role-eval:compute
        {--config-version= : Identifiant role_config_versions à utiliser}
        {--is-demo= : 1 pour traiter les données de démonstration, 0 pour les données réelles (obligatoire)}
        {--players= : Liste d\'identifiants players.id séparés par des virgules ; sinon, tous les joueurs avec au moins une participation correspondant à --is-demo (et --matches, si fourni)}
        {--matches= : Liste d\'identifiants matches.id séparés par des virgules, pour restreindre la population et les statistiques utilisées}
        {--dry-run : N\'écrit rien, affiche seulement combien de lignes seraient produites}';

    protected $description = 'Calcule score, fiabilité, intervalle et adéquation au rôle pour des joueurs, à partir d\'une version de configuration de poids';

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

        $version = DB::table('role_config_versions')->find($roleConfigVersionId);
        if ($version === null) {
            $this->error("role_config_versions #{$roleConfigVersionId} introuvable.");

            return self::FAILURE;
        }

        if ($version->status === 'draft') {
            $this->warn("Version #{$roleConfigVersionId} (\"{$version->label}\") au statut \"draft\" : ".
                'à n\'utiliser que pour valider le code, jamais comme résultat définitif — conformément au mandat, '.
                'les poids réels attendent de vraies données.');
        }

        $matchIds = $this->parseIdList($this->option('matches'));

        $playerIds = $this->parseIdList($this->option('players'));
        if ($playerIds === null) {
            $query = DB::table('match_participations')->where('is_demo', $isDemo);
            if ($matchIds !== null) {
                $query->whereIn('match_id', $matchIds);
            }
            $playerIds = $query->distinct()->pluck('player_id')->all();
        }

        if ($playerIds === []) {
            $this->info('Aucun joueur à évaluer (population vide pour ces filtres).');

            return self::SUCCESS;
        }

        $evaluator = new RoleFitEvaluator(new MatchStatsDataSource, new CalculatorConfigBuilder);

        try {
            $rows = $evaluator->evaluate($playerIds, $roleConfigVersionId, $isDemo, $matchIds);
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->info(count($rows)." ligne(s) seraient écrites dans player_role_evaluations (--dry-run : rien n'est écrit).");

            return self::SUCCESS;
        }

        $now = now();
        $toInsert = array_map(fn (array $row) => $row + [
            'role_config_version_id' => $roleConfigVersionId,
            'model_version' => RoleFitEvaluator::MODEL_VERSION,
            'is_demo' => $isDemo,
            'source_import_batch_id' => null,
            'source_demo_batch_id' => null,
            'computed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows);

        foreach (array_chunk($toInsert, 200) as $chunk) {
            DB::table('player_role_evaluations')->insert($chunk);
        }

        $this->info(count($toInsert).' ligne(s) écrites dans player_role_evaluations (version de configuration #'.$roleConfigVersionId.', is_demo='.($isDemo ? '1' : '0').').');

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
