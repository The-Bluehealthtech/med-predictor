<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
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
            'name' => $this->name,
            'short_name' => $this->short_name,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'registration_start_date' => $this->registration_start_date?->toDateString(),
            'registration_end_date' => $this->registration_end_date?->toDateString(),
            'status' => $this->status,
            'is_current' => $this->is_current,
            'description' => $this->description,
            'settings' => $this->settings,
            'duration' => $this->duration,
            'status_color' => $this->status_color,
            'competitions_count' => $this->whenLoaded('competitions', function () {
                return $this->competitions->count();
            }),
            'players_count' => $this->whenLoaded('players', function () {
                return $this->players->count();
            }),
            'is_registration_open' => $this->isRegistrationOpen(),
            'created_by' => $this->whenLoaded('createdBy', function () {
                return [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                    'email' => $this->createdBy->email,
                ];
            }),
            'updated_by' => $this->whenLoaded('updatedBy', function () {
                return [
                    'id' => $this->updatedBy->id,
                    'name' => $this->updatedBy->name,
                    'email' => $this->updatedBy->email,
                ];
            }),
            'competitions' => CompetitionResource::collection($this->whenLoaded('competitions')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
} 