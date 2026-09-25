<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalNoteResource extends JsonResource
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
            'note_json' => $this->note_json,
            'note_type' => $this->note_type,
            'generated_by_ai' => $this->generated_by_ai,
            'approved_by_physician_id' => $this->approved_by_physician_id,
            'approved_by_physician_name' => $this->approvedByPhysician?->name,
            'signed_at' => $this->signed_at?->toISOString(),
            'status' => $this->status,
            'status_display' => $this->getStatusDisplayName(),
            'ai_metadata' => $this->ai_metadata,
            'fifa_compliance_data' => $this->fifa_compliance_data,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Computed fields
            'is_draft' => $this->status === 'draft',
            'is_approved' => $this->status === 'approved',
            'is_signed' => $this->status === 'signed',
            'is_ai_generated' => $this->generated_by_ai,
            'days_since_creation' => $this->created_at->diffInDays(now()),
            'days_since_signed' => $this->signed_at ? $this->signed_at->diffInDays(now()) : null,
            
            // Relationships
            'athlete' => $this->whenLoaded('athlete', function () {
                return [
                    'id' => $this->athlete->id,
                    'name' => $this->athlete->name,
                    'fifa_id' => $this->athlete->fifa_id,
                ];
            }),
            'approvedByPhysician' => $this->whenLoaded('approvedByPhysician', function () {
                return [
                    'id' => $this->approvedByPhysician->id,
                    'name' => $this->approvedByPhysician->name,
                    'email' => $this->approvedByPhysician->email,
                ];
            }),
        ];
    }

    /**
     * Get the display name for the note status.
     */
    private function getStatusDisplayName(): string
    {
        return match ($this->status) {
            'draft' => 'Brouillon',
            'approved' => 'Approuvé',
            'signed' => 'Signé',
            default => ucfirst($this->status),
        };
    }
} 