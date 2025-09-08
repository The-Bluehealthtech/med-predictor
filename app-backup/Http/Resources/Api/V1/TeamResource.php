<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'type' => $this->type,
            'status' => $this->status,
            'club_id' => $this->club_id,
            'association_id' => $this->association_id,
            'founded_year' => $this->founded_year,
            'home_ground' => $this->home_ground,
            'capacity' => $this->capacity,
            'colors' => $this->colors,
            'logo_url' => $this->logo_url,
            'website' => $this->website,
            'description' => $this->description,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
