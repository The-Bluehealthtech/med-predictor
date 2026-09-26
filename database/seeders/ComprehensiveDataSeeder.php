<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ComprehensiveDataSeeder extends Seeder
{
    private const REFERENCE_DATE = '2026-09-01';

    private int $userId;
    private int $competitionId;
    private array $columnCache = [];

    public function run(): void
    {
        $this->assertBaseData();

        // Canonical portal coverage for every player.
        $this->call(PlayerPortalDataSeeder::class);

        $this->seedCompetitionRelations();

        DB::table('players')
            ->orderBy('id')
            ->chunkById(100, function ($players): void {
                DB::transaction(function () use ($players): void {
                    foreach ($players as $player) {
                        $this->seedPlayerScopedData($player);
                    }
                });
            });

        $this->seedTransfers();
        $this->seedCompetitionStandings();
        $this->seedWorkflowData();
        $this->seedRefereeReports();

        $this->command?->info(
            'Comprehensive deterministic synthetic demo data seeded.'
        );
    }

    private function assertBaseData(): void
    {
        $user = DB::table('users')->orderBy('id')->first();
        $competition = DB::table('competitions')->orderBy('id')->first();
        $players = DB::table('players')->count();

        if (!$user || !$competition || $players === 0) {
            throw new \RuntimeException(
                'Base data missing: user, competition and players are required.'
            );
        }

        $linkedAthletes = Schema::hasTable('athletes')
            ? DB::table('athletes')->whereNotNull('player_id')->count()
            : 0;

        if ($linkedAthletes !== $players) {
            throw new \RuntimeException(
                "Player/Athlete bridge incomplete: players={$players}, athletes={$linkedAthletes}."
            );
        }

        $this->userId = (int) $user->id;
        $this->competitionId = (int) $competition->id;
    }

    private function seedCompetitionRelations(): void
    {
        if (!Schema::hasTable('competition_team')) {
            return;
        }

        $competitions = DB::table('competitions')
            ->orderBy('id')
            ->get();

        DB::table('teams')
            ->orderBy('id')
            ->get()
            ->each(function ($team) use ($competitions): void {
                $associationId = null;

                if ($this->hasColumn('teams', 'club_id') && $team->club_id) {
                    $associationId = DB::table('clubs')
                        ->where('id', $team->club_id)
                        ->value('association_id');
                }

                $competition = $competitions->first(
                    fn ($item) =>
                        $associationId !== null
                        && property_exists($item, 'association_id')
                        && (int) $item->association_id === (int) $associationId
                ) ?? $competitions->first();

                if (!$competition) {
                    return;
                }

                $this->put('competition_team', [
                    'competition_id' => (int) $competition->id,
                    'team_id' => (int) $team->id,
                ], [
                    'joined_at' => self::REFERENCE_DATE . ' 00:00:00',
                    'season_id' => DB::table('seasons')->orderBy('id')->value('id'),
                ]);
            });
    }

    private function seedPlayerScopedData(object $player): void
    {
        $id = (int) $player->id;
        $athlete = DB::table('athletes')
            ->where('player_id', $id)
            ->first();

        if (!$athlete) {
            throw new \RuntimeException("Missing athlete for player {$id}.");
        }

        $teamId = $this->value($player, 'team_id');
        $clubId = $this->value($player, 'club_id');

        if ($teamId && Schema::hasTable('team_players')) {
            $this->put('team_players', [
                'team_id' => (int) $teamId,
                'player_id' => $id,
            ], [
                'role' => $id % 3 === 0 ? 'starter' : 'substitute',
                'squad_number' => null,
                'joined_date' => '2026-07-01',
                'contract_end_date' => '2027-06-30',
                'position_preference' => $this->value($player, 'position'),
                'notes' => 'synthetic_demo team membership',
                'status' => 'active',
            ]);
        }

        $this->seedAppointments($id, (int) $athlete->id);
        $this->seedHealthExtensions($id, (int) $athlete->id);
        $this->seedPerformanceExtensions($player);
        $this->seedContract($id, $clubId);
    }

    private function seedAppointments(int $playerId, int $athleteId): void
    {
        if (!Schema::hasTable('appointments')) {
            return;
        }

        $day = 1 + ($playerId % 20);
        $date = sprintf('2026-10-%02d 10:00:00', $day);

        $this->put('appointments', [
            'athlete_id' => $athleteId,
            'appointment_date' => $date,
            'appointment_type' => 'routine_checkup',
        ], [
            'doctor_id' => $this->userId,
            'created_by' => $this->userId,
            'duration_minutes' => 30,
            'status' => 'Planifié',
            'reason' => 'Synthetic demo routine check-up',
            'notes' => 'synthetic_demo; not a clinical appointment',
            'reminder_settings' => $this->json([
                'synthetic_demo' => true,
                'email_reminder' => false,
                'sms_reminder' => false,
            ]),
        ]);
    }

    private function seedHealthExtensions(
        int $playerId,
        int $athleteId
    ): void {
        if (Schema::hasTable('health_scores')) {
            $this->put('health_scores', [
                'athlete_id' => $athleteId,
                'calculated_date' => self::REFERENCE_DATE,
            ], [
                'score' => 74 + ($playerId % 18),
                'trend' => 'stable',
                'contributing_factors' => $this->json([
                    'synthetic_demo' => true,
                    'source' => 'portal_baseline',
                ]),
                'metrics' => $this->json([
                    'readiness' => 72 + ($playerId % 18),
                ]),
                'ai_analysis' =>
                    'Synthetic demo score; not a clinical assessment.',
            ]);
        }

        if (Schema::hasTable('immunisations')) {
            $this->put('immunisations', [
                'athlete_id' => $athleteId,
                'vaccine_code' => 'SYNTH-DEMO',
                'date_administered' => '2026-01-15 09:00:00',
            ], [
                'vaccine_name' => 'Synthetic Demo Immunisation Record',
                'dose_number' => 1,
                'total_doses' => 1,
                'route' => 'IM',
                'site' => 'LA',
                'status' => 'active',
                'notes' =>
                    'synthetic_demo; not an actual vaccination record',
                'administered_by' => $this->userId,
                'verified_by' => null,
                'source' => 'manual',
                'sync_status' => 'pending',
            ]);
        }

        if (Schema::hasTable('postural_assessments')) {
            $this->put('postural_assessments', [
                'player_id' => $playerId,
                'assessment_date' => self::REFERENCE_DATE . ' 09:00:00',
                'view' => 'anterior',
            ], [
                'user_id' => $this->userId,
                'assessment_type' => 'routine',
                'angles' => $this->json([
                    'synthetic_demo' => true,
                    'shoulder_alignment' => 88 + ($playerId % 8),
                ]),
                'clinical_notes' =>
                    'Synthetic demo postural assessment.',
                'recommendations' =>
                    'Synthetic demo mobility routine.',
                'status' => 'active',
            ]);
        }

        if (Schema::hasTable('scat_assessments')) {
            $this->put('scat_assessments', [
                'athlete_id' => $athleteId,
                'assessment_date' => self::REFERENCE_DATE . ' 09:30:00',
                'assessment_type' => 'baseline',
            ], [
                'assessor_id' => $this->userId,
                'data_json' => $this->json([
                    'synthetic_demo' => true,
                    'symptom_count' => 0,
                ]),
                'result' => 'normal',
                'concussion_confirmed' => false,
                'scat_score' => 90 + ($playerId % 8),
                'recommendations' =>
                    'Synthetic baseline only; no clinical conclusion.',
            ]);
        }

        if (
            Schema::hasTable('tue_requests')
            && $playerId % 25 === 0
        ) {
            $this->put('tue_requests', [
                'athlete_id' => $athleteId,
                'request_date' => self::REFERENCE_DATE,
                'medication' => 'Synthetic demo medication',
            ], [
                'reason' =>
                    'Synthetic demo only; not a medical claim.',
                'physician_id' => $this->userId,
                'status' => 'pending',
                'supporting_documents' => $this->json([
                    'synthetic_demo' => true,
                ]),
            ]);
        }
    }

    private function seedPerformanceExtensions(object $player): void
    {
        $id = (int) $player->id;

        if (Schema::hasTable('performance_recommendations')) {
            $this->put('performance_recommendations', [
                'player_id' => $id,
                'type' => 'recovery',
                'title' => 'Synthetic demo recovery plan',
            ], [
                'description' =>
                    'Synthetic recommendation for interface testing only.',
                'priority' => 'medium',
                'status' => 'pending',
                'target_date' => '2026-10-15',
                'ai_analysis_data' => $this->json([
                    'synthetic_demo' => true,
                ]),
            ]);
        }

        if (Schema::hasTable('training_sessions')) {
            $day = 1 + ($id % 20);

            $this->put('training_sessions', [
                'player_id' => $id,
                'session_date' => sprintf('2026-10-%02d', $day),
                'title' => 'Synthetic demo training session',
            ], [
                'description' =>
                    'Synthetic training session for feature testing.',
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'priority' => 'medium',
                'type' => 'physical',
                'location' => 'Synthetic Demo Training Center',
                'coach' => 'Synthetic Demo Coach',
                'is_mandatory' => false,
            ]);
        }

        if (!Schema::hasTable('match_performances')) {
            return;
        }

        $position = (string) (
            $this->value($player, 'position') ?: 'MID'
        );
        $result = ['W', 'D', 'L'][$id % 3];

        $this->put('match_performances', [
            'player_id' => $id,
            'match_date' => '2026-08-24',
            'opponent' => 'Synthetic Demo Opponent',
        ], [
            'result' => $result,
            'competition' => 'Synthetic Demo Match',
            'venue' => 'Synthetic Demo Venue',
            'goals_scored' => $position === 'GK' ? 0 : $id % 3,
            'assists' => $position === 'GK' ? 0 : ($id + 1) % 3,
            'rating' => round(6.4 + (($id % 25) / 10), 1),
            'minutes_played' => 70 + ($id % 21),
            'notes' => 'synthetic_demo; not an official match',
        ]);

        $performance = DB::table('match_performances')
            ->where('player_id', $id)
            ->where('match_date', '2026-08-24')
            ->where('opponent', 'Synthetic Demo Opponent')
            ->first();

        if (!$performance || !Schema::hasTable('match_metrics')) {
            return;
        }

        $shotsOnTarget = $position === 'GK' ? 0 : 1 + ($id % 4);
        $totalShots = $shotsOnTarget + ($id % 3);

        $this->put('match_metrics', [
            'match_performance_id' => (int) $performance->id,
        ], [
            'shots_on_target' => $shotsOnTarget,
            'total_shots' => $totalShots,
            'shot_accuracy' => $totalShots > 0
                ? round(($shotsOnTarget / $totalShots) * 100, 1)
                : 0,
            'key_passes' => $id % 5,
            'successful_crosses' => $id % 4,
            'successful_dribbles' => 2 + ($id % 5),
            'distance' => round(8.5 + (($id % 35) / 10), 1),
            'max_speed' => round(27 + (($id % 60) / 10), 1),
            'avg_speed' => round(7 + (($id % 30) / 10), 1),
            'sprints' => 12 + ($id % 14),
            'accelerations' => 18 + ($id % 16),
            'decelerations' => 14 + ($id % 12),
            'direction_changes' => 25 + ($id % 20),
            'jumps' => 4 + ($id % 8),
            'pass_accuracy' => 72 + ($id % 22),
            'long_passes' => 2 + ($id % 5),
            'crosses' => $id % 5,
            'tackles' => $position === 'GK' ? 0 : 1 + ($id % 6),
            'interceptions' => $position === 'FWD' ? $id % 2 : 1 + ($id % 5),
            'clearances' => in_array($position, ['DEF', 'CB', 'LB', 'RB'], true)
                ? 2 + ($id % 6)
                : $id % 2,
        ]);
    }

    private function seedContract(int $playerId, mixed $clubId): void
    {
        if (!$clubId || !Schema::hasTable('contracts')) {
            return;
        }

        $this->put('contracts', [
            'player_id' => $playerId,
            'club_id' => (int) $clubId,
            'start_date' => '2026-07-01',
        ], [
            'contract_type' => 'permanent',
            'end_date' => '2027-06-30',
            'is_active' => true,
            'salary' => 2500 + (($playerId % 30) * 100),
            'bonus' => 250 + (($playerId % 10) * 25),
            'currency' => 'EUR',
            'payment_frequency' => 'monthly',
            'clauses' => $this->json([
                'synthetic_demo' => true,
            ]),
            'special_conditions' =>
                'Synthetic demo contract; not an official agreement.',
            'fifa_contract_id' => null,
            'fifa_contract_data' => null,
            'created_by' => $this->userId,
            'updated_by' => $this->userId,
        ]);

        if (Schema::hasTable('player_trophies')) {
            $this->put('player_trophies', [
                'player_id' => $playerId,
                'trophy_name' => 'Synthetic Demo Recognition',
                'year' => 2026,
            ], [
                'trophy_type' => 'demo',
                'competition' => 'Synthetic Demo Competition',
                'club' => DB::table('clubs')
                    ->where('id', $clubId)
                    ->value('name'),
                'description' =>
                    'Synthetic demo achievement; not a real sporting result.',
            ]);
        }
    }

    private function seedTransfers(): void
    {
        if (!Schema::hasTable('transfers')) {
            return;
        }

        $clubs = DB::table('clubs')->orderBy('id')->pluck('id')->values();
        if ($clubs->count() < 2) {
            return;
        }

        DB::table('players')
            ->whereNotNull('club_id')
            ->orderBy('id')
            ->limit(24)
            ->get()
            ->each(function ($player, $index) use ($clubs): void {
                $origin = (int) $player->club_id;
                $destination = (int) $clubs[($index + 1) % $clubs->count()];

                if ($destination === $origin) {
                    $destination = (int) $clubs[($index + 2) % $clubs->count()];
                }

                $this->put('transfers', [
                    'player_id' => (int) $player->id,
                    'transfer_date' => '2026-09-15',
                    'transfer_type' => 'permanent',
                ], [
                    'club_origin_id' => $origin,
                    'club_destination_id' => $destination,
                    'transfer_status' => 'draft',
                    'itc_status' => 'not_requested',
                    'transfer_window_start' => '2026-09-01',
                    'transfer_window_end' => '2026-10-31',
                    'contract_start_date' => '2026-11-01',
                    'contract_end_date' => '2027-10-31',
                    'transfer_fee' => 100000 + ((int) $player->id * 100),
                    'currency' => 'EUR',
                    'payment_status' => 'pending',
                    'fifa_transfer_id' => null,
                    'fifa_itc_id' => null,
                    'fifa_payload' => null,
                    'fifa_response' => null,
                    'is_minor_transfer' => false,
                    'is_international' => false,
                    'special_conditions' =>
                        'Synthetic demo transfer; no official clearance.',
                    'notes' => 'synthetic_demo',
                    'created_by' => $this->userId,
                    'updated_by' => $this->userId,
                ]);
            });
    }

    private function seedCompetitionStandings(): void
    {
        if (
            !Schema::hasTable('standings')
            || !Schema::hasTable('competition_team')
        ) {
            return;
        }

        $seasonId = Schema::hasTable('seasons')
            ? DB::table('seasons')->orderBy('id')->value('id')
            : null;

        DB::table('competition_team')
            ->orderBy('competition_id')
            ->orderBy('team_id')
            ->get()
            ->groupBy('competition_id')
            ->each(function ($rows, $competitionId) use ($seasonId): void {
                foreach ($rows->values() as $index => $row) {
                    $played = 8;
                    $won = 2 + ((int) $row->team_id % 4);
                    $drawn = (int) $row->team_id % 3;
                    $lost = max(0, $played - $won - $drawn);
                    $goalsFor = 8 + ((int) $row->team_id % 10);
                    $goalsAgainst = 5 + ((int) $row->team_id % 8);

                    $this->put('standings', [
                        'competition_id' => (int) $competitionId,
                        'team_id' => (int) $row->team_id,
                    ], [
                        'season_id' => $seasonId,
                        'played' => $played,
                        'won' => $won,
                        'drawn' => $drawn,
                        'lost' => $lost,
                        'goals_for' => $goalsFor,
                        'goals_against' => $goalsAgainst,
                        'goal_difference' => $goalsFor - $goalsAgainst,
                        'points' => ($won * 3) + $drawn,
                        'position' => $index + 1,
                        'form' => 'DEMO',
                        'last_updated' => self::REFERENCE_DATE . ' 12:00:00',
                    ]);
                }

                if (Schema::hasTable('competition_rankings')) {
                    $this->put('competition_rankings', [
                        'competition_id' => (int) $competitionId,
                        'round' => 1,
                    ], [
                        'standings' => $this->json([
                            'synthetic_demo' => true,
                            'generated_from' => 'standings',
                            'team_count' => $rows->count(),
                        ]),
                    ]);
                }
            });
    }

    private function seedWorkflowData(): void
    {
        if (Schema::hasTable('registration_requests')) {
            for ($i = 1; $i <= 6; $i++) {
                $this->put('registration_requests', [
                    'email' => "synthetic.demo+{$i}@example.invalid",
                ], [
                    'association' => 'Synthetic Demo Association',
                    'profile_type' => 'player',
                    'organization' => 'Synthetic Demo Club',
                    'first_name' => 'Synthetic',
                    'last_name' => "Demo {$i}",
                    'phone' => null,
                    'reason' =>
                        'Synthetic demo registration request for UI testing.',
                    'status' => ['pending', 'approved', 'rejected'][$i % 3],
                    'admin_notes' => 'synthetic_demo',
                    'reviewed_at' => $i % 3 === 0
                        ? self::REFERENCE_DATE . ' 12:00:00'
                        : null,
                    'reviewed_by' => $i % 3 === 0
                        ? $this->userId
                        : null,
                ]);
            }
        }

        if (Schema::hasTable('system_settings')) {
            $settings = [
                ['demo_data_enabled', 'Synthetic demo data enabled', 'true', 'boolean'],
                ['demo_reference_date', 'Synthetic demo reference date', self::REFERENCE_DATE, 'string'],
                ['demo_notice', 'Synthetic data notice', 'Data marked synthetic_demo is not official, clinical, FIFA or WADA data.', 'string'],
            ];

            foreach ($settings as [$key, $name, $value, $type]) {
                $this->put('system_settings', ['key' => $key], [
                    'name' => $name,
                    'description' => 'Synthetic demo environment setting.',
                    'value' => $value,
                    'type' => $type,
                    'group' => 'demo',
                    'is_public' => true,
                    'is_editable' => true,
                    'is_required' => false,
                    'default_value' => $value,
                    'updated_by' => $this->userId,
                ]);
            }
        }

        if (Schema::hasTable('audit_trails')) {
            for ($i = 1; $i <= 12; $i++) {
                $requestId = sprintf('SYNTH-DEMO-%03d', $i);

                $this->put('audit_trails', [
                    'request_id' => $requestId,
                ], [
                    'user_id' => $this->userId,
                    'action' => $i % 2 === 0 ? 'view' : 'update',
                    'model_type' => 'Player',
                    'model_id' => $i,
                    'table_name' => 'players',
                    'event_type' => 'user_action',
                    'severity' => 'info',
                    'description' =>
                        'Synthetic demo audit event; not a real user action.',
                    'metadata' => $this->json([
                        'synthetic_demo' => true,
                    ]),
                    'ip_address' => '192.0.2.' . $i,
                    'user_agent' => 'SyntheticDemo/1.0',
                    'session_id' => 'synthetic-demo',
                    'request_method' => 'GET',
                    'request_url' => '/synthetic-demo/player/' . $i,
                    'occurred_at' => self::REFERENCE_DATE . ' 12:00:00',
                ]);
            }
        }
    }

    private function seedRefereeReports(): void
    {
        if (
            !Schema::hasTable('referee_reports')
            || !Schema::hasTable('matches')
        ) {
            return;
        }

        DB::table('matches')
            ->orderBy('id')
            ->limit(5)
            ->get()
            ->each(function ($match, $index): void {
                $this->put('referee_reports', [
                    'match_id' => (int) $match->id,
                    'referee_id' => $this->userId,
                ], [
                    'competition_name' => 'Synthetic Demo Competition',
                    'home_team' => 'Synthetic Demo Home',
                    'away_team' => 'Synthetic Demo Away',
                    'match_date' => '2026-08-' . sprintf('%02d', 10 + $index),
                    'kickoff_time' => '15:00:00',
                    'venue' => 'Synthetic Demo Venue',
                    'attendance' => 1000 + ($index * 250),
                    'weather' => 'Synthetic demo conditions',
                    'pitch_condition' => 'good',
                    'main_referee' => 'Synthetic Demo Referee',
                    'assistant_referee_1' => 'Synthetic Demo Assistant 1',
                    'assistant_referee_2' => 'Synthetic Demo Assistant 2',
                    'fourth_official' => 'Synthetic Demo Fourth Official',
                    'final_score' => '1-1',
                    'half_time_score' => '0-0',
                    'extra_time_minutes' => 0,
                    'penalty_shootout' => false,
                    'goals' => $this->json(['synthetic_demo' => true]),
                    'yellow_cards' => $this->json([]),
                    'red_cards' => $this->json([]),
                    'substitutions' => $this->json([]),
                    'injuries' => $this->json([]),
                    'general_comments' =>
                        'Synthetic demo draft report; not an official referee report.',
                    'match_quality_assessment' =>
                        'Synthetic demo assessment.',
                    'match_rating' => 7,
                    'status' => 'draft',
                    'electronic_signature' => null,
                ]);
            });
    }

    private function put(
        string $table,
        array $where,
        array $values
    ): void {
        if (!Schema::hasTable($table)) {
            return;
        }

        $safeWhere = $this->filter($table, $where);

        if (count($safeWhere) !== count($where)) {
            $missing = array_diff(
                array_keys($where),
                array_keys($safeWhere)
            );

            throw new \RuntimeException(
                $table . ': missing key column(s): '
                . implode(', ', $missing)
            );
        }

        $safeValues = $this->filter($table, $values);

        if ($this->hasColumn($table, 'updated_at')) {
            $safeValues['updated_at'] = now();
        }

        $exists = DB::table($table)
            ->where($safeWhere)
            ->exists();

        if (!$exists && $this->hasColumn($table, 'created_at')) {
            $safeValues['created_at'] = now();
        }

        DB::table($table)->updateOrInsert(
            $safeWhere,
            $safeValues
        );
    }

    private function filter(string $table, array $data): array
    {
        $columns = $this->columns($table);

        return array_filter(
            $data,
            static fn ($value, $key) =>
                in_array($key, $columns, true),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function columns(string $table): array
    {
        return $this->columnCache[$table]
            ??= Schema::getColumnListing($table);
    }

    private function hasColumn(
        string $table,
        string $column
    ): bool {
        return in_array(
            $column,
            $this->columns($table),
            true
        );
    }

    private function value(
        object $row,
        string $property,
        mixed $default = null
    ): mixed {
        return property_exists($row, $property)
            && $row->{$property} !== null
                ? $row->{$property}
                : $default;
    }

    private function json(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        );
    }
}
