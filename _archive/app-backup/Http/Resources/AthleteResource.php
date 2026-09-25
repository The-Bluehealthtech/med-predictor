<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AthleteResource extends JsonResource
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
            'fifa_id' => $this->fifa_id,
            'name' => $this->name,
            'dob' => $this->dob->format('Y-m-d'),
            'age' => $this->age,
            'nationality' => $this->nationality,
            'team' => [
                'id' => $this->team->id,
                'name' => $this->team->name,
                'level' => $this->team->level,
            ],
            'position' => $this->position,
            'jersey_number' => $this->jersey_number,
            'gender' => $this->gender,
            'blood_type' => $this->blood_type,
            'emergency_contact' => $this->emergency_contact,
            'medical_history' => $this->medical_history,
            'allergies' => $this->allergies,
            'medications' => $this->medications,
            'active' => $this->active,
            'medical_status' => $this->getMedicalStatus(),
            'health_score' => $this->getHealthScore(),
            'fifa_compliance' => $this->getFifaComplianceStatus(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
} 