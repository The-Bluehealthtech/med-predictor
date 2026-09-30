<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Test de bout en bout du Livrable 2 (commande role-eval:import), sur le
 * fichier d'exemple réel documenté (docs/role-evaluation/livrable-2-importer/).
 *
 * DatabaseTransactions, jamais RefreshDatabase (phpunit.xml pointe la vraie
 * base de dev) — voir RoleEvaluationSchemaTest pour la même précaution.
 *
 * Suppose que les migrations du Livrable 1 et du Livrable 2
 * (2026_09_30_09*.php et 2026_09_30_100000_*.php) ont déjà été appliquées.
 *
 * Non exécuté par moi (aucun PHP disponible dans l'environnement de
 * rédaction) — voir docs/role-evaluation/03-implementation-livrable-2.md.
 */
class RoleEvaluationImportCommandTest extends TestCase
{
    use DatabaseTransactions;

    private string $mappingPath;
    private string $csvPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mappingPath = base_path('docs/role-evaluation/livrable-2-importer/example-mapping-participations.json');
        $this->csvPath = base_path('docs/role-evaluation/livrable-2-importer/example-source-participations.csv');
    }

    /**
     * Crée les données de référence (équipes, match, joueurs) qui
     * correspondent EXACTEMENT au fichier d'exemple, pour pouvoir importer
     * le fichier réel livré en documentation sans le dupliquer dans le test.
     */
    private function seedFixturesMatchingExampleFile(): MatchModel
    {
        // Le mapping d'exemple résout le match et l'équipe via le FIFA
        // Connect ID des CLUBS (décision du 30/09 : identifiant imposé aux
        // fournisseurs), jamais via le nom d'équipe ("First Team" existe
        // pour tous les clubs en base, donc ambigu).
        $homeClub = Club::factory()->create(['fifa_connect_id' => 'CLUB-FIFA-001']);
        $awayClub = Club::factory()->create(['fifa_connect_id' => 'CLUB-FIFA-002']);

        $home = Team::factory()->create(['club_id' => $homeClub->id]);
        $away = Team::factory()->create(['club_id' => $awayClub->id]);

        $match = MatchModel::factory()->create([
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'home_club_id' => $homeClub->id,
            'away_club_id' => $awayClub->id,
            'match_date' => '2026-08-01',
        ]);

        Player::factory()->create(['fifa_connect_id' => 'FIFA-000123']);
        Player::factory()->create(['fifa_connect_id' => 'FIFA-000456']);
        Player::factory()->create(['fifa_connect_id' => 'FIFA-000789']);
        Player::factory()->create(['fifa_connect_id' => 'FIFA-000999']);

        return $match;
    }

    public function test_example_file_imports_valid_rows_and_rejects_invalid_ones_with_reasons(): void
    {
        $match = $this->seedFixturesMatchingExampleFile();

        $exitCode = Artisan::call('role-eval:import', [
            'type' => 'participations',
            'file' => $this->csvPath,
            '--mapping' => $this->mappingPath,
        ]);

        $this->assertSame(0, $exitCode);

        // 4 lignes dans le fichier d'exemple : 2 valides, 2 rejetées
        // (poste 'ZZZ' inconnu, minute_sortie=190 hors [0,130]).
        $rows = DB::table('match_participations')->where('match_id', $match->id)->get();
        $this->assertCount(2, $rows);

        $row1 = $rows->firstWhere('jersey_number', 6);
        $this->assertNotNull($row1);
        $this->assertSame('CDM', $row1->detailed_position);
        $this->assertSame(1, (int) $row1->is_starter);
        $this->assertSame(90, $row1->minute_out);
        $this->assertSame(0, (int) $row1->is_demo);

        $row2 = $rows->firstWhere('jersey_number', 2);
        $this->assertNotNull($row2);
        $this->assertNull($row2->minute_out, "'Données non disponibles' doit devenir NULL, jamais 0.");

        $batch = DB::table('import_batches')->orderByDesc('id')->first();
        $this->assertSame(4, $batch->rows_read);
        $this->assertSame(2, $batch->rows_imported);
        $this->assertSame(2, $batch->rows_rejected);

        $report = json_decode($batch->report, true);
        $this->assertCount(2, $report['rejections']);
        $reasonsJoined = implode(' ', array_merge(...array_column($report['rejections'], 'reasons')));
        $this->assertStringContainsString('code de poste', $reasonsJoined);
        $this->assertStringContainsString('130', $reasonsJoined);
    }

    public function test_re_running_the_same_import_does_not_duplicate_rows(): void
    {
        $match = $this->seedFixturesMatchingExampleFile();

        Artisan::call('role-eval:import', [
            'type' => 'participations',
            'file' => $this->csvPath,
            '--mapping' => $this->mappingPath,
        ]);
        $firstCount = DB::table('match_participations')->where('match_id', $match->id)->count();

        Artisan::call('role-eval:import', [
            'type' => 'participations',
            'file' => $this->csvPath,
            '--mapping' => $this->mappingPath,
        ]);
        $secondCount = DB::table('match_participations')->where('match_id', $match->id)->count();

        $this->assertSame(2, $firstCount);
        $this->assertSame($firstCount, $secondCount, "Rejouer le même import ne doit créer aucun doublon.");

        // Deux lots distincts sont bien tracés (l'historique des imports),
        // même si aucune ligne de participation n'a été dupliquée.
        $batchCount = DB::table('import_batches')->count();
        $this->assertGreaterThanOrEqual(2, $batchCount);
    }

    public function test_dry_run_writes_nothing(): void
    {
        $match = $this->seedFixturesMatchingExampleFile();

        $batchesBefore = DB::table('import_batches')->count();
        $participationsBefore = DB::table('match_participations')->where('match_id', $match->id)->count();

        Artisan::call('role-eval:import', [
            'type' => 'participations',
            'file' => $this->csvPath,
            '--mapping' => $this->mappingPath,
            '--dry-run' => true,
        ]);

        $this->assertSame($participationsBefore, DB::table('match_participations')->where('match_id', $match->id)->count());
        $this->assertSame($batchesBefore, DB::table('import_batches')->count(), 'Le dry-run ne doit laisser aucun lot en base.');
    }

    public function test_unknown_player_is_rejected_with_an_explicit_reason(): void
    {
        // Match et équipes présents, mais AUCUN joueur créé : toutes les
        // lignes doivent être rejetées avec une raison "joueur : ...".
        $homeClub = Club::factory()->create(['fifa_connect_id' => 'CLUB-FIFA-001']);
        $awayClub = Club::factory()->create(['fifa_connect_id' => 'CLUB-FIFA-002']);
        $home = Team::factory()->create(['club_id' => $homeClub->id]);
        $away = Team::factory()->create(['club_id' => $awayClub->id]);
        MatchModel::factory()->create([
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'home_club_id' => $homeClub->id,
            'away_club_id' => $awayClub->id,
            'match_date' => '2026-08-01',
        ]);

        Artisan::call('role-eval:import', [
            'type' => 'participations',
            'file' => $this->csvPath,
            '--mapping' => $this->mappingPath,
        ]);

        $batch = DB::table('import_batches')->orderByDesc('id')->first();
        $this->assertSame(0, $batch->rows_imported);
        $this->assertSame(4, $batch->rows_rejected);

        $report = json_decode($batch->report, true);
        $reasonsJoined = implode(' ', array_merge(...array_column($report['rejections'], 'reasons')));
        $this->assertStringContainsString('joueur', $reasonsJoined);
    }
}
