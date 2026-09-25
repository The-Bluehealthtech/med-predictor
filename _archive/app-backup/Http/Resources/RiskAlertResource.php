<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiskAlertResource extends JsonResource
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
            'type' => $this->type,
            'type_display' => $this->getTypeDisplayName(),
            'source' => $this->source,
            'score' => $this->score,
            'score_percentage' => round($this->score * 100, 1),
            'message' => $this->message,
            'priority' => $this->priority,
            'priority_display' => $this->getPriorityDisplayName(),
            'resolved' => $this->resolved,
            'acknowledged_by' => $this->acknowledged_by,
            'acknowledged_by_name' => $this->acknowledgedBy?->name,
            'acknowledged_at' => $this->acknowledged_at?->toISOString(),
            'recommendations' => $this->recommendations,
            'ai_metadata' => $this->ai_metadata,
            'fifa_alert_data' => $this->fifa_alert_data,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Computed fields
            'is_resolved' => $this->resolved,
            'is_unresolved' => !$this->resolved,
            'is_critical' => $this->priority === 'critical',
            'is_high' => $this->priority === 'high',
            'is_medium' => $this->priority === 'medium',
            'is_low' => $this->priority === 'low',
            'days_since_creation' => $this->created_at->diffInDays(now()),
            'hours_since_creation' => $this->created_at->diffInHours(now()),
            'response_time_hours' => $this->acknowledged_at ? $this->created_at->diffInHours($this->acknowledged_at) : null,
            'risk_level' => $this->getRiskLevel(),
            'urgency_indicator' => $this->getUrgencyIndicator(),
            
            // Relationships
            'athlete' => $this->whenLoaded('athlete', function () {
                return [
                    'id' => $this->athlete->id,
                    'name' => $this->athlete->name,
                    'fifa_id' => $this->athlete->fifa_id,
                    'team' => $this->athlete->team?->name,
                ];
            }),
            'acknowledgedBy' => $this->whenLoaded('acknowledgedBy', function () {
                return [
                    'id' => $this->acknowledgedBy->id,
                    'name' => $this->acknowledgedBy->name,
                    'email' => $this->acknowledgedBy->email,
                ];
            }),
        ];
    }

    /**
     * Get the display name for the alert type.
     */
    private function getTypeDisplayName(): string
    {
        return match ($this->type) {
            'sca' => 'Risque d\'Arrêt Cardiaque Soudain',
            'injury' => 'Risque de Blessure',
            'concussion' => 'Risque de Commotion',
            'medication' => 'Risque Médicamenteux',
            'other' => 'Autre Risque',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get the display name for the priority.
     */
    private function getPriorityDisplayName(): string
    {
        return match ($this->priority) {
            'critical' => 'Critique',
            'high' => 'Élevé',
            'medium' => 'Moyen',
            'low' => 'Faible',
            default => ucfirst($this->priority),
        };
    }

    /**
     * Get the risk level based on score.
     */
    private function getRiskLevel(): string
    {
        if ($this->score >= 0.8) {
            return 'Très élevé';
        } elseif ($this->score >= 0.6) {
            return 'Élevé';
        } elseif ($this->score >= 0.4) {
            return 'Modéré';
        } elseif ($this->score >= 0.2) {
            return 'Faible';
        } else {
            return 'Très faible';
        }
    }

    /**
     * Get urgency indicator based on priority and time.
     */
    private function getUrgencyIndicator(): string
    {
        if ($this->resolved) {
            return 'resolved';
        }

        $hoursSinceCreation = $this->created_at->diffInHours(now());

        if ($this->priority === 'critical') {
            return $hoursSinceCreation > 2 ? 'overdue' : 'urgent';
        } elseif ($this->priority === 'high') {
            return $hoursSinceCreation > 24 ? 'overdue' : 'urgent';
        } elseif ($this->priority === 'medium') {
            return $hoursSinceCreation > 72 ? 'overdue' : 'normal';
        } else {
            return $hoursSinceCreation > 168 ? 'overdue' : 'normal';
        }
    }
} 