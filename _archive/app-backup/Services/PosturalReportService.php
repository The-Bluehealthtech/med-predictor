<?php

namespace App\Services;

use App\Models\PosturalAssessment;
use App\Models\Player;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

class PosturalReportService
{
    /**
     * Générer un rapport PDF d'évaluation posturale
     */
    public function generateReport(PosturalAssessment $assessment): string
    {
        $dompdf = new Dompdf();
        
        // Configuration PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf->setOptions($options);
        
        // Générer le HTML du rapport
        $html = $this->generateReportHTML($assessment);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Sauvegarder le PDF
        $filename = 'postural_report_' . $assessment->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $path = 'reports/postural/' . $filename;
        
        Storage::put('public/' . $path, $dompdf->output());
        
        return $path;
    }
    
    /**
     * Générer le HTML du rapport
     */
    private function generateReportHTML(PosturalAssessment $assessment): string
    {
        $player = $assessment->player;
        $analysis = $assessment->analysis_data ?? [];
        $annotations = $assessment->annotations ?? [];
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Rapport d\'Évaluation Posturale</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .section { margin-bottom: 30px; }
                .section h2 { color: #2563eb; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
                .info-grid { display: table; width: 100%; margin-bottom: 20px; }
                .info-row { display: table-row; }
                .info-cell { display: table-cell; padding: 8px; border-bottom: 1px solid #f3f4f6; }
                .info-label { font-weight: bold; width: 30%; }
                .risk-high { color: #dc2626; }
                .risk-moderate { color: #d97706; }
                .risk-low { color: #059669; }
                .recommendation { background: #f0f9ff; padding: 10px; margin: 5px 0; border-left: 4px solid #2563eb; }
                .annotation { background: #fef3c7; padding: 8px; margin: 3px 0; border-radius: 4px; }
                .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #6b7280; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📊 Rapport d\'Évaluation Posturale</h1>
                <p>Med Predictor - Système d\'Analyse Posturale</p>
                <p>Généré le ' . now()->format('d/m/Y à H:i') . '</p>
            </div>
            
            <div class="section">
                <h2>👤 Informations du Patient</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell info-label">Nom:</div>
                        <div class="info-cell">' . $player->name . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell info-label">Position:</div>
                        <div class="info-cell">' . $player->position . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell info-label">Club:</div>
                        <div class="info-cell">' . ($player->club->name ?? 'Non défini') . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell info-label">Date d\'évaluation:</div>
                        <div class="info-cell">' . $assessment->assessment_date->format('d/m/Y H:i') . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell info-label">Type d\'évaluation:</div>
                        <div class="info-cell">' . ucfirst($assessment->assessment_type) . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell info-label">Vue analysée:</div>
                        <div class="info-cell">' . ucfirst($assessment->view) . '</div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2>📊 Analyse Posturale</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell info-label">Score de risque:</div>
                        <div class="info-cell risk-' . ($analysis['overall_score'] ?? 0 >= 7 ? 'high' : ($analysis['overall_score'] ?? 0 >= 4 ? 'moderate' : 'low')) . '">
                            ' . ($analysis['overall_score'] ?? 0) . '/10
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell info-label">Déséquilibres détectés:</div>
                        <div class="info-cell">' . count($analysis['imbalances'] ?? []) . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell info-label">Facteurs de risque:</div>
                        <div class="info-cell">' . count($analysis['risk_factors'] ?? []) . '</div>
                    </div>
                </div>
                
                ' . $this->generateImbalancesSection($analysis) . '
                ' . $this->generateRiskFactorsSection($analysis) . '
            </div>
            
            <div class="section">
                <h2>🎯 Annotations</h2>
                ' . $this->generateAnnotationsSection($annotations) . '
            </div>
            
            <div class="section">
                <h2>💡 Recommandations</h2>
                ' . $this->generateRecommendationsSection($analysis) . '
            </div>
            
            ' . ($assessment->clinical_notes ? '
            <div class="section">
                <h2>📝 Notes Cliniques</h2>
                <p>' . nl2br(htmlspecialchars($assessment->clinical_notes)) . '</p>
            </div>
            ' : '') . '
            
            ' . ($assessment->recommendations ? '
            <div class="section">
                <h2>📋 Recommandations Cliniques</h2>
                <p>' . nl2br(htmlspecialchars($assessment->recommendations)) . '</p>
            </div>
            ' : '') . '
            
            <div class="footer">
                <p>Ce rapport a été généré automatiquement par le système Med Predictor.</p>
                <p>Pour toute question, veuillez contacter l\'équipe médicale.</p>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Générer la section des déséquilibres
     */
    private function generateImbalancesSection(array $analysis): string
    {
        $imbalances = $analysis['imbalances'] ?? [];
        
        if (empty($imbalances)) {
            return '<p>Aucun déséquilibre postural détecté.</p>';
        }
        
        $html = '<h3>Déséquilibres Détectés</h3><ul>';
        
        foreach ($imbalances as $imbalance) {
            $severityClass = $imbalance['severity'] === 'high' ? 'risk-high' : 
                           ($imbalance['severity'] === 'moderate' ? 'risk-moderate' : 'risk-low');
            
            $html .= '<li class="' . $severityClass . '">
                <strong>' . ucfirst($imbalance['type']) . '</strong> - ' . $imbalance['location'] . '<br>
                <em>' . $imbalance['description'] . '</em>
            </li>';
        }
        
        return $html . '</ul>';
    }
    
    /**
     * Générer la section des facteurs de risque
     */
    private function generateRiskFactorsSection(array $analysis): string
    {
        $riskFactors = $analysis['risk_factors'] ?? [];
        
        if (empty($riskFactors)) {
            return '<p>Aucun facteur de risque majeur détecté.</p>';
        }
        
        $html = '<h3>Facteurs de Risque</h3><ul>';
        
        foreach ($riskFactors as $risk) {
            $severityClass = $risk['severity'] === 'high' ? 'risk-high' : 
                           ($risk['severity'] === 'moderate' ? 'risk-moderate' : 'risk-low');
            
            $html .= '<li class="' . $severityClass . '">
                <strong>' . ucfirst($risk['type']) . '</strong><br>
                <em>' . $risk['description'] . '</em>
            </li>';
        }
        
        return $html . '</ul>';
    }
    
    /**
     * Générer la section des annotations
     */
    private function generateAnnotationsSection(array $annotations): string
    {
        $markers = $annotations['markers'] ?? [];
        $angles = $annotations['angles'] ?? [];
        
        $html = '';
        
        if (!empty($markers)) {
            $html .= '<h3>Marqueurs (' . count($markers) . ')</h3><ul>';
            foreach ($markers as $marker) {
                $html .= '<li class="annotation">
                    <strong>' . $marker['landmark'] . '</strong> - 
                    Position: (' . round($marker['x']) . ', ' . round($marker['y']) . ')
                </li>';
            }
            $html .= '</ul>';
        }
        
        if (!empty($angles)) {
            $html .= '<h3>Angles (' . count($angles) . ')</h3><ul>';
            foreach ($angles as $angle) {
                $html .= '<li class="annotation">
                    <strong>' . $angle['value'] . '°</strong> - 
                    Mesuré le ' . \Carbon\Carbon::parse($angle['timestamp'])->format('d/m/Y H:i') . '
                </li>';
            }
            $html .= '</ul>';
        }
        
        if (empty($markers) && empty($angles)) {
            $html = '<p>Aucune annotation enregistrée.</p>';
        }
        
        return $html;
    }
    
    /**
     * Générer la section des recommandations
     */
    private function generateRecommendationsSection(array $analysis): string
    {
        $recommendations = $analysis['recommendations'] ?? [];
        
        if (empty($recommendations)) {
            return '<p>Aucune recommandation spécifique générée.</p>';
        }
        
        $html = '<ul>';
        foreach ($recommendations as $recommendation) {
            $html .= '<li class="recommendation">' . htmlspecialchars($recommendation) . '</li>';
        }
        
        return $html . '</ul>';
    }
    
    /**
     * Générer un rapport comparatif pour un joueur
     */
    public function generateComparativeReport(Player $player, array $assessments): string
    {
        $dompdf = new Dompdf();
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf->setOptions($options);
        
        $html = $this->generateComparativeReportHTML($player, $assessments);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'comparative_report_' . $player->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $path = 'reports/postural/comparative/' . $filename;
        
        Storage::put('public/' . $path, $dompdf->output());
        
        return $path;
    }
    
    /**
     * Générer le HTML du rapport comparatif
     */
    private function generateComparativeReportHTML(Player $player, array $assessments): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Rapport Comparatif Postural</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .section { margin-bottom: 30px; }
                .section h2 { color: #2563eb; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
                .assessment { border: 1px solid #e5e7eb; margin: 10px 0; padding: 15px; border-radius: 8px; }
                .assessment-header { font-weight: bold; margin-bottom: 10px; }
                .score { font-size: 18px; font-weight: bold; }
                .score-high { color: #dc2626; }
                .score-moderate { color: #d97706; }
                .score-low { color: #059669; }
                .trend { font-style: italic; color: #6b7280; }
                .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #6b7280; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📊 Rapport Comparatif Postural</h1>
                <p>Med Predictor - Analyse Évolutive</p>
                <p>Généré le ' . now()->format('d/m/Y à H:i') . '</p>
            </div>
            
            <div class="section">
                <h2>👤 Patient</h2>
                <p><strong>Nom:</strong> ' . $player->name . '</p>
                <p><strong>Position:</strong> ' . $player->position . '</p>
                <p><strong>Score de risque actuel:</strong> ' . number_format($player->injury_risk_score * 100, 1) . '%</p>
            </div>
            
            <div class="section">
                <h2>📈 Évolution des Évaluations</h2>
                ' . $this->generateAssessmentsTimeline($assessments) . '
            </div>
            
            <div class="footer">
                <p>Ce rapport comparatif a été généré automatiquement par le système Med Predictor.</p>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Générer la timeline des évaluations
     */
    private function generateAssessmentsTimeline(array $assessments): string
    {
        if (empty($assessments)) {
            return '<p>Aucune évaluation disponible pour la comparaison.</p>';
        }
        
        $html = '';
        
        foreach ($assessments as $assessment) {
            $scoreClass = $assessment['score'] >= 7 ? 'score-high' : 
                         ($assessment['score'] >= 4 ? 'score-moderate' : 'score-low');
            
            $html .= '
            <div class="assessment">
                <div class="assessment-header">
                    Évaluation du ' . \Carbon\Carbon::parse($assessment['date'])->format('d/m/Y H:i') . '
                </div>
                <div class="score ' . $scoreClass . '">
                    Score: ' . $assessment['score'] . '/10
                </div>
                <div class="trend">
                    ' . $assessment['imbalances_count'] . ' déséquilibres • ' . 
                    $assessment['risk_factors_count'] . ' facteurs de risque
                </div>
            </div>';
        }
        
        return $html;
    }
} 