<?php

namespace App\Http\Controllers;

use App\Services\CoachCockpit\CoachCockpitData;
use App\Services\RoleEvaluationImport\ImportMapping;
use App\Services\RoleEvaluationImport\RoleEvaluationImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Cockpit entraîneur (module Analytics & Performance) : bilan, pronostic,
 * onze optimal du modèle de sélection, grille des postes, indicateurs de jeu
 * et effectif d'une équipe choisie.
 */
class CoachCockpitController extends Controller
{
    public function show(Request $request, CoachCockpitData $data)
    {
        $user = $request->user();
        abort_if($user->isPlayer(), 403, 'Accès réservé au staff et aux administrateurs.');

        $clubs = $data->availableClubs();
        if ($user->isClubUser()) {
            $clubs = $clubs->where('id', (int) $user->club_id)->values();
        }

        $requested = $request->filled('club_id')
            ? filter_var($request->query('club_id'), FILTER_VALIDATE_INT)
            : null;

        if ($requested !== null) {
            abort_if(
                $requested === false || ! $clubs->contains('id', $requested),
                404,
                'Équipe introuvable ou non autorisée.'
            );
            $clubId = $requested;
        } else {
            $clubId = $clubs->contains('id', (int) $user->club_id)
                ? (int) $user->club_id
                : ($clubs->firstWhere('has_matches', true) ?? $clubs->first())?->id;
        }

        $cockpit = $clubId !== null ? $data->forClub((int) $clubId) : null;

        return view('modules.coach-cockpit.show', [
            'clubs' => $clubs,
            'clubId' => $clubId,
            'cockpit' => $cockpit,
            // Club sans feuille de match : fiche club (identité, staff, effectif, profils de saison).
            'sheet' => $cockpit === null && $clubId !== null ? app(\App\Services\CoachCockpit\ClubSheet::class)->forClub((int) $clubId) : null,
            'roleEvaluationStatus' => $this->roleEvaluationStatus(),
        ]);
    }

    public function importRoleEvaluationData(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:participations,player-match-stats,team-stats,events'],
            'source' => ['nullable', 'string', 'max:120'],
            'csv_file' => ['required', 'file', 'max:20480'],
            'mapping_file' => ['required', 'file', 'max:2048'],
        ]);

        try {
            $mapping = ImportMapping::fromFile($request->file('mapping_file')->getRealPath());

            if ($mapping->type !== $validated['type']) {
                return back()->with('error', 'Le type choisi ne correspond pas au type déclaré dans le mapping.');
            }

            $csvPath = $request->file('csv_file')->getRealPath();
            $source = $validated['source'] ?? null;

            $dryRun = new RoleEvaluationImporter($mapping, true, true);
            $dryRun->run($csvPath, $source);

            if ($dryRun->report->rowsRejected() > 0) {
                return back()->with('error', sprintf(
                    'Import refusé après dry-run : %d ligne(s) rejetée(s) sur %d. Corrigez le fichier avant écriture.',
                    $dryRun->report->rowsRejected(),
                    $dryRun->report->rowsRead
                ));
            }

            $importer = new RoleEvaluationImporter($mapping, false, true);
            $batchId = $importer->run($csvPath, $source);

            return back()->with('success', sprintf(
                'Import terminé dans PostgreSQL : lot #%d, %s.',
                $batchId,
                $importer->report->summaryLine()
            ));
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Échec de l’import rôle/apport : '.$exception->getMessage());
        }
    }

    public function computeRoleEvaluations(Request $request)
    {
        $validated = $request->validate([
            'config_version' => ['required', 'integer'],
            'club_id' => ['nullable', 'integer'],
        ]);

        $config = DB::table('role_config_versions')->find((int) $validated['config_version']);
        if (! $config || $config->status !== 'published') {
            return back()->with('error', 'Le calcul exige une configuration de poids au statut published.');
        }

        $playerIds = null;
        if (! empty($validated['club_id'])) {
            $playerIds = DB::table('players')
                ->where('club_id', (int) $validated['club_id'])
                ->pluck('id');

            if ($playerIds->isEmpty()) {
                return back()->with('error', 'Aucun joueur trouvé pour ce club.');
            }
        }

        // Deux sources possibles : statistiques match par match (démonstration)
        // et profils de période importés depuis les exports « Player statistics »
        // (données réelles). Chacune n'est calculée que si elle a des joueurs.
        $periodPlayers = (new \App\Services\RoleEvaluationEngine\PeriodStatsDataSource)->playerIds();
        $runs = [
            'matches' => ['--is-demo' => '1', 'players' => $playerIds?->all()],
            'period' => ['--is-demo' => '0', 'players' => $playerIds === null ? $periodPlayers : array_values(array_intersect($playerIds->all(), $periodPlayers))],
        ];

        $messages = [];
        foreach ($runs as $source => $run) {
            if ($run['players'] === []) {
                continue;
            }
            $arguments = [
                '--config-version' => (string) $config->id,
                '--is-demo' => $run['--is-demo'],
                '--source' => $source,
                '--no-interaction' => true,
            ];
            if ($run['players'] !== null) {
                $arguments['--players'] = implode(',', $run['players']);
            }

            $dryCode = Artisan::call('role-eval:compute', $arguments + ['--dry-run' => true]);
            $dryOutput = trim(Artisan::output());
            if ($dryCode !== 0) {
                return back()->with('error', 'Dry-run du calcul refusé : '.$dryOutput);
            }
            if (str_contains($dryOutput, 'Aucun joueur à évaluer') || str_starts_with($dryOutput, '0 ligne')) {
                if ($source === 'period') {
                    $periodWithoutScore = true;
                }
                continue;
            }

            $code = Artisan::call('role-eval:compute', $arguments);
            $output = trim(Artisan::output());
            if ($code !== 0) {
                return back()->with('error', 'Échec du calcul rôle/apport : '.$output);
            }
            $messages[] = ($source === 'period' ? 'profils de saison importés — ' : 'matchs — ').$output;
        }

        if ($messages === []) {
            return back()->with('error', ! empty($periodWithoutScore)
                ? 'Profils de saison lus, mais aucun score n’atteint la fiabilité minimale : la référence par poste est trop petite. Importez les exports « Player statistics » des autres clubs de la compétition pour comparer chaque joueur aux joueurs de son poste.'
                : 'Aucune donnée de performance éligible n’est disponible pour ce calcul.');
        }

        return back()->with('success', 'Évaluations calculées : '.implode(' ; ', $messages));
    }

    private function roleEvaluationStatus(): array
    {
        $publishedConfigs = DB::table('role_config_versions')
            ->where('status', 'published')
            ->orderByDesc('id')
            ->get();

        return [
            'participations' => DB::table('match_participations')->count(),
            'stats' => DB::table('player_match_detailed_stats')->count(),
            'evaluations' => DB::table('player_role_evaluations')->count(),
            'players_evaluated' => DB::table('player_role_evaluations')->distinct()->count('player_id'),
            'published_configs' => $publishedConfigs,
            'draft_configs' => DB::table('role_config_versions')->where('status', 'draft')->orderByDesc('id')->get(),
        ];
    }
}
