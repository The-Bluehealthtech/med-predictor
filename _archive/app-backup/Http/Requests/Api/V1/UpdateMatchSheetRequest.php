<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatchSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'match_id' => 'sometimes|exists:matches,id',
            'match_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('match_sheets', 'match_number')->ignore($this->match_sheet->id)
            ],
            'stadium_venue' => 'sometimes|string|max:255',
            'weather_conditions' => 'nullable|string|max:100',
            'pitch_conditions' => 'nullable|string|max:100',
            'kickoff_time' => 'sometimes|date',
            'home_team_roster' => 'nullable|array',
            'home_team_roster.*' => 'integer|exists:players,id',
            'away_team_roster' => 'nullable|array',
            'away_team_roster.*' => 'integer|exists:players,id',
            'home_team_substitutes' => 'nullable|array',
            'home_team_substitutes.*' => 'integer|exists:players,id',
            'away_team_substitutes' => 'nullable|array',
            'away_team_substitutes.*' => 'integer|exists:players,id',
            'home_team_coach' => 'nullable|string|max:255',
            'away_team_coach' => 'nullable|string|max:255',
            'home_team_manager' => 'nullable|string|max:255',
            'away_team_manager' => 'nullable|string|max:255',
            'referee_id' => 'nullable|exists:users,id',
            'main_referee_id' => 'nullable|exists:users,id',
            'assistant_referee_1_id' => 'nullable|exists:users,id',
            'assistant_referee_2_id' => 'nullable|exists:users,id',
            'fourth_official_id' => 'nullable|exists:users,id',
            'var_referee_id' => 'nullable|exists:users,id',
            'var_assistant_id' => 'nullable|exists:users,id',
            'match_statistics' => 'nullable|array',
            'home_team_score' => 'nullable|integer|min:0|max:50',
            'away_team_score' => 'nullable|integer|min:0|max:50',
            'referee_report' => 'nullable|string|max:2000',
            'match_status' => 'nullable|string|in:scheduled,in_progress,completed,cancelled,postponed',
            'suspension_reason' => 'nullable|string|max:500',
            'crowd_issues' => 'nullable|string|max:500',
            'protests_incidents' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:2000',
            'home_team_signature' => 'nullable|string|max:255',
            'away_team_signature' => 'nullable|string|max:255',
            'home_team_signed_at' => 'nullable|date',
            'away_team_signed_at' => 'nullable|date',
            'match_observer_id' => 'nullable|exists:users,id',
            'observer_comments' => 'nullable|string|max:1000',
            'observer_signed_at' => 'nullable|date',
            'referee_digital_signature' => 'nullable|string|max:255',
            'referee_signed_at' => 'nullable|date',
            'penalty_shootout_data' => 'nullable|array',
            'var_decisions' => 'nullable|array',
            'status' => 'nullable|string|in:draft,submitted,validated,rejected',
            'submitted_at' => 'nullable|date',
            'validated_at' => 'nullable|date',
            'validated_by' => 'nullable|exists:users,id',
            'validation_notes' => 'nullable|string|max:1000',
            'rejected_at' => 'nullable|date',
            'rejected_by' => 'nullable|exists:users,id',
            'rejection_reason' => 'nullable|string|max:1000',
            'version' => 'nullable|integer|min:1',
            'change_log' => 'nullable|array',
            'stage' => 'nullable|string|in:draft,before_game,in_progress,after_game,fa_validation,completed',
            'stage_in_progress_at' => 'nullable|date',
            'stage_before_game_signed_at' => 'nullable|date',
            'stage_after_game_signed_at' => 'nullable|date',
            'stage_fa_validated_at' => 'nullable|date',
            'home_team_lineup_signature' => 'nullable|string|max:255',
            'away_team_lineup_signature' => 'nullable|string|max:255',
            'home_team_lineup_signed_at' => 'nullable|date',
            'away_team_lineup_signed_at' => 'nullable|date',
            'home_team_post_match_signature' => 'nullable|string|max:255',
            'away_team_post_match_signature' => 'nullable|string|max:255',
            'home_team_post_match_signed_at' => 'nullable|date',
            'away_team_post_match_signed_at' => 'nullable|date',
            'home_team_post_match_comments' => 'nullable|string|max:1000',
            'away_team_post_match_comments' => 'nullable|string|max:1000',
            'fa_validated_by' => 'nullable|exists:users,id',
            'fa_validation_notes' => 'nullable|string|max:1000',
            'assigned_referee_id' => 'nullable|exists:users,id',
            'referee_assigned_at' => 'nullable|date',
            'lineups_locked' => 'nullable|boolean',
            'lineups_locked_at' => 'nullable|date',
            'match_events_locked' => 'nullable|boolean',
            'match_events_locked_at' => 'nullable|date',
            'stage_transition_log' => 'nullable|array',
            'user_action_log' => 'nullable|array',
            'signed_sheet_path' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'match_id.exists' => 'The selected match does not exist.',
            'match_number.unique' => 'This match number is already taken.',
            'home_team_score.min' => 'Home team score cannot be negative.',
            'home_team_score.max' => 'Home team score cannot exceed 50.',
            'away_team_score.min' => 'Away team score cannot be negative.',
            'away_team_score.max' => 'Away team score cannot exceed 50.',
            'match_status.in' => 'Invalid match status.',
            'status.in' => 'Invalid status.',
            'stage.in' => 'Invalid stage.',
        ];
    }
} 