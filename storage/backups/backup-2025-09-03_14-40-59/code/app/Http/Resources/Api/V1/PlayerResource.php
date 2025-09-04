<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'age' => $this->date_of_birth ? $this->date_of_birth->age : null,
            'nationality' => $this->nationality,
            'position' => $this->position,
            'club_id' => $this->club_id,
            'team_id' => $this->team_id,
            'status' => $this->status,
            'fifa_connect_id' => $this->fifa_connect_id,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'medical_conditions' => $this->medical_conditions,
            'allergies' => $this->allergies,
            'blood_type' => $this->blood_type,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relationships
            'club' => $this->whenLoaded('club', function () {
                return [
                    'id' => $this->club->id,
                    'name' => $this->club->name,
                    'short_name' => $this->club->short_name,
                ];
            }),
            
            'team' => $this->whenLoaded('team', function () {
                return [
                    'id' => $this->team->id,
                    'name' => $this->team->name,
                    'short_name' => $this->team->short_name,
                ];
            }),
            
            'health_records' => $this->whenLoaded('healthRecords', function () {
                return $this->healthRecords->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'record_type' => $record->record_type,
                        'description' => $record->description,
                        'record_date' => $record->record_date->toDateString(),
                        'created_at' => $record->created_at->toISOString(),
                    ];
                });
            }),
            
            'licenses' => $this->whenLoaded('licenses', function () {
                return $this->licenses->map(function ($license) {
                    return [
                        'id' => $license->id,
                        'license_number' => $license->license_number,
                        'status' => $license->status,
                        'issued_date' => $license->issued_date?->toDateString(),
                        'expiry_date' => $license->expiry_date?->toDateString(),
                    ];
                });
            }),
            
            'season_stats' => $this->whenLoaded('seasonStats', function () {
                return $this->seasonStats->map(function ($stat) {
                    return [
                        'id' => $stat->id,
                        'season_id' => $stat->season_id,
                        'matches_played' => $stat->matches_played,
                        'goals_scored' => $stat->goals_scored,
                        'assists' => $stat->assists,
                        'yellow_cards' => $stat->yellow_cards,
                        'red_cards' => $stat->red_cards,
                    ];
                });
            }),
        ];
    }
} 