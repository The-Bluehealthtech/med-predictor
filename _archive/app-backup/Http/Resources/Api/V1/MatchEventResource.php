<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'match_sheet_id' => $this->match_sheet_id,
            'player_id' => $this->player_id,
            'team_id' => $this->team_id,
            'assisted_by_player_id' => $this->assisted_by_player_id,
            'substituted_player_id' => $this->substituted_player_id,
            'recorded_by_user_id' => $this->recorded_by_user_id,
            'event_type' => $this->event_type,
            'type' => $this->type,
            'minute' => $this->minute,
            'extra_time_minute' => $this->extra_time_minute,
            'period' => $this->period,
            'event_data' => $this->event_data,
            'description' => $this->description,
            'location' => $this->location,
            'severity' => $this->severity,
            'is_confirmed' => $this->is_confirmed,
            'is_contested' => $this->is_contested,
            'contest_reason' => $this->contest_reason,
            'recorded_at' => $this->recorded_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Computed attributes
            'display_time' => $this->display_time,
            'event_type_label' => $this->event_type_label,
            'event_type_color' => $this->event_type_color,
            'severity_color' => $this->severity_color,
            'event_description' => $this->getEventDescription(),
            
            // Boolean helpers
            'is_goal_event' => $this->isGoalEvent(),
            'is_card_event' => $this->isCardEvent(),
            'is_substitution_event' => $this->isSubstitutionEvent(),
            'is_match_control_event' => $this->isMatchControlEvent(),
            
            // Relationships
            'match' => $this->whenLoaded('match', function () {
                return [
                    'id' => $this->match->id,
                    'home_team_id' => $this->match->home_team_id,
                    'away_team_id' => $this->match->away_team_id,
                    'competition_id' => $this->match->competition_id,
                    'match_date' => $this->match->match_date instanceof \Carbon\Carbon ? $this->match->match_date->toDateString() : $this->match->match_date,
                    'status' => $this->match->status
                ];
            }),
            'player' => $this->whenLoaded('player', function () {
                return [
                    'id' => $this->player->id,
                    'name' => $this->player->name,
                    'first_name' => $this->player->first_name,
                    'last_name' => $this->player->last_name,
                    'position' => $this->player->position,
                    'nationality' => $this->player->nationality
                ];
            }),
            'team' => $this->whenLoaded('team', function () {
                return [
                    'id' => $this->team->id,
                    'name' => $this->team->name,
                    'short_name' => $this->team->short_name,
                    'club_id' => $this->team->club_id
                ];
            }),
            'assisted_by_player' => $this->whenLoaded('assistedByPlayer', function () {
                return [
                    'id' => $this->assistedByPlayer->id,
                    'name' => $this->assistedByPlayer->name,
                    'first_name' => $this->assistedByPlayer->first_name,
                    'last_name' => $this->assistedByPlayer->last_name
                ];
            }),
            'substituted_player' => $this->whenLoaded('substitutedPlayer', function () {
                return [
                    'id' => $this->substitutedPlayer->id,
                    'name' => $this->substitutedPlayer->name,
                    'first_name' => $this->substitutedPlayer->first_name,
                    'last_name' => $this->substitutedPlayer->last_name
                ];
            }),
            'recorded_by_user' => $this->whenLoaded('recordedByUser', function () {
                return [
                    'id' => $this->recordedByUser->id,
                    'name' => $this->recordedByUser->name,
                    'email' => $this->recordedByUser->email,
                    'role' => $this->recordedByUser->role
                ];
            })
        ];
    }
} 