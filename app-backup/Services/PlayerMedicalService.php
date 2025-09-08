<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PCMA;
use App\Models\MedicalPrediction;
use App\Models\HealthRecord;
use Carbon\Carbon;

class PlayerMedicalService
{
    protected $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getMedicalData()
    {
        return [
            'recentHealthRecords' => $this->getRecentHealthRecords(),
            'pcmas' => $this->getPCMAs(),
            'medicalPredictions' => $this->getMedicalPredictions(),
            'injuries' => $this->getInjuries(),
            'illnesses' => $this->getIllnesses(),
            'alerts' => $this->getAlerts(),
            'recommendations' => $this->getRecommendations()
        ];
    }

    private function getRecentHealthRecords()
    {
        return HealthRecord::where('player_id', $this->player->id)
            ->orderBy('record_date', 'desc')
            ->limit(3)
            ->get()
            ->map(function($record) {
                return [
                    'id' => $record->id,
                    'date' => Carbon::parse($record->record_date)->format('d/m/Y'),
                    'type' => $record->visit_type ?? 'Bilan général',
                    'doctor' => $record->doctor_name ?? 'Dr. Médecin',
                    'status' => $record->status ?? 'active',
                    'risk_score' => $record->risk_score ? round($record->risk_score * 100) : 85
                ];
            });
    }

    private function getPCMAs()
    {
        return PCMA::where('athlete_id', $this->player->id)
            ->orderBy('assessment_date', 'desc')
            ->limit(3)
            ->get()
            ->map(function($pcma) {
                return [
                    'id' => $pcma->id,
                    'type' => strtoupper($pcma->type ?? 'PCMA'),
                    'date' => Carbon::parse($pcma->assessment_date)->format('d/m/Y'),
                    'status' => $pcma->status ?? 'pending',
                    'fitness_status' => $this->getFitnessStatus($pcma),
                    'assessor' => $pcma->signed_by ?? 'Non spécifié'
                ];
            });
    }

    private function getMedicalPredictions()
    {
        return MedicalPrediction::where('player_id', $this->player->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($prediction) {
                return [
                    'id' => $prediction->id,
                    'condition' => $prediction->predicted_condition ?? 'Aucune',
                    'probability' => $prediction->probability ? round($prediction->probability * 100) : 0,
                    'risk_level' => $this->getRiskLevel($prediction->probability ?? 0),
                    'recommendation' => $prediction->recommendation ?? 'Surveillance continue'
                ];
            });
    }

    private function getInjuries()
    {
        // Simuler des blessures basées sur les health_records
        $records = HealthRecord::where('player_id', $this->player->id)
            ->where('diagnosis', 'like', '%blessure%')
            ->orWhere('diagnosis', 'like', '%injury%')
            ->orderBy('record_date', 'desc')
            ->limit(2)
            ->get();

        if ($records->isEmpty()) {
            return [
                [
                    'id' => 1,
                    'type' => 'Entorse cheville droite',
                    'date' => '15/02/2025',
                    'severity' => 'Légère',
                    'status' => 'Résolue',
                    'recovery_time' => '3 semaines',
                    'treatment' => 'Kinésithérapie + repos'
                ]
            ];
        }

        return $records->map(function($record) {
            return [
                'id' => $record->id,
                'type' => $record->diagnosis ?? 'Blessure',
                'date' => Carbon::parse($record->record_date)->format('d/m/Y'),
                'severity' => $this->getSeverity($record->risk_score ?? 0.5),
                'status' => $record->status ?? 'active',
                'recovery_time' => 'En cours',
                'treatment' => $record->treatment_plan ?? 'Surveillance'
            ];
        });
    }

    private function getIllnesses()
    {
        // Simuler des maladies basées sur les health_records
        $records = HealthRecord::where('player_id', $this->player->id)
            ->where('diagnosis', 'like', '%infection%')
            ->orWhere('diagnosis', 'like', '%maladie%')
            ->orderBy('record_date', 'desc')
            ->limit(2)
            ->get();

        if ($records->isEmpty()) {
            return [
                [
                    'id' => 1,
                    'type' => 'Infection respiratoire',
                    'date' => '10/01/2025',
                    'severity' => 'Modérée',
                    'status' => 'Résolue',
                    'symptoms' => 'Toux, fièvre',
                    'treatment' => 'Antibiotiques'
                ]
            ];
        }

        return $records->map(function($record) {
            return [
                'id' => $record->id,
                'type' => $record->diagnosis ?? 'Maladie',
                'date' => Carbon::parse($record->record_date)->format('d/m/Y'),
                'severity' => $this->getSeverity($record->risk_score ?? 0.5),
                'status' => $record->status ?? 'active',
                'symptoms' => $record->chief_complaint ?? 'Non spécifié',
                'treatment' => $record->prescriptions ?? 'Surveillance'
            ];
        });
    }

    private function getAlerts()
    {
        $alerts = [];
        
        // Alertes basées sur les PCMA
        $pendingPCMAs = PCMA::where('athlete_id', $this->player->id)
            ->where('status', 'pending')
            ->count();
        
        if ($pendingPCMAs > 0) {
            $alerts[] = [
                'id' => 'pcma_pending',
                'type' => 'PCMA en attente',
                'message' => $pendingPCMAs . ' évaluation(s) PCMA en attente',
                'priority' => 'HAUTE',
                'icon' => 'fas fa-exclamation-triangle'
            ];
        }

        // Alertes basées sur les health_records
        $highRiskRecords = HealthRecord::where('player_id', $this->player->id)
            ->where('risk_score', '>', 0.8)
            ->count();
        
        if ($highRiskRecords > 0) {
            $alerts[] = [
                'id' => 'health_risk',
                'type' => 'Risque santé élevé',
                'message' => $highRiskRecords . ' bilan(s) avec risque élevé',
                'priority' => 'MOYENNE',
                'icon' => 'fas fa-heartbeat'
            ];
        }

        if (empty($alerts)) {
            $alerts[] = [
                'id' => 'all_clear',
                'type' => 'Aucune alerte',
                'message' => 'Tous les bilans sont à jour',
                'priority' => 'BASSE',
                'icon' => 'fas fa-check-circle'
            ];
        }

        return $alerts;
    }

    private function getRecommendations()
    {
        $recommendations = [];
        
        // Recommandations basées sur les PCMA
        $lastPCMA = PCMA::where('athlete_id', $this->player->id)
            ->orderBy('assessment_date', 'desc')
            ->first();
        
        if ($lastPCMA && $lastPCMA->status === 'pending') {
            $recommendations[] = [
                'id' => 'pcma_complete',
                'title' => 'Compléter l\'évaluation PCMA',
                'description' => 'Finaliser l\'évaluation médicale en cours',
                'priority' => 'HAUTE',
                'icon' => 'fas fa-clipboard-check'
            ];
        }

        // Recommandations basées sur les health_records
        $lastRecord = HealthRecord::where('player_id', $this->player->id)
            ->orderBy('record_date', 'desc')
            ->first();
        
        if ($lastRecord) {
            $daysSinceLastRecord = Carbon::now()->diffInDays($lastRecord->record_date);
            if ($daysSinceLastRecord > 90) {
                $recommendations[] = [
                    'id' => 'health_checkup',
                    'title' => 'Bilan de santé recommandé',
                    'description' => 'Dernier bilan il y a ' . $daysSinceLastRecord . ' jours',
                    'priority' => 'MOYENNE',
                    'icon' => 'fas fa-stethoscope'
                ];
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'id' => 'maintain',
                'title' => 'Maintenir la routine',
                'description' => 'Continuer les bonnes pratiques actuelles',
                'priority' => 'BASSE',
                'icon' => 'fas fa-thumbs-up'
            ];
        }

        return $recommendations;
    }

    private function getFitnessStatus($pcma)
    {
        if (!$pcma->result_json) return 'Non évalué';
        
        // Vérifier si c'est déjà un array
        if (is_array($pcma->result_json)) {
            return $pcma->result_json['fitnessStatus'] ?? 'Non évalué';
        }
        
        // Si c'est une string, la décoder
        if (is_string($pcma->result_json)) {
            $result = json_decode($pcma->result_json, true);
            return $result['fitnessStatus'] ?? 'Non évalué';
        }
        
        return 'Non évalué';
    }

    private function getRiskLevel($probability)
    {
        if ($probability >= 0.8) return 'ÉLEVÉ';
        if ($probability >= 0.5) return 'MOYEN';
        return 'FAIBLE';
    }

    private function getSeverity($riskScore)
    {
        if ($riskScore >= 0.8) return 'Élevée';
        if ($riskScore >= 0.5) return 'Modérée';
        return 'Légère';
    }
}
