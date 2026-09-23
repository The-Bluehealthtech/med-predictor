<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlayerPortalDataSeeder extends Seeder
{
    private const REFERENCE_DATE = '2026-09-01';

    private int $systemUserId;
    private int $competitionId;
    private array $columnCache = [];

    public function run(): void
    {
        $user = DB::table('users')->orderBy('id')->first();

        if (!$user) {
            throw new \RuntimeException(
                'Aucun utilisateur disponible pour user_id/assessor_id/created_by.'
            );
        }

        $competition = DB::table('competitions')->orderBy('id')->first();

        if (!$competition) {
            throw new \RuntimeException(
                'Aucune compétition disponible pour player_season_stats.'
            );
        }

        $playerCount = DB::table('players')->count();
        $linkedAthletes = DB::table('athletes')
            ->whereNotNull('player_id')
            ->count();

        if ($playerCount === 0 || $linkedAthletes !== $playerCount) {
            throw new \RuntimeException(
                "Bridge incomplet: players={$playerCount}, athletes={$linkedAthletes}."
            );
        }

        $this->systemUserId = (int) $user->id;
        $this->competitionId = (int) $competition->id;

        DB::table('players')
            ->orderBy('id')
            ->chunkById(100, function ($players) {
                DB::transaction(function () use ($players) {
                    foreach ($players as $player) {
                        $this->seedPlayer($player);
                    }
                });
            });

        $this->command?->info('Player portal synthetic data seeded.');
    }

    private function seedPlayer(object $player): void
    {
        $id = (int) $player->id;

        $athlete = DB::table('athletes')
            ->where('player_id', $id)
            ->first();

        if (!$athlete) {
            throw new \RuntimeException(
                "Athlete introuvable pour player_id={$id}."
            );
        }

        $recordDate = self::REFERENCE_DATE . ' 08:00:00';
        $assessmentDate = self::REFERENCE_DATE;

        $height = (float) (
            $this->value($player, 'height')
            ?: 170 + ($id % 16)
        );

        $weight = (float) (
            $this->value($player, 'weight')
            ?: 68 + ($id % 15)
        );

        $bmi = round(
            $weight / (($height / 100) ** 2),
            1
        );

        $readiness = 72 + ($id % 18);
        $restingHeartRate = 52 + ($id % 12);
        $riskScore = round((8 + ($id % 18)) / 100, 4);

        /*
         * HEALTH
         */
        $this->put('health_records', [
            'user_id' => $this->systemUserId,
            'player_id' => $id,
            'record_date' => $recordDate,
        ], [
            'risk_score' => $riskScore,
            'prediction_confidence' => 0.88,
            'status' => 'active',
            'visit_type' => 'pre_season',
            'heart_rate' => $restingHeartRate,
            'blood_pressure_systolic' => 112 + ($id % 12),
            'blood_pressure_diastolic' => 70 + ($id % 9),
            'temperature' => 36.4 + (($id % 4) / 10),
            'oxygen_saturation' => 97 + ($id % 3),
            'blood_glucose' => 82 + ($id % 12),
            'bmi' => $bmi,
            'body_weight' => $weight,
            'body_height' => $height,
            'body_fat_percentage' => 10 + ($id % 7),
            'hydration_percentage' => 67 + ($id % 10),
            'muscle_mass_percentage' => 42 + ($id % 8),
            'hemoglobin' => 14 + (($id % 8) / 10),
            'hematocrit' => 41 + ($id % 7),
            'serum_iron' => 85 + ($id % 30),
            'vitamin_d' => 32 + ($id % 12),
            'crp' => 0.5 + (($id % 10) / 10),
            'total_cholesterol' => 150 + ($id % 30),
            'notes' =>
                'synthetic_demo: not clinical data',
            'metadata' => $this->json([
                'source' => 'synthetic_demo',
                'version' => 1,
            ]),
        ]);

        $healthRecord = DB::table('health_records')
            ->where('user_id', $this->systemUserId)
            ->where('player_id', $id)
            ->where('record_date', $recordDate)
            ->first();

        if (!$healthRecord) {
            throw new \RuntimeException(
                "health_record absent pour player_id={$id}."
            );
        }

        /*
         * MEDICAL
         */
        $this->put('medical_records', [
            'player_id' => $id,
            'record_date' => $assessmentDate,
            'title' => 'Synthetic baseline medical review',
        ], [
            'record_type' => 'pre_season_assessment',
            'description' =>
                'Synthetic demo record; not a clinical record.',
            'doctor_name' => 'Synthetic Demo Clinician',
            'medical_center' => 'Synthetic Demo Medical Center',
            'status' => 'completed',
            'test_results' => $this->json([
                'synthetic_demo' => true,
            ]),
        ]);

        $this->put('medical_predictions', [
            'health_record_id' => (int) $healthRecord->id,
            'prediction_type' => 'injury_risk',
            'prediction_date' => $recordDate,
        ], [
            'player_id' => $id,
            'user_id' => $this->systemUserId,
            'predicted_condition' =>
                'Low synthetic musculoskeletal risk',
            'risk_probability' => $riskScore,
            'confidence_score' => 0.88,
            'status' => 'active',
            'ai_model_version' => 'synthetic-demo-1.0',
        ]);

        /*
         * PCMA
         */
        $this->put('pcmas', [
            'athlete_id' => (int) $athlete->id,
            'type' => 'cardio',
            'assessment_date' => $assessmentDate,
        ], [
            'player_id' => $id,
            'result_json' => $this->json([
                'synthetic_demo' => true,
                'overall_score' => 78 + ($id % 15),
                'pcma_score' => 78 + ($id % 15),
                'cardiovascular_fitness' => 76 + ($id % 18),
                'respiratory_fitness' => 77 + ($id % 17),
                'musculoskeletal_fitness' => 74 + ($id % 20),
                'neurological_fitness' => 80 + ($id % 15),
                'next_assessment_date' => '2027-09-01',
            ]),
            'status' => 'cleared',
            'assessor_id' => $this->systemUserId,
            'fifa_compliant' => false,
            'form_version' => 'synthetic-demo-1.0',
            'is_signed' => false,
        ]);

        /*
         * INJURY HISTORY
         */
        $this->put('injuries', [
            'athlete_id' => (int) $athlete->id,
            'date' => '2026-06-15',
            'type' => 'synthetic_demo_strain',
        ], [
            'player_id' => $id,
            'body_zone' => 'posterior_thigh',
            'severity' => 'mild',
            'description' =>
                'Synthetic demo historical strain.',
            'status' => 'resolved',
            'diagnosed_by' => $this->systemUserId,
        ]);

        /*
         * REAL-TIME HEALTH
         */
        $this->put('player_real_time_health', [
            'player_id' => $id,
            'measurement_time' => $recordDate,
        ], [
            'data_source' => 'smartwatch',
            'heart_rate' => $restingHeartRate + 6,
            'blood_pressure_systolic' =>
                (string) (112 + ($id % 12)),
            'blood_pressure_diastolic' =>
                (string) (70 + ($id % 9)),
            'temperature' => 36.4 + (($id % 4) / 10),
            'oxygen_saturation' => 97 + ($id % 3),
            'hydration_level' => 70 + ($id % 16),
            'respiratory_rate' => 12 + ($id % 5),
            'weight_kg' => $weight,
            'body_fat_percentage' => 10 + ($id % 7),
            'muscle_mass_kg' =>
                round($weight * 0.46, 1),
            'water_percentage' => 57 + ($id % 8),
            'bmi' => $bmi,
            'vo2_max' => 48 + ($id % 12),
            'max_heart_rate' => 184 + ($id % 12),
            'resting_heart_rate' => $restingHeartRate,
            'heart_rate_variability' => 48 + ($id % 30),
            'sleep_duration_hours' => 7 + ($id % 2),
            'sleep_efficiency' => 82 + ($id % 13),
            'sleep_quality_score' => 76 + ($id % 19),
            'stress_level' => 22 + ($id % 24),
            'mood_score' => 72 + ($id % 22),
            'energy_level' => 70 + ($id % 23),
            'recovery_score' => 70 + ($id % 21),
            'steps_count' =>
                7000 + (($id * 137) % 5000),
            'calories_burned' =>
                2100 + (($id * 19) % 700),
            'active_minutes' => 55 + ($id % 45),
            'exercise_minutes' => 45 + ($id % 35),
            'distance_walked_km' =>
                6 + (($id % 30) / 10),
            'muscle_fatigue' => 20 + ($id % 30),
            'recovery_time_hours' => 12 + ($id % 16),
            'readiness_score' => $readiness,

            'high_heart_rate_alert' => false,
            'low_oxygen_alert' => false,
            'high_temperature_alert' => false,
            'dehydration_alert' => false,
            'stress_alert' => false,

            'data_accuracy' => 96,
            'data_quality' => 'good',
            'notes' => 'synthetic_demo wearable snapshot',
        ]);

        /*
         * FITNESS
         */
        $this->put('player_fitness_logs', [
            'player_id' => $id,
            'log_date' => $assessmentDate,
        ], [
            'is_completed' => true,
            'fatigue_level' => 20 + ($id % 30),
            'energy_level' => 70 + ($id % 23),
            'muscle_soreness' => 15 + ($id % 25),
            'sleep_hours' => 7 + ($id % 2),
            'sleep_quality_score' => 76 + ($id % 19),
            'stretching_minutes' => 10 + ($id % 15),
            'notes' => 'synthetic_demo',
        ]);

        /*
         * SDOH
         */
        $this->put('player_sdoh_data', [
            'player_id' => $id,
            'assessment_date' => $assessmentDate,
            'assessment_type' => 'initial',
        ], [
            'assessment_method' => 'questionnaire',
            'assessment_reliability' => 95,
            'requires_follow_up' => false,

            'living_environment_score' => 78 + ($id % 17),
            'social_support_score' => 76 + ($id % 20),
            'healthcare_access_score' => 80 + ($id % 16),
            'financial_situation_score' => 72 + ($id % 22),
            'mental_wellbeing_score' => 75 + ($id % 20),
            'overall_sdoh_score' => 77 + ($id % 18),

            'risk_category' => 'low',
            'education_level' => 'secondary',
            'employment_status' => 'employed',
            'housing_quality' => 'good',
            'housing_stability' => 'stable',
            'income_level' => 'middle',
            'insurance_quality' => 'good',
            'relationship_status' => 'single',
            'smoking_status' => 'never',
            'alcohol_consumption' => 'none',
            'stress_level' => 'low',
            'anxiety_level' => 'low',
            'depression_level' => 'very_low',

            'general_notes' => 'synthetic_demo',
        ]);

        /*
         * ANTI-DOPING
         * Important: jamais présenté comme contrôle FIFA/WADA réel.
         */
        $this->put('doping_controls', [
            'player_id' => $id,
            'control_date' => $assessmentDate,
            'control_type' => 'synthetic_demo',
        ], [
            'location' => 'Synthetic Demo Facility',
            'result' => 'negative',
            'control_authority' =>
                'Synthetic Demo - not an official authority',
            'sample_number' =>
                sprintf('DEMO-%06d', $id),
            'substances_tested' => $this->json([]),
            'notes' =>
                'synthetic_demo; not an official doping control',
            'next_control' => '2027-03-01',
        ]);

        /*
         * DEVICE
         */
        $this->put('player_connected_devices', [
            'serial_number' =>
                sprintf('SYNTH-WEAR-%06d', $id),
        ], [
            'player_id' => $id,
            'device_name' => 'Synthetic Player Wearable',
            'device_type' => 'smartwatch',
            'device_model' => 'DemoWatch 1',
            'manufacturer' => 'Synthetic Demo',

            'is_connected' => true,
            'connection_status' => 'connected',

            'battery_level' => 60 + ($id % 40),
            'battery_status' => 'discharging',

            'sensors' =>
                $this->json(['heart_rate', 'sleep', 'activity']),
            'features' =>
                $this->json(['synthetic_demo' => true]),

            'gps_enabled' => true,
            'heart_rate_enabled' => true,
            'sleep_tracking_enabled' => true,
            'activity_tracking_enabled' => true,

            'timezone' => 'UTC',
            'language' => 'en',
            'is_authenticated' => false,
            'is_encrypted' => true,
            'status' => 'active',
            'last_sync_at' => $recordDate,
        ]);

        /*
         * PERFORMANCE
         */
        $this->put('player_performances', [
            'player_id' => $id,
            'performance_date' => $recordDate,
        ], [
            'competition_id' => $this->competitionId,
            'team_id' => $this->value($player, 'team_id'),
            'data_source' => 'assessment',
            'assessment_method' => 'coach_evaluation',
            'created_by' => $this->systemUserId,

            'sprint_30m' =>
                round(4.0 + (($id % 35) / 100), 2),
            'cooper_test' =>
                2800 + (($id * 17) % 500),
            'yoyo_test' =>
                1600 + (($id * 23) % 700),
            'wingate_test' =>
                650 + (($id * 7) % 180),
            'squat_jump' => 34 + ($id % 12),
            'flexibility_test' => 22 + ($id % 10),

            'notes' => $this->json([
                'source' => 'synthetic_demo',
                'clinical' => false,
            ]),
        ]);

        $this->put('performance_metrics', [
            'player_id' => $id,
            'metric_name' => 'Synthetic Readiness Score',
            'measurement_date' => $recordDate,
        ], [
            'metric_type' => 'physical',
            'metric_value' => $readiness,
            'metric_unit' => 'score',
            'data_source' => 'manual',
            'confidence_score' => 0.90,
            'is_verified' => false,
            'created_by' => $this->systemUserId,
        ]);

        $metric = DB::table('performance_metrics')
            ->where('player_id', $id)
            ->where('metric_name', 'Synthetic Readiness Score')
            ->where('measurement_date', $recordDate)
            ->first();

        if (!$metric) {
            throw new \RuntimeException(
                "performance_metric absent pour player_id={$id}."
            );
        }

        $initialValue = max(60, $readiness - 4);

        $change = round(
            (($readiness - $initialValue) / $initialValue) * 100,
            2
        );

        $this->put('performance_trends', [
            'metric_id' => (int) $metric->id,
            'player_id' => $id,
            'trend_period' => 'monthly',
            'start_date' => '2026-08-01',
            'end_date' => self::REFERENCE_DATE,
        ], [
            'initial_value' => $initialValue,
            'final_value' => $readiness,
            'change_percentage' => $change,
            'trend_direction' => 'increasing',
            'trend_strength' => 'moderate',
            'data_points_count' => 4,
            'confidence_level' => 0.90,
            'created_by' => $this->systemUserId,
        ]);

        $this->put('performance_alerts', [
            'player_id' => $id,
            'title' => 'Synthetic readiness monitoring',
        ], [
            'club_id' => $this->value($player, 'club_id'),
            'team_id' => $this->value($player, 'team_id'),
            'metric_id' => (int) $metric->id,

            'alert_type' => 'fitness_alert',
            'alert_level' => 'low',

            'description' =>
                'Synthetic demo alert; not a medical warning.',

            'alert_condition' => 'trend',

            'is_active' => true,
            'is_acknowledged' => false,
            'is_resolved' => false,
            'notification_sent' => false,

            'created_by' => $this->systemUserId,
        ]);

        /*
         * SEASON STATS
         */
        $matches = 12 + ($id % 10);

        $position = (string) (
            $this->value($player, 'position')
            ?: 'MID'
        );

        $goals = match ($position) {
            'FWD' => 3 + ($id % 8),
            'MID' => 1 + ($id % 5),
            'GK' => 0,
            default => $id % 2,
        };

        $assists = $position === 'GK'
            ? 0
            : 1 + ($id % 6);

        $this->put('player_season_stats', [
            'player_id' => $id,
            'competition_id' => $this->competitionId,
        ], [
            'matches_played' => $matches,
            'minutes_played' =>
                $matches * (62 + ($id % 20)),
            'goals' => $goals,
            'assists' => $assists,
            'yellow_cards' => $id % 4,
            'red_cards' =>
                $id % 17 === 0 ? 1 : 0,
        ]);

        /*
         * LOCAL LICENSE
         * Aucun numéro FIFA n'est inventé.
         */
        $this->put('player_licenses', [
            'player_id' => $id,
            'issue_date' => '2026-07-01',
        ], [
            'club_id' => $this->value($player, 'club_id'),

            'issued_date' => '2026-07-01',
            'expiry_date' => '2027-06-30',

            'contract_start_date' => '2026-07-01',
            'contract_end_date' => '2027-06-30',

            'license_type' => 'player',
            'status' => 'active',

            'notes' =>
                'synthetic_demo local license; not a FIFA license',
        ]);
    }

    private function put(
        string $table,
        array $where,
        array $values
    ): void {
        $safeWhere = $this->filter($table, $where);

        if (count($safeWhere) !== count($where)) {
            $missing = array_diff(
                array_keys($where),
                array_keys($safeWhere)
            );

            throw new \RuntimeException(
                $table
                . ': colonne(s) de clé absente(s): '
                . implode(', ', $missing)
            );
        }

        $safeValues = $this->filter($table, $values);

        if ($this->hasColumn($table, 'updated_at')) {
            $safeValues['updated_at'] = now();
        }

        $existing = DB::table($table)
            ->where($safeWhere)
            ->exists();

        if (
            !$existing
            && $this->hasColumn($table, 'created_at')
        ) {
            $safeValues['created_at'] = now();
        }

        DB::table($table)->updateOrInsert(
            $safeWhere,
            $safeValues
        );
    }

    private function filter(
        string $table,
        array $data
    ): array {
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
