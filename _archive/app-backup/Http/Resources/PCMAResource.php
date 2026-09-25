<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PCMAResource extends JsonResource
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
            'result_json' => $this->result_json,
            'status' => $this->status,
            'status_display' => $this->getStatusDisplayName(),
            'completed_at' => $this->completed_at?->toISOString(),
            'assessor_id' => $this->assessor_id,
            'assessor_name' => $this->assessor?->name,
            'notes' => $this->notes,
            'fifa_compliance_data' => $this->fifa_compliance_data,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Computed fields
            'is_completed' => $this->status === 'completed',
            'is_pending' => $this->status === 'pending',
            'is_failed' => $this->status === 'failed',
            'days_since_completion' => $this->completed_at ? $this->completed_at->diffInDays(now()) : null,
            
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
     * Get the display name for the PCMA type.
     */
    private function getTypeDisplayName(): string
    {
        return match ($this->type) {
            'bpma' => 'Basic Pre-Competition Medical Assessment',
            'cardio' => 'Cardiovascular Assessment',
            'dental' => 'Dental Assessment',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get the display name for the PCMA status.
     */
    private function getStatusDisplayName(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }
} 