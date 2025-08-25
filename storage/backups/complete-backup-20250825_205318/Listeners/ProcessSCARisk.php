<?php

namespace App\Listeners;

use App\Events\CardioPCMASubmitted;
use App\Models\RiskAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessSCARisk implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 30;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CardioPCMASubmitted $event): void
    {
        try {
            Log::info('Processing SCA risk analysis for PCMA', [
                'pcma_id' => $event->pcma->id,
                'athlete_id' => $event->pcma->athlete_id,
            ]);

            // Extract cardiovascular data from PCMA result
            $cardiovascularData = $this->extractCardiovascularData($event->pcma->result_json);

            // Prepare data for AI analysis
            $aiRequestData = [
                'patientId' => $event->pcma->athlete_id,
                'ecgData' => $cardiovascularData['ecg'] ?? null,
                'hrv' => $cardiovascularData['hrv'] ?? null,
                'history' => $cardiovascularData['history'] ?? [],
                'assessment_date' => $event->pcma->completed_at?->toISOString(),
                'pcma_id' => $event->pcma->id,
            ];

            // Make request to AI service
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.ai.token'),
                    'Content-Type' => 'application/json',
                ])
                ->post(config('services.ai.base_url') . '/ai/sca-risk-score', $aiRequestData);

            if (!$response->successful()) {
                Log::error('AI service request failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'pcma_id' => $event->pcma->id,
                ]);
                return;
            }

            $aiResponse = $response->json();
            
            Log::info('AI SCA risk analysis completed', [
                'pcma_id' => $event->pcma->id,
                'risk_score' => $aiResponse['riskScore'] ?? null,
                'confidence' => $aiResponse['confidence'] ?? null,
            ]);

            // Check if risk score exceeds threshold
            $riskThreshold = config('medical.sca_risk_threshold', 0.7);
            $riskScore = $aiResponse['riskScore'] ?? 0;

            if ($riskScore >= $riskThreshold) {
                $this->createRiskAlert($event->pcma, $aiResponse);
            }

        } catch (\Exception $e) {
            Log::error('Error processing SCA risk analysis', [
                'pcma_id' => $event->pcma->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Extract cardiovascular data from PCMA result.
     */
    private function extractCardiovascularData(array $resultJson): array
    {
        $cardiovascularData = [
            'ecg' => null,
            'hrv' => null,
            'history' => [],
        ];

        // Extract ECG data
        if (isset($resultJson['ecg_analysis'])) {
            $cardiovascularData['ecg'] = $resultJson['ecg_analysis'];
        } elseif (isset($resultJson['cardiovascular']['ecg'])) {
            $cardiovascularData['ecg'] = $resultJson['cardiovascular']['ecg'];
        }

        // Extract HRV data
        if (isset($resultJson['hrv_analysis'])) {
            $cardiovascularData['hrv'] = $resultJson['hrv_analysis'];
        } elseif (isset($resultJson['cardiovascular']['hrv'])) {
            $cardiovascularData['hrv'] = $resultJson['cardiovascular']['hrv'];
        }

        // Extract medical history
        if (isset($resultJson['medical_history'])) {
            $cardiovascularData['history'] = $resultJson['medical_history'];
        } elseif (isset($resultJson['cardiovascular']['history'])) {
            $cardiovascularData['history'] = $resultJson['cardiovascular']['history'];
        }

        return $cardiovascularData;
    }

    /**
     * Create a risk alert based on AI analysis.
     */
    private function createRiskAlert($pcma, array $aiResponse): void
    {
        $riskScore = $aiResponse['riskScore'] ?? 0;
        $confidence = $aiResponse['confidence'] ?? 0;
        $explanation = $aiResponse['explanation'] ?? 'Risk analysis completed';
        $recommendation = $aiResponse['recommendation'] ?? 'Review by cardiologist recommended';

        // Determine priority based on risk score
        $priority = $this->determinePriority($riskScore);

        // Create risk alert
        RiskAlert::create([
            'athlete_id' => $pcma->athlete_id,
            'type' => 'sca',
            'source' => 'AI_Agent_SCA_Risk',
            'score' => $riskScore,
            'message' => $explanation,
            'priority' => $priority,
            'resolved' => false,
            'recommendations' => [
                'immediate_action' => $recommendation,
                'follow_up' => 'Schedule follow-up with sports cardiologist',
                'monitoring' => 'Continuous ECG monitoring recommended',
            ],
            'ai_metadata' => [
                'model_version' => 'sca-risk-v1.0',
                'confidence_score' => $confidence,
                'analysis_timestamp' => now()->toISOString(),
                'pcma_id' => $pcma->id,
                'assessment_date' => $pcma->completed_at?->toISOString(),
            ],
            'fifa_alert_data' => [
                'compliance_level' => 'high',
                'reporting_required' => true,
                'fifa_category' => 'cardiovascular_risk',
            ],
        ]);

        Log::info('Risk alert created for SCA', [
            'athlete_id' => $pcma->athlete_id,
            'risk_score' => $riskScore,
            'priority' => $priority,
        ]);
    }

    /**
     * Determine alert priority based on risk score.
     */
    private function determinePriority(float $riskScore): string
    {
        if ($riskScore >= 0.9) {
            return 'critical';
        } elseif ($riskScore >= 0.7) {
            return 'high';
        } elseif ($riskScore >= 0.5) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(CardioPCMASubmitted $event, \Throwable $exception): void
    {
        Log::error('SCA risk analysis failed permanently', [
            'pcma_id' => $event->pcma->id,
            'athlete_id' => $event->pcma->athlete_id,
            'error' => $exception->getMessage(),
        ]);

        // Could send notification to medical staff about failed analysis
        // or create a manual review task
    }
} 