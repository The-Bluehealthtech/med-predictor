<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
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
            'description' => $this->description,
            'type' => $this->type,
            'format' => $this->format,
            'status' => $this->status,
            'association' => [
                'id' => $this->association->id ?? null,
                'name' => $this->association->name ?? null,
            ],
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'registration_deadline' => $this->registration_deadline?->toDateString(),
            'max_teams' => $this->max_teams,
            'min_teams' => $this->min_teams,
            'age_group' => $this->age_group,
            'gender' => $this->gender,
            'venue_type' => $this->venue_type,
            'rules' => $this->rules,
            'prize_pool' => $this->prize_pool,
            'entry_fee' => $this->entry_fee,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'website' => $this->website,
            'logo_url' => $this->logo ? asset('storage/' . $this->logo) : null,
            'banner_url' => $this->banner ? asset('storage/' . $this->banner) : null,
            'is_featured' => $this->is_featured,
            'is_public' => $this->is_public,
            'requires_approval' => $this->requires_approval,
            'auto_approve_teams' => $this->auto_approve_teams,
            'allow_substitutions' => $this->allow_substitutions,
            'max_substitutions' => $this->max_substitutions,
            'match_duration' => $this->match_duration,
            'extra_time' => $this->extra_time,
            'penalties' => $this->penalties,
            'var_enabled' => $this->var_enabled,
            'streaming_enabled' => $this->streaming_enabled,
            'ticket_sales_enabled' => $this->ticket_sales_enabled,
            'sponsorship_enabled' => $this->sponsorship_enabled,
            'metadata' => $this->metadata,
            'teams_count' => $this->whenLoaded('teams', function () {
                return $this->teams->count();
            }),
            'seasons_count' => $this->whenLoaded('seasons', function () {
                return $this->seasons->count();
            }),
            'matches_count' => $this->whenLoaded('matches', function () {
                return $this->matches->count();
            }),
            'teams' => TeamResource::collection($this->whenLoaded('teams')),
            'seasons' => $this->whenLoaded('seasons', function () {
                return $this->seasons->map(function ($season) {
                    return [
                        'id' => $season->id,
                        'name' => $season->name,
                        'start_date' => $season->start_date?->toDateString(),
                        'end_date' => $season->end_date?->toDateString(),
                        'status' => $season->status,
                    ];
                });
            }),
            'matches' => $this->whenLoaded('matches', function () {
                return $this->matches->map(function ($match) {
                    return [
                        'id' => $match->id,
                        'home_team' => $match->homeTeam->name ?? null,
                        'away_team' => $match->awayTeam->name ?? null,
                        'match_date' => $match->match_date?->toDateString(),
                        'match_status' => $match->match_status,
                        'home_score' => $match->home_score,
                        'away_score' => $match->away_score,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
