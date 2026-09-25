<?php

namespace App\Http\Requests\Api\V1\MatchEvent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatchEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('match_event'));
    }

    public function rules(): array
    {
        return [
            'match_id' => 'sometimes|integer|exists:matches,id',
            'match_sheet_id' => 'sometimes|integer|exists:match_sheets,id',
            'player_id' => 'sometimes|integer|exists:players,id',
            'team_id' => 'sometimes|integer|exists:teams,id',
            'assisted_by_player_id' => 'sometimes|integer|exists:players,id',
            'substituted_player_id' => 'sometimes|integer|exists:players,id',
            'event_type' => 'sometimes|string|in:goal,assist,yellow_card,red_card,substitution_in,substitution_out,injury,save,missed_penalty,penalty_saved,own_goal,var_decision,free_kick_goal,header_goal,volley_goal,long_range_goal,penalty_goal,match_start,half_time,full_time,extra_time_start,extra_time_end,penalty_shootout_start,penalty_shootout_end',
            'type' => 'sometimes|string|max:100',
            'minute' => 'sometimes|integer|min:0|max:120',
            'extra_time_minute' => 'sometimes|integer|min:0|max:30',
            'period' => 'sometimes|string|in:first_half,second_half,extra_time_first,extra_time_second,penalty_shootout',
            'event_data' => 'sometimes|array',
            'description' => 'sometimes|string|max:1000',
            'location' => 'sometimes|string|max:200',
            'severity' => 'sometimes|string|in:low,medium,high',
            'is_confirmed' => 'sometimes|boolean',
            'is_contested' => 'sometimes|boolean',
            'contest_reason' => 'sometimes|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'match_id.exists' => 'The selected match does not exist.',
            'team_id.exists' => 'The selected team does not exist.',
            'player_id.exists' => 'The selected player does not exist.',
            'assisted_by_player_id.exists' => 'The selected assisting player does not exist.',
            'substituted_player_id.exists' => 'The selected substituted player does not exist.',
            'event_type.in' => 'The event type must be a valid match event type.',
            'minute.min' => 'The minute must be at least 0.',
            'minute.max' => 'The minute cannot exceed 120.',
            'extra_time_minute.min' => 'The extra time minute must be at least 0.',
            'extra_time_minute.max' => 'The extra time minute cannot exceed 30.',
            'period.in' => 'The period must be a valid match period.',
            'severity.in' => 'The severity must be low, medium, or high.'
        ];
    }
} 