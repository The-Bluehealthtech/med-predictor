<?php

namespace App\Services;

use App\Models\PosturalAssessment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PosturalAIAnalysisService
{
    private $aiServiceUrl;
    
    public function __construct()
    {
        $this->aiServiceUrl = config('services.ai.postural_analysis_url', 'http://localhost:3001');
    }
    
    /**
     * Analyser automatiquement les déséquilibres posturaux avec IA
     */
    public function analyzeWithAI(PosturalAssessment $assessment): array
    {
        try {
            // Préparer les données pour l'analyse IA
            $analysisData = $this->prepareAnalysisData($assessment);
            
            // Appeler le service IA
            $response = Http::timeout(30)->post($this->aiServiceUrl . '/api/v1/postural/analyze', [
                'assessment_data' => $analysisData,
                'player_profile' => $this->getPlayerProfile($assessment->player),
                'analysis_type' => 'postural_imbalance'
            ]);
            
            if ($response->successful()) {
                $aiAnalysis = $response->json();
                
                // Enrichir l'analyse avec des données supplémentaires
                $enrichedAnalysis = $this->enrichAnalysis($aiAnalysis, $assessment);
                
                // Mettre en cache l'analyse pour éviter les appels répétés
                Cache::put("postural_ai_analysis_{$assessment->id}", $enrichedAnalysis, 3600);
                
                Log::info('Analyse IA posturale réussie', [
                    'assessment_id' => $assessment->id,
                    'ai_score' => $enrichedAnalysis['ai_confidence'] ?? 0
                ]);
                
                return $enrichedAnalysis;
                
            } else {
                Log::warning('Échec de l\'analyse IA posturale', [
                    'assessment_id' => $assessment->id,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                // Retourner une analyse de fallback
                return $this->generateFallbackAnalysis($assessment);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse IA posturale', [
                'assessment_id' => $assessment->id,
                'error' => $e->getMessage()
            ]);
            
            return $this->generateFallbackAnalysis($assessment);
        }
    }
    
    /**
     * Préparer les données pour l'analyse IA
     */
    private function prepareAnalysisData(PosturalAssessment $assessment): array
    {
        $annotations = $assessment->annotations ?? [];
        $markers = $annotations['markers'] ?? [];
        $angles = $annotations['angles'] ?? [];
        
        return [
            'assessment_id' => $assessment->id,
            'view' => $assessment->view,
            'markers' => $this->normalizeMarkers($markers),
            'angles' => $this->normalizeAngles($angles),
            'clinical_notes' => $assessment->clinical_notes,
            'assessment_date' => $assessment->assessment_date->toISOString(),
            'player_position' => $assessment->player->position,
            'player_age' => $assessment->player->age ?? 25,
            'player_height' => $assessment->player->height ?? 175,
            'player_weight' => $assessment->player->weight ?? 70
        ];
    }
    
    /**
     * Normaliser les marqueurs pour l'analyse IA
     */
    private function normalizeMarkers(array $markers): array
    {
        return array_map(function($marker) {
            return [
                'x' => $marker['x'] ?? 0,
                'y' => $marker['y'] ?? 0,
                'landmark' => $marker['landmark'] ?? 'unknown',
                'color' => $marker['color'] ?? '#FF0000',
                'timestamp' => $marker['timestamp'] ?? now()->toISOString()
            ];
        }, $markers);
    }
    
    /**
     * Normaliser les angles pour l'analyse IA
     */
    private function normalizeAngles(array $angles): array
    {
        return array_map(function($angle) {
            return [
                'value' => $angle['value'] ?? 0,
                'points' => $angle['points'] ?? [],
                'timestamp' => $angle['timestamp'] ?? now()->toISOString()
            ];
        }, $angles);
    }
    
    /**
     * Obtenir le profil du joueur
     */
    private function getPlayerProfile($player): array
    {
        return [
            'id' => $player->id,
            'name' => $player->name,
            'position' => $player->position,
            'age' => $player->age ?? 25,
            'height' => $player->height ?? 175,
            'weight' => $player->weight ?? 70,
            'injury_history' => $this->getInjuryHistory($player),
            'previous_assessments' => $this->getPreviousAssessments($player)
        ];
    }
    
    /**
     * Obtenir l'historique des blessures
     */
    private function getInjuryHistory($player): array
    {
        // Simuler un historique de blessures (à connecter avec le vrai système)
        return [
            'total_injuries' => 0,
            'last_injury_date' => null,
            'injury_types' => [],
            'recovery_time' => 0
        ];
    }
    
    /**
     * Obtenir les évaluations précédentes
     */
    private function getPreviousAssessments($player): array
    {
        return $player->posturalAssessments()
            ->orderBy('assessment_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function($assessment) {
                return [
                    'id' => $assessment->id,
                    'date' => $assessment->assessment_date->toISOString(),
                    'score' => $assessment->analysis_data['overall_score'] ?? 0,
                    'view' => $assessment->view
                ];
            })
            ->toArray();
    }
    
    /**
     * Enrichir l'analyse IA avec des données supplémentaires
     */
    private function enrichAnalysis(array $aiAnalysis, PosturalAssessment $assessment): array
    {
        $enriched = $aiAnalysis;
        
        // Ajouter des métadonnées
        $enriched['metadata'] = [
            'analysis_timestamp' => now()->toISOString(),
            'ai_model_version' => 'postural-v1.0',
            'confidence_threshold' => 0.7
        ];
        
        // Ajouter des recommandations personnalisées
        $enriched['personalized_recommendations'] = $this->generatePersonalizedRecommendations(
            $aiAnalysis, 
            $assessment->player
        );
        
        // Ajouter des alertes de risque
        $enriched['risk_alerts'] = $this->generateRiskAlerts($aiAnalysis, $assessment);
        
        // Ajouter des métriques de progression
        $enriched['progression_metrics'] = $this->calculateProgressionMetrics($assessment);
        
        return $enriched;
    }
    
    /**
     * Générer des recommandations personnalisées
     */
    private function generatePersonalizedRecommendations(array $aiAnalysis, $player): array
    {
        $recommendations = [];
        
        // Recommandations basées sur la position du joueur
        switch ($player->position) {
            case 'ST':
            case 'CF':
                $recommendations[] = 'Exercices de stabilisation pour les attaquants';
                break;
            case 'MF':
                $recommendations[] = 'Renforcement de la mobilité pour les milieux';
                break;
            case 'DF':
                $recommendations[] = 'Exercices de puissance pour les défenseurs';
                break;
            case 'GK':
                $recommendations[] = 'Travail spécifique pour les gardiens';
                break;
        }
        
        // Recommandations basées sur l'âge
        if ($player->age > 30) {
            $recommendations[] = 'Programme de récupération adapté aux joueurs seniors';
        }
        
        // Recommandations basées sur l'analyse IA
        if (isset($aiAnalysis['imbalances'])) {
            foreach ($aiAnalysis['imbalances'] as $imbalance) {
                if ($imbalance['type'] === 'muscle_tension') {
                    $recommendations[] = 'Séances de massage thérapeutique recommandées';
                }
            }
        }
        
        return array_unique($recommendations);
    }
    
    /**
     * Générer des alertes de risque
     */
    private function generateRiskAlerts(array $aiAnalysis, PosturalAssessment $assessment): array
    {
        $alerts = [];
        
        $overallScore = $aiAnalysis['overall_score'] ?? 0;
        
        if ($overallScore >= 8) {
            $alerts[] = [
                'level' => 'high',
                'message' => 'Risque élevé de blessure détecté',
                'action' => 'Évaluation médicale immédiate recommandée'
            ];
        } elseif ($overallScore >= 5) {
            $alerts[] = [
                'level' => 'moderate',
                'message' => 'Risque modéré de blessure',
                'action' => 'Surveillance renforcée nécessaire'
            ];
        }
        
        // Alertes basées sur les déséquilibres spécifiques
        if (isset($aiAnalysis['imbalances'])) {
            foreach ($aiAnalysis['imbalances'] as $imbalance) {
                if ($imbalance['severity'] === 'high') {
                    $alerts[] = [
                        'level' => 'high',
                        'message' => 'Déséquilibre sévère détecté: ' . $imbalance['type'],
                        'action' => 'Intervention corrective urgente'
                    ];
                }
            }
        }
        
        return $alerts;
    }
    
    /**
     * Calculer les métriques de progression
     */
    private function calculateProgressionMetrics(PosturalAssessment $assessment): array
    {
        $previousAssessments = $assessment->player->posturalAssessments()
            ->where('id', '!=', $assessment->id)
            ->orderBy('assessment_date', 'desc')
            ->limit(3)
            ->get();
        
        if ($previousAssessments->isEmpty()) {
            return [
                'trend' => 'baseline',
                'improvement_rate' => 0,
                'sessions_count' => 1
            ];
        }
        
        $currentScore = $assessment->analysis_data['overall_score'] ?? 0;
        $previousScore = $previousAssessments->first()->analysis_data['overall_score'] ?? 0;
        
        $improvementRate = $previousScore > 0 ? (($previousScore - $currentScore) / $previousScore) * 100 : 0;
        
        return [
            'trend' => $improvementRate > 0 ? 'improving' : ($improvementRate < 0 ? 'worsening' : 'stable'),
            'improvement_rate' => $improvementRate,
            'sessions_count' => $previousAssessments->count() + 1,
            'previous_scores' => $previousAssessments->pluck('analysis_data.overall_score')->toArray()
        ];
    }
    
    /**
     * Générer une analyse de fallback
     */
    private function generateFallbackAnalysis(PosturalAssessment $assessment): array
    {
        $annotations = $assessment->annotations ?? [];
        $markers = $annotations['markers'] ?? [];
        $angles = $annotations['angles'] ?? [];
        
        $score = 0;
        $imbalances = [];
        $riskFactors = [];
        
        // Analyse basique des marqueurs
        foreach ($markers as $marker) {
            $landmark = $marker['landmark'] ?? 'unknown';
            
            if (str_contains($landmark, 'shoulder')) {
                $imbalances[] = [
                    'type' => 'muscle_tension',
                    'location' => $landmark,
                    'severity' => 'moderate',
                    'description' => 'Tension détectée dans la région de l\'épaule'
                ];
                $score += 2;
            }
        }
        
        // Analyse basique des angles
        foreach ($angles as $angle) {
            $value = $angle['value'] ?? 0;
            
            if ($value < 150 || $value > 180) {
                $riskFactors[] = [
                    'type' => 'abnormal_angle',
                    'value' => $value,
                    'severity' => $value < 120 ? 'high' : 'moderate',
                    'description' => 'Angle anormal détecté: ' . $value . '°'
                ];
                $score += 3;
            }
        }
        
        return [
            'overall_score' => min($score, 10),
            'imbalances' => $imbalances,
            'risk_factors' => $riskFactors,
            'recommendations' => $this->generateBasicRecommendations($imbalances, $riskFactors),
            'ai_confidence' => 0.3, // Faible confiance pour l'analyse de fallback
            'fallback_analysis' => true
        ];
    }
    
    /**
     * Générer des recommandations de base
     */
    private function generateBasicRecommendations(array $imbalances, array $riskFactors): array
    {
        $recommendations = [];
        
        if (!empty($imbalances)) {
            $recommendations[] = 'Évaluation par un physiothérapeute recommandée';
        }
        
        if (!empty($riskFactors)) {
            $recommendations[] = 'Surveillance renforcée de la posture';
        }
        
        if (empty($imbalances) && empty($riskFactors)) {
            $recommendations[] = 'Posture dans les normes';
        }
        
        return $recommendations;
    }
    
    /**
     * Analyser les tendances à long terme
     */
    public function analyzeLongTermTrends($player): array
    {
        $assessments = $player->posturalAssessments()
            ->orderBy('assessment_date', 'asc')
            ->get();
        
        if ($assessments->count() < 2) {
            return [
                'trend' => 'insufficient_data',
                'message' => 'Données insuffisantes pour l\'analyse des tendances'
            ];
        }
        
        $scores = $assessments->pluck('analysis_data.overall_score')->filter()->toArray();
        
        if (empty($scores)) {
            return [
                'trend' => 'no_scores',
                'message' => 'Aucun score disponible pour l\'analyse'
            ];
        }
        
        // Calculer la tendance linéaire
        $n = count($scores);
        $sumX = array_sum(range(1, $n));
        $sumY = array_sum($scores);
        $sumXY = 0;
        $sumX2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumXY += ($i + 1) * $scores[$i];
            $sumX2 += ($i + 1) * ($i + 1);
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        
        return [
            'trend' => $slope > 0.5 ? 'worsening' : ($slope < -0.5 ? 'improving' : 'stable'),
            'slope' => $slope,
            'average_score' => array_sum($scores) / count($scores),
            'sessions_count' => $n,
            'trend_strength' => abs($slope)
        ];
    }
} 