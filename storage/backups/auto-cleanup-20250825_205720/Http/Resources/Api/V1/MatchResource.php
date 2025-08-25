<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competition_id' => $this->competition_id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'match_date' => $this->match_date,
            'kickoff_time' => $this->kickoff_time,
            'venue' => $this->venue,
            'stadium' => $this->stadium,
            'capacity' => $this->capacity,
            'referee_id' => $this->referee_id,
            'match_status' => $this->match_status,
            'matchday' => $this->matchday,
            'home_score' => $this->home_score,
            'away_score' => $this->away_score,
            'attendance' => $this->attendance,
            'weather_conditions' => $this->weather_conditions,
            'pitch_condition' => $this->pitch_condition,
            'match_highlights' => $this->match_highlights,
            'match_report' => $this->match_report,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'competition' => $this->whenLoaded('competition', function () {
                return [
                    'id' => $this->competition->id,
                    'name' => $this->competition->name,
                    'type' => $this->competition->type,
                ];
            }),
            
            'home_team' => $this->whenLoaded('homeTeam', function () {
                return [
                    'id' => $this->homeTeam->id,
                    'name' => $this->homeTeam->name,
                    'short_name' => $this->homeTeam->short_name,
                    'club' => [
                        'id' => $this->homeTeam->club->id ?? null,
                        'name' => $this->homeTeam->club->name ?? null,
                    ] ?? null,
                ];
            }),
            
            'away_team' => $this->whenLoaded('awayTeam', function () {
                return [
                    'id' => $this->awayTeam->id,
                    'name' => $this->awayTeam->name,
                    'short_name' => $this->awayTeam->short_name,
                    'club' => [
                        'id' => $this->awayTeam->club->id ?? null,
                        'name' => $this->awayTeam->club->name ?? null,
                    ] ?? null,
                ];
            }),
            
            'referee' => $this->whenLoaded('assignedReferee', function () {
                return [
                    'id' => $this->assignedReferee->id,
                    'name' => $this->assignedReferee->name,
                    'email' => $this->assignedReferee->email,
                ];
            }),
            
            'events' => $this->whenLoaded('events', function () {
                return $this->events->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'event_type' => $event->event_type,
                        'minute' => $event->minute,
                        'player_id' => $event->player_id,
                        'team_id' => $event->team_id,
                        'description' => $event->description,
                        'additional_data' => $event->additional_data,
                        'player' => [
                            'id' => $event->player->id ?? null,
                            'name' => $event->player->name ?? null,
                        ] ?? null,
                        'team' => [
                            'id' => $event->team->id ?? null,
                            'name' => $event->team->name ?? null,
                        ] ?? null,
                    ];
                });
            }),
            
            'lineups' => $this->whenLoaded('rosters', function () {
                return $this->rosters->map(function ($roster) {
                    return [
                        'id' => $roster->id,
                        'team_id' => $roster->team_id,
                        'formation' => $roster->formation,
                        'players' => $roster->players->map(function ($player) {
                            return [
                                'id' => $player->id,
                                'name' => $player->name,
                                'position' => $player->pivot->position,
                                'shirt_number' => $player->pivot->shirt_number,
                            ];
                        }),
                    ];
                });
            }),
        ];
    }
} 