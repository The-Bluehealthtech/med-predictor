<?php

namespace App\Services;

use App\Models\PosturalAssessment;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PosturalAssessmentService
{
    /**
     * Sauvegarder automatiquement les annotations posturales
     */
    public function saveAnnotations(PosturalAssessment $assessment, array $annotations): bool
    {
        try {
            DB::beginTransaction();
            
            // Mettre à jour l'évaluation avec les nouvelles annotations
            $assessment->update([
                'annotations' => $annotations,
                'last_updated' => now(),
            ]);
            
            // Analyser les annotations pour détecter les déséquilibres
            $analysis = $this->analyzePosturalImbalances($annotations);
            
            // Sauvegarder l'analyse
            $assessment->update([
                'analysis_data' => $analysis,
            ]);
            
            // Mettre à jour le score de risque du joueur si nécessaire
            $this->updatePlayerRiskScore($assessment->player, $analysis);
            
            DB::commit();
            
            Log::info('Annotations posturales sauvegardées', [
                'assessment_id' => $assessment->id,
                'player_id' => $assessment->player_id,
                'annotations_count' => count($annotations['markers'] ?? []) + count($annotations['angles'] ?? [])
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la sauvegarde des annotations posturales', [
                'assessment_id' => $assessment->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Analyser les déséquilibres posturaux
     */
    private function analyzePosturalImbalances(array $annotations): array
    {
        $analysis = [
            'imbalances' => [],
            'risk_factors' => [],
            'recommendations' => [],
            'overall_score' => 0
        ];
        
        $markers = $annotations['markers'] ?? [];
        $angles = $annotations['angles'] ?? [];
        
        // Analyser les marqueurs de tension
        foreach ($markers as $marker) {
            $landmark = $marker['landmark'] ?? 'unknown';
            
            // Détecter les tensions musculaires
            if (str_contains($landmark, 'shoulder')) {
                $analysis['imbalances'][] = [
                    'type' => 'muscle_tension',
                    'location' => $landmark,
                    'severity' => 'moderate',
                    'description' => 'Tension détectée dans la région de l\'épaule'
                ];
            }
            
            if (str_contains($landmark, 'hip')) {
                $analysis['imbalances'][] = [
                    'type' => 'pelvic_alignment',
                    'location' => $landmark,
                    'severity' => 'moderate',
                    'description' => 'Désalignement pelvien détecté'
                ];
            }
        }
        
        // Analyser les angles
        foreach ($angles as $angle) {
            $value = $angle['value'] ?? 0;
            
            // Détecter les angles anormaux
            if ($value < 150 || $value > 180) {
                $analysis['risk_factors'][] = [
                    'type' => 'abnormal_angle',
                    'value' => $value,
                    'severity' => $value < 120 ? 'high' : 'moderate',
                    'description' => 'Angle anormal détecté: ' . $value . '°'
                ];
            }
        }
        
        // Calculer le score global
        $analysis['overall_score'] = $this->calculateRiskScore($analysis);
        
        // Générer des recommandations
        $analysis['recommendations'] = $this->generateRecommendations($analysis);
        
        return $analysis;
    }
    
    /**
     * Calculer le score de risque
     */
    private function calculateRiskScore(array $analysis): int
    {
        $score = 0;
        
        // Points pour les déséquilibres
        foreach ($analysis['imbalances'] as $imbalance) {
            $score += $imbalance['severity'] === 'high' ? 3 : 1;
        }
        
        // Points pour les facteurs de risque
        foreach ($analysis['risk_factors'] as $risk) {
            $score += $risk['severity'] === 'high' ? 4 : 2;
        }
        
        return min($score, 10); // Score max de 10
    }
    
    /**
     * Générer des recommandations basées sur l'analyse
     */
    private function generateRecommendations(array $analysis): array
    {
        $recommendations = [];
        
        foreach ($analysis['imbalances'] as $imbalance) {
            switch ($imbalance['type']) {
                case 'muscle_tension':
                    $recommendations[] = 'Exercices d\'étirement pour la région ' . $imbalance['location'];
                    $recommendations[] = 'Massage thérapeutique recommandé';
                    break;
                    
                case 'pelvic_alignment':
                    $recommendations[] = 'Exercices de stabilisation pelvienne';
                    $recommendations[] = 'Évaluation par un physiothérapeute';
                    break;
            }
        }
        
        foreach ($analysis['risk_factors'] as $risk) {
            if ($risk['type'] === 'abnormal_angle') {
                $recommendations[] = 'Correction posturale nécessaire';
                $recommendations[] = 'Surveillance renforcée recommandée';
            }
        }
        
        return array_unique($recommendations);
    }
    
    /**
     * Mettre à jour le score de risque du joueur
     */
    private function updatePlayerRiskScore(Player $player, array $analysis): void
    {
        $riskScore = $analysis['overall_score'] ?? 0;
        
        // Mettre à jour le score de risque du joueur
        $player->update([
            'injury_risk_score' => $riskScore / 10, // Normaliser entre 0 et 1
            'injury_risk_level' => $this->getRiskLevel($riskScore),
            'injury_risk_reason' => 'Analyse posturale: ' . count($analysis['imbalances']) . ' déséquilibres détectés',
            'injury_risk_last_assessed' => now(),
        ]);
    }
    
    /**
     * Déterminer le niveau de risque
     */
    private function getRiskLevel(int $score): string
    {
        if ($score >= 7) return 'high';
        if ($score >= 4) return 'moderate';
        return 'low';
    }
    
    /**
     * Comparer les évaluations au fil du temps
     */
    public function compareAssessments(Player $player, int $limit = 5): array
    {
        $assessments = $player->posturalAssessments()
            ->orderBy('assessment_date', 'desc')
            ->limit($limit)
            ->get();
            
        $comparison = [
            'player' => $player->only(['id', 'name', 'position']),
            'assessments' => [],
            'trends' => [],
            'improvements' => [],
            'worsening' => []
        ];
        
        foreach ($assessments as $assessment) {
            $analysis = $assessment->analysis_data ?? [];
            
            $comparison['assessments'][] = [
                'id' => $assessment->id,
                'date' => $assessment->assessment_date,
                'score' => $analysis['overall_score'] ?? 0,
                'imbalances_count' => count($analysis['imbalances'] ?? []),
                'risk_factors_count' => count($analysis['risk_factors'] ?? []),
                'recommendations' => $analysis['recommendations'] ?? []
            ];
        }
        
        // Analyser les tendances
        if (count($comparison['assessments']) > 1) {
            $comparison['trends'] = $this->analyzeTrends($comparison['assessments']);
        }
        
        return $comparison;
    }
    
    /**
     * Analyser les tendances entre les évaluations
     */
    private function analyzeTrends(array $assessments): array
    {
        $trends = [
            'score_trend' => 'stable',
            'improvement_rate' => 0,
            'key_changes' => []
        ];
        
        if (count($assessments) < 2) return $trends;
        
        $firstScore = $assessments[0]['score'];
        $lastScore = end($assessments)['score'];
        
        if ($lastScore < $firstScore) {
            $trends['score_trend'] = 'improving';
            $trends['improvement_rate'] = (($firstScore - $lastScore) / $firstScore) * 100;
        } elseif ($lastScore > $firstScore) {
            $trends['score_trend'] = 'worsening';
            $trends['improvement_rate'] = (($lastScore - $firstScore) / $firstScore) * 100;
        }
        
        return $trends;
    }
    
    /**
     * Exporter les données d'évaluation
     */
    public function exportAssessmentData(PosturalAssessment $assessment): array
    {
        return [
            'assessment_id' => $assessment->id,
            'player' => [
                'id' => $assessment->player->id,
                'name' => $assessment->player->name,
                'position' => $assessment->player->position
            ],
            'assessment_date' => $assessment->assessment_date,
            'view' => $assessment->view,
            'annotations' => $assessment->annotations,
            'analysis' => $assessment->analysis_data,
            'clinical_notes' => $assessment->clinical_notes,
            'recommendations' => $assessment->recommendations,
            'exported_at' => now()->toISOString()
        ];
    }
} 