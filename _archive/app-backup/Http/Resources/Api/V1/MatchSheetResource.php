<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchSheetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'match_id' => $this->match_id,
            'match_number' => $this->match_number,
            'stadium_venue' => $this->stadium_venue,
            'weather_conditions' => $this->weather_conditions,
            'pitch_conditions' => $this->pitch_conditions,
            'kickoff_time' => $this->kickoff_time?->toISOString(),
            'home_team_roster' => $this->home_team_roster,
            'away_team_roster' => $this->away_team_roster,
            'home_team_substitutes' => $this->home_team_substitutes,
            'away_team_substitutes' => $this->away_team_substitutes,
            'home_team_coach' => $this->home_team_coach,
            'away_team_coach' => $this->away_team_coach,
            'home_team_manager' => $this->home_team_manager,
            'away_team_manager' => $this->away_team_manager,
            'referee_id' => $this->referee_id,
            'main_referee_id' => $this->main_referee_id,
            'assistant_referee_1_id' => $this->assistant_referee_1_id,
            'assistant_referee_2_id' => $this->assistant_referee_2_id,
            'fourth_official_id' => $this->fourth_official_id,
            'var_referee_id' => $this->var_referee_id,
            'var_assistant_id' => $this->var_assistant_id,
            'match_statistics' => $this->match_statistics,
            'home_team_score' => $this->home_team_score,
            'away_team_score' => $this->away_team_score,
            'referee_report' => $this->referee_report,
            'match_status' => $this->match_status,
            'match_status_label' => $this->match_status_label,
            'match_status_color' => $this->match_status_color,
            'suspension_reason' => $this->suspension_reason,
            'crowd_issues' => $this->crowd_issues,
            'protests_incidents' => $this->protests_incidents,
            'notes' => $this->notes,
            'home_team_signature' => $this->home_team_signature,
            'away_team_signature' => $this->away_team_signature,
            'home_team_signed_at' => $this->home_team_signed_at?->toISOString(),
            'away_team_signed_at' => $this->away_team_signed_at?->toISOString(),
            'match_observer_id' => $this->match_observer_id,
            'observer_comments' => $this->observer_comments,
            'observer_signed_at' => $this->observer_signed_at?->toISOString(),
            'referee_digital_signature' => $this->referee_digital_signature,
            'referee_signed_at' => $this->referee_signed_at?->toISOString(),
            'penalty_shootout_data' => $this->penalty_shootout_data,
            'var_decisions' => $this->var_decisions,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'submitted_at' => $this->submitted_at?->toISOString(),
            'validated_at' => $this->validated_at?->toISOString(),
            'validated_by' => $this->validated_by,
            'validation_notes' => $this->validation_notes,
            'rejected_at' => $this->rejected_at?->toISOString(),
            'rejected_by' => $this->rejected_by,
            'rejection_reason' => $this->rejection_reason,
            'version' => $this->version,
            'change_log' => $this->change_log,
            'stage' => $this->stage,
            'stage_label' => $this->stage_label,
            'stage_color' => $this->stage_color,
            'stage_in_progress_at' => $this->stage_in_progress_at?->toISOString(),
            'stage_before_game_signed_at' => $this->stage_before_game_signed_at?->toISOString(),
            'stage_after_game_signed_at' => $this->stage_after_game_signed_at?->toISOString(),
            'stage_fa_validated_at' => $this->stage_fa_validated_at?->toISOString(),
            'home_team_lineup_signature' => $this->home_team_lineup_signature,
            'away_team_lineup_signature' => $this->away_team_lineup_signature,
            'home_team_lineup_signed_at' => $this->home_team_lineup_signed_at?->toISOString(),
            'away_team_lineup_signed_at' => $this->away_team_lineup_signed_at?->toISOString(),
            'home_team_post_match_signature' => $this->home_team_post_match_signature,
            'away_team_post_match_signature' => $this->away_team_post_match_signature,
            'home_team_post_match_signed_at' => $this->home_team_post_match_signed_at?->toISOString(),
            'away_team_post_match_signed_at' => $this->away_team_post_match_signed_at?->toISOString(),
            'home_team_post_match_comments' => $this->home_team_post_match_comments,
            'away_team_post_match_comments' => $this->away_team_post_match_comments,
            'fa_validated_by' => $this->fa_validated_by,
            'fa_validation_notes' => $this->fa_validation_notes,
            'assigned_referee_id' => $this->assigned_referee_id,
            'referee_assigned_at' => $this->referee_assigned_at?->toISOString(),
            'lineups_locked' => $this->lineups_locked,
            'lineups_locked_at' => $this->lineups_locked_at?->toISOString(),
            'match_events_locked' => $this->match_events_locked,
            'match_events_locked_at' => $this->match_events_locked_at?->toISOString(),
            'stage_transition_log' => $this->stage_transition_log,
            'user_action_log' => $this->user_action_log,
            'signed_sheet_path' => $this->signed_sheet_path,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'match' => $this->whenLoaded('match', function () {
                return [
                    'id' => $this->match->id,
                    'home_team_id' => $this->match->home_team_id,
                    'away_team_id' => $this->match->away_team_id,
                    'competition_id' => $this->match->competition_id,
                    'match_date' => $this->match->match_date instanceof \Carbon\Carbon ? $this->match->match_date->toDateString() : $this->match->match_date,
                    'status' => $this->match->status,
                    'home_team' => $this->whenLoaded('match.homeTeam', function () {
                        return [
                            'id' => $this->match->homeTeam->id,
                            'name' => $this->match->homeTeam->name,
                            'short_name' => $this->match->homeTeam->short_name,
                        ];
                    }),
                    'away_team' => $this->whenLoaded('match.awayTeam', function () {
                        return [
                            'id' => $this->match->awayTeam->id,
                            'name' => $this->match->awayTeam->name,
                            'short_name' => $this->match->awayTeam->short_name,
                        ];
                    }),
                    'competition' => $this->whenLoaded('match.competition', function () {
                        return [
                            'id' => $this->match->competition->id,
                            'name' => $this->match->competition->name,
                            'short_name' => $this->match->competition->short_name,
                        ];
                    }),
                ];
            }),

            'referee' => $this->whenLoaded('referee', function () {
                return [
                    'id' => $this->referee->id,
                    'name' => $this->referee->name,
                    'email' => $this->referee->email,
                ];
            }),

            'main_referee' => $this->whenLoaded('mainReferee', function () {
                return [
                    'id' => $this->mainReferee->id,
                    'name' => $this->mainReferee->name,
                    'email' => $this->mainReferee->email,
                ];
            }),

            'assistant_referee_1' => $this->whenLoaded('assistantReferee1', function () {
                return [
                    'id' => $this->assistantReferee1->id,
                    'name' => $this->assistantReferee1->name,
                    'email' => $this->assistantReferee1->email,
                ];
            }),

            'assistant_referee_2' => $this->whenLoaded('assistantReferee2', function () {
                return [
                    'id' => $this->assistantReferee2->id,
                    'name' => $this->assistantReferee2->name,
                    'email' => $this->assistantReferee2->email,
                ];
            }),

            'fourth_official' => $this->whenLoaded('fourthOfficial', function () {
                return [
                    'id' => $this->fourthOfficial->id,
                    'name' => $this->fourthOfficial->name,
                    'email' => $this->fourthOfficial->email,
                ];
            }),

            'var_referee' => $this->whenLoaded('varReferee', function () {
                return [
                    'id' => $this->varReferee->id,
                    'name' => $this->varReferee->name,
                    'email' => $this->varReferee->email,
                ];
            }),

            'var_assistant' => $this->whenLoaded('varAssistant', function () {
                return [
                    'id' => $this->varAssistant->id,
                    'name' => $this->varAssistant->name,
                    'email' => $this->varAssistant->email,
                ];
            }),

            'match_observer' => $this->whenLoaded('matchObserver', function () {
                return [
                    'id' => $this->matchObserver->id,
                    'name' => $this->matchObserver->name,
                    'email' => $this->matchObserver->email,
                ];
            }),

            'validator' => $this->whenLoaded('validator', function () {
                return [
                    'id' => $this->validator->id,
                    'name' => $this->validator->name,
                    'email' => $this->validator->email,
                ];
            }),

            'rejector' => $this->whenLoaded('rejector', function () {
                return [
                    'id' => $this->rejector->id,
                    'name' => $this->rejector->name,
                    'email' => $this->rejector->email,
                ];
            }),

            'fa_validator' => $this->whenLoaded('faValidator', function () {
                return [
                    'id' => $this->faValidator->id,
                    'name' => $this->faValidator->name,
                    'email' => $this->faValidator->email,
                ];
            }),

            'assigned_referee' => $this->whenLoaded('assignedReferee', function () {
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
                        'period' => $event->period,
                        'description' => $event->description,
                        'is_confirmed' => $event->is_confirmed,
                        'is_contested' => $event->is_contested,
                    ];
                });
            }),

            // Computed attributes
            'total_goals' => $this->getTotalGoals(),
            'goal_difference' => $this->getGoalDifference(),
            'winner' => $this->getWinner(),
            'is_in_progress' => $this->isInProgress(),
            'is_before_game_signed' => $this->isBeforeGameSigned(),
            'is_after_game_signed' => $this->isAfterGameSigned(),
            'is_fa_validated' => $this->isFaValidated(),
        ];
    }
} 