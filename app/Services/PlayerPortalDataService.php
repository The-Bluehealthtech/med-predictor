<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlayerPortalDataService
{
    public function forPlayer(Player $player): array
    {
        $playerId = $player->id;

        $healthRecords = DB::table('health_records')
            ->where('player_id', $playerId)
            ->orderByDesc('record_date')
            ->get();

        $latestHealth = $healthRecords->first();

        $latestRealtime = DB::table('player_real_time_health')
            ->where('player_id', $playerId)
            ->orderByDesc('measurement_time')
            ->first();

        $latestFitness = DB::table('player_fitness_logs')
            ->where('player_id', $playerId)
            ->orderByDesc('log_date')
            ->first();

        $latestSdoh = DB::table('player_sdoh_data')
            ->where('player_id', $playerId)
            ->orderByDesc('assessment_date')
            ->first();

        $latestPcma = DB::table('pcmas')
            ->where('player_id', $playerId)
            ->orderByDesc('assessment_date')
            ->first();

        $medicalRecords = DB::table('medical_records')
            ->where('player_id', $playerId)
            ->orderByDesc('record_date')
            ->get();

        $performanceAlertsRaw = DB::table('performance_alerts')
            ->where('player_id', $playerId)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        $medicalPredictionsRaw = DB::table('medical_predictions')
            ->where('player_id', $playerId)
            ->orderByDesc('prediction_date')
            ->get();

        $dopingRaw = DB::table('doping_controls')
            ->where('player_id', $playerId)
            ->orderByDesc('control_date')
            ->get();

        $devicesRaw = DB::table('player_connected_devices')
            ->where('player_id', $playerId)
            ->orderByDesc('last_sync_at')
            ->get();

        $playerStats = DB::table('player_season_stats')
            ->where('player_id', $playerId)
            ->get();

        $performanceTrends = DB::table('performance_trends')
            ->where('player_id', $playerId)
            ->whereNull('deleted_at')
            ->orderByDesc('start_date')
            ->get();

        $playerLicenses = DB::table('player_licenses')
            ->where('player_id', $playerId)
            ->orderByDesc('issue_date')
            ->get()
            ->map(function ($license) {
                $license->start_date =
                    $license->contract_start_date
                    ?? $license->issue_date
                    ?? $license->issued_date
                    ?? null;

                $license->end_date =
                    $license->contract_end_date
                    ?? $license->expiry_date
                    ?? null;

                return $license;
            });

        /*
         * SDOH
         */
        $sdohFactors = null;

        if ($latestSdoh) {
            $sdohFactors = (object) [
                'environment_score' =>
                    $latestSdoh->living_environment_score,

                'social_support_score' =>
                    $latestSdoh->social_support_score,

                'healthcare_access_score' =>
                    $latestSdoh->healthcare_access_score,

                'financial_status_score' =>
                    $latestSdoh->financial_situation_score,

                'education_score' =>
                    $this->educationScore($latestSdoh->education_level),
            ];
        }

        /*
         * Predictions de performance dérivées des tendances enregistrées.
         */
        $performancePredictions = $performanceTrends
            ->take(6)
            ->map(function ($trend) {
                $current = (float) $trend->final_value;

                $delta = (float) $trend->final_value
                    - (float) $trend->initial_value;

                return (object) [
                    'prediction_type' => $trend->trend_period,
                    'current_score' => round($current, 1),
                    'predicted_score_3months' =>
                        round($this->clamp($current + $delta, 0, 100), 1),
                    'trend_direction' => $trend->trend_direction,
                ];
            });

        /*
         * Risque de blessure.
         */
        $injuryAlerts = null;

        if ($player->injury_risk_score !== null || $latestHealth) {
            $injuryAlerts = (object) [
                'risk_level' => (float) ($player->injury_risk_score ?? 0),
                'injury_type' =>
                    $latestHealth->injury_mechanism
                    ?? $player->injury_risk_reason
                    ?? 'Risque général',
                'body_part' =>
                    $latestHealth->injury_location
                    ?? 'Non spécifié',
            ];
        }

        /*
         * Médicaments depuis health_records.medications.
         */
        $playerMedications = $healthRecords
            ->flatMap(function ($record) {
                $items = $this->jsonList($record->medications ?? null);

                return collect($items)->map(function ($item) use ($record) {
                    if (!is_array($item)) {
                        $item = ['name' => (string) $item];
                    }

                    return (object) [
                        'medication_type' =>
                            $item['type']
                            ?? $item['name']
                            ?? 'Médicament',

                        'start_date' =>
                            $item['start_date']
                            ?? $record->visit_date
                            ?? $record->record_date
                            ?? null,
                    ];
                });
            })
            ->values();

        /*
         * Notifications du portail:
         * alertes performance + prédictions médicales.
         */
        $performanceNotifications = $performanceAlertsRaw
            ->map(fn ($alert) => (object) [
                'notification_type' => 'performance_alert',
                'title' => $alert->title,
                'message' => $alert->description,
                'severity' => $alert->alert_level,
                'created_at' => $alert->created_at,
            ]);

        $medicalNotifications = $medicalPredictionsRaw
            ->map(fn ($prediction) => (object) [
                'notification_type' => 'medical_alert',
                'title' => $prediction->predicted_condition,
                'message' => implode(
                    ', ',
                    $this->jsonList($prediction->recommendations ?? null)
                ),
                'severity' =>
                    ((float) $prediction->risk_probability >= 0.7)
                        ? 'high'
                        : (((float) $prediction->risk_probability >= 0.4)
                            ? 'medium'
                            : 'low'),
                'created_at' => $prediction->prediction_date,
            ]);

        $playerNotifications = $performanceNotifications
            ->concat($medicalNotifications)
            ->sortByDesc('created_at')
            ->values();

        /*
         * Santé / bien-être.
         */
        $playerHealthWellbeing = null;

        if ($latestRealtime || $player->fitness_score !== null) {
            $playerHealthWellbeing = (object) [
                'fitness_score' =>
                    $player->fitness_score
                    ?? $latestRealtime->readiness_score
                    ?? null,

                'energy_level' =>
                    $latestRealtime->energy_level
                    ?? $latestFitness->energy_level
                    ?? null,

                'sleep_quality' =>
                    $latestRealtime && $latestRealtime->sleep_quality_score !== null
                        ? round(((float) $latestRealtime->sleep_quality_score) / 10, 1)
                        : null,
            ];
        }

        /*
         * Nutrition via metadata du flux santé temps réel.
         */
        $realtimeMetadata = $this->json(
            $latestRealtime->metadata ?? null
        );

        $playerNutrition = $latestRealtime
            ? (object) [
                'hydration_score' =>
                    $latestRealtime->hydration_level,

                'total_calories' =>
                    data_get($realtimeMetadata, 'nutrition.total_calories'),

                'protein_grams' =>
                    data_get($realtimeMetadata, 'nutrition.protein_grams'),

                'meal_quality_score' =>
                    data_get($realtimeMetadata, 'nutrition.meal_quality_score'),
            ]
            : null;

        /*
         * Récupération.
         */
        $playerRecovery = $latestRealtime
            ? (object) [
                'sleep_hours' =>
                    $latestRealtime->sleep_duration_hours,

                'sleep_quality_score' =>
                    $latestRealtime->sleep_quality_score !== null
                        ? round(((float) $latestRealtime->sleep_quality_score) / 10, 1)
                        : null,

                'muscle_soreness' =>
                    $latestRealtime->muscle_fatigue,

                'fatigue_level' =>
                    $latestFitness->fatigue_level
                    ?? $latestRealtime->central_fatigue
                    ?? null,

                'stretching_minutes' =>
                    data_get($realtimeMetadata, 'recovery.stretching_minutes'),
            ]
            : null;

        /*
         * Aptitude médicale locale.
         */
        $playerMedicalAptitude = null;

        if ($latestHealth || $latestPcma) {
            $healthRisk = (float) ($latestHealth->risk_score ?? 0);

            // health_records.risk_score peut être stocké comme ratio
            // (0.09 = 9 %) ou, pour d'anciennes données, en pourcentage.
            $healthRiskPercent = $healthRisk <= 1
                ? $healthRisk * 100
                : $healthRisk;

            $healthScore =
                $player->fitness_score
                ?? (100 - $healthRiskPercent);

            $playerMedicalAptitude = (object) [
                'overall_health_score' =>
                    round($this->clamp((float) $healthScore, 0, 100), 1),

                'medical_status' =>
                    $player->match_availability
                        ? 'fit'
                        : 'temporarily_unfit',

                'fitness_level' =>
                    $this->fitnessLevel((float) $healthScore),

                'assessment_date' =>
                    $latestPcma->assessment_date
                    ?? $latestHealth->visit_date
                    ?? $latestHealth->record_date
                    ?? null,
            ];
        }

        /*
         * Signes vitaux.
         */
        $playerVitalSigns = null;

        if ($latestRealtime) {
            $weight = $latestRealtime->weight_kg
                ?? $player->weight;

            $musclePercentage = null;

            if (
                $latestRealtime->muscle_mass_kg !== null
                && $weight
                && (float) $weight > 0
            ) {
                $musclePercentage = round(
                    ((float) $latestRealtime->muscle_mass_kg / (float) $weight) * 100,
                    1
                );
            }

            $playerVitalSigns = (object) [
                'heart_rate_resting' =>
                    $latestRealtime->resting_heart_rate,

                'heart_rate_max' =>
                    $latestRealtime->max_heart_rate,

                'heart_rate_recovery' =>
                    data_get($realtimeMetadata, 'vitals.heart_rate_recovery'),

                'blood_pressure_systolic' =>
                    $latestRealtime->blood_pressure_systolic,

                'blood_pressure_diastolic' =>
                    $latestRealtime->blood_pressure_diastolic,

                'temperature' =>
                    $latestRealtime->temperature,

                'oxygen_saturation' =>
                    $latestRealtime->oxygen_saturation,

                'body_weight' => $weight,

                'body_height' =>
                    $player->height,

                'bmi' =>
                    $latestRealtime->bmi,

                'body_fat_percentage' =>
                    $latestRealtime->body_fat_percentage,

                'muscle_mass_percentage' =>
                    $musclePercentage,

                'hydration_percentage' =>
                    $latestRealtime->water_percentage,

                'blood_glucose' =>
                    data_get($realtimeMetadata, 'vitals.blood_glucose'),
            ];
        }

        /*
         * PCMA.
         */
        $playerPcma = null;

        if ($latestPcma) {
            $pcmaResults = $this->json($latestPcma->result_json);

            $status = $latestPcma->status;

            if ($status === 'completed') {
                $status = $latestPcma->fifa_compliant
                    ? 'approved'
                    : 'pending';
            }

            $playerPcma = (object) [
                'pcma_status' => $status,

                'pcma_score' =>
                    data_get($pcmaResults, 'pcma_score'),

                'cardiovascular_fitness' =>
                    data_get($pcmaResults, 'cardiovascular_fitness'),

                'respiratory_fitness' =>
                    data_get($pcmaResults, 'respiratory_fitness'),

                'musculoskeletal_fitness' =>
                    data_get($pcmaResults, 'musculoskeletal_fitness'),

                'neurological_fitness' =>
                    data_get($pcmaResults, 'neurological_fitness'),

                'next_assessment_date' =>
                    $latestPcma->assessment_date
                        ? Carbon::parse($latestPcma->assessment_date)
                            ->addYear()
                            ->toDateString()
                        : null,
            ];
        }

        /*
         * Blessures depuis health_records.
         */
        $playerInjuriesDiseases = $healthRecords
            ->filter(fn ($record) => !empty($record->injury_date))
            ->map(fn ($record) => (object) [
                'incident_date' => $record->injury_date,
                'severity' => $record->injury_severity,
                'icd_11_code' => $record->icd_10_injury,
                'icd_11_description' =>
                    $record->snomed_ct_injury
                    ?? $record->injury_mechanism
                    ?? $record->injury_location,
            ])
            ->values();

        /*
         * Devices.
         */
        $sportsDevices = $devicesRaw
            ->map(fn ($device) => (object) [
                'device_name' => $device->device_name,
                'brand' => $device->manufacturer,
                'model' => $device->device_model,
                'battery_level' => $device->battery_level,
                'connection_status' => $device->connection_status,
                'last_sync' => $device->last_sync_at,
                'api_endpoint' =>
                    data_get($this->json($device->metadata), 'api_endpoint'),
            ]);

        /*
         * Données comportementales.
         */
        $behavioralData = $latestRealtime
            ? (object) [
                'sleep_hours' => $latestRealtime->sleep_duration_hours,
                'steps_count' => $latestRealtime->steps_count,
                'active_minutes' => $latestRealtime->active_minutes,
                'mood_score' =>
                    $latestRealtime->mood_score !== null
                        ? round(((float) $latestRealtime->mood_score) / 10, 1)
                        : null,
                'device_source' => $latestRealtime->data_source,
                'created_at' => $latestRealtime->measurement_time,
            ]
            : null;

        /*
         * Centres médicaux / physio depuis medical_records.
         */
        $physioCenters = $medicalRecords
            ->whereNotNull('medical_center')
            ->groupBy('medical_center')
            ->map(function (Collection $records, $center) {
                $latest = $records->first();

                return (object) [
                    'center_name' => $center,
                    'center_type' => $latest->record_type,
                    'last_session_date' => $latest->record_date,
                    'next_appointment' => $latest->next_appointment,
                    'session_count' => $records->count(),
                    'treatment_plan' =>
                        $latest->notes
                        ?? $latest->description,
                    'api_endpoint' => null,
                ];
            })
            ->values();

        /*
         * Suivi mental depuis SDOH.
         */
        $mentalHealthApps = $latestSdoh
            ? collect([
                (object) [
                    'app_name' => 'Suivi bien-être mental (SDOH)',
                    'app_type' => 'sdoh',
                    'api_endpoint' => null,
                    'last_session_date' => $latestSdoh->assessment_date,
                    'session_duration' => null,
                    'mood_score' =>
                        $latestSdoh->mental_wellbeing_score !== null
                            ? round(((float) $latestSdoh->mental_wellbeing_score) / 10, 1)
                            : null,
                    'wellness_score' =>
                        $latestSdoh->mental_wellbeing_score,
                ],
            ])
            : collect();

        /*
         * Intégrations / sources de données connectées.
         */
        $apiIntegrations = $devicesRaw
            ->map(fn ($device) => (object) [
                'api_name' =>
                    trim($device->manufacturer . ' ' . $device->device_name),
                'api_type' => $device->device_type,
                'connection_status' => $device->connection_status,
                'last_sync' => $device->last_sync_at,
                'data_types' => $device->sensors_available,
                'api_endpoint' =>
                    data_get($this->json($device->metadata), 'api_endpoint'),
            ]);

        /*
         * Contrôles antidopage.
         */
        $dopingTests = $dopingRaw
            ->map(fn ($test) => (object) [
                'test_type' => $test->control_type,
                'test_date' => $test->control_date,
                'test_location' => $test->location,
                'test_laboratory' => $test->control_authority,
                'test_result' => $test->result,
                'test_status' => $test->result,
                'test_notes' => $test->notes,
                'next_test_date' => $test->next_control,
            ]);

        /*
         * Substances du panel de contrôle.
         * Ce sont des substances analysées, pas une déclaration de violation.
         */
        $bannedSubstances = $dopingRaw
            ->flatMap(fn ($test) =>
                $this->jsonList($test->substances_tested)
            )
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($substance) => (object) [
                'substance_name' =>
                    is_array($substance)
                        ? ($substance['name'] ?? 'Substance analysée')
                        : (string) $substance,
                'substance_category' => 'Panel antidopage',
                'wada_code' => null,
                'detection_count' => 0,
                'status' => 'screened',
                'notes' =>
                    'Présente dans le panel de contrôle; aucune violation n’est déduite.',
            ]);

        /*
         * AUT / TUE.
         */
        $therapeuticUseExemptions = $healthRecords
            ->filter(fn ($record) =>
                !empty($record->aut_status)
                || !empty($record->aut_substance)
                || !empty($record->aut_authorized_substance)
            )
            ->map(fn ($record) => (object) [
                'substance_name' =>
                    $record->aut_substance
                    ?? $record->aut_authorized_substance,

                'medical_condition' =>
                    $record->aut_diagnosis,

                'prescribing_doctor' =>
                    $record->aut_authorizing_physician
                    ?? $record->doctor_name,

                'doctor_license' => null,

                'exemption_start_date' =>
                    $record->aut_start_date
                    ?? $record->aut_approval_date,

                'exemption_end_date' =>
                    $record->aut_end_date
                    ?? $record->aut_expiry_date,

                'fifa_approval' =>
                    $record->aut_status,

                'wada_approval' =>
                    $record->aut_status,

                'approval_notes' =>
                    $record->aut_notes
                    ?? $record->aut_medical_justification,
            ])
            ->values();

        /*
         * Alertes antidopage dérivées des résultats non négatifs.
         */
        $dopingAlerts = $dopingRaw
            ->filter(fn ($test) =>
                strtolower((string) $test->result) !== 'negative'
            )
            ->map(fn ($test) => (object) [
                'alert_type' => 'doping_control',
                'alert_date' => $test->control_date,
                'alert_message' =>
                    'Contrôle antidopage nécessitant une vérification.',
                'alert_severity' =>
                    strtolower((string) $test->result) === 'positive'
                        ? 'high'
                        : 'medium',
                'alert_status' => 'active',
            ])
            ->values();

        /*
         * Conformité locale synthétisée à partir des données canoniques.
         */
        $complianceStatus = collect();

        if ($playerLicenses->isNotEmpty()) {
            $license = $playerLicenses->first();

            $complianceStatus->push((object) [
                'compliance_type' => 'license',
                'status' => $license->status ?? 'unknown',
                'compliance_score' =>
                    ($license->status ?? null) === 'active' ? 100 : 50,
                'last_assessment_date' =>
                    $license->issue_date
                    ?? $license->updated_at,
                'violations_count' => 0,
                'warnings_count' => 0,
            ]);
        }

        if ($latestPcma) {
            $complianceStatus->push((object) [
                'compliance_type' => 'pcma',
                'status' => $latestPcma->status,
                'compliance_score' =>
                    $latestPcma->fifa_compliant ? 100 : 60,
                'last_assessment_date' =>
                    $latestPcma->assessment_date,
                'violations_count' => 0,
                'warnings_count' =>
                    $latestPcma->fifa_compliant ? 0 : 1,
            ]);
        }

        if ($dopingRaw->isNotEmpty()) {
            $positive = $dopingRaw
                ->filter(fn ($test) =>
                    strtolower((string) $test->result) === 'positive'
                )
                ->count();

            $complianceStatus->push((object) [
                'compliance_type' => 'anti_doping',
                'status' => $positive === 0 ? 'compliant' : 'review_required',
                'compliance_score' => $positive === 0 ? 100 : 0,
                'last_assessment_date' =>
                    $dopingRaw->first()->control_date,
                'violations_count' => $positive,
                'warnings_count' => $dopingAlerts->count(),
            ]);
        }

        $currentPortalUrl =
            url('/test-portail-joueur-simple')
            . '?player_id='
            . $playerId;

        $complianceResources = collect([
            (object) [
                'resource_name' => 'Dossier médical',
                'resource_type' => 'medical',
                'resource_description' =>
                    'Données médicales et aptitude du joueur.',
                'resource_url' => $currentPortalUrl,
            ],
            (object) [
                'resource_name' => 'PCMA',
                'resource_type' => 'pcma',
                'resource_description' =>
                    'Évaluation médicale pré-compétition.',
                'resource_url' => $currentPortalUrl,
            ],
            (object) [
                'resource_name' => 'Antidopage',
                'resource_type' => 'anti_doping',
                'resource_description' =>
                    'Contrôles antidopage et AUT.',
                'resource_url' => $currentPortalUrl,
            ],
            (object) [
                'resource_name' => 'Licences',
                'resource_type' => 'license',
                'resource_description' =>
                    'Statut des licences du joueur.',
                'resource_url' => $currentPortalUrl,
            ],
        ]);

        /*
         * Demandes de licence:
         * jamais de LIKE sur l'id interne du joueur.
         */
        $licenseRequestsQuery = DB::table('license_requests')
            ->where('first_name', $player->first_name)
            ->where('last_name', $player->last_name);

        if ($player->club_id) {
            $licenseRequestsQuery
                ->where('current_club_id', $player->club_id);
        }

        if ($player->fifa_connect_id) {
            $licenseRequestsQuery
                ->where('fifa_connect_id', $player->fifa_connect_id);
        }

        $licenseRequests = $licenseRequestsQuery->get();

        return compact(
            'healthRecords',
            'playerStats',
            'playerLicenses',
            'performanceTrends',
            'sdohFactors',
            'performancePredictions',
            'injuryAlerts',
            'playerMedications',
            'playerNotifications',
            'playerHealthWellbeing',
            'playerNutrition',
            'playerRecovery',
            'playerPcma',
            'playerMedicalAptitude',
            'playerVitalSigns',
            'playerInjuriesDiseases',
            'sportsDevices',
            'behavioralData',
            'physioCenters',
            'mentalHealthApps',
            'apiIntegrations',
            'dopingTests',
            'bannedSubstances',
            'therapeuticUseExemptions',
            'dopingAlerts',
            'complianceStatus',
            'complianceResources',
            'licenseRequests'
        );
    }

    private function json(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function jsonList(mixed $value): array
    {
        $decoded = $this->json($value);

        if ($decoded === []) {
            return [];
        }

        return array_is_list($decoded)
            ? $decoded
            : [$decoded];
    }

    private function educationScore(?string $level): ?int
    {
        if (!$level) {
            return null;
        }

        return match (strtolower(trim($level))) {
            'primary', 'primaire' => 40,
            'secondary', 'secondaire', 'high_school' => 60,
            'college', 'technical', 'vocational' => 70,
            'university', 'bachelor', 'licence' => 80,
            'master' => 90,
            'doctorate', 'phd' => 100,
            default => 65,
        };
    }

    private function fitnessLevel(float $score): string
    {
        return match (true) {
            $score >= 90 => 'excellent',
            $score >= 75 => 'good',
            $score >= 60 => 'moderate',
            default => 'low',
        };
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
