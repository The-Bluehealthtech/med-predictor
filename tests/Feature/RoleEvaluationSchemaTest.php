<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Vérifie le schéma cible du Livrable 1 (évaluation "rôle et apport" +
 * fondation cockpit) une fois les migrations 2026_09_30_09* appliquées.
 *
 * Utilise DatabaseTransactions (comme AdministrationViewSmokeTest), jamais
 * RefreshDatabase : phpunit.xml pointe DB_DATABASE sur
 * database/database.sqlite, la base de dev elle-même. RefreshDatabase (ou
 * migrate:fresh) la videndrait entièrement, ce que le mandat interdit
 * explicitement ("Ne supprime ni ne modifie aucune table ou donnée
 * existante sans mon accord écrit"). Ce test ne fait qu'inspecter le
 * schéma et lire/écrire dans une transaction annulée en fin de test.
 *
 * Pré-requis pour l'exécuter : `php artisan migrate` doit avoir été lancé
 * au préalable (voir docs/role-evaluation/02-implementation-livrable-1.md).
 * Non exécuté par moi : aucun binaire PHP disponible dans cet environnement.
 */
class RoleEvaluationSchemaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_new_reference_tables_exist_with_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('position_catalog'));
        $this->assertTrue(Schema::hasColumns('position_catalog', [
            'code', 'label_fr', 'label_en', 'family', 'broad_group', 'display_order',
        ]));

        $this->assertTrue(Schema::hasTable('event_types'));
        $this->assertTrue(Schema::hasColumns('event_types', ['code', 'label_fr', 'label_en']));

        $this->assertTrue(Schema::hasTable('import_batches'));
        $this->assertTrue(Schema::hasColumns('import_batches', [
            'batch_type', 'source_label', 'status', 'seed', 'params',
            'started_at', 'finished_at', 'created_by',
        ]));
    }

    public function test_position_catalog_contains_the_twelve_mandated_codes(): void
    {
        $codes = DB::table('position_catalog')->pluck('broad_group', 'code')->toArray();

        $expected = [
            'GK' => 'GK',
            'LCB' => 'DEF', 'RCB' => 'DEF', 'LB' => 'DEF', 'RB' => 'DEF',
            'CDM' => 'MID', 'LCM' => 'MID', 'CAM' => 'MID', 'RCAM' => 'MID',
            'LAM' => 'MID', 'RAM' => 'MID', // hypothèse à confirmer (Livrable 1, §9)
            'CF' => 'FWD',
        ];

        $this->assertCount(12, $codes, 'position_catalog doit contenir exactement les 12 codes fournis dans le mandat, aucun ajouté de ma propre initiative.');
        $this->assertSame($expected, $codes);
    }

    public function test_event_types_contains_the_seven_mandated_codes(): void
    {
        $codes = DB::table('event_types')->pluck('code')->sort()->values()->toArray();

        $this->assertSame([
            'goal', 'own_goal', 'pass', 'red_card',
            'second_yellow_card', 'substitution', 'yellow_card',
        ], $codes);
    }

    public function test_match_participations_table_has_expected_shape(): void
    {
        $this->assertTrue(Schema::hasTable('match_participations'));
        $this->assertTrue(Schema::hasColumns('match_participations', [
            'match_id', 'player_id', 'team_id', 'detailed_position',
            'is_starter', 'minute_in', 'minute_out', 'jersey_number',
            'is_demo', 'import_batch_id',
        ]));
    }

    public function test_match_team_stats_table_has_expected_shape(): void
    {
        $this->assertTrue(Schema::hasTable('match_team_stats'));
        $this->assertTrue(Schema::hasColumns('match_team_stats', [
            'match_id', 'team_id', 'is_home', 'possession_pct',
            'shots_total', 'shots_on_target', 'corners', 'fouls', 'offsides',
            'yellow_cards', 'red_cards', 'expected_goals', 'goals_scored',
            'is_demo', 'import_batch_id',
        ]));
    }

    public function test_player_match_detailed_stats_gained_raw_and_gk_columns_without_losing_existing_ones(): void
    {
        // Colonnes historiques : preuve que l'extension n'a rien supprimé.
        $this->assertTrue(Schema::hasColumns('player_match_detailed_stats', [
            'player_id', 'match_id', 'team_id', 'position_played', 'minutes_played',
            'shots_total', 'passes_total', 'tackles_total', 'match_rating',
        ]));

        // Nouvelles colonnes brutes (Livrable 1, §2)
        $this->assertTrue(Schema::hasColumns('player_match_detailed_stats', [
            'expected_goals', 'expected_goals_on_target',
            'progressive_passes', 'progressive_passes_completed',
            'challenges_defensive', 'challenges_defensive_won',
            'challenges_attacking', 'challenges_attacking_won',
            'dribbles_final_third', 'dribbles_final_third_completed',
            'chances_created', 'mistakes_leading_to_shot', 'mistakes_leading_to_goal',
            'is_demo', 'import_batch_id',
        ]));

        // Colonnes gardien, option A (Livrable 1, §2)
        $this->assertTrue(Schema::hasColumns('player_match_detailed_stats', [
            'gk_shots_faced', 'gk_shots_faced_on_target', 'gk_saves', 'gk_goals_conceded',
            'gk_expected_goals_faced', 'gk_claims_exits', 'gk_long_passes',
            'gk_long_passes_completed', 'gk_errors',
        ]));
    }

    public function test_match_events_gained_pass_recipient_and_zone_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('match_events', [
            'type', 'event_type', 'assisted_by_player_id', 'substituted_player_id', // historiques, inchangées
            'recipient_player_id', 'origin_zone', 'destination_zone', 'is_successful',
            'is_demo', 'import_batch_id',
        ]));
    }

    public function test_role_configuration_and_evaluation_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('role_config_versions'));
        $this->assertTrue(Schema::hasColumns('role_config_versions', [
            'label', 'description', 'status', 'published_at', 'created_by',
        ]));

        $this->assertTrue(Schema::hasTable('role_config_weights'));
        $this->assertTrue(Schema::hasColumns('role_config_weights', [
            'role_config_version_id', 'position_family', 'dimension_key', 'weight',
        ]));

        $this->assertTrue(Schema::hasTable('player_role_evaluations'));
        $this->assertTrue(Schema::hasColumns('player_role_evaluations', [
            'player_id', 'match_id', 'period_start', 'period_end',
            'position_family_evaluated', 'role_config_version_id', 'model_version',
            'score', 'reliability', 'interval_low', 'interval_high', 'role_fit_score',
            'is_demo', 'source_import_batch_id', 'source_demo_batch_id', 'computed_at',
        ]));
    }

    public function test_matches_players_teams_gained_is_demo_without_losing_existing_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('matches', [
            'home_score', 'away_score', 'is_demo', 'import_batch_id', // historiques + nouvelles
        ]));
        $this->assertTrue(Schema::hasColumns('players', [
            'first_name', 'last_name', 'position', 'is_demo', 'import_batch_id',
        ]));
        $this->assertTrue(Schema::hasColumns('teams', [
            'name', 'is_demo', 'import_batch_id',
        ]));
    }

    public function test_role_config_weights_rejects_duplicate_dimension_for_same_version_and_family(): void
    {
        $versionId = DB::table('role_config_versions')->insertGetId([
            'label' => 'test-unique-constraint',
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role_config_weights')->insert([
            'role_config_version_id' => $versionId,
            'position_family' => 'gardien',
            'dimension_key' => 'gk_shot_stopping',
            'weight' => 0.5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('role_config_weights')->insert([
            'role_config_version_id' => $versionId,
            'position_family' => 'gardien',
            'dimension_key' => 'gk_shot_stopping',
            'weight' => 0.9,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_match_participations_rejects_duplicate_player_for_same_match(): void
    {
        $match = DB::table('matches')->first();
        $player = DB::table('players')->first();
        $team = DB::table('teams')->first();

        if (! $match || ! $player || ! $team) {
            $this->markTestSkipped('Nécessite au moins un match, un joueur et une équipe existants en base.');
        }

        DB::table('match_participations')->insert([
            'match_id' => $match->id,
            'player_id' => $player->id,
            'team_id' => $team->id,
            'detailed_position' => 'CDM',
            'is_starter' => true,
            'is_demo' => true, // ligne de test, jamais une vraie donnée
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('match_participations')->insert([
            'match_id' => $match->id,
            'player_id' => $player->id,
            'team_id' => $team->id,
            'detailed_position' => 'CAM',
            'is_starter' => false,
            'is_demo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
