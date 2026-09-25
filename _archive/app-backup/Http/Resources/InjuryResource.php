<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InjuryResource extends JsonResource
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
            'date' => $this->date->toISOString(),
            'type' => $this->type,
            'body_zone' => $this->body_zone,
            'severity' => $this->severity,
            'severity_display' => $this->getSeverityDisplayName(),
            'description' => $this->description,
            'status' => $this->status,
            'status_display' => $this->getStatusDisplayName(),
            'estimated_recovery_days' => $this->estimated_recovery_days,
            'expected_return_date' => $this->expected_return_date?->toISOString(),
            'actual_return_date' => $this->actual_return_date?->toISOString(),
            'diagnosed_by' => $this->diagnosed_by,
            'diagnosed_by_name' => $this->diagnosedBy?->name,
            'treatment_plan' => $this->treatment_plan,
            'rehabilitation_progress' => $this->rehabilitation_progress,
            'fifa_injury_data' => $this->fifa_injury_data,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Computed fields
            'is_active' => $this->status === 'open',
            'is_resolved' => $this->status === 'resolved',
            'is_recurring' => $this->status === 'recurring',
            'days_since_injury' => $this->date->diffInDays(now()),
            'days_until_expected_return' => $this->expected_return_date ? $this->expected_return_date->diffInDays(now()) : null,
            'recovery_progress_percentage' => $this->getRecoveryProgressPercentage(),
            
            // Relationships
            'athlete' => $this->whenLoaded('athlete', function () {
                return [
                    'id' => $this->athlete->id,
                    'name' => $this->athlete->name,
                    'fifa_id' => $this->athlete->fifa_id,
                ];
            }),
            'diagnosedBy' => $this->whenLoaded('diagnosedBy', function () {
                return [
                    'id' => $this->diagnosedBy->id,
                    'name' => $this->diagnosedBy->name,
                    'email' => $this->diagnosedBy->email,
                ];
            }),
        ];
    }

    /**
     * Get the display name for the injury severity.
     */
    private function getSeverityDisplayName(): string
    {
        return match ($this->severity) {
            'minor' => 'Mineur',
            'moderate' => 'Modéré',
            'severe' => 'Sévère',
            default => ucfirst($this->severity),
        };
    }

    /**
     * Get the display name for the injury status.
     */
    private function getStatusDisplayName(): string
    {
        return match ($this->status) {
            'open' => 'Ouverte',
            'resolved' => 'Résolue',
            'recurring' => 'Récurrente',
            default => ucfirst($this->status),
        };
    }

    /**
     * Calculate recovery progress percentage.
     */
    private function getRecoveryProgressPercentage(): ?int
    {
        if (!$this->estimated_recovery_days || !$this->days_since_injury) {
            return null;
        }

        $progress = ($this->days_since_injury / $this->estimated_recovery_days) * 100;
        return min(100, max(0, round($progress)));
    }
} 