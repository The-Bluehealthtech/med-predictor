<?php

namespace App\Services;

use App\Models\Player;
use App\Services\Fit\FitSnapshotService;
use App\Services\Fit\FitScoreService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlayerPortalDataService
{
    public function __construct(
        private readonly FitSnapshotService $fitSnapshotService,
        private readonly FitScoreService $fitScoreService
    ) {
    }

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

        $profileSynthetic = str_contains(
            $latestRealtime?->notes ?? '',
            'synthetic_demo'
        );

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

        $latestPerformance = DB::table('player_performances')
            ->where('player_id', $playerId)
            ->orderByDesc('performance_date')
            ->first();

        $latestMatchPerformance = DB::table('performances')
            ->where('player_id', $playerId)
            ->orderByDesc('match_date')
            ->orderByDesc('id')
            ->first();

        $fitSnapshotData = $this->fitSnapshotService->latestForPlayer($player);
        $latestFitSnapshot = $fitSnapshotData['snapshot'];
        $previousFitSnapshot = $fitSnapshotData['previous_snapshot'];
        $latestFitAttempt = $fitSnapshotData['latest_attempt'];
        $fitMissingAxes = $fitSnapshotData['missing_axes'];
        $fitEvolution = $fitSnapshotData['evolution'];
        $fitDiagnosis = $this->fitScoreService->diagnose($player, 30);

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

                'education_level' =>
                    $this->educationLabel($latestSdoh->education_level),
            ];
        }

        /*
         * Évolution mesurée, directement tirée des tendances enregistrées.
         */
        $performancePredictions = $performanceTrends
            ->take(6)
            ->map(function ($trend) {
                $current = (float) $trend->final_value;
                $initial = (float) $trend->initial_value;
                $delta = $current - $initial;

                $confidence = (float) ($trend->confidence_level ?? 0);
                $confidencePercent = $confidence <= 1
                    ? $confidence * 100
                    : $confidence;

                return (object) [
                    'prediction_type' => 'performance',
                    'prediction_period' => $trend->trend_period,
                    'initial_score' => round($initial, 1),
                    'current_score' => round($current, 1),
                    'observed_change' => round($delta, 1),
                    'observed_change_percentage' =>
                        $trend->change_percentage !== null
                            ? round((float) $trend->change_percentage, 1)
                            : null,
                    'trend_direction' => $trend->trend_direction,
                    'confidence_percent' =>
                        round($this->clamp($confidencePercent, 0, 100), 1),
                ];
            });

        /*
         * Risque de blessure.
         */
        $injuryAlerts = null;

        $injuryRiskRatio = $player->injury_risk_score !== null
            ? (float) $player->injury_risk_score
            : null;

        $injuryMechanism = $latestHealth->injury_mechanism ?? null;
        $injuryLocation = $latestHealth->injury_location ?? null;

        $hasInjuryRiskSignal = $injuryRiskRatio !== null && (
            $injuryRiskRatio > 0
            || !empty($player->injury_risk_reason)
            || !empty($injuryMechanism)
            || !empty($injuryLocation)
        );

        if ($hasInjuryRiskSignal) {
            $injuryRiskPercent = $injuryRiskRatio <= 1
                ? $injuryRiskRatio * 100
                : $injuryRiskRatio;

            $injuryAlerts = (object) [
                'risk_level' =>
                    round($this->clamp($injuryRiskPercent, 0, 100), 1),
                'injury_type' =>
                    $injuryMechanism
                    ?? $player->injury_risk_reason
                    ?? 'Risque général',
                'body_part' =>
                    $injuryLocation
                    ?? 'Non spécifié',
            ];
        }

        /*
         * Médicaments depuis health_records.medications.
         */
        $playerMedications = $healthRecords
            ->flatMap(function ($record) {
                $items = $this->jsonList($record->medications ?? null);

                return collect($items)
                    ->filter(fn ($item) =>
                        is_array($item)
                        && ($item['status'] ?? null) === 'active'
                    )
                    ->map(fn ($item) => (object) [
                        'medication_type' =>
                            $item['type']
                            ?? $item['name']
                            ?? 'Traitement',
                        'start_date' =>
                            $item['start_date']
                            ?? $record->visit_date
                            ?? $record->record_date
                            ?? null,
                        'synthetic_test' =>
                            ($item['source'] ?? null) === 'synthetic_demo',
                    ]);
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

        if ($latestRealtime || $latestFitness) {
            $playerHealthWellbeing = (object) [
                'fitness_score' => $latestRealtime?->readiness_score,

                'energy_level' => $latestRealtime?->energy_level,

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
        $playerRecovery = ($latestRealtime || $latestFitness)
            ? (object) [
                'sleep_hours' => $latestRealtime?->sleep_duration_hours,
                'sleep_quality_score' =>
                    $latestRealtime?->sleep_quality_score !== null
                        ? round(((float) $latestRealtime->sleep_quality_score) / 10, 1)
                        : null,
                'muscle_soreness' =>
                    data_get($realtimeMetadata, 'recovery.muscle_soreness'),
                'fatigue_level' => $latestFitness?->fatigue_level,
                'stretching_minutes' =>
                    data_get($realtimeMetadata, 'recovery.stretching_minutes'),
            ]
            : null;

        /*
         * Aptitude médicale locale.
         */
        $playerMedicalAptitude = null;

        if ($latestHealth || $latestPcma) {
            $healthRisk = $latestHealth?->risk_score;
            $healthRiskPercent = null;

            if ($healthRisk !== null) {
                $healthRisk = (float) $healthRisk;
                $healthRiskPercent = $healthRisk <= 1
                    ? $healthRisk * 100
                    : $healthRisk;
            }

            // Une évaluation non signée ne constitue pas une aptitude médicale.
            $signedStatus = $latestPcma && $latestPcma->is_signed
                ? $latestPcma->status
                : null;

            $playerMedicalAptitude = (object) [
                'health_risk_percent' =>
                    $healthRiskPercent !== null
                        ? round($this->clamp($healthRiskPercent, 0, 100), 1)
                        : null,
                'medical_status' => match ($signedStatus) {
                    'cleared', 'approved' => 'fit',
                    'not_cleared', 'failed', 'rejected' => 'unfit',
                    default => null,
                },
                'assessment_date' =>
                    $latestPcma?->assessment_date
                    ?? $latestHealth?->visit_date
                    ?? $latestHealth?->record_date
                    ?? null,
                'latest_clinician' =>
                    $medicalRecords->first()?->doctor_name
                    ?? $latestHealth?->doctor_name,
            ];
        }

        /*
         * Signes vitaux.
         */
        $playerVitalSigns = null;

        if ($latestRealtime) {
            $weight = $latestRealtime->weight_kg;

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

                'body_weight' => $latestRealtime->weight_kg,

                'body_height' => $player->height,

                'measurement_time' => $latestRealtime->measurement_time,

                'bmi' => $latestRealtime->bmi,

                'body_fat_percentage' =>
                    $latestRealtime->body_fat_percentage,

                'muscle_mass_percentage' =>
                    $musclePercentage,

                'hydration_percentage' =>
                    $latestRealtime->water_percentage,

                'blood_glucose' =>
                    data_get($realtimeMetadata, 'vitals.blood_glucose'),

                'blood_glucose_unit' =>
                    data_get(
                        $realtimeMetadata,
                        'vitals.blood_glucose_unit'
                    ),
            ];
        }

        /*
         * PCMA.
         */
        $playerPcma = null;

        if ($latestPcma) {
            $pcmaResults = $this->json($latestPcma->result_json);

            $rawStatus = $latestPcma->status;

            $status = match ($rawStatus) {
                'approved' => 'cleared',
                'rejected' => 'not_cleared',
                default => $rawStatus ?: null,
            };

            $playerPcma = (object) [
                'pcma_status' => $status,
                'is_signed' => (bool) $latestPcma->is_signed,
                'synthetic_test' => (bool) data_get($pcmaResults, 'synthetic_demo'),

                'pcma_score' =>
                    data_get($pcmaResults, 'pcma_score')
                    ?? data_get($pcmaResults, 'overall_score'),

                'cardiovascular_fitness' =>
                    data_get($pcmaResults, 'cardiovascular_fitness'),

                'respiratory_fitness' =>
                    data_get($pcmaResults, 'respiratory_fitness'),

                'musculoskeletal_fitness' =>
                    data_get($pcmaResults, 'musculoskeletal_fitness'),

                'neurological_fitness' =>
                    data_get($pcmaResults, 'neurological_fitness'),

                'next_assessment_date' =>
                    data_get($pcmaResults, 'next_assessment_date')
                    ?? (
                        $latestPcma->assessment_date
                            ? Carbon::parse($latestPcma->assessment_date)
                                ->addYear()
                                ->toDateString()
                            : null
                    ),
            ];
        }

        /*
         * Tests physiques depuis player_performances.notes.
         */
        $playerPerformanceTests = null;

        if ($latestPerformance) {
            $performanceNotes = $this->json($latestPerformance->notes);
            $physicalTests = data_get(
                $performanceNotes,
                'physical_tests',
                []
            );

            if (is_array($physicalTests) && $physicalTests !== []) {
                $playerPerformanceTests = (object) $physicalTests;
            }
        }

        /*
         * Analyses biologiques depuis les champs JSON canoniques
         * de health_records.
         */
        $playerLaboratoryResults = null;

        if ($latestHealth) {
            $hematology = $this->json(
                $latestHealth->hematology_results
            );
            $minerals = $this->json(
                $latestHealth->mineral_results
            );
            $vitamins = $this->json(
                $latestHealth->vitamin_results
            );
            $inflammatory = $this->json(
                $latestHealth->inflammatory_markers
            );
            $biochemistry = $this->json(
                $latestHealth->biochemistry_results
            );

            $playerLaboratoryResults = (object) [
                'hemoglobin' =>
                    data_get($hematology, 'hemoglobin.value'),
                'hemoglobin_unit' =>
                    data_get($hematology, 'hemoglobin.unit'),

                'hematocrit' =>
                    data_get($hematology, 'hematocrit.value'),
                'hematocrit_unit' =>
                    data_get($hematology, 'hematocrit.unit'),

                'serum_iron' =>
                    data_get($minerals, 'serum_iron.value'),
                'serum_iron_unit' =>
                    data_get($minerals, 'serum_iron.unit'),

                'vitamin_d' =>
                    data_get($vitamins, 'vitamin_d.value'),
                'vitamin_d_unit' =>
                    data_get($vitamins, 'vitamin_d.unit'),

                'crp' =>
                    data_get($inflammatory, 'crp.value'),
                'crp_unit' =>
                    data_get($inflammatory, 'crp.unit'),

                'total_cholesterol' =>
                    data_get(
                        $biochemistry,
                        'total_cholesterol.value'
                    ),
                'total_cholesterol_unit' =>
                    data_get(
                        $biochemistry,
                        'total_cholesterol.unit'
                    ),
            ];
        }

        /*
         * Indicateurs factuels affichés dans la section Tendances.
         * Aucun changement temporel n'est inventé sans mesure disponible.
         */
        $latestPerformanceTrend = $performanceTrends->first();

        $trendIndicators = (object) [
            'sleep_quality_score' =>
                $latestRealtime?->sleep_quality_score !== null
                    ? round((float) $latestRealtime->sleep_quality_score, 1)
                    : null,

            'sleep_duration_hours' =>
                $latestRealtime?->sleep_duration_hours,

            'performance_change' =>
                $latestPerformanceTrend?->change_percentage !== null
                    ? round(
                        (float) $latestPerformanceTrend->change_percentage,
                        1
                    )
                    : null,

            'performance_direction' =>
                $latestPerformanceTrend?->trend_direction,

            'mental_wellbeing_score' =>
                $latestSdoh?->mental_wellbeing_score !== null
                    ? round((float) $latestSdoh->mental_wellbeing_score, 1)
                    : null,

            'stress_level' =>
                $latestSdoh?->stress_level,

            'healthy_diet' =>
                $latestSdoh?->has_healthy_diet,
        ];

        /*
         * Historique canonique des blessures.
         */
        $playerInjuriesDiseases = DB::table('injuries')
            ->where('player_id', $player->id)
            ->orderByDesc('date')
            ->get()
            ->map(fn ($injury) => (object) [
                'incident_date' => $injury->date,
                'type' => 'injury',
                'injury_type' => $injury->type,
                'body_zone' => $injury->body_zone,
                'severity' => $injury->severity,
                'status' => $injury->status,
                'description' => $injury->description,
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
         * Statuts factuels issus des données canoniques.
         * Aucun score de conformité FIFA/WADA n'est inventé.
         */
        $complianceStatus = collect();

        if ($playerLicenses->isNotEmpty()) {
            $license = $playerLicenses->first();
            $licenseStatus = $license->status ?? 'unknown';

            $complianceStatus->push((object) [
                'compliance_type' => 'license',
                'label' => 'Licence',
                'status' => $licenseStatus,
                'summary' => match ($licenseStatus) {
                    'active' => 'Licence active',
                    'expired' => 'Licence expirée',
                    'pending' => 'Licence en attente',
                    default => ucfirst(str_replace('_', ' ', $licenseStatus)),
                },
                'detail' => $license->expiry_date
                    ? 'Expiration : ' . $license->expiry_date
                    : null,
                'last_assessment_date' =>
                    $license->issue_date ?? $license->updated_at,
            ]);
        }

        if ($latestPcma) {
            $pcmaStatus = $latestPcma->status;

            $complianceStatus->push((object) [
                'compliance_type' => 'pcma',
                'label' => 'PCMA',
                'status' => $pcmaStatus,
                'summary' => match ($pcmaStatus) {
                    'cleared' => 'Aptitude médicale : APTE',
                    'not_cleared' => 'Aptitude médicale : NON APTE',
                    'pending' => 'Évaluation en attente',
                    'failed' => 'Évaluation non aboutie',
                    'completed' => 'Évaluation terminée',
                    default => ucfirst(str_replace('_', ' ', $pcmaStatus)),
                },
                'detail' =>
                    'Conformité FIFA attestée : '
                    . ($latestPcma->fifa_compliant ? 'oui' : 'non')
                    . ' · Document signé : '
                    . ($latestPcma->is_signed ? 'oui' : 'non'),
                'last_assessment_date' =>
                    $latestPcma->assessment_date,
            ]);
        }

        if ($dopingRaw->isNotEmpty()) {
            $latestDoping = $dopingRaw
                ->sortByDesc('control_date')
                ->first();

            $result = strtolower((string) $latestDoping->result);

            $complianceStatus->push((object) [
                'compliance_type' => 'anti_doping',
                'label' => 'Contrôle antidopage',
                'status' => $result,
                'summary' => match ($result) {
                    'negative' => 'Résultat négatif',
                    'positive' => 'Résultat positif',
                    'inconclusive' => 'Résultat inconclusif',
                    'pending' => 'Résultat en attente',
                    default => ucfirst($result ?: 'Résultat indisponible'),
                },
                'detail' => 'Résultat enregistré du dernier contrôle.',
                'last_assessment_date' =>
                    $latestDoping->control_date,
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
            'latestPerformance',
            'latestMatchPerformance',
            'latestFitSnapshot',
            'previousFitSnapshot',
            'latestFitAttempt',
            'fitMissingAxes',
            'fitEvolution',
            'fitDiagnosis',
            'playerLicenses',
            'performanceTrends',
            'playerPerformanceTests',
            'playerLaboratoryResults',
            'trendIndicators',
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
            'profileSynthetic',
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

    private function educationLabel(?string $level): ?string
    {
        if (!$level) {
            return null;
        }

        return match (strtolower(trim($level))) {
            'none' => 'Aucun',
            'primary' => 'Primaire',
            'secondary' => 'Secondaire',
            'bachelor' => 'Licence',
            'master' => 'Master',
            'doctorate' => 'Doctorat',
            default => $level,
        };
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
