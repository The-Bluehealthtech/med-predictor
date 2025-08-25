<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SCATAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'athlete_id' => $this->athlete_id,
            'assessor_id' => $this->assessor_id,
            'assessor_name' => $this->assessor?->name,
            'data_json' => $this->data_json,
            'result' => $this->result,
            'result_display' => $this->getResultDisplayName(),
            'concussion_confirmed' => $this->concussion_confirmed,
            'assessment_date' => $this->assessment_date->toISOString(),
            'assessment_type' => $this->assessment_type,
            'assessment_type_display' => $this->getAssessmentTypeDisplayName(),
            'scat_score' => $this->scat_score,
            'recommendations' => $this->recommendations,
            'fifa_concussion_data' => $this->fifa_concussion_data,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Computed fields
            'is_baseline' => $this->assessment_type === 'baseline',
            'is_post_injury' => $this->assessment_type === 'post_injury',
            'is_follow_up' => $this->assessment_type === 'follow_up',
            'is_normal' => $this->result === 'normal',
            'is_abnormal' => $this->result === 'abnormal',
            'is_unclear' => $this->result === 'unclear',
            'days_since_assessment' => $this->assessment_date->diffInDays(now()),
            'score_category' => $this->getScoreCategory(),
            
            // Relationships
            'athlete' => $this->whenLoaded('athlete', function () {
                return [
                    'id' => $this->athlete->id,
                    'name' => $this->athlete->name,
                    'fifa_id' => $this->athlete->fifa_id,
                ];
            }),
            'assessor' => $this->whenLoaded('assessor', function () {
                return [
                    'id' => $this->assessor->id,
                    'name' => $this->assessor->name,
                    'email' => $this->assessor->email,
                ];
            }),
        ];
    }

    /**
     * Get the display name for the assessment result.
     */
    private function getResultDisplayName(): string
    {
        return match ($this->result) {
            'normal' => 'Normal',
            'abnormal' => 'Anormal',
            'unclear' => 'Imprécis',
            default => ucfirst($this->result),
        };
    }

    /**
     * Get the display name for the assessment type.
     */
    private function getAssessmentTypeDisplayName(): string
    {
        return match ($this->assessment_type) {
            'baseline' => 'Évaluation de base',
            'post_injury' => 'Post-blessure',
            'follow_up' => 'Suivi',
            default => ucfirst($this->assessment_type),
        };
    }

    /**
     * Get the score category based on SCAT score.
     */
    private function getScoreCategory(): string
    {
        if (!$this->scat_score) {
            return 'N/A';
        }

        if ($this->scat_score >= 90) {
            return 'Excellent';
        } elseif ($this->scat_score >= 80) {
            return 'Bon';
        } elseif ($this->scat_score >= 70) {
            return 'Moyen';
        } elseif ($this->scat_score >= 60) {
            return 'Faible';
        } else {
            return 'Très faible';
        }
    }
} 