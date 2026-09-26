<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (DB::getDriverName() !== 'pgsql') {
    throw new RuntimeException('Audit réservé à PostgreSQL : préciser DB_CONNECTION=pgsql et DATABASE_URL.');
}

$ids = DB::table('player_real_time_health')->where('notes', 'like', 'synthetic_demo%')
    ->distinct()->pluck('player_id')->map(fn ($id) => (int) $id)->all();
if (!$ids) {
    throw new RuntimeException('Aucun joueur synthetic_demo trouvé.');
}

// Chaque entrée correspond à une source effectivement lue par PlayerPortalDataService.
// JSON : préfixer le chemin par @ pour inspecter la valeur JSON de la colonne.
$sources = [
    'players' => [null, ['first_name', 'last_name', 'date_of_birth', 'position', 'nationality', 'height', 'weight', 'preferred_foot', 'fifa_connect_id', 'club_id', 'association_id']],
    'player_passports' => ['issue_date', ['passport_number', 'status', 'issue_date', 'expiry_date', 'issuing_authority', 'issuing_country']],
    'player_real_time_health' => ['measurement_time', [
        'readiness_score', 'energy_level', 'sleep_quality_score', 'sleep_duration_hours',
        'hydration_level', 'resting_heart_rate', 'max_heart_rate', 'blood_pressure_systolic',
        'blood_pressure_diastolic', 'temperature', 'oxygen_saturation', 'weight_kg', 'bmi',
        'body_fat_percentage', 'muscle_mass_kg', 'water_percentage', 'steps_count',
        'active_minutes', 'mood_score', 'data_source', 'metadata@nutrition.total_calories',
        'metadata@nutrition.protein_grams', 'metadata@nutrition.meal_quality_score',
        'metadata@vitals.heart_rate_recovery', 'metadata@vitals.blood_glucose', 'metadata@vitals.blood_glucose_unit',
        'metadata@recovery.stretching_minutes', 'metadata@recovery.muscle_soreness',
    ]],
    'player_fitness_logs' => ['log_date', ['fatigue_level']],
    'player_sdoh_data' => ['assessment_date', ['living_environment_score', 'social_support_score', 'healthcare_access_score', 'financial_situation_score', 'education_level', 'mental_wellbeing_score', 'stress_level', 'has_healthy_diet']],
    'pcmas' => ['assessment_date', ['status', 'is_signed', 'result_json@pcma_score', 'result_json@cardiovascular_fitness', 'result_json@respiratory_fitness', 'result_json@musculoskeletal_fitness', 'result_json@neurological_fitness']],
    'player_performances' => ['performance_date', ['overall_performance_score', 'physical_score', 'technical_score', 'tactical_score', 'mental_score', 'social_score', 'endurance_score', 'strength_score', 'speed_score', 'agility_score', 'passing_accuracy', 'shooting_accuracy', 'notes@physical_tests']],
    'performances' => ['match_date', ['tackles_won', 'shots_on_target', 'passes_attempted', 'passes_completed', 'additional_metrics@interceptions']],
    'health_records' => ['record_date', ['risk_score', 'doctor_name', 'medications', 'hematology_results@hemoglobin.value', 'hematology_results@hemoglobin.unit', 'hematology_results@hematocrit.value', 'hematology_results@hematocrit.unit', 'mineral_results@serum_iron.value', 'mineral_results@serum_iron.unit', 'vitamin_results@vitamin_d.value', 'vitamin_results@vitamin_d.unit', 'inflammatory_markers@crp.value', 'inflammatory_markers@crp.unit', 'biochemistry_results@total_cholesterol.value', 'biochemistry_results@total_cholesterol.unit']],
    'player_season_stats' => [null, ['matches_played', 'goals', 'assists', 'minutes_played', 'yellow_cards', 'red_cards']],
    'medical_records' => ['record_date', ['doctor_name', 'medical_center', 'record_type', 'next_appointment']],
    'performance_trends' => ['start_date', ['initial_value', 'final_value', 'change_percentage', 'trend_direction', 'confidence_level']],
    'player_connected_devices' => ['last_sync_at', ['device_name', 'manufacturer', 'device_model', 'battery_level', 'connection_status', 'metadata@api_endpoint']],
    'player_licenses' => ['issue_date', ['license_number', 'status', 'license_type', 'season', 'issuing_authority']],
    'injuries' => ['date', ['type', 'body_zone', 'severity', 'status', 'description']],
    'medical_predictions' => ['prediction_date', ['predicted_condition', 'risk_probability', 'recommendations']],
    'performance_alerts' => ['created_at', ['title', 'description', 'alert_level']],
    'doping_controls' => ['control_date', ['control_type', 'location', 'control_authority', 'result', 'notes', 'next_control']],
    'fit_score_snapshots' => ['snapshot_at', ['fit_score', 'physical_score', 'technical_score', 'tactical_score', 'mental_score', 'social_score', 'confidence_score']],
];

$missing = [];
$total = count($ids);
printf("Joueurs synthetic_demo : %d\n", $total);
foreach ($sources as $table => [$dateColumn, $paths]) {
    if (!Schema::hasTable($table)) {
        printf("TABLE ABSENTE : %s\n", $table);
        $missing[] = $table . '.*';
        continue;
    }
    $columns = Schema::getColumnListing($table);
    $valid = array_values(array_filter($paths, function ($path) use ($columns) {
        $column = explode('@', $path, 2)[0];
        return in_array($column, $columns, true);
    }));
    foreach (array_diff($paths, $valid) as $path) {
        printf("COLONNE ABSENTE : %s.%s\n", $table, $path);
        $missing[] = $table . '.' . $path;
    }
    $query = DB::table($table)->whereIn($table === 'players' ? 'id' : 'player_id', $ids);
    if ($table === 'fit_score_snapshots') {
        $query->where('calculation_version', 'fit_v1')->where('is_complete', true);
    }
    if ($dateColumn !== null && in_array($dateColumn, $columns, true)) {
        $query->orderByDesc($dateColumn);
    }
    if (in_array('id', $columns, true)) {
        $query->orderByDesc('id');
    }
    $rows = $query->get()->unique($table === 'players' ? 'id' : 'player_id')
        ->keyBy($table === 'players' ? 'id' : 'player_id');
    foreach ($valid as $path) {
        [$column, $jsonPath] = array_pad(explode('@', $path, 2), 2, null);
        $absent = [];
        foreach ($ids as $id) {
            $row = $rows->get($id);
            $value = $row?->$column;
            if ($jsonPath !== null && $value !== null) {
                $decoded = is_string($value) ? json_decode($value, true) : $value;
                $value = data_get($decoded, $jsonPath);
            }
            if ($value === null || $value === '' || $value === []) {
                $absent[] = $id;
            }
        }
        if ($absent) {
            printf("MANQUANT %s.%s : %d/%d (exemples ID : %s)\n", $table, $path, count($absent), $total, implode(',', array_slice($absent, 0, 5)));
            $missing[] = $table . '.' . $path;
        }
    }
}
printf("Champs avec au moins un manque : %d\n", count($missing));
exit($missing ? 1 : 0);
