<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'association_id' => $this->association_id,
            'status' => $this->status,
            'founded_year' => $this->founded_year,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'logo_url' => $this->logo_url,
            'description' => $this->description,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relationships
            'association' => $this->whenLoaded('association', function () {
                return [
                    'id' => $this->association->id,
                    'name' => $this->association->name,
                    'short_name' => $this->association->short_name,
                ];
            }),
            
            'teams' => $this->whenLoaded('teams', function () {
                return $this->teams->map(function ($team) {
                    return [
                        'id' => $team->id,
                        'name' => $team->name,
                        'short_name' => $team->short_name,
                        'status' => $team->status,
                    ];
                });
            }),
            
            'players' => $this->whenLoaded('players', function () {
                return $this->players->map(function ($player) {
                    return [
                        'id' => $player->id,
                        'first_name' => $player->first_name,
                        'last_name' => $player->last_name,
                        'full_name' => $player->first_name . ' ' . $player->last_name,
                        'position' => $player->position,
                        'status' => $player->status,
                    ];
                });
            }),
            
            'competitions' => $this->whenLoaded('competitions', function () {
                return $this->competitions->map(function ($competition) {
                    return [
                        'id' => $competition->id,
                        'name' => $competition->name,
                        'short_name' => $competition->short_name,
                        'status' => $competition->status,
                    ];
                });
            }),
        ];
    }
} 